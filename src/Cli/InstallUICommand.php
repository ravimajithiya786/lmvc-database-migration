<?php

namespace Regur\LMVC\Framework\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallUICommand extends Command
{
    protected static $defaultName = 'install:ui';

    protected function configure()
    {
        $this->setDescription('Install LMVC migration UI into project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $log = [];
        $log[] = "========== UI Install Started at " . date('Y-m-d H:i:s') . " ==========";

        $projectRoot = getcwd();

       

        return Command::SUCCESS;
    }

    private function writeLog(array $log): void
    {
        $logDir = __DIR__ . '/../../bin/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logPath = $logDir . '/install-ui.log';
        file_put_contents($logPath, implode("\n", $log) . "\n", FILE_APPEND);
    }
}
