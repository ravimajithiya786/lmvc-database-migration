<?php

namespace Regur\LMVC\Framework\Database\Init;

class Install
{
    public static function copyCommand()
    {
        // ob_start();
        // $log = [];

        // // Add timestamp for the log start
        // $log[] = "========== Install Started at " . date('Y-m-d H:i:s') . " ==========";

        // $targetPath = getcwd() . '/../lmvcdb';
        // $log[] = microtime(true) . ' targetPath: ' . $targetPath;

        // $sourcePath = __DIR__ . '/../../bin/lmvcdb';
        // $log[] = microtime(true) . ' sourcePath: ' . $sourcePath;

        // // Create directory if missing
        // if (!file_exists(dirname($targetPath))) {
        //     if (mkdir(dirname($targetPath), 0755, true)) {
        //         $log[] = microtime(true) . " Created directory: " . dirname($targetPath);
        //     } else {
        //         $log[] = microtime(true) . " Failed to create directory: " . dirname($targetPath);
        //     }
        // }

        // // Copy command file
        // if (!copy($sourcePath, $targetPath)) {
        //     $log[] = microtime(true) . " Failed to copy lmvcdb ".$targetPath;
        // } else {
        //     if (chmod($targetPath, 0755)) {
        //         $log[] = microtime(true) . " command copied to ".$targetPath;
        //     } else {
        //         $log[] = microtime(true) . " Failed to set permissions for lmvcdb";
        //     }
        // }

        // // Save log inside bin/
        // $logPath = __DIR__ . '/../../bin/database/install.log';
        // file_put_contents($logPath, implode("\n", $log) . "\n", FILE_APPEND);

        // $log[] = "========== Install Ended ==========";

        // return $log;
    }
}
