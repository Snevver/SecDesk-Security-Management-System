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
            $app->handleDashboardRoute();
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

        case '/edit':
            $result = $app->useController('AuthenticatorController', 'doesUserHaveAccess');
            if ($result['status'] == 200) {
                $app->handleAuthenticatedRoute('editTest.html.php', 'pentester');
            } else {
                throw new HTTPException('Access denied', 403);
            }
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
            $app->checkApiAuthorization('admin');
            $result = $app->useController("AdminDashboardController", "getCustomers");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/employees':
            $app->checkApiAuthorization('admin');
            $result = $app->useController("AdminDashboardController", "getEmployees");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/tests':
            $app->checkApiAuthorization(['admin', 'customer']);
            $result = $app->useController("IndexController", "getCustomersTests");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/employee-tests':
            $app->checkApiAuthorization('pentester');
            $result = $app->useController("EmployeeDashboardController", "getEmployeeTests");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/update-test-completion':
            $app->checkApiAuthorization('pentester');
            $result = $app->useController("EmployeeDashboardController", "updateTestCompletion");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;        
            
        case '/api/targets':
            $app->checkApiAuthorization('pentester');
            $test_id = isset($_GET['test_id']) ? (int)$_GET['test_id'] : null;
            $result = $app->useController("TargetController", "getTargets", [$test_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/vulnerabilities':
            $app->checkApiAuthorization('pentester');
            $target_id = isset($_GET['target_id']) ? (int)$_GET['target_id'] : null;
            $result = $app->useController("TargetController", "getVulnerabilities", [$target_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/check-access':
            $app->checkApiAuthentication();
            $result = $app->useController("AuthenticatorController", "doesUserHaveAccess");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;        
        
        case '/create-customer':
            $app->checkApiAuthorization('admin');
            $email = $app->decodeBody();
            $result = $app->useController("AdminDashboardController", "createCustomer", [$email]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/create-employee':
            $app->checkApiAuthorization('admin');
            $email = $app->decodeBody();
            $result = $app->useController("AdminDashboardController", "createEmployee", [$email]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;        
            
        case '/create-test':
            $app->checkApiAuthorization('pentester');
            $result = $app->useController("EmployeeDashboardController", "createTest");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/change-password':
            $app->checkApiAuthentication();
            $result = $app->useController("AuthenticatorController", "changePassword");
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