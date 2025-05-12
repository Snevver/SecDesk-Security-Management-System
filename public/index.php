<?php

$uri = strtok($_SERVER['REQUEST_URI'], '?');	

if (!str_starts_with($uri, '/')) {
    // If the URI does not start with a '/', prepend it
    $uri = '/' . $uri;
}
if (is_file(__DIR__ . $uri)) {
    // If the request is for a file, serve it directly
    return false;
}

/**
 * Most of this code is written by AI to help with error handling and logging.
 */

 
// Set up JSON error handling
function jsonErrorHandler($errno, $errstr, $errfile, $errline) {
    // Don't show errors for suppressed errors (using @)
    if (error_reporting() === 0) {
        return true;
    }
    
    // Log error details to server logs
    file_put_contents("php://stderr", "PHP Error: [$errno] $errstr in $errfile on line $errline\n");
    
    // Set content type to JSON
    header('Content-Type: application/json');
    
    // Create standardized error response
    $errorResponse = json_encode([
        'success' => false,
        'error' => 'Server error occurred',
        'details' => $errstr,
        'code' => $errno
    ]);
    
    // Send response and exit
    http_response_code(500);
    echo $errorResponse;
    exit;
}

// Register our custom error handler
set_error_handler('jsonErrorHandler', E_ALL);

// Similarly, handle uncaught exceptions
set_exception_handler(function($exception) {
    // Log exception to server logs
    file_put_contents("php://stderr", "Uncaught Exception: " . $exception->getMessage() . "\n");
    file_put_contents("php://stderr", "In file: " . $exception->getFile() . " on line " . $exception->getLine() . "\n");
    
    // Set content type to JSON
    header('Content-Type: application/json');
    
    // Create standardized error response
    $errorResponse = json_encode([
        'success' => false,
        'error' => 'Server exception occurred',
        'details' => $exception->getMessage(),
        'file' => basename($exception->getFile()),
        'line' => $exception->getLine()
    ]);
    
    // Send response and exit
    http_response_code(500);
    echo $errorResponse;
    exit;
});

// Handle fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Log fatal error
        file_put_contents("php://stderr", "Fatal Error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'] . "\n");
        
        // Only set headers if they haven't been sent yet
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500);
        }
        
        // Create standardized error response
        $errorResponse = json_encode([
            'success' => false,
            'error' => 'Fatal server error occurred',
            'details' => $error['message']
        ]);
        
        echo $errorResponse;
    }
});

// Disable HTML error output
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
// Still log errors to PHP error log
error_reporting(E_ALL);

// Configure session
ini_set('session.gc_maxlifetime', 3600);
session_save_path(sys_get_temp_dir());
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

// Log request information
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
file_put_contents("php://stdout", "Request received: $requestMethod $requestUri\n");

// Load the router for API requests
require_once __DIR__ . '/../router/router.php';
