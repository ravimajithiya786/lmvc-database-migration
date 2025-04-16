<?php

namespace Regur\LMVC\Framework\Init;

class Install
{
    public static function copyCommand()
    {
        $targetPath = getcwd() . '/database/command';
        $sourcePath = __DIR__ . '/../../bin/command';

        if (!file_exists(dirname($targetPath))) {
            mkdir(dirname($targetPath), 0755, true);
        }

        if (!copy($sourcePath, $targetPath)) {
            echo "Failed to copy migration-command to database folder.\n";
        } else {
            chmod($targetPath, 0755);
            echo "migration-command copied to ./database/migration-command\n";
        }
    }
}
