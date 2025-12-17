<?php

namespace Regur\LMVC\Framework\Cli;

use Regur\LMVC\Framework\Database\Core\Seeder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCommand extends Command
{
    protected static $defaultName = 'seed';

    protected \PDO $pdo;
    protected string $seedersPath;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->pdo = $pdo;
        $this->seedersPath = dirname(__DIR__, 2) . '/database/seeders';
    }

    protected function configure(): void
    {
        $this->addOption(
            'class',
            null,
            InputOption::VALUE_REQUIRED,
            'Run a specific seeder class'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!is_dir($this->seedersPath)) {
            $output->writeln('<error>No seeders directory found.</error>');
            return Command::FAILURE;
        }

        $class = $input->getOption('class');

        if ($class) {
            return $this->runSingleSeeder($class, $output);
        }

        return $this->runAllSeeders($output);
    }

    /* ============================================================
        Run all seeders
    ============================================================ */
    protected function runAllSeeders(OutputInterface $output): int
    {
        $files = glob($this->seedersPath . '/*.php');

        // Sort by file modification time (oldest → newest)
        usort($files, function ($a, $b) {
            return filemtime($a) <=> filemtime($b);
        });

        foreach ($files as $file) {
            $this->runSeederFile($file, $output);
        }

        $output->writeln('<info>Database seeding completed.</info>');
        return Command::SUCCESS;
    }


    /* ============================================================
        Run single seeder
    ============================================================ */
    protected function runSingleSeeder(string $class, OutputInterface $output): int
    {
        $file = $this->seedersPath . '/' . $class . '.php';

        if (!file_exists($file)) {
            $output->writeln("<error>Seeder not found: {$class}</error>");
            return Command::FAILURE;
        }

        $this->runSeederFile($file, $output);

        $output->writeln('<info>Database seeding completed.</info>');
        return Command::SUCCESS;
    }

    /* ============================================================
        Execute a seeder file
    ============================================================ */
    protected function runSeederFile(string $file, OutputInterface $output): void
    {
        $seeder = require $file;

        if (!$seeder instanceof Seeder) {
            throw new \Exception("Invalid seeder file: {$file}");
        }

        $seeder->setConnection($this->pdo);

        $output->writeln("Seeding: " . basename($file));
        $seeder->run();
    }
}
