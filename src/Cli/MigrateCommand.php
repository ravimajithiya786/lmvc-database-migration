<?php

namespace Regur\LMVC\Framework\Cli;

use Regur\LMVC\Framework\Database\Core\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';
    protected $pdo = null;
    protected $migrationsPath;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = dirname(__DIR__, 5) . '/database/migrations/';
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Run the database migrations')
            ->addOption('fresh', null, InputOption::VALUE_NONE, 'Drop all tables and re-run migrations')
            ->addOption('refresh', null, InputOption::VALUE_NONE, 'Rollback all migrations and re-run them')
            ->addOption('class', null, InputOption::VALUE_REQUIRED, 'Run a specific migration class')
            ->addOption('up', null, InputOption::VALUE_NONE, 'Run all migrations')
            ->addOption('down', null, InputOption::VALUE_NONE, 'Drop all the tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        //Get connection instance
        $pdo = $this->pdo;

        // Ensure the migrations table exists
        $this->ensureMigrationsTable($pdo);

        $migrationsPath = $this->migrationsPath;
        $migrations = glob($migrationsPath . '*.php');

        if ($input->getOption('up')) {
            $nextBatch = $this->getLastBatchNumber($pdo) + 1;
            $this->executeAllMigrations($migrations, $pdo, $output, $nextBatch);
            $output->writeln("<info>All pending migrations executed successfully.</info>");
            return Command::SUCCESS;
        }

        if ($input->getOption('down')) {
            $lastBatch = $this->getLastBatchNumber($pdo);

            if ($lastBatch === 0) {
                $output->writeln("<info>Droping last batch of migrations.</info>");
                return Command::SUCCESS;
            }

            $migrations = $this->getMigrationsByBatch($pdo, $lastBatch);
            $this->executeDownMigrations($migrations, $output);
            $this->deleteMigrationsByBatch($pdo, $lastBatch);
            $output->writeln("<comment>Rolled back last batch (Batch {$lastBatch}).</comment>");
            return Command::SUCCESS;
        }

        if ($input->getOption('fresh')) {
            $output->writeln("<comment>Dropping all tables..., Migrating all tables...</comment>");
            Schema::dropAllTables();
            $this->ensureMigrationsTable($pdo);
        }

        if ($input->getOption('refresh')) {
            $output->writeln("<comment>Database all tables refreshed</comment>");
            foreach (array_reverse($migrations) as $migrationFile) {
                require_once $migrationFile;
                $migration = require $migrationFile;
                $migration->down();
            }
            $this->clearMigrationsTable($pdo);
        }

        if ($class = $input->getOption('class')) {
            $migrationFile = $migrationsPath . $class . '.php';
            if (!file_exists($migrationFile)) {
                $output->writeln("<error>Migration class not found:</error> $class");
                return Command::FAILURE;
            }

            require_once $migrationFile;
            $migration = require $migrationFile;

            if ($this->isMigrationExecuted($pdo, $class)) {
                $output->writeln("<comment>Migration already executed:</comment> $class");
                return Command::SUCCESS;
            }
            $nextBatch = $this->getLastBatchNumber($pdo) + 1;
            $migration->up();
            $this->recordMigration($pdo, $class, $nextBatch);
            $output->writeln("<info>Migration executed:</info> $class");

            return Command::SUCCESS;
        }
        $executedMigrationsCount = $this->executeAllMigrations($migrations, $pdo, $output);
        if($executedMigrationsCount > 0){
            $output->writeln("<info>All migrations executed successfully.</info>");
        }else{
            $output->writeln("<comment>No pending migrations found</comment>");
        }
        return Command::SUCCESS;
    }

    private function executeAllMigrations($migrations, $pdo, $output)
    {
        $executedMigrationsCount = 0;
        foreach ($migrations as $migrationFile) {
            require_once $migrationFile;
            $migration = require $migrationFile;
            $className = basename($migrationFile, '.php');

            if ($this->isMigrationExecuted($pdo, $className)) {
                continue;
            }

            $migration->up();

            // Count it
            $executedMigrationsCount += 1;
            
            // Record it in migrations table
            $nextBatch = $this->getLastBatchNumber($pdo) + 1;
            $this->recordMigration($pdo, $className, $nextBatch);
            $output->writeln("<info>Migration executed:</info> $className");
        }
        return $executedMigrationsCount;
    }


    private function ensureMigrationsTable($pdo)
    {

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migrationName VARCHAR(255) NOT NULL,
                batch INT NOT NULL DEFAULT 1,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }


    private function isMigrationExecuted($pdo, $migrationName): bool
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migrationName = ?");
        $stmt->execute([$migrationName]);
        return $stmt->fetchColumn() > 0;
    }

    private function recordMigration($pdo, $migrationName, $batch)
    {
        $stmt = $pdo->prepare("INSERT INTO migrations (migrationName, batch) VALUES (?, ?)");
        $stmt->execute([$migrationName, $batch]);
    }

    private function clearMigrationsTable($pdo)
    {
        $pdo->exec("DELETE FROM migrations");
    }

    private function getLastBatchNumber($pdo)
    {
        $stmt = $pdo->query("SELECT MAX(batch) FROM migrations");
        return $stmt->fetchColumn() ?: 0;
    }

    private function getMigrationsByBatch($pdo, $batch)
    {
        $stmt = $pdo->prepare("SELECT migrationName FROM migrations WHERE batch = ?");
        $stmt->execute([$batch]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function deleteMigrationsByBatch($pdo, $batch)
    {
        $stmt = $pdo->prepare("DELETE FROM migrations WHERE batch = ?");
        $stmt->execute([$batch]);
    }

    private function executeDownMigrations($migrations, $output)
    {
        foreach ($migrations as $migrationName) {
            $migrationsPath = $this->migrationsPath;
            $migrationFile = $migrationsPath."/{$migrationName}.php";

            if (file_exists($migrationFile)) {
                require_once $migrationFile;
                $migration = require $migrationFile;
                $migration->down();
                $output->writeln("<comment>Rolled back:</comment> $migrationName");
            } else {
                $output->writeln("<error>Migration file not found:</error> $migrationName");
            }
        }
    }
}
