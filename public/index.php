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
        case '/':
            $app->handleDashboardRoute();
            break;        
            
        case '/login':
            if (session_status() === PHP_SESSION_NONE) session_start();
            include DIR_VIEWS . 'login.html.php';
            break;
          
        case '/targets':
            $app->handleAuthenticatedRoute('targets.html.php');
            break;
            
        case '/edit':
            $app->checkApiAuthorization('pentester');
            $result = $app->useController('AuthenticatorController', 'handleEditRoute');
            if ($result['status'] == 200) {
                $app->handleAuthenticatedRoute($result['data']['view'], $result['data']['role']);
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
            $app->handleApiRoute(['admin', 'pentester'], "AdminDashboardController", "getCustomers");
            break;

        case '/api/employees':
            $app->handleApiRoute(['admin', 'pentester'], "AdminDashboardController", "getEmployees");
            break;

        case '/api/admins':
            $app->handleApiRoute('admin', "AdminDashboardController", "getAdmins");
            break;
        
        case '/api/tests':
            $app->handleApiRoute(['admin', 'customer'], "IndexController", "getCustomersTests");
            break;

        case '/api/employee-tests':
            $app->handleApiRoute('pentester', "EmployeeDashboardController", "getEmployeeTests");
            break;

        case '/api/update-test-completion':
            $app->handleApiRoute('pentester', "EmployeeDashboardController", "updateTestCompletion");
            break;
        
        case '/api/targets':
            $app->checkApiAuthorization(['pentester', 'customer']);
            $result = $app->useController("TargetController", "handleApiTargets");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/vulnerabilities':
            $app->checkApiAuthorization(['pentester', 'customer']);
            $target_id = isset($_GET['target_id']) ? (int)$_GET['target_id'] : null;
            $result = $app->useController("VulnerabilityController", "getVulnerabilities", [$target_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/get-test-data':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            $app->handleApiRoute('pentester', "DataController", "getTestData", [$data['test_id']]);
            break;

        case '/check-access':
            $app->checkApiAuthentication();
            $result = $app->useController("AuthenticatorController", "doesUserHaveAccess");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/create-account':
            $data = $app->decodeBody();
            $app->handleApiRoute('admin', "AdminDashboardController", "createNewAccount", [$data]);
            break;

        case '/create-test':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            Logger::write('info', 'Creating test with data: ' . json_encode($data));
            $app->handleApiRoute('pentester', "EmployeeDashboardController", "createTest", [$data['customer_id']]);
            break;        
            
        // !!! To be implemented
        case '/add':
            $app->checkApiAuthorization('pentester');
            break;

        case '/update-test':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            Logger::write('info', 'Updating test with data: ' . json_encode($data));
            $app->handleApiRoute('pentester', "DataController", "updateTest", [$data['test_id'], $data['test_name'], $data['test_description']]);
            break;

        case '/api/change-password':
            $app->checkApiAuthentication();
            $result = $app->useController("AuthenticatorController", "changePassword");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customer-details':
            $app->checkApiAuthorization('admin');
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            $result = $app->useController("AdminDashboardController", "getCustomerDetails", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customer-tests':
            $app->checkApiAuthorization('admin');
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            $result = $app->useController("AdminDashboardController", "getCustomerTests", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customer-targets':
            $app->checkApiAuthorization('admin');
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            $result = $app->useController("AdminDashboardController", "getCustomerTargets", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;        
            
        case '/api/customer-vulnerabilities':
            $app->checkApiAuthorization('admin');
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            $result = $app->useController("AdminDashboardController", "getCustomerVulnerabilities", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/pentester-details':
            $app->checkApiAuthorization('admin');
            $pentester_id = isset($_GET['pentester_id']) ? (int)$_GET['pentester_id'] : null;
            $result = $app->useController("AdminDashboardController", "getPentesterDetails", [$pentester_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/pentester-tests':
            $app->checkApiAuthorization('admin');
            $pentester_id = isset($_GET['pentester_id']) ? (int)$_GET['pentester_id'] : null;
            $result = $app->useController("AdminDashboardController", "getPentesterTests", [$pentester_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/pentester-targets':
            $app->checkApiAuthorization('admin');
            $pentester_id = isset($_GET['pentester_id']) ? (int)$_GET['pentester_id'] : null;
            $result = $app->useController("AdminDashboardController", "getPentesterTargets", [$pentester_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/pentester-vulnerabilities':
            $app->checkApiAuthorization('admin');
            $pentester_id = isset($_GET['pentester_id']) ? (int)$_GET['pentester_id'] : null;
            $result = $app->useController("AdminDashboardController", "getPentesterVulnerabilities", [$pentester_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;
        
        case '/api/delete':
            $app->checkApiAuthentication();
            if (isset($_GET['test_id'])) {
                $test_id = (int)$_GET['test_id'];
                $app->handleApiRoute('pentester', "EmployeeDashboardController", "deleteTest", [$test_id]);
            } else if (isset($_GET['target_id'])) {
                $target_id = (int)$_GET['target_id'];
                $app->handleApiRoute('pentester', "TargetController", "deleteTarget", [$target_id]);
            } else if (isset($_GET['vulnerability_id'])) {
                $vulnerability_id = (int)$_GET['vulnerability_id'];
                $app->handleApiRoute('pentester', "VulnerabilityController", "deleteVulnerability", [$vulnerability_id]);
            } else if (isset($_GET['user_id'])) {
                $user_id = (int)$_GET['user_id'];
                $app->handleApiRoute('admin', "AdminDashboardController", "deleteUser", [$user_id]);
            } else {
                throw new HTTPException('Invalid request: test_id, target_id or vulnerability_id is required', 400);
            }
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
    Logger::write('error', 'HTTP error: ' . $e->getMessage() . ' - ' . $uri . ' - Line: ' . $e->getLine() . ' - File: ' . basename($e->getFile()));
    $c = new ErrorController();
    $c($e);
} catch (\Throwable $e) {
    Logger::write('error', 'Unhandled error: ' . $e->getMessage());
    header('Content-Type: text/html');
    http_response_code(500);
    include DIR_VIEWS . 'error.html.php';
}