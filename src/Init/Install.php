<?php

namespace Regur\LMVC\Framework\Init;

class Install
{
    public static function copyCommand()
    {
        ob_start();
        $log = [];

        // Add timestamp for the log start
        $log[] = "========== Install Started at " . date('Y-m-d H:i:s') . " ==========";

        $targetPath = getcwd() . '/../database/command';
        $log[] = microtime(true) . ' targetPath: ' . $targetPath;

        $sourcePath = __DIR__ . '/../../bin/command';
        $log[] = microtime(true) . ' sourcePath: ' . $sourcePath;

        // Create directory if missing
        if (!file_exists(dirname($targetPath))) {
            if (mkdir(dirname($targetPath), 0755, true)) {
                $log[] = microtime(true) . " Created directory: " . dirname($targetPath);
            } else {
                $log[] = microtime(true) . " Failed to create directory: " . dirname($targetPath);
            }
        }

        // Copy command file
        if (!copy($sourcePath, $targetPath)) {
            $log[] = microtime(true) . " Failed to copy command to database folder";
        } else {
            if (chmod($targetPath, 0755)) {
                $log[] = microtime(true) . " command copied to ./database/command";
            } else {
                $log[] = microtime(true) . " Failed to set permissions for command";
            }
        }

        // Save log inside bin/
        $logPath = __DIR__ . '/../../bin/install.log';
        file_put_contents($logPath, implode("\n", $log) . "\n", FILE_APPEND);

        $log[] = "========== Install Ended ==========";

        return $log;
    }
}
