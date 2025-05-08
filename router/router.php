<?php

namespace DeepDiveAPI;

// Include database connection
require_once __DIR__ . '/../Database/db.php';

// Include the Controllers
require_once __DIR__ . '/../App/Controllers/AuthenticatorController.php';
require_once __DIR__ . '/../App/Controllers/AdminDashboardController.php';
require_once __DIR__ . '/../App/Controllers/IndexController.php';

// Register routes
// request method, path(regex), resource, method
$routes = [
    // Home page
    ['get', '/^(index\.php)?$|^$|^SecDesk-Security-Management-System\/public\/?$/', 'IndexController', 'index'],
    ['get', '/^getCustomersTestss\/?$|^SecDesk-Security-Management-System\/getCustomersTestss\/?$|^SecDesk-Security-Management-System\/public\/getCustomersTests\/?$/i', 'IndexController', 'getCustomersTests'],
    
    // Authentication routes
    ['post', '/^login\/?$|^SecDesk-Security-Management-System\/login\/?$|^SecDesk-Security-Management-System\/public\/login\/?$/i', 'AuthenticatorController', 'login'],
    ['get', '/^logout\/?$|^SecDesk-Security-Management-System\/logout\/?$|^SecDesk-Security-Management-System\/public\/logout\/?$/i', 'AuthenticatorController', 'logout'],
    ['get', '/^isLoggedIn\/?$|^SecDesk-Security-Management-System\/isLoggedIn\/?$|^SecDesk-Security-Management-System\/public\/isLoggedIn\/?$/i', 'AuthenticatorController', 'isLoggedIn'],

    // AdminDashboard routes
    ['get', '/^getCustomers\/?$|^SecDesk-Security-Management-System\/getCustomers\/?$|^SecDesk-Security-Management-System\/public\/getCustomers\/?$/i', 'AdminDashboardController', 'getCustomers'],
];

// Disable CORS errors
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Headers: Content-Type");
    exit();
}

// Get the HTTP method
$methodName = match ($_SERVER['REQUEST_METHOD']) {
    'POST' => 'post',
    'PUT' => 'put',
    'DELETE' => 'delete',
    default => 'get',
};

// Parse the URL path and break it into resource and arguments
$urlPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Log all request details
file_put_contents("php://stdout", "Full URI: " . $_SERVER['REQUEST_URI'] . "\n");
file_put_contents("php://stdout", "Parsed path: " . $urlPath . "\n");
file_put_contents("php://stdout", "Method: " . $methodName . "\n");

// Check if we need to handle the home route specially
if (empty($urlPath) || $urlPath == 'index.php') {
    file_put_contents("php://stdout", "Handling home route\n");
    require_once __DIR__ . '/../public/index.html';
    exit();
}

// Log the request
file_put_contents("php://stdout", "Requesting  $urlPath with method $methodName\n");

foreach ($routes as $route) {
    $routeMethod = $route[0];
    $routePath = $route[1];
    $resource = $route[2];
    $method = $route[3];
    
    // continue if the method or path don't match
    if ($methodName != $routeMethod || preg_match($routePath, $urlPath, $matches) === 0) {
        continue;
    }

    $arguments = parseArguments($matches);

    callRoute($resource, $method, $arguments);

    // If no route is found, the client will receive a 404 response
    exit();
}

// If no route is found, the client will receive a 404 response
header('Content-Type: application/json');
http_response_code(404);
echo json_encode(['error' => 'Not found']);
exit();

function callRoute(string $resource, string $method, array|null $arguments) {
    header('Content-Type: application/json');
    try {
        // Get the resource class path based on the resource name
        if ($resource === 'AuthenticatorController') {
            $resourceClassPath = "App\\Controllers\\AuthenticationController";
        } elseif ($resource === 'AdminDashboardController') {
            $resourceClassPath = "App\\Controllers\\AdminDashboardController";
        } elseif ($resource === 'IndexController') {
            $resourceClassPath = "App\\Controllers\\IndexController";
        } else {
            throw new \Exception("Resource not found: $resource");
        }
        
        // Construct the full class name
        $fullClassName = "$resourceClassPath\\$resource";
        
        // Debug output to see what's happening
        file_put_contents("php://stdout", "Attempting to load class: $fullClassName\n");
        
        // Use the PDO connection from db.php
        $resourceInstance = new $fullClassName(getPDO());
        $result = is_null($arguments) ?
            $resourceInstance->$method() :
            $resourceInstance->$method(...$arguments);
        
        // Debug output to see the result
        file_put_contents("php://stdout", "Method result: " . json_encode($result) . "\n");
        
        http_response_code($result['status']);
        if (!empty($result['data'])) {
            echo json_encode($result['data']);
        } else {
            echo json_encode(['success' => true]);
        }
    } catch (\PDOException $e) {
        // Log the error
        file_put_contents("php://stderr", $e->getMessage() . "\n");
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    } catch (\Exception $e) {
        // Log the error with more details
        file_put_contents("php://stderr", "Error: " . $e->getMessage() . "\n");
        file_put_contents("php://stderr", "Class: " . get_class($e) . "\n");
        file_put_contents("php://stderr", "Trace: " . $e->getTraceAsString() . "\n");
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function parseArguments(array $matches): array|null {
    if (!empty($matches[1])) {
        return array_map(fn($arg) => (int) $arg, array_slice($matches, 1));
    }
    return null;
}