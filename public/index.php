<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Still log errors to PHP error log
error_reporting(E_ALL);

// Configure session with enhanced settings
ini_set('session.gc_maxlifetime', 3600);
session_save_path(sys_get_temp_dir());
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);

// Configure app
define('APP_ROOT', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
const DIR_PUBLIC = APP_ROOT . 'public' . DIRECTORY_SEPARATOR;
const DIR_VIEWS = APP_ROOT . 'views' . DIRECTORY_SEPARATOR;
const DIR_CONTROLLERS = APP_ROOT . 'libraries\Ssms\Controllers' . DIRECTORY_SEPARATOR;
const DIR_INCLUDES = APP_ROOT . 'includes' . DIRECTORY_SEPARATOR;
const DIR_DATABASE = APP_ROOT . 'libraries\Ssms\Database' . DIRECTORY_SEPARATOR;

require APP_ROOT . 'vendor/autoload.php';
require_once DIR_INCLUDES . 'errorHandler.php';

use Ssms\Controllers\IndexController;
use Ssms\Controllers\AuthenticatorController;
use Ssms\Exceptions\HTTPException;
use Ssms\Controllers\ErrorController;
use Ssms\Controllers\EmployeeDashboardController;
use Ssms\Controllers\TargetController;
use Ssms\Database\Db;

$uri = strtok($_SERVER['REQUEST_URI'], '?');

// Debug log
file_put_contents("logs.txt", date('Y-m-d H:i:s') . " Request URI: " . $uri . "\n", FILE_APPEND);

if (!str_starts_with($uri, '/')) {
    $uri = '/' . $uri;
}

if (is_file(__DIR__ . $uri)) {
    return false;
}

// Only get what comes after the public directory
$uri = strrchr($uri, '/');

file_put_contents("logs.txt", date('Y-m-d H:i:s') . " Cleaned URI: " . $uri . "\n", FILE_APPEND);

// Get the HTTP method
$methodName = match ($_SERVER['REQUEST_METHOD']) {
    'POST' => 'post',
    'PUT' => 'put',
    'DELETE' => 'delete',
    default => 'get',
};

// Debug log
file_put_contents("logs.txt", date('Y-m-d H:i:s') . " HTTP Method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);

// Disable CORS errors
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Helper function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

file_put_contents("logs.txt", date('Y-m-d H:i:s') . " Route: " . $uri . "\n", FILE_APPEND);

// Determine the request
try {
    switch ($uri) {
        case '/':
            file_put_contents("logs.txt", date('Y-m-d H:i:s') . " Index page requested\n", FILE_APPEND);
            header('Content-Type: text/html');
            include DIR_VIEWS . 'index.html';
            break;
        
        case '/employee-dashboard':
            file_put_contents("logs.txt", date('Y-m-d H:i:s') . " Employee dashboard requested\n", FILE_APPEND);
            include DIR_VIEWS . 'employee-dashboard.html';
            break;

        case '/login':
            file_put_contents("logs.txt", date('Y-m-d H:i:s') . " Login page requested\n", FILE_APPEND);
            include DIR_VIEWS . 'login.html';
            break;
        
        case '/targets':
            file_put_contents("logs.txt", date('Y-m-d H:i:s') . " Targets page requested\n", FILE_APPEND);
            include DIR_VIEWS . 'targets.html';
            break;

        case '/api/login':
            // Ensure JSON response
            header('Content-Type: application/json');
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->login();
            sendJsonResponse($result['data'], $result['status']);

        case '/logout':
            header('Content-Type: application/json');
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->logout();
            sendJsonResponse($result['data'], $result['status']);

        case '/isLoggedIn':
            header('Content-Type: application/json');
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->isLoggedIn();
            sendJsonResponse($result['data'], $result['status']);

        case '/customers':
            header('Content-Type: application/json');
            $c = new EmployeeDashboardController(Db::getInstance());
            $result = $c->getCustomers();
            sendJsonResponse($result['data'], $result['status']);
        
        case '/tests':
            header('Content-Type: application/json');
            $c = new IndexController(Db::getInstance());
            $result = $c->getCustomersTests();
            sendJsonResponse($result['data'], $result['status']);

        case '/api/targets':
            header('Content-Type: application/json');
            $c = new TargetController(Db::getInstance());
            $result = $c->getTargets();
            sendJsonResponse($result['data'], $result['status']);

        case '/js/bootstrap.js':
            header('Content-Type: application/javascript');
            echo file_get_contents(APP_ROOT . '/node_modules/bootstrap/dist/js/bootstrap.bundle.js');
            break;
        
        case '/bootstrap.css':
            header('Content-Type: text/css');
            echo file_get_contents(APP_ROOT . '/node_modules/bootstrap/dist/css/bootstrap.css');
            break;
        
        default:
            file_put_contents("logs.txt", date('Y-m-d H:i:s') . " Route not found: " . $uri . "\n", FILE_APPEND);
            throw new HTTPException('Route not found', 404);
    }
} catch (HTTPException $e) {
    file_put_contents("logs.txt", date('Y-m-d H:i:s') . " HTTP Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    $c = new ErrorController();
    $c($e);

} catch (\Throwable $e) {
    file_put_contents("logs.txt", date('Y-m-d H:i:s') . " General Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    header('Content-Type: text/html');
    http_response_code(500);
    include DIR_VIEWS . 'error.html.php';
    
}