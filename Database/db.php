<?php

$env_file = __DIR__ . '/.env';
$env = @parse_ini_file($env_file);

$host = $env['HOST'];
$port = $env['PORT'];
$dbname = $env['DBNAME'];
$user = $env['USER'];
$password = $env['PASSWORD'];

if (!function_exists('pg_connect')) {
    die("PostgreSQL extension is not installed. Please install the php-pgsql extension by uncommenting / adding 'extension=php-pgsql' in your php.ini file.\n");
}

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";
$db = pg_connect($connection_string);

if (!$db) {
    die("Connection failed: " . pg_last_error());
}

?>
