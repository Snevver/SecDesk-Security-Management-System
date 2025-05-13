<?php

declare(strict_types=1);

namespace Ssms;

class Logger extends \Psr\Log\AbstractLogger
{
    use \Psr\Log\LoggerTrait;

    public function log($level, $message, mixed $context = null): void
    {
        $fh = fopen(APP_ROOT . 'log.txt', 'a');

        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] [$level] $message" . PHP_EOL;

        if (!is_null($context)) {
            $formattedMessage .= print_r($context, true) . PHP_EOL;
        }

        fwrite($fh, $formattedMessage);
        fclose($fh);
    }

    public static function write($level, $message, mixed $context = null): void
    {
        $logger = new self();
        $logger->log($level, $message, $context);
    }

    public static function __callStatic($name, $arguments)
    {
        $logger = new self();
        Logger::log($name, ...$arguments);
    }
}
