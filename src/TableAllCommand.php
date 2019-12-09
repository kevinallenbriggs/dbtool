<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TableAllCommand extends Command
{
    protected static $defaultName = 'table:all';

    protected function configure()
    {
        $this->setDescription('Selects all data from a given table.');

        $this->addArgument(
            'table',
            InputArgument::REQUIRED,
            'The table to query.'
        );

        $this->addOption(
            'host',
            'H',
            InputOption::VALUE_REQUIRED,
            'The host to connect to'
        );

        $this->addOption(
            'username',
            'u',
            InputOption::VALUE_REQUIRED,
            'The user to connect as'
        );

        $this->addOption(
            'password',
            'p',
            InputOption::VALUE_REQUIRED,
            'The password to connect with'
        );

        $this->addOption(
            'database',
            'd',
            InputOption::VALUE_REQUIRED,
            'The database to connect to'
        );

        $this->addOption(
            'port',
            null,
            InputOption::VALUE_REQUIRED,
            'The port to connect to',
            3306
        );

        $this->addOption(
            'driver',
            null,
            InputOption::VALUE_REQUIRED,
            'The database driver to use.  Defaults to mysql',
            'pdo_mysql'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = new Configuration();
        $connectionParams = [
            'dbname' => $input->getOption('database'),
            'user' => $input->getOption('username'),
            'password' => $input->getOption('password'),
            'host' => $input->getOption('host'),
            'driver' => $input->getOption('driver'),
            'engine' => $input->getOption('engine'),
        ];

        $connection = DriverManager::getConnection(
            $connectionParams,
            $config
        );

        $tables_in_db = $connection->query("SHOW TABLES;");

        $results_table = new Table($output);

        while ($row = $tables_in_db->fetch()) {

            $engine = '';

            if ($input->getOption('engine')) {
                $engine = $connection->query(
                    "SELECT ENGINE
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = '{$input->getOption('database')}'
                    AND TABLE_NAME = '{$row['Tables_in_db']}'"
                );

                $output->writeln("{$row['Tables_in_db']} --> {$engine->fetch()['ENGINE']}");
            } else {
                $output->writeln($row['Tables_in_db']);
            }
        }

        $results_table->setHeaders([]);
        $results_table->setRows([]);
        $results_table->render();
    }
}
