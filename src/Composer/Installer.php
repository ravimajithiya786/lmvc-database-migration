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

        $command = PHP_BINARY . ' vendor/bin/lmvcdb install';

        exec($command, $output, $status);

        foreach ($output as $line) {
            $io->write($line);
        }

        if ($status !== 0) {
            $io->writeError("<error>LMVC installer failed.</error>");
        } else {
            $io->write("<info>LMVC installer completed successfully.</info>");
        }
    }
}
