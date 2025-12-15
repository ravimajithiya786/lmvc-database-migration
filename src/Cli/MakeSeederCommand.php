<?php

namespace Regur\LMVC\Framework\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSeederCommand extends Command
{
    protected static $defaultName = 'make:seeder';

    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Seeder name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $path = getcwd() . '/database/seeders';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $file = "{$path}/{$name}.php";
        $template = $this->getTemplate();

        file_put_contents($file, $template);

        $output->writeln("<info>Seeder created:</info> {$file}");

        return Command::SUCCESS;
    }

    private function getTemplate()
    {
        $template = <<<PHP
<?php

use Regur\LMVC\Framework\Database\Core\Seeder;

return new class extends Seeder
{
    public function run(): void
    {
       /* Example - Suppose database having animal table with name type and created_at columns then this seeder can insert the data into that
       /* Seeder::truncate('animals');
        Seeder::insert('animals',[
            [
                'name' => 'Dog',
                'type' => 'Mammal',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Cat',
                'type' => 'Mammal',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Eagle',
                'type' => 'Bird',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ]); */
    }
};

PHP;

    return $template;
    }
}
