<?php

declare(strict_types=1);

namespace Ssms;

use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerInterface;
use Ssms\Exceptions\HTTPException;
use Ssms\Database\Db;
use Ssms\Controllers\AuthenticatorController;


class Application implements LoggerAwareInterface
{
    private string $uri;
    private string $HTTPMethod;
    private null|LoggerInterface $logger = null;
    private AuthenticatorController $authenticationController;
    private array $protectedRoutes;    
    
    public function __construct() {        
        $this->protectedRoutes = [
            '/',
            '/targets',
            '/edit',
            '/admin/customer',
            '/admin/pentester',
            '/api/get-all-customers',
            '/api/get-all-employees',
            '/api/get-all-admins',
            '/api/get-all-customer-tests', 
            '/api/update-test-completion',
            '/api/get-all-targets',
            '/api/get-all-vulnerabilities',
            '/api/get-test',
            '/check-access',
            '/create-account',
            '/create-test',
            '/add-target',
            '/update-test',
            '/api/change-password',
            '/api/delete',
        ];

        $this->authenticationController = new AuthenticatorController(Db::getInstance());
    }
    
    /**
     * Initializes the application.
     * 
     * @return bool Returns true if initialization is successful, false if a file is found.
     */
    public function init(): bool {
        $this->setLogger(new Logger());
        $this->disableCORSErrors();

        if ($this->checkForFile()) {
            return false;
        }

        return true;
    }


    /**
     * Returns the URI of the current request.
     * 
     * @return string The sanitized URI without query parameters.
     */
    public function getUri(): string {
        if (!isset($this->uri)) {   
            $this->uri = strtok(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '?');
            $this->logger?->info("Request recieved: " . $this->getHTTPMethod() . ' ' . $this->uri);
        }

        return $this->uri; 
    }

    /**
     * Returns the HTTP method of the current request.
     * 
     * @return string The HTTP method in lowercase (get, post, put, delete, options).
     */
    public function getHTTPMethod(): string {
        if (!isset($this->HTTPMethod)) {
            $this->HTTPMethod = strtoupper(match ($_SERVER['REQUEST_METHOD']) {
                'POST' => 'post',
                'PUT' => 'put',
                'DELETE' => 'delete',
                'OPTIONS' => 'options',
                default => 'get',
            });
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        return $this->HTTPMethod;
    }   

    /**
     * Disables CORS errors by setting appropriate headers.
     * 
     * This method allows cross-origin requests from any origin and supports common HTTP methods and headers.
     */
    public function disableCORSErrors() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }

    /**
     * Sets the logger for the application.
     * 
     * @param LoggerInterface $logger The logger instance to be used by the application.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }    
    
    /**
     * Checks if a file exists at the requested URI.
     * 
     * @return bool Returns true if the file exists, false otherwise.
     */
    public function checkForFile(): bool
    {
        if (is_file(__DIR__ . '/../../public' . $this->getUri())) {
            $this->logger?->info("FILE: " . $this->getUri());
            return true;
        }

        return false;
    }    
    
    /**
     * Uses a controller and method to handle the request.
     * 
     * @param string $controllerName The name of the controller class.
     * @param string $methodName The method to call on the controller.
     * @param array $args Arguments to pass to the method.
     * @return mixed The result of the method call.
     * @throws HTTPException If the controller or method does not exist.
     */
    public function useController($controllerName, $methodName, $args = []) {
        $controllerClass = "Ssms\\Controllers\\$controllerName";
        if (!class_exists($controllerClass)) {
            $this->logger?->error("Controller $controllerName not found");
            throw new HTTPException("Controller $controllerName not found", 404);
        }

        $controller = new $controllerClass(Db::getInstance());
        if (!method_exists($controller, $methodName)) {
            $this->logger?->error("Method $methodName not found in controller $controllerName");
            throw new HTTPException("Method $methodName not found in controller $controllerName", 404);
        }

        return call_user_func_array([$controller, $methodName], $args);
    }

    /**
     * Sends a JSON response with the given data and status code.
     * 
     * @param mixed $data The data to send in the response.
     * @param int $statusCode The HTTP status code for the response (default is 200).
     */
    public function sendJsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }    
    
    /**
     * Handles authenticated routes by checking login status and role.
     * 
     * @param string $viewFile The view file to include if access is granted.
     * @param string|null $neededRole The role required to access the route, or null for no role restriction.
     */    
    public function handleAuthenticatedRoute($viewFile, $allowedRoles = null) {
        $result = $this->authenticationController->isLoggedIn();
        
        if ($result['status'] !== 200) {
            $this->logger?->info("User is not logged in, redirecting to /login");
            include DIR_VIEWS . 'login.html.php';
            exit;
        }

        // Start session to access role information
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($allowedRoles !== null) {
            // Convert single role to array for consistent handling
            if (is_string($allowedRoles)) {
                $allowedRoles = [$allowedRoles];
            }
            
            $userRole = $_SESSION['role'] ?? null;
            
            if (!in_array($userRole, $allowedRoles)) {
                $this->logger?->error("Access denied for user with role '{$userRole}' to $viewFile. Required roles: " . implode(', ', $allowedRoles));
                http_response_code(403);
                include DIR_VIEWS . 'error.html.php';
                return;
            }
        }
        
        $this->logger?->info("Redirecting authenticated user to $viewFile");
        include DIR_VIEWS . $viewFile;
    }
    
    /**
     * Applies authentication middleware to protected routes.
     * 
     * This method checks if the current URI matches any of the protected routes
     * and applies the authentication check if it does.
     */
    public function applyAuthMiddleware() {
        foreach ($this->protectedRoutes as $route) {
            if ($this->getUri() === $route) {
                $result = $this->authenticationController->isLoggedIn();
                
                if ($result['status'] !== 200) {
                    $this->logger?->info("User is not logged in, redirecting to /login");
                    include DIR_VIEWS . 'login.html.php';
                    exit;
                }
                break;
            }
        }
    }
    
      /**
     * Checks if the user is authenticated for API access.
     * 
     * This method uses the centralized authentication controller to check login status
     * and throws an HTTPException if the user is not authenticated.
     * 
     * @throws HTTPException If the user is not authenticated.
     */
    public function checkApiAuthentication() {
        // Start session to access authentication information
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $result = $this->authenticationController->isLoggedIn();
        
        if ($result['status'] !== 200) {
            $this->logger?->error("Unauthenticated API access attempt to " . $this->getUri());
            throw new HTTPException('Authentication required.', 401);
        }
        
        $this->logger?->info("User authenticated for API access to " . $this->getUri());
        return true;
    }

    /**
     * Checks if the user has the required role for API access.
     * 
     * This method checks if the user is authenticated and then verifies if their role
     * matches any of the allowed roles. If not, it throws an HTTPException.
     * 
     * @param string|array $allowedRoles A single role or an array of roles that are allowed access.
     * @throws HTTPException If the user does not have the required role.
     */
    public function checkApiAuthorization(string|array $allowedRoles) {
        $this->checkApiAuthentication();
        
        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        
        $userRole = $_SESSION['role'] ?? null;
        
        if (!in_array($userRole, $allowedRoles)) {
            $this->logger?->error("Access denied for user with role '{$userRole}' to " . $this->getUri() . ". Required roles: " . implode(', ', $allowedRoles));
            throw new HTTPException('Access denied. Insufficient permissions.', 403);
        }
        
        $this->logger?->info("User with role '{$userRole}' authorized for API access to " . $this->getUri());
        return true;
    }

    /**
     * Returns the authentication controller instance.
     * 
     * @return AuthenticatorController The authentication controller.
     */
    public function getAuthenticationController(): AuthenticatorController {
        return $this->authenticationController;
    }

    /**
     * Handles static assets by serving the requested file with the appropriate content type.
     * 
     * @param string $assetPath The path to the asset relative to the application root.
     * @param string $contentType The content type of the asset (e.g., 'text/css', 'application/javascript').
     * @throws HTTPException If the asset is not found.
     */
    public function handleStaticAsset($assetPath, $contentType) {
        $fullPath = APP_ROOT . $assetPath;
        
        if (file_exists($fullPath)) {
            header("Content-Type: $contentType");
            echo file_get_contents($fullPath);
            exit;
        } else {
            throw new HTTPException('Asset not found', 404);
        }
    }    
    
    /**
     * Checks if the user is authenticated.
     * 
     * This method uses the centralized authentication controller to check login status.
     * 
     * @return bool Returns true if the user is authenticated, false otherwise.
     */
    public function isAuthenticated(): bool {
        $result = $this->authenticationController->isLoggedIn();
        return $result['status'] === 200;
    }
    
    /**
     * Handles the dashboard route based on user role.
     * 
     * This method checks if the user is logged in and redirects to the appropriate dashboard
     * based on their role (admin, pentester, or default).
     */
    public function handleDashboardRoute() {
        if ($this->isAuthenticated()) {
            // Start session to access role information
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            // Redirect to the appropriate dashboard based on role
            if ($_SESSION['role'] === 'admin') {
                $this->handleAuthenticatedRoute('adminDashboard.html.php');
            } else if ($_SESSION['role'] === 'pentester') {
                $this->handleAuthenticatedRoute('employeeDashboard.html.php');
            } else {
                $this->handleAuthenticatedRoute('index.html.php');
            }
        }
    }

    /**
     * Returns the current user's information if authenticated.
     * 
     * This method retrieves the user's ID, email, role, and role ID from the session.
     * 
     * @return array|null Returns an associative array with user information or null if not authenticated.
     */
    public function getCurrentUser(): ?array {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? null,
            'role_id' => $_SESSION['role_id'] ?? null
        ];
    }    
    
    /**
     * Decode and sanitize the request body
     * 
     * @return mixed The sanitized data (string for email-only requests, array for complex data).
     * @throws HTTPException If the data is invalid.
     */
    public function decodeBody() {
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);
        
        // If it's the new format with accountType and email
        if (isset($data['accountType']) && isset($data['email'])) {
            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            if (!$email) {
                throw new HTTPException('Invalid email format', 400);
            }
            return [
                'accountType' => $data['accountType'],
                'email' => $email
            ];
        }
        
        // Legacy format - just email
        if (!isset($data['email'])) {
            throw new HTTPException('Email is required', 400);
        }
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        
        return $email;
    }

    /**
     * Handles an API route with authentication and authorization.
     * 
     * This method combines authentication and authorization checks, then executes
     * the specified controller method and sends the JSON response.
     * 
     * @param string|array $allowedRoles A single role or an array of roles that are allowed access.
     * @param string $controllerName The name of the controller class.
     * @param string $methodName The name of the method to call on the controller.
     * @param array $parameters Optional parameters to pass to the controller method.
     * @throws HTTPException If authentication or authorization fails.
     */
    public function handleApiRoute(string|array $allowedRoles, string $controllerName, string $methodName, array $parameters = []) {
        $this->checkApiAuthorization($allowedRoles);
        $result = $this->useController($controllerName, $methodName, $parameters);
        $this->sendJsonResponse($result['data'], $result['status']);
    }
    
    /**
     * Defines route-specific authorization requirements.
     * 
     * @return array An associative array mapping routes to their required roles.
     */
    private function getRouteAuthorization(): array {
        return [
            '/targets' => ['pentester', 'customer', 'admin'],
            '/edit' => ['pentester'],
            '/api/get-all-customers' => ['admin', 'pentester'],
            '/api/get-all-employees' => ['admin', 'pentester'],
            '/api/get-all-admins' => ['admin'],
            '/api/get-all-customer-tests' => ['admin', 'customer', 'pentester'],
            '/api/get-all-employee-tests' => ['pentester'],
            '/api/update-test-completion' => ['pentester'],
            '/api/get-all-targets' => ['pentester', 'customer'],
            '/api/get-all-vulnerabilities' => ['pentester', 'customer'],
            '/api/get-test' => ['pentester'],
            '/check-access' => [],
            '/create-customer' => ['admin'],
            '/create-account' => ['admin'],
            '/create-test' => ['pentester'],
            '/add-target' => ['pentester'],
            '/update-test' => ['pentester'],
            '/api/change-password' => [],
            '/api/delete' => ['admin', 'pentester']
        ];
    }

    /**
     * Enhanced authentication middleware that applies both authentication and authorization.
     */
    public function applyEnhancedAuthMiddleware() {
        $uri = $this->getUri();
        $routeAuth = $this->getRouteAuthorization();
        
        // Check if route requires any authentication
        if (in_array($uri, $this->protectedRoutes) || array_key_exists($uri, $routeAuth)) {
            $result = $this->authenticationController->isLoggedIn();
            
            if ($result['status'] !== 200) {
                $this->logger?->info("User is not logged in, redirecting to /login");
                include DIR_VIEWS . 'login.html.php';
                exit;
            }
            
            // Check role-based authorization if required
            if (array_key_exists($uri, $routeAuth) && !empty($routeAuth[$uri])) {
                $this->checkUserRole($routeAuth[$uri]);
            }
        }
    }

    /**
     * Checks if the current user has one of the required roles.
     * 
     * @param array $allowedRoles Array of roles allowed for this route.
     * @throws HTTPException If the user doesn't have the required role.
     */
    private function checkUserRole(array $allowedRoles) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userRole = $_SESSION['role'] ?? null;
        
        if (!in_array($userRole, $allowedRoles)) {
            $this->logger?->error("Access denied for user with role '{$userRole}' to " . $this->getUri() . ". Required roles: " . implode(', ', $allowedRoles));
            throw new HTTPException('Access denied. Insufficient permissions.', 403);
        }
    }
}