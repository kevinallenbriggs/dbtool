<?php

use Symfony\Component\Console\Input\ArrayInput;
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
            'backup-dir',
            null,
            InputOption::VALUE_REQUIRED,
            'Path to backup the table to before dropping it.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // check if the table exists
        $table_list_command = $this->getApplication()->find('table:list');
        $list_command_arguments = new ArrayInput([
            'command' => 'table:list',
            
        ]);
        $stmt = $this->connection->prepare("INSERT INTO REGISTRY (name, value) VALUES (:name, :value)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':value', $value);

        // insert one row
        $name = 'one';
        $value = 1;
        $stmt->execute();

        // check if a backup should be made && perform if so

        // drop the table
    }
}