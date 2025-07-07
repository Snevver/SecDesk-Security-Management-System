<?php
//======================================================================
// DATABASE CONNECTION
//======================================================================

namespace Ssms\Database;

use Ssms\Logger;

class Db extends \PDO
{
    private static ?Db $instance = null;

    //-----------------------------------------------------
    // Get The Singleton Instance Of The Database Class
    //-----------------------------------------------------
    public static function getInstance(): Db
    {
        if (Db::$instance === null) {
            Logger::write('info', 'Creating new Db instance...');

            $env = parse_ini_file(DIR_DATABASE . '/.env');
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $env['HOST'] ?? 'localhost',
                $env['PORT'] ?? '5432',
                $env['DBNAME'] ?? 'postgres'
            );
            $username = $env['USER'] ?? 'postgres';
            $password = $env['PASSWORD'] ?? '';
            Db::$instance = new Db($dsn, $username, $password);

            Logger::write('info', 'Db instance created successfully.');
        }

        return Db::$instance;
    }

    public function __construct(string $dsn, string $username, string $password)
    {
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            // Disable persistent connections to avoid prepared statement conflicts
            \PDO::ATTR_PERSISTENT => false,
        ];

        parent::__construct($dsn, $username, $password, $options);
    }

    /**
     * Override prepare to handle PostgreSQL prepared statement errors
     */
    public function prepare(string $statement, array $driver_options = []): \PDOStatement|false
    {
        try {
            return parent::prepare($statement, $driver_options);
        } catch (\PDOException $e) {
            // If we get a prepared statement error, try to reconnect and retry once
            if (strpos($e->getMessage(), 'prepared statement') !== false) {
                Logger::write('warning', 'Prepared statement error detected, attempting reconnection: ' . $e->getMessage());
                
                // Force a new instance by clearing the singleton
                self::$instance = null;
                $newInstance = self::getInstance();
                
                // Retry the prepare on the new connection
                return $newInstance->prepare($statement, $driver_options);
            }
            
            // Re-throw other PDO exceptions
            throw $e;
        }
    }
}