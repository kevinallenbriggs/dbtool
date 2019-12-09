<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TableListCommand extends Command
{
    protected static $defaultName = 'table:list';

    public function __construct(array $params = null)
    {


        parent::__construct();
    }
    protected function configure()
    {
        $this->setDescription('Lists all tables within the database.');

        $this->addOption(
            'show-engine',
            null,
            InputOption::VALUE_NONE,
            'Displays the storage engine for each table.'
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

        $tables = $connection->query("SHOW TABLES;");

        $output->writeln($input->getOption('database'));

        for ($i = 0; $i < strlen($input->getOption('database')) + 2; $i++) {
            $output->write('*');
        }
        $output->write("\n");

        while ($row = $tables->fetch()) {

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
    }
}
