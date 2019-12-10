<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;


abstract class BaseCommand extends Command
{
    const SQLITE_DRIVER = 'sqlite';

    const MYSQL_DRIVER = 'mysql';

     /**
     * The connection to the databse.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * The PDO dsn used to connect to the database.
     *
     * @var string
     */
    protected $dsn;

    /**
     * The type of database to connect to.
     *
     * @see https://www.php.net/manual/en/pdo.drivers.php
     * @var string
     */
    protected $driver;


    /**
     * Provides an output interface outside of the execute() method.
     *
     * @var \Symfony\Component\Console\Output\ConsoleOutput
     */
    protected $output;


    /**
     * Creates a new BaseCommand object.  Must call the parent constructor last.
     * 
     * @see https://symfony.com/doc/4.4/console.html#creating-a-command
     */
    public function __construct()
    {
        $this->output = new ConsoleOutput();
        $this->driver = $this->get_pdo_driver();

        // get the connection dsn
        $this->dsn = $this->driver === BaseCommand::SQLITE_DRIVER ? 
            $this->create_sqlite_dsn() :
            $this->create_mysql_dsn();

        $this->connection = $this->get_pdo_connection();

        parent::__construct();
    }
    
    /**
     * Configures the command.
     *
     * @return void
     */
    protected function configure()
    {
        // nothing to do here.
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // nothing to do here.
    }

    /**
     * Determines the PDO driver to use from the app environment variables.
     *
     * @return string
     */
    protected function get_pdo_driver() :string
    {
        if (!key_exists('DB_DRIVER', $_ENV)) {
            $this->output->writeln("<error>You must supply a database driver in your .env file.</error>");
            exit();
        }

        $driver = $_ENV['DB_DRIVER'];

        if (!in_array($driver, PDO::getAvailableDrivers())) {
            $this->output->writeln("<error>No {$driver} driver available.</error>");
            exit;
        }

        return $driver;
    }

    /**
     * Creates and returns a dsn to use for sqlite connections.
     *
     * @see https://www.php.net/manual/en/ref.pdo-sqlite.connection.php
     * @return string
     */
    protected function create_sqlite_dsn() :string
    {
        if (!key_exists('DB_PATH', $_ENV)) {
            $this->output->writeln("<error>You must supply a database path in your .env file when using SQLite.</error>");
            exit();
        }

        return sprintf('sqlite:%s', $_ENV['DB_PATH']);
    }

    /**
     * Creates and returns a dsn to use for mysql connections.
     * 
     * https://www.php.net/manual/en/ref.pdo-mysql.connection.php
     * @return string
     */
    protected function create_mysql_dsn() :string
    {
        if (!key_exists('DB_HOST', $_ENV) || !key_exists('DB_NAME', $_ENV)) {
            $this->output->writeln("<error>You must supply a database host and name in our .env file when using MySQL.</error>");
            exit();
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s',
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME']
        );

        if (key_exists('DB_PORT', $_ENV)) {
            $dsn .= sprintf(';port=%s', $_ENV['DB_PORT']);
        }

        return $dsn;
    }

    protected function get_pdo_connection() :\PDO
    {
        // if using MySQL credentials must be included
        if ($this->driver === BaseCommand::MYSQL_DRIVER) {
            if (!key_exists('DB_USER', $_ENV) || !key_exists('DB_PASSWORD', $_ENV)) {
                $this->output->writeln("<error>You must supply a database user and password in our .env file when using MySQL.</error>");
                exit();
            }

            return new \PDO(
                $this->dsn,
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );
        }

        // sqlite only requires the dsn
        return new \PDO($this->dsn);

    }
}
