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

        $storagePath = $projectRoot . '/storage/migrations/drafts';
        $publicPath  = $projectRoot . '/public/lmvc-migrations';
        $configPath  = $projectRoot . '/config';
        $uiSource    = __DIR__ . '/../UI';
        $configSrc   = __DIR__ . '/../Config/lmvc-migration.php';

        $log[] = microtime(true) . " Project Root: $projectRoot";
        $log[] = microtime(true) . " UI Source: $uiSource";

        /* -------------------------------------------------
         | 1. Create storage directory
         |--------------------------------------------------*/
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
            $log[] = microtime(true) . " Created storage path: $storagePath";
        } else {
            $log[] = microtime(true) . " Storage path exists: $storagePath";
        }

        /* -------------------------------------------------
         | 2. Publish UI to public/
         |--------------------------------------------------*/
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
            $log[] = microtime(true) . " Created public UI path: $publicPath";
        }

        $this->copyDirectory($uiSource, $publicPath, $log);

        /* -------------------------------------------------
         | 3. Publish config
         |--------------------------------------------------*/
        if (!is_dir($configPath)) {
            mkdir($configPath, 0755, true);
            $log[] = microtime(true) . " Created config directory";
        }

        $configTarget = $configPath . '/lmvc-migration.php';

        if (!file_exists($configTarget)) {
            copy($configSrc, $configTarget);
            $log[] = microtime(true) . " Config published: $configTarget";
        } else {
            $log[] = microtime(true) . " Config already exists, skipped";
        }

        $log[] = "========== UI Install Ended ==========";
        $this->writeLog($log);

        $output->writeln("<info>LMVC Migration UI installed successfully.</info>");
        $output->writeln("<info>Access it via:</info>");
        $output->writeln("<comment>/lmvc-migrations</comment>");
        $output->writeln("<comment>⚠ Protect this route in production.</comment>");

        return Command::SUCCESS;
    }

    /* -------------------------------------------------
     | Helpers
     |--------------------------------------------------*/
    private function copyDirectory(string $source, string $destination, array &$log): void
    {
        $dir = opendir($source);

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $src = $source . '/' . $file;
            $dst = $destination . '/' . $file;

            if (is_dir($src)) {
                if (!is_dir($dst)) {
                    mkdir($dst, 0755, true);
                }
                $this->copyDirectory($src, $dst, $log);
            } else {
                copy($src, $dst);
                $log[] = microtime(true) . " Copied: $dst";
            }
        }

        closedir($dir);
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
