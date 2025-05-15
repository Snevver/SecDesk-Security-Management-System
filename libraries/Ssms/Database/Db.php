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
        ];

        parent::__construct($dsn, $username, $password, $options);
    }
}