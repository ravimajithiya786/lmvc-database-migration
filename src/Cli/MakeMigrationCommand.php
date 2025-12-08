<?php

namespace Regur\LMVC\Framework\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigrationCommand extends Command
{
    protected static $defaultName = 'make:migration';

    protected function configure()
    {
        $this
            ->setDescription('Create a new migration file')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration')
            ->addOption('table', null, InputOption::VALUE_OPTIONAL, 'The table to modify');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name        = $input->getArgument('name');
        $tableOption = $input->getOption('table');
        $isAlter     = !empty($tableOption);

        // For create migrations, determine table name automatically
        $tableName = $this->generateTableName($name);

         if (!$isAlter) {
            $existing = glob("database/migrations/*create_{$tableName}_table.php");

            if (!empty($existing)) {
                $output->writeln("<error>Create migration already exists for table '{$tableName}'. Please choose another name.</error>");
                return Command::FAILURE;
            }
        }

        $timestamp = date('Y_m_d_His');
        $fileName  = "database/migrations/{$timestamp}_{$name}.php";

        //
        // CREATE MIGRATION TEMPLATE
        //
        $createTemplate = <<<PHP
<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;
use Regur\LMVC\Framework\Database\Core\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        //
        // ALTER MIGRATION TEMPLATE
        //
        $alterTemplate = <<<PHP
<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;
use Regur\LMVC\Framework\Database\Core\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('{$tableOption}', function (Blueprint \$table) {
            // Add your alterations here
            // Example: \$table->integer('price')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('{$tableOption}', function (Blueprint \$table) {
            // Reverse your alterations here
            // Example: \$table->dropColumn('price');
        });
    }
};
PHP;

        // Pick correct template for file
        $template = $isAlter ? $alterTemplate : $createTemplate;

        // Create folder if missing
        if (!is_dir('database/migrations')) {
            mkdir('database/migrations', 0777, true);
        }

        // Save file
        file_put_contents($fileName, $template);

        $output->writeln("<info>Migration created:</info> $fileName");

        return Command::SUCCESS;
    }

    private function generateTableName(string $name): string
    {
        // e.g. create_products_table → products
        $result = preg_replace('/^create_(.*?)_table$/', '$1', $name);
        return strtolower($result ?: $name);
    }
}
