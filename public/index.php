<?php

//======================================================================
// ROUTE ALL REQUESTS
//======================================================================

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

// Declare all the used namespaces
use Ssms\Exceptions\HTTPException;
use Ssms\Controllers\ErrorController;
use Ssms\Logger;

$app = new Ssms\Application();
$app->init();
$uri = $app->getUri();


//-----------------------------------------------------
// Route Requests
//-----------------------------------------------------
try {
    $app->applyAuthMiddleware();
    
    switch ($uri) {
        // Routes to views
        case '/login':
            if (session_status() === PHP_SESSION_NONE) session_start();
            include DIR_VIEWS . 'login.html.php';
            break;
        
        case '/':
            $app->handleAuthenticatedRoute('index.html.php');
            break;

        case '/employee':
            $app->handleAuthenticatedRoute('employeeDashboard.html.php', 'pentester');
            break;

        case '/admin':
            $app->handleAuthenticatedRoute('adminDashboard.html.php', 'admin');
            break;
        
        case '/targets':
            $app->handleAuthenticatedRoute('targets.html.php');
            break;

        // API Routes
        case '/api/login':
            $result = $app->useController("AuthenticatorController", "login");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/logout':
            $result = $app->useController("AuthenticatorController", "logout");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/check-login':
            $result = $app->useController("AuthenticatorController", "isLoggedIn");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customers':
            $result = $app->useController("AdminDashboardController", "getCustomers");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/employees':
            $result = $app->useController("AdminDashboardController", "getEmployees");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/tests':
            $result = $app->useController("IndexController", "getCustomersTests");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/employee-tests':
            $result = $app->useController("EmployeeDashboardController", "getEmployeeTests");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/update-test-completion':
            $result = $app->useController("EmployeeDashboardController", "updateTestCompletion");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;
    
        case '/api/targets':
            $test_id = $app->getIntQueryParam('test_id');
            $result = $app->useController("TargetController", "getTargets", [$test_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/vulnerabilities':
            $target_id = $app->getIntQueryParam('target_id');
            $result = $app->useController("TargetController", "getVulnerabilities", [$target_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;
        
        // Routes for styling and scripts
        case '/js/bootstrap.js':
            $app->handleStaticAsset('/node_modules/bootstrap/dist/js/bootstrap.bundle.js', 'application/javascript');
            break;
        
        case '/css/bootstrap.css':
            $app->handleStaticAsset('/node_modules/bootstrap/dist/css/bootstrap.css', 'text/css');
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