<?php

namespace Regur\LMVC\Framework\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRawMigrationCommand extends Command
{
    protected static $defaultName = 'make:raw-migration';

    protected function configure()
    {
        $this
            ->setDescription('Create a new raw SQL migration file')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration file')
            ->addOption(
                'table',
                't',
                InputOption::VALUE_OPTIONAL,
                'Table name to use inside migration SQL examples'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name  = $input->getArgument('name');
        $tableName = $input->getOption('table') ?: 'test';

        $timestamp = date('Y_m_d_His');

        $fileName = "{$timestamp}_{$name}";

        $path  = getcwd() . "/database/migrations";

        // TEMPLATE
        $template = $this->getTemplate($tableName);

        // Ensure migrations directory exists
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file = "{$path}/{$fileName}.php";

        file_put_contents($file, $template);

        $output->writeln("<info>Migration created:</info> $fileName");

        return Command::SUCCESS;
    }

    private function getTemplate($tableName)
    {
                $template = <<<PHP
<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Write your raw SQL here
        // Example:
        // Schema::execute("ALTER TABLE {$tableName} ADD COLUMN count INT DEFAULT 0");
    }

    public function down(): void
    {
        // Reverse your raw SQL here
        // Example:
        // Schema::execute("ALTER TABLE {$tableName} DROP COLUMN count");
    }
};
PHP;

        return $template;
    }
}
