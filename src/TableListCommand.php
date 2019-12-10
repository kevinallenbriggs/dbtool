<?php

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TableListCommand extends BaseCommand
{
    protected static $defaultName = 'table:list';

    protected function configure()
    {
        $this->setDescription('Lists all tables within the database.');

        $this->addOption(
            'show-engine',
            null,
            InputOption::VALUE_NONE,
            'Displays the storage engine for each table.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $this->driver === $this::SQLITE_DRIVER ?
            "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;" :
            "SHOW TABLES;";

        $stmt = $this->connection->query($query);
        $results = [];

        $table = new Table($output);
        $table->setHeaders(['Tables']);


        while ($row = $stmt->fetch(PDO::FETCH_COLUMN)) {
            $table->addRow([$row]);
        }

        $table->render();
    }
}
