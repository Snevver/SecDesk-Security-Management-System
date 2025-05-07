<?php

// Set up error handling
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize connection parameters
$host = null;
$port = null;
$dbname = null;
$user = null;
$password = null;

/**
 * Custom function to read .env files because i had issues with parse_ini_file
 * @param string $filepath Path to the .env file
 * @return array|false Parsed environment variables or false on failure
 */
function readEnvFile($filepath) {
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

// Get credentials from .env file
$db_env_file = __DIR__ . '/.env';
$env = readEnvFile($db_env_file);

if ($env && !empty($env)) {
    error_log("Successfully read Database .env file with custom parser");
    $host = $env['HOST'] ?? null;
    $port = $env['PORT'] ?? null;
    $dbname = $env['DBNAME'] ?? null;
    $user = $env['USER'] ?? null;
    $password = $env['PASSWORD'] ?? null;
} else {
    error_log("Failed to read Database .env file or file is empty");
}

// Initialize connection status variable
$connection_successful = false;

// Try PDO connection
try {
    // Check if PDO is available
    if (!extension_loaded('PDO')) {
        throw new Exception("PDO extension is not loaded");
    }
    
    // Check for pgsql driver
    $drivers = PDO::getAvailableDrivers();
    if (!in_array('pgsql', $drivers)) {
        error_log("PDO PostgreSQL driver not available. Available drivers: " . implode(', ', $drivers));
        throw new Exception("PDO PostgreSQL driver not available. You need to enable it in php.ini");
    }
    
    // Check for required parameters before attempting connection
    if (!$host || !$dbname || !$user || !$password) {
        throw new Exception("Missing required database connection parameters");
    }
    
    // Create PDO connection
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    // Log connection attempt
    error_log("Attempting PDO connection to: $host:$port/$dbname as $user");
    
    $pdo = new PDO($dsn, $user, $password, $options);
    
    $connection_successful = true;
    error_log("PDO PostgreSQL connection established successfully");
    
} catch (Exception $e) {
    // Log PDO connection error
    $pdo_error = "PDO connection error: " . $e->getMessage();
    error_log($pdo_error);
    $connection_successful = false;
}

// Handle case where connection failed
if (!$connection_successful) {
    // For API endpoints, return JSON error
    if (PHP_SAPI !== 'cli' && strpos($_SERVER['REQUEST_URI'] ?? '', 'login') !== false) {
        // Set header for JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Database connection failed. Please try again later.'
        ]);
        exit;
    } else if (PHP_SAPI === 'cli') {
        // For CLI, show detailed error
        echo "Database connection failed: " . ($pdo_error ?? "Unknown error") . PHP_EOL;
        exit(1);
    } else {
        // For other web requests
        header('Content-Type: text/plain');
        echo "Database connection error. Please try again later.";
        exit;
    }
}

// Function to get PDO instance
function getPDO() {
    global $pdo, $connection_successful;
    
    if (!$connection_successful) {
        throw new Exception("No database connection available");
    }
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    throw new Exception("No database connection available");
}
