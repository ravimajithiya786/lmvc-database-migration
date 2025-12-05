<?php

namespace Regur\LMVC\Framework\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRawMigrationCommand extends Command
{
    protected static $defaultName = 'make:raw-migration';

    protected function configure()
    {
        $this
            ->setDescription('Create a new raw SQL migration file')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        $timestamp = date('Y_m_d_His');
        $fileName  = "database/migrations/{$timestamp}_{$name}.php";

        //
        // TEMPLATE
        //
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
        // Schema::execute("ALTER TABLE users ADD COLUMN age INT DEFAULT 0");
    }

    public function down(): void
    {
        // Reverse your raw SQL here
        // Example:
        // Schema::execute("ALTER TABLE users DROP COLUMN age");
    }
};
PHP;

        // Ensure migrations directory exists
        if (!is_dir('database/migrations')) {
            mkdir('database/migrations', 0777, true);
        }

        file_put_contents($fileName, $template);

        $output->writeln("<info>Raw SQL migration created:</info> $fileName");

        return Command::SUCCESS;
    }
}
