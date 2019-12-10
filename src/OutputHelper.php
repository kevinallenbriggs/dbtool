<?php

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class OutputHelper
{
    const ERROR = 1;
    const INFO = 2;
    const WARNING = 3;
    // const QUESTION = 4;

    public static function error(
        string $message,
        OutputInterface $output = null
    ) {
        self::output(self::formatMessage($message, self::ERROR), $output);
        exit;
    }

    // public static function question(
    //     string $message,
    //     OutputInterface $output = null
    // ) {
    //     self::output(self::formatMessage($message, self::QUESTION), $output);
    // }

    public static function info(
        string $message,
        OutputInterface $output = null
    ) {
        self::output(self::formatMessage($message, self::INFO), $output);
    }

    public static function warning(
        string $message,
        OutputInterface $output = null
    ) {
        self::output(self::formatMessage($message, self::WARNING), $output);
    }

    /**
     * Undocumented function
     *
     * @see https://symfony.com/doc/current/console/coloring.html
     * @param string $message
     * @param OutputInterface $output
     * @param integer $messageType
     * @return string
     */
    protected function formatMessage(string $message, int $messageType) :string
    {
        $format = '';

        switch ($messageType) {
            case self::ERROR:
                $format = 'error';  // white text on red background
                break;
            case self::WARNING:
                $format = 'comment';    // yellow text
                break;
            case self::INFO:
                $format = 'info';   // green text
                break;
            // case self::QUESTION:
            //     $format = 'question';   // black text on cyan background
            //     break;
        }

        return "<{$format}>{$message}</{$format}>";
    }

    /**
     * Outputs the message using the supplied OutputInterface or a new
     * ConsoleOutput if none was provided.
     *
     * @param string $message
     * @param OutputInterface $output
     * @return void
     */
    protected function output(string $message, OutputInterface $output = null)
    {
        $output = $output ?? new ConsoleOutput();
        $output->writeln($message . PHP_EOL);
    }
}
