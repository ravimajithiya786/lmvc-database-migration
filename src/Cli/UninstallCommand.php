<?php

namespace Regur\LMVC\Framework\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends Command
{
    protected static $defaultName = 'uninstall';

    protected function configure()
    {
        $this->setDescription('Remove lmvcdb command from project root');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectRoot = getcwd();
        $targetPath  = $projectRoot . '/lmvcdb';

        if (!file_exists($targetPath)) {
            $output->writeln('<comment>lmvcdb not found. Nothing to uninstall.</comment>');
            return Command::SUCCESS;
        }

        if (!is_writable($targetPath)) {
            $output->writeln('<error>lmvcdb exists but is not writable.</error>');
            return Command::FAILURE;
        }

        if (!unlink($targetPath)) {
            $output->writeln('<error>Failed to remove lmvcdb.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>lmvcdb successfully removed.</info>');

        return Command::SUCCESS;
    }
}
