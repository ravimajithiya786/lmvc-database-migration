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

        // Determine migration type and parameters from name
        $info = $this->parseMigrationName($name, $tableOption);

        // If it's a create operation, ensure we don't already have a create migration for the same table
        if ($info['type'] === 'create') {
            $tableName = $info['table'];
            $existing = glob("database/migrations/*create_{$tableName}_table.php");
            if (!empty($existing)) {
                $output->writeln("<error>Create migration already exists for table '{$tableName}'. Please choose another name.</error>");
                return Command::FAILURE;
            }
        }

        $timestamp = date('Y_m_d_His');

        $fileName  = "{$timestamp}_{$name}";
        
        $path = getcwd() . "/database/migrations";

        // Select template based on parsed info
        $template = $this->buildTemplate($info);

        // Create folder if missing
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file = "{$path}/{$fileName}.php";

        // Save file
        file_put_contents($file, $template);

        $output->writeln("<info>Migration created:</info> $fileName");

        return Command::SUCCESS;
    }

    /**
     * Parse migration name to determine operation and targets.
     *
     * Returns an array with:
     *  - type: create|alter|rename_table|add_column|drop_column|rename_column|drop_table|raw
     *  - table: table name (if applicable)
     *  - to_table: new table name (for rename_table)
     *  - column: column name (for add/drop/rename column)
     *  - to_column: new column name (for rename column)
     *  - name: original name
     */
    private function parseMigrationName(string $name, ?string $tableOption = null): array
    {
        $info = [
            'type' => 'create',
            'table' => $this->generateTableName($name),
            'to_table' => null,
            'column' => null,
            'to_column' => null,
            'name' => $name,
        ];

        // If explicit --table provided, prefer alter template
        if (!empty($tableOption)) {
            return array_merge($info, ['type' => 'alter', 'table' => $tableOption]);
        }

        // rename table: rename_{from}_to_{to}
        if (preg_match('/^rename_([a-z0-9]+)_to_([a-z0-9]+)$/i', $name, $m)) {
            return array_merge($info, [
                'type' => 'rename_table',
                'table' => strtolower($m[1]),
                'to_table' => strtolower($m[2]),
            ]);
        }

        // add column: add_{column}_to_{table}
        if (preg_match('/^add_([a-z0-9]+)_to_([a-z0-9_]+)$/i', $name, $m)) {
            return array_merge($info, [
                'type' => 'add_column',
                'column' => strtolower($m[1]),
                'table' => strtolower($m[2]),
            ]);
        }

        // drop column: drop_{column}_from_{table}
        if (preg_match('/^drop_([a-z0-9]+)_from_([a-z0-9_]+)$/i', $name, $m)) {
            return array_merge($info, [
                'type' => 'drop_column',
                'column' => strtolower($m[1]),
                'table' => strtolower($m[2]),
            ]);
        }

        // rename column: rename_{old}_to_{new}_in_{table}
        if (preg_match('/^rename_([a-z0-9]+)_to_([a-z0-9]+)_in_([a-z0-9_]+)$/i', $name, $m)) {
            return array_merge($info, [
                'type' => 'rename_column',
                'column' => strtolower($m[1]),
                'to_column' => strtolower($m[2]),
                'table' => strtolower($m[3]),
            ]);
        }

        // drop table: drop_{table}_table
        if (preg_match('/^drop_([a-z0-9_]+)_table$/i', $name, $m)) {
            return array_merge($info, [
                'type' => 'drop_table',
                'table' => strtolower($m[1]),
            ]);
        }

        // create (default) - create_{table}_table already handled by generateTableName
        return $info;
    }

    private function buildTemplate(array $info): string
    {
        // Common header
        $header = <<<PHP
<?php

use Regur\\LMVC\\Framework\\Database\\Core\\Migration;
use Regur\\LMVC\\Framework\\Database\\Core\\Schema;
use Regur\\LMVC\\Framework\\Database\\Core\\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
PHP;

        $footer = <<<PHP

    }

    public function down(): void
    {
%s
    }
};
PHP;

        switch ($info['type']) {
            case 'rename_table':
                $up = "        Schema::renameTable('{$info['table']}', '{$info['to_table']}');\n";
                $down = "        Schema::renameTable('{$info['to_table']}', '{$info['table']}');\n";
                return $header . "\n" . $up . sprintf($footer, $down);

            case 'add_column':
                $up = <<<PHP
        Schema::table('{$info['table']}', function (Blueprint \$table) {
            // Example: \$table->{$this->guessColumnType($info['column'])}('{$info['column']}')->nullable();
        });
PHP;
                $down = <<<PHP
        Schema::table('{$info['table']}', function (Blueprint \$table) {
            // Example: \$table->dropColumn('{$info['column']}');
        });
PHP;
                return $header . "\n" . $up . sprintf($footer, $down);

            case 'drop_column':
                $up = <<<PHP
        Schema::table('{$info['table']}', function (Blueprint \$table) {
            // Example: \$table->dropColumn('{$info['column']}');
        });
PHP;
                $down = <<<PHP
        Schema::table('{$info['table']}', function (Blueprint \$table) {
            // Example: \$table->{$this->guessColumnType($info['column'])}('{$info['column']}')->nullable();
        });
PHP;
                return $header . "\n" . $up . sprintf($footer, $down);

            case 'rename_column':
                $up = "        Schema::table('{$info['table']}', function (Blueprint \$table) {\n            // Example: \$table->renameColumn('{$info['column']}', '{$info['to_column']}');\n        });\n";
                $down = "        Schema::table('{$info['table']}', function (Blueprint \$table) {\n            // Example: \$table->renameColumn('{$info['to_column']}', '{$info['column']}');\n        });\n";
                return $header . "\n" . $up . sprintf($footer, $down);

            case 'drop_table':
                $up = "        Schema::dropIfExists('{$info['table']}');\n";
                $down = "        Schema::create('{$info['table']}', function (Blueprint \$table) {\n            \$table->id();\n            \$table->timestamps();\n        });\n";
                return $header . "\n" . $up . sprintf($footer, $down);

            case 'alter':
                // generic alter template using provided --table
                $up = <<<PHP
        Schema::table('{$info['table']}', function (Blueprint \$table) {
            // Add your alterations here
        });
PHP;
                $down = <<<PHP
        Schema::table('{$info['table']}', function (Blueprint \$table) {
            // Reverse your alterations here
        });
PHP;
                return $header . "\n" . $up . sprintf($footer, $down);

            case 'create':
            default:
                // Default create template
                $table = $info['table'] ?: 'table_name';
                $up = <<<PHP
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
PHP;
                $down = "        Schema::dropIfExists('{$table}');\n";
                return $header . "\n" . $up . sprintf($footer, $down);
        }
    }

    private function generateTableName(string $name): string
    {
        // e.g. create_products_table → products
        $result = preg_replace('/^create_(.*?)_table$/', '$1', $name);
        return strtolower($result ?: $name);
    }

    /**
     * Guess a basic column type from column name (very naive).
     * You can extend this as you like. This is only used to populate comment/example.
     */
    private function guessColumnType(string $column): string
    {
        if (preg_match('/_id$/', $column)) {
            return 'integer';
        }
        if (preg_match('/is_|has_/', $column)) {
            return 'boolean';
        }
        if (preg_match('/price|amount|total|cost/', $column)) {
            return 'decimal';
        }
        return 'string';
    }
}
