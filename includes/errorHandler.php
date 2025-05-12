<?php

use Ssms\Exceptions\HTTPException;

// Set up JSON error handling
function errorHandler($errno, $errstr, $errfile, $errline) {
    // Don't show errors for suppressed errors (using @)
    if (error_reporting() === 0) {
        return true;
    }

    throw new HTTPException($errstr, $errno, $errfile, $errline);
}

// Register our custom error handler
set_error_handler('errorHandler', E_ALL);

// Handle fatal errors
register_shutdown_function(function() {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Log fatal error
        file_put_contents("php://stderr", "Fatal Error: " . $e['message'] . " in " . $e['file'] . " on line " . $e['line'] . "\n");
        
        // Only set headers if they haven't been sent yet
        if (!headers_sent()) {
            header('Content-Type: text/html');
            http_response_code(500);
        }

        include  DIR_VIEWS . 'error.html.php';

    }
});
