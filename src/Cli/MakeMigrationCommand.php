<?php

namespace Regur\LMVC\Framework\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigrationCommand extends Command
{
    protected static $defaultName = 'make:migration';

    protected function configure()
    {
        $this
            ->setDescription('Create a new migration file')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $tableName = $this->generateTableName($name);
        $timestamp = date('Y_m_d_His');
        $fileName = "database/migrations/{$timestamp}_{$name}.php";

        $template = <<<PHP
<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;
use Regur\LMVC\Framework\Database\Core\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        if (!is_dir('database/migrations')) {
            mkdir('database/migrations', 0777, true);
        }

        file_put_contents($fileName, $template);
        $output->writeln("<info>Migration created:</info> $fileName");

        return Command::SUCCESS;
    }

    private function generateTableName(string $name): string
    {
        return strtolower(preg_replace('/^create_(.*?)_table$/', '$1', $name));
    }
}
