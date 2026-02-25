<?php

namespace Regur\LMVC\Framework\Composer;

use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\Script\Event;

class Installer implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'post-install-cmd' => 'install',
            'post-update-cmd'  => 'install',
            'pre-package-uninstall' => 'remove',
        ];
    }

    public static function install(Event $event)
    {
        $io = $event->getIO();
        $io->write('<info>Installing lmvcdb binary...</info>');

        $projectRoot = getcwd();
        $target = $projectRoot . '/lmvcdb';
        $source = __DIR__ . '/../../bin/lmvcdb';

        if (!file_exists($source)) {
            $io->writeError('<error>lmvcdb source not found.</error>');
            return;
        }

        copy($source, $target);
        chmod($target, 0755);

        $io->write('<info>lmvcdb installed.</info>');
    }

    public static function remove(PackageEvent $event)
    {
        $operation = $event->getOperation();

        if (! $operation instanceof UninstallOperation) {
            return;
        }

        if ($operation->getPackage()->getName() !== 'regur/lmvc-database-migration') {
            return;
        }

        $io = $event->getIO();
        $io->write('<info>Removing lmvcdb binary...</info>');

        $target = getcwd() . '/lmvcdb';

        if (file_exists($target)) {
            unlink($target);
            $io->write('<info>lmvcdb removed.</info>');
        } else {
            $io->write('<comment>lmvcdb not found.</comment>');
        }
    }
}
