<?php

namespace Regur\LMVC\Framework\Init;

class Install
{
    public static function copyCommand()
    {
        ob_start();
        
        $targetPath = getcwd() . '/database/command';
        $sourcePath = __DIR__ . '/../../bin/command';
        $log = [];
        if (!file_exists(dirname($targetPath))) {
            if (mkdir(dirname($targetPath), 0755, true)) {
                $log[] = "Created directory: " . dirname($targetPath);
            } else {
                $log[] = "Failed to create directory: " . dirname($targetPath);
            }
        }

        if (!copy($sourcePath, $targetPath)) {
            $log[] = "Failed to copy migration-command to database folder";
        } else {
            if (chmod($targetPath, 0755)) {
                $log[] = "migration-command copied to ./database/migration-command";
            } else {
                $log[] = "Failed to set permissions for migration-command";
            }
        }

        // Save log to bin directory
        $logPath = __DIR__ . '/../../bin/install.log';
        file_put_contents($logPath, implode("\n", $log));

        // Return log for buffering
        return $log;
    }
}
