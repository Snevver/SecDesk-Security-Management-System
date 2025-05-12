<?php

namespace Ssms\Database;

class Db extends \PDO
{
    private static ?Db $instance = null;

    public static function getInstance(): Db
    {
        if (Db::$instance === null) {
            Db::$instance = new Db("", "", "");
        }

        return Db::$instance;
    }


    public function __construct(string $dsn, string $username, string $password)
    {
        // Check for pgsql driver
        $drivers = \PDO::getAvailableDrivers();
        if (!in_array('pgsql', $drivers)) {
            error_log("PDO PostgreSQL driver not available. Available drivers: " . implode(', ', $drivers));
            throw new \Exception("PDO PostgreSQL driver not available. You need to enable it in php.ini");
        }

        // Initialize connection parameters
        $host = null;
        $port = null;
        $dbname = null;
        $user = null;
        $password = null;

        // Get credentials from .env file
        if (file_exists(APP_ROOT . '/.env')) {
            error_log("Using .env file for database credentials");
            $env = parse_ini_file(APP_ROOT . '/.env');
            if ($env && !empty($env)) {
                $host = $env['HOST'] ?? null;
                $port = $env['PORT'] ?? null;
                $dbname = $env['DBNAME'] ?? null;
                $user = $env['USER'] ?? null;
                $password = $env['PASSWORD'] ?? null;
            } else {
                error_log("Failed to read .env file or file is empty");
            }
        }

        // Create PDO connection
        error_log("DSN: $dsn, User: $user");
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];          

        parent::__construct($dsn, $username, $password, $options);
    }


    /**
     * Custom function to read .env files because i had issues with parse_ini_file
     * @param string $filepath Path to the .env file
     * @return array|false Parsed environment variables or false on failure
     */
    public function readEnvFile($filepath) {
        if (!file_exists($filepath)) {
            error_log("ENV file not found: $filepath");
            return false;
        }
        
        $result = [];
        $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments and empty lines
            if (empty($line) || $line[0] === '#' || $line[0] === '/') {
                continue;
            }
            
            // Parse the line (handle quotes correctly)
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                $result[$name] = $value;
            }
        }
        
        return $result;
    }
}