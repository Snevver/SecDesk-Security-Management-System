<?php

//======================================================================
// PRINT LOGS IN LOG FILE
//======================================================================

declare(strict_types=1);

namespace Ssms;

class Logger extends \Psr\Log\AbstractLogger
{
    use \Psr\Log\LoggerTrait;

    public function log($level, $message, mixed $context = null): void
    {
        // Determine the log file based on the log level
        $logFile = match (strtolower($level)) {
            'error' => APP_ROOT . 'logs/error.log',
            'info' => APP_ROOT . 'logs/info.log',
            default => APP_ROOT . 'logs/log.log',
        };

        // Open the appropriate log file
        $fh = fopen($logFile, 'a');

        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] [$level] $message" . PHP_EOL;

        if (!is_null($context) && !empty($context)) $formattedMessage .= print_r($context, true) . PHP_EOL;

        fwrite($fh, $formattedMessage);
        fclose($fh);
    }

    public static function write($level, $message, mixed $context = null): void
    {
        $logger = new self();
        $logger->log($level, $message, $context);
    }    public static function __callStatic($name, $arguments)
    {
        $logger = new self();
        $logger->log($name, ...$arguments);
    }
}
