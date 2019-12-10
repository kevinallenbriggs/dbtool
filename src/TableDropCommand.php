<?php

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
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
            'backup-dir',
            null,
            InputOption::VALUE_REQUIRED,
            'Path to backup the table to before dropping it.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // check if the table exists
        $stmt = $this->connection->prepare("SELECT 1 FROM :table LIMIT 1");
        $stmt->bindValue(':table', $input->getArgument('table'));
        dd($result = $stmt->execute());

        // check if a backup should be made && perform if so

        // drop the table
    }
}