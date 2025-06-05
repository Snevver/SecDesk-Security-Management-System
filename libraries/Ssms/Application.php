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
            '/employee',
            '/admin',
            '/targets',
            '/edit',
            '/api/customers',
            '/api/employees',
            '/api/tests', 
            '/api/employee-tests',
            '/api/update-test-completion',
            '/api/targets',
            '/api/vulnerabilities',
            '/check-access',
            '/create-test',
            '/create-customer',
            '/create-employee',
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
    public function handleAuthenticatedRoute($viewFile, $neededRole = null) {
        $result = $this->authenticationController->isLoggedIn();
        
        if ($result['status'] !== 200) {
            $this->logger?->info("User is not logged in, redirecting to /login");
            include DIR_VIEWS . 'login.html.php';
            exit;
        }

        // Start session to access role information
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($neededRole !== null && isset($_SESSION['role']) && $_SESSION['role'] !== $neededRole) {
            $this->logger?->error("Access denied for user with role {$_SESSION['role']} to $viewFile");
            http_response_code(403);
            include DIR_VIEWS . 'index.html.php';
        } else {
            $this->logger?->info("Redirecting authenticated user to $viewFile");
            include DIR_VIEWS . $viewFile;
        }
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
    public function checkApiAuthentication($allowedRoles = []) {
        // Start session to access role information
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (in_array($_SESSION['role'], $allowedRoles)) {
            return true;
        } else {
            return false;
        }
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
            $this->logger?->warning("Access denied for user with role '{$userRole}' to " . $this->getUri() . ". Required roles: " . implode(', ', $allowedRoles));
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
     * Decodes the request body and extracts the email.
     * 
     * This method reads the raw request body, decodes it from JSON, and sanitizes the email.
     * 
     * @return string The sanitized email address.
     * @throws HTTPException If the email is not provided or invalid.
     */
    public function decodeBody() {
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);
        if (!isset($data['email'])) {
            throw new HTTPException('Email is required', 400);
        }
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        
        return $email;
    }
}