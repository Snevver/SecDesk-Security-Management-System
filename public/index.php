<?php

declare(strict_types=1);

// Configure session with enhanced settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);

session_save_path(sys_get_temp_dir());
error_reporting(E_ALL);

// Configure app
define('APP_ROOT', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
const DIR_PUBLIC = APP_ROOT . 'public' . DIRECTORY_SEPARATOR;
const DIR_VIEWS = APP_ROOT . 'views' . DIRECTORY_SEPARATOR;
const DIR_CONTROLLERS = APP_ROOT . 'libraries\Ssms\Controllers' . DIRECTORY_SEPARATOR;
const DIR_INCLUDES = APP_ROOT . 'includes' . DIRECTORY_SEPARATOR;
const DIR_DATABASE = APP_ROOT . 'libraries\Ssms\Database' . DIRECTORY_SEPARATOR;

// Include the autoload file and error handler
require_once APP_ROOT . 'vendor/autoload.php';
require_once DIR_INCLUDES . 'errorHandler.php';

// Declare all the used namespaces
use Ssms\Controllers\IndexController;
use Ssms\Controllers\AuthenticatorController;
use Ssms\Exceptions\HTTPException;
use Ssms\Controllers\ErrorController;
use Ssms\Controllers\EmployeeDashboardController;
use Ssms\Controllers\TargetController;
use Ssms\Database\Db;
use Ssms\Logger;

$uri = strtok($_SERVER['REQUEST_URI'], '?');

// If the URI is a file, return false
if (is_file(__DIR__ . $uri)) {
    Logger::write('info', "FILE: " . $uri);
    return false;
}

// Get the HTTP method
$methodName = strtoupper(match ($_SERVER['REQUEST_METHOD']) {
    'POST' => 'post',
    'PUT' => 'put',
    'DELETE' => 'delete',
    default => 'get',
});

Logger::write('info', "URI: " . $methodName . ' ' . $uri);

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
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Determine the request
try {
    switch ($uri) {
        case '/':
            include DIR_VIEWS . 'index.html.php';
            break;
        
        case '/employee-dashboard':
            include DIR_VIEWS . 'employeeDashboard.html.php';
            break;

        case '/login':
            include DIR_VIEWS . 'login.html.php';
            break;
        
        case '/targets':
            include DIR_VIEWS . 'targets.html.php';
            break;

        case '/api/login':
            Logger::write('info', "Login attempt");
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->login();
            $jsonResult = json_encode($result['data']);
            Logger::write('info', "Login result: " , $jsonResult);
            sendJsonResponse($result['data'], $result['status']);

        case '/api/logout':
            $c = new AuthenticatorController(Db::getInstance());
            $c->logout();
            header('Location: /login');
            exit;

        case '/api/check-login':
            Logger::write('info', "Check login");
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->isLoggedIn();
            sendJsonResponse($result['data'], $result['status']);

        case '/api/customers':
            $c = new EmployeeDashboardController(Db::getInstance());
            $result = $c->getCustomers();
            sendJsonResponse($result['data'], $result['status']);
        
        case '/api/tests':
            $c = new IndexController(Db::getInstance());
            $result = $c->getCustomersTests();
            sendJsonResponse($result['data'], $result['status']);

        case '/api/targets':
            $c = new TargetController(Db::getInstance());
            $result = $c->getTargets();
            sendJsonResponse($result['data'], $result['status']);

        case '/js/bootstrap.js':
            Logger::write('Debug', "Loading bootstrap.js");
            header('Content-Type: application/javascript');
            echo file_get_contents(APP_ROOT . '/node_modules/bootstrap/dist/js/bootstrap.bundle.js');
            break;
        
        case '/css/bootstrap.css':
            header('Content-Type: text/css');
            echo file_get_contents(APP_ROOT . '/node_modules/bootstrap/dist/css/bootstrap.css');
            break;
        
        default:
            throw new HTTPException('Route not found', 404);
    }
} catch (HTTPException $e) {
    $c = new ErrorController();
    $c($e);

} catch (\Throwable $e) {
    header('Content-Type: text/html');
    http_response_code(500);
    include DIR_VIEWS . 'error.html.php';
}