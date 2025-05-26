<?php

//======================================================================
// ROUTE ALL REQUESTS
//======================================================================

declare(strict_types=1);

//-----------------------------------------------------
// Prepare for routing
//-----------------------------------------------------

// Configure application
define('APP_ROOT', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
const DIR_PUBLIC = APP_ROOT . 'public' . DIRECTORY_SEPARATOR;
const DIR_VIEWS = APP_ROOT . 'views' . DIRECTORY_SEPARATOR;
const DIR_CONTROLLERS = APP_ROOT . 'libraries\Ssms\Controllers' . DIRECTORY_SEPARATOR;
const DIR_INCLUDES = APP_ROOT . 'includes' . DIRECTORY_SEPARATOR;
const DIR_DATABASE = APP_ROOT . 'libraries\Ssms\Database' . DIRECTORY_SEPARATOR;

// Require files
require_once DIR_INCLUDES . 'config.php';
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

// Get the URI and remove the query string
$uri = strtok(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '?');

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

// Log the request
Logger::write('info', "Request recieved: " . $methodName . ' ' . $uri);

// Disable CORS errors
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
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

// This is the route to get the vulnerabilities for a target
if (preg_match('#^/api/vulnerabilities/(\d+)$#', $uri, $matches)) {
    $targetId = (int)$matches[1];
    Logger::write('info', "Fetching vulnerabilities for target ID $targetId...");
    $c = new IndexController(Db::getInstance());
    $result = $c->getVulnerabilities($targetId);
    sendJsonResponse($result['data'], $result['status']);
}

//-----------------------------------------------------
// Route Requests
//-----------------------------------------------------
try {
    switch ($uri) {
        // Route to dashboard
        case '/':
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->isLoggedIn();
            if ($result['data']['success']) {
                Logger::write('info', "User is logged in, redirecting to /index.html.php");
                include DIR_VIEWS . 'index.html.php';
                exit;
            } else {
                Logger::write('info', "User is not logged in, redirecting to /login.html.php");
                include DIR_VIEWS . 'login.html.php';
                exit;
            }
            break;

        // Route to employee dashboard
        case '/employee-dashboard':
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->isLoggedIn();

            // Start the session
            if (session_status() === PHP_SESSION_NONE) session_start();

            if ($result['data']['success'] && isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                Logger::write('info', "Redirecting to /employeeDashboard.html.php");
                include DIR_VIEWS . 'employeeDashboard.html.php';
                exit;
            } else {
                Logger::write('info', "Redirecting to /login.html.php");
                include DIR_VIEWS . 'login.html.php';
                exit;
            }
            break;

        // Route to target page
        case '/targets':
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->isLoggedIn();

            // Start the session
            if (session_status() === PHP_SESSION_NONE) session_start();

            if ($result['data']['success'] && isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                Logger::write('info', "Redirecting to /targets.html.php");
                include DIR_VIEWS . 'targets.html.php';
                exit;
            } else {
                Logger::write('info', "Redirecting to /login.html.php");
                include DIR_VIEWS . 'login.html.php';
                exit;
            }
            break;

        // Route to login page
        case '/login':
            // Start the session
            if (session_status() === PHP_SESSION_NONE) session_start();

            Logger::write('info', "Redirecting " . ($_SESSION['email'] ?? "Unknown user") . " to /login.html.php");
            include DIR_VIEWS . 'login.html.php';
            break;

        // Route to logout API
        case '/api/logout':
            $c = new AuthenticatorController(Db::getInstance());
            $c->logout();
            Logger::write('info', "Successfully logged out!");
            header('Location: /login');
            exit;

        // Route to login API
        case '/api/login':
            Logger::write('info', 'Login request recieved');
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->login();
            sendJsonResponse($result['data'], $result['status']);
            break;

        // Route to login checking API
        case '/api/check-login':
            Logger::write('info', 'Checking if ' . ($_SESSION['email'] ?? "Unknown user") . " is logged in");
            $c = new AuthenticatorController(Db::getInstance());
            $result = $c->isLoggedIn();
            sendJsonResponse($result['data'], $result['status']);
            break;

        // Route to customers API
        case '/api/customers':
            Logger::write('info', 'Fetching customers...');
            $c = new EmployeeDashboardController(Db::getInstance());
            $result = $c->getCustomers();
            sendJsonResponse($result['data'], $result['status']);
            break;
        
        // Route to tests API
        case '/api/tests':
            Logger::write('info', 'Fetching tests...');
            $c = new IndexController(Db::getInstance());
            $result = $c->getCustomersTests();
            sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/targets':
            $test_id = isset($_GET['test_id']) ? (int)$_GET['test_id'] : null;
            Logger::write('info', 'Fetching targets for test ID: ' . $test_id);
            $c = new TargetController(Db::getInstance());
            $result = $c->getTargets($test_id);
            sendJsonResponse($result['data'], $result['status']);
            break;

        // Route to Bootstrap Javascript
        case '/js/bootstrap.js':
            header('Content-Type: application/javascript');
            echo file_get_contents(APP_ROOT . '/node_modules/bootstrap/dist/js/bootstrap.bundle.js');
            break;
        
        // Route to Bootstrap CSS
        case '/css/bootstrap.css':
            header('Content-Type: text/css');
            echo file_get_contents(APP_ROOT . '/node_modules/bootstrap/dist/css/bootstrap.css');
            break;
        
        default:
            throw new HTTPException('Route not found', 404);
    }
} catch (HTTPException $e) {
    Logger::write('error', 'HTTP error: ' . $e->getMessage() . ' - ' . $uri);
    $c = new ErrorController();
    $c($e);
} catch (\Throwable $e) {
    Logger::write('error', 'Unhandled error: ' . $e->getMessage());
    header('Content-Type: text/html');
    http_response_code(500);
    include DIR_VIEWS . 'error.html.php';
}