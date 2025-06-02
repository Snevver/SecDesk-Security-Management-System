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
use Ssms\Exceptions\HTTPException;
use Ssms\Controllers\ErrorController;
use Ssms\Database\Db;
use Ssms\Logger;

// Get the URI and remove the query string
$uri = strtok(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '?');

// Get possible query parameters
$test_id = isset($_GET['test_id']) ? (int)$_GET['test_id'] : null;
$target_id = isset($_GET['target_id']) ? (int)$_GET['target_id'] : null;

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

function useController($controllerName, $methodName, $args = []) {
    $controllerClass = "Ssms\\Controllers\\$controllerName";
    if (!class_exists($controllerClass)) {
        throw new HTTPException("Controller $controllerName not found", 404);
    }

    $controller = new $controllerClass(Db::getInstance());
    if (!method_exists($controller, $methodName)) {
        throw new HTTPException("Method $methodName not found in controller $controllerName", 404);
    }

    return call_user_func_array([$controller, $methodName], $args);
}

function checkLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        Logger::write('info', "User " . ($_SESSION['email'] ?? 'unknown') . " is logged in, proceeding with request.");
        return true;
    } else {
        Logger::write('info', "User is not logged in, redirecting to /login");
        header('Location: /login');
        exit;
    }
}

// Helper function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Authenticated route handler
function handleAuthenticatedRoute($viewFile) {
    checkLogin();
    
    Logger::write('info', "Redirecting authenticated user to $viewFile");
    include DIR_VIEWS . $viewFile;
    exit;
}

// Define protected routes that require authentication
$protectedRoutes = [
    '/',
    '/employee',
    '/admin',
    '/targets',
    '/api/customers',
    '/api/tests', 
    '/api/targets',
    '/api/vulnerabilities'
];

// Automatically checks login for protected routes
function applyAuthMiddleware($uri, $protectedRoutes) {
    foreach ($protectedRoutes as $route) {
        if ($uri === $route) {
            checkLogin();
            break;
        }
    }
}

//-----------------------------------------------------
// Route Requests
//-----------------------------------------------------
try {
    applyAuthMiddleware($uri, $protectedRoutes);
    
    switch ($uri) {
        // Routes to views
        case '/login':
            if (session_status() === PHP_SESSION_NONE) session_start();
            include DIR_VIEWS . 'login.html.php';
            break;
        
        case '/':
            handleAuthenticatedRoute('index.html.php');
            break;

        case '/employee':
            handleAuthenticatedRoute('employeeDashboard.html.php');
            break;

        case '/admin':
            handleAuthenticatedRoute('adminDashboard.html.php');
            break;
        
        case '/targets':
            handleAuthenticatedRoute('targets.html.php');
            break;

        // API Routes
        case '/api/login':
            $result = useController("AuthenticatorController", "login");
            sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/logout':
            $result = useController("AuthenticatorController", "logout");
            sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/check-login':
            $result = useController("AuthenticatorController", "isLoggedIn");
            sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customers':
            $result = useController("AdminDashboardController", "getCustomers");
            sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/tests':
            $result = useController("IndexController", "getCustomersTests");
            sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/targets':
            $result = useController("TargetController", "getTargets", [$test_id]);
            sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/vulnerabilities':
            $result = useController("TargetController", "getVulnerabilities", [$target_id]);
            sendJsonResponse($result['data'], $result['status']);
            break;
            
        // Routes for styling and scripts
        case '/js/bootstrap.js':
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
    Logger::write('error', 'HTTP error: ' . $e->getMessage() . ' - ' . $uri);
    $c = new ErrorController();
    $c($e);
} catch (\Throwable $e) {
    Logger::write('error', 'Unhandled error: ' . $e->getMessage());
    header('Content-Type: text/html');
    http_response_code(500);
    include DIR_VIEWS . 'error.html.php';
}