<?php
namespace Ssms\Database;

class Db extends \PDO
{
    private static ?Db $instance = null;

    /**
     * Get the singleton instance of the Db class
     * @return Db
     */
    public static function getInstance(): Db
    {
        if (Db::$instance === null) {
            $env = parse_ini_file(DIR_DATABASE . '/.env');
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $env['HOST'] ?? 'localhost',
                $env['PORT'] ?? '5432',
                $env['DBNAME'] ?? 'postgres'
            );
            $username = $env['USER'] ?? 'postgres';
            $password = $env['PASSWORD'] ?? '';
            error_log("Constructed DSN: $dsn");
            Db::$instance = new Db($dsn, $username, $password);
        }

        return Db::$instance;
    }

    /**
     * Db constructor.
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
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