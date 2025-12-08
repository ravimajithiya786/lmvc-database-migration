<?php

namespace Regur\LMVC\Framework\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected static $defaultName = 'install';

    protected function configure()
    {
        $this->setDescription('Install lmvcdb command into project root');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $log = [];
        $log[] = "========== Install Started at " . date('Y-m-d H:i:s') . " ==========";

        // Path where lmvcdb should be installed in the project
        $projectRoot = getcwd();
        $targetPath  = $projectRoot . '/lmvcdb.php';

        // Source lmvcdb script inside vendor
        $sourcePath  = __DIR__ . '/../../bin/lmvcdb.php';

        $log[] = microtime(true) . " Project Root: $projectRoot";
        $log[] = microtime(true) . " Source Path: $sourcePath";
        $log[] = microtime(true) . " Target Path: $targetPath";

        // Ensure source exists
        if (!file_exists($sourcePath)) {
            $output->writeln("<error>Source lmvcdb script not found at: $sourcePath</error>");
            return Command::FAILURE;
        }

        // Copy lmvcdb script into project root
        if (!copy($sourcePath, $targetPath)) {
            $log[] = microtime(true) . " Failed to copy lmvcdb to $targetPath";
            $output->writeln("<error>Failed to copy lmvcdb to project.</error>");
            $this->writeLog($log);
            return Command::FAILURE;
        }

        // Set executable permissions
        if (!chmod($targetPath, 0755)) {
            $log[] = microtime(true) . " Failed to set 0755 permissions";
        } else {
            $log[] = microtime(true) . " Successfully set permissions";
        }

        $log[] = "========== Install Ended ==========";

        // Save log
        $this->writeLog($log);

        $output->writeln("<info>Installation complete!</info>");
        $output->writeln("<info>You can now run:</info>");
        $output->writeln("<comment>php lmvcdb</comment>");

        return Command::SUCCESS;
    }

    private function writeLog(array $log): void
    {
        // Ensure log directory exists
        $logDir = __DIR__ . '/../../bin/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logPath = $logDir . '/install.log';
        file_put_contents($logPath, implode("\n", $log) . "\n", FILE_APPEND);
    }
}
