<?php

namespace SSMSRouter;

// Include database connection
require_once __DIR__ . '/../database/db.php';

// Include the Controllers
require_once __DIR__ . '/../App/Controllers/AuthenticatorController.php';
require_once __DIR__ . '/../App/Controllers/EmployeeDashboardController.php';
require_once __DIR__ . '/../App/Controllers/IndexController.php';
require_once __DIR__ . '/../App/Controllers/TargetController.php';

// Register routes
// request method, path(regex), resource, method
$routes = [
    // Home page routes
    ['get', 'index.php', 'IndexController', 'index'],
    ['get', '', 'IndexController', 'index'],
    ['get', 'SecDesk-Security-Management-System/public', 'IndexController', 'index'],
    ['get', 'getCustomersTests', 'IndexController', 'getCustomersTests'],
    
    // Authentication routes
    ['post', 'login', 'AuthenticatorController', 'login'],
    ['get', 'logout', 'AuthenticatorController', 'logout'],
    ['get', 'isLoggedIn', 'AuthenticatorController', 'isLoggedIn'],

    // EmployeeDashboard routes
    ['get', 'getCustomers', 'EmployeeDashboardController', 'getCustomers'],

    // Targets
    ['get', 'getTargets', 'TargetController', 'getTargets'],

    // Route with parameter
    ['get', 'getTarget', 'TargetController', 'getTarget', true],
];

// Disable CORS errors
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Always set JSON content type for all API responses
header('Content-Type: application/json');

// handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
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

// Log the request
file_put_contents("php://stdout", "Requesting  $urlPath with method $methodName\n");

// Check if the URL path matches any of the defined routes
foreach ($routes as $route) {
    $routeMethod = $route[0];
    $routePath = $route[1];
    $resource = $route[2];
    $method = $route[3];
    $hasParam = isset($route[4]) ? $route[4] : false;
    
    // Clean the URL path
    $cleanPath = trim(strtok($urlPath, '?'), '/');
    
    // Extract the path and any ID
    $pathParts = explode('/', $cleanPath);
    $basePath = $pathParts[0];
    $id = isset($pathParts[1]) ? $pathParts[1] : null;
    
    $pathMatches = false;
    
    if ($hasParam) {
        // For parameterized routes, only match the base part
        $pathMatches = ($basePath === $routePath) && $id !== null;
    } else {
        // For exact routes, match the whole path
        $pathMatches = ($cleanPath === $routePath);
    }
    
    // Also check alternate paths with the project prefix
    if (!$pathMatches) {
        $prefixedPath = 'SecDesk-Security-Management-System/public/' . $routePath;
        $prefixedPath2 = 'SecDesk-Security-Management-System/' . $routePath;
        
        if ($hasParam && $id !== null) {
            $pathMatches = (strpos($cleanPath, $prefixedPath) === 0) || 
                           (strpos($cleanPath, $prefixedPath2) === 0);
        } else {
            $pathMatches = ($cleanPath === $prefixedPath) || 
                           ($cleanPath === $prefixedPath2);
        }
    }
    
    if ($methodName !== $routeMethod || !$pathMatches) {
        continue;
    }
    
    // Route matched, call the controller method
    $arguments = $hasParam && $id !== null ? [$id] : null;
    callRoute($resource, $method, $arguments);
    exit();
}

// If no route is found, the client will receive a 404 response
http_response_code(404);
echo json_encode(['success' => false, 'error' => 'Not found', 'path' => $urlPath]);
exit();

function callRoute(string $resource, string $method, array|null $arguments) {
    try {
        // All controllers now share the same namespace
        $resourceClassPath = "App\\Controllers";
        
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
        echo json_encode([
            'success' => false, 
            'error' => 'Database error', 
            'details' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
    } catch (\Exception $e) {
        // Log the error with more details
        file_put_contents("php://stderr", "Error: " . $e->getMessage() . "\n");
        file_put_contents("php://stderr", "Class: " . get_class($e) . "\n");
        file_put_contents("php://stderr", "Trace: " . $e->getTraceAsString() . "\n");
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'error' => 'Server error', 
            'details' => $e->getMessage(),
            'type' => get_class($e)
        ]);
    }
}

function parseArguments(array $matches): array|null {
    if (!empty($matches[1])) {
        return array_map(fn($arg) => (int) $arg, array_slice($matches, 1));
    }
    return null;
}