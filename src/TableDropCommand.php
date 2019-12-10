<?php

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TableDropCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('table:drop')
             ->setDescription('Drops a table from the database.');

        $this->addArgument(
            'table',
            InputArgument::REQUIRED,
            'The name of the table to drop.'
        );

        $this->addOption(
            'backup',
            null,
            InputOption::VALUE_NONE,
            'Whether to create a dump of the table before removing it.'
        );

        $this->addOption(
            'backup-dir',
            null,
            InputOption::VALUE_REQUIRED,
            'Absolute path to a directory to save the dump to if the --backup options is specified.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // check if the table exists
        $stmt = $this->connection->prepare("SELECT 1 FROM `{$input->getArgument('table')}` LIMIT 1");
        if (!$stmt->execute()) {
            OutputHelper::error($stmt->errorInfo()[2], $output);
            exit;
        }

        // validate the backup options
        if ($input->getOption('backup-dir') && !$input->getOption('backup')) {
            OutputHelper::warning("--backup-dir flag ignored when --backup is not specified.");
        }

        if ($input->getOption('backup-dir')) {
            if (strpos($input->getOption('backup-dir'), '/') !== 0) {
                OutputHelper::error('--backup-dir must specify an absolute path.');
            }
        }

        // backup tables if necessary
        if ($input->getOption('backup')) {

        }

        // drop the table
    }
}
