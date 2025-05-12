<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Still log errors to PHP error log
error_reporting(E_ALL);

// Configure session
ini_set('session.gc_maxlifetime', 3600);
session_save_path(sys_get_temp_dir());
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

// Configure app
define('APP_ROOT', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
const DIR_PUBLIC = APP_ROOT . 'public' . DIRECTORY_SEPARATOR;
const DIR_VIEWS = APP_ROOT . 'views' . DIRECTORY_SEPARATOR;
const DIR_CONTROLLERS = APP_ROOT . 'Controllers' . DIRECTORY_SEPARATOR;
const DIR_INCLUDES = APP_ROOT . 'includes' . DIRECTORY_SEPARATOR;

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

if (!str_starts_with($uri, '/')) {
    $uri = '/' . $uri;
}

if (is_file(__DIR__ . $uri)) {
    return false;
}

// Get the HTTP method
$methodName = match ($_SERVER['REQUEST_METHOD']) {
    'POST' => 'post',
    'PUT' => 'put',
    'DELETE' => 'delete',
    default => 'get',
};

// Disable CORS errors
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Determine the request
try {
    switch ($uri) {
        case '/':
            header('Content-Type: text/html');
            include DIR_VIEWS . 'index.html';
            break;
        
        case '/employee-dashboard':
            include DIR_VIEWS . 'employee-dashboard.html';
            break;

        case '/login':
            include DIR_VIEWS . 'login.html';
            break;
        
        case '/targets':
            include DIR_VIEWS . 'targets.html';
            break;

        case '/api/login':
            header('Content-Type: application/json');
            $c = new AuthenticatorController(Db::getInstance());
            return $c->login();

        case '/logout':
            $c = new AuthenticatorController(Db::getInstance());
            return $c->logout();

        case '/isLoggedIn':
            $c = new AuthenticatorController(Db::getInstance());
            return $c->isLoggedIn();

        case '/customers':
            $c = new EmployeeDashboardController(Db::getInstance());
            return $c->getCustomers();
        
        case '/tests':
            header('Content-Type: text/plain');
            $c = new IndexController(Db::getInstance());
            return $c->getCustomersTests();

        case '/api/targets':
            $c = new TargetController(Db::getInstance());
            return $c->getTargets();

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

    $c = new ErrorController();
    $c($e);
} catch (\Throwable $e) {

    header('Content-Type: text/html');
    http_response_code(500);

    include DIR_VIEWS . 'error.html.php';
}


