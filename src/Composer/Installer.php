<?php

namespace Regur\LMVC\Framework\Composer;

use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;

class Installer implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        // nothing needed
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // nothing needed
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // nothing needed
    }

    public static function getSubscribedEvents()
    {
        return [
            'post-autoload-dump' => 'run'
        ];
    }

    public static function run(Event $event)
    {
        $io = $event->getIO();
        $io->write("<info>Running LMVC installer...</info>");

        // Ensure vendor binaries exist
        $lmvcdb = PHP_BINARY . ' vendor/bin/lmvcdb';

        $commands = [
            'install',
            'install:ui',
        ];

        foreach ($commands as $cmd) {
            $io->write("<info>Executing: lmvcdb {$cmd}</info>");

            $command = "{$lmvcdb} {$cmd}";
            exec($command, $output, $status);

            foreach ($output as $line) {
                $io->write($line);
            }

            if ($status !== 0) {
                $io->writeError("<error>LMVC command '{$cmd}' failed.</error>");
                return;
            }

            $output = []; // reset buffer
        }

        $io->write("<info>LMVC installation completed successfully.</info>");
        $io->write("<comment>You can now use:</comment>");
        $io->write("<comment>php lmvcdb</comment>");
        $io->write("<comment>UI available at /lmvc-migrations</comment>");
    }
}
