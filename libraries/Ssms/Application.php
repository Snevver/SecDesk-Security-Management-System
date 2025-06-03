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
            '/api/customers',
            '/api/tests', 
            '/api/targets',
            '/api/vulnerabilities'
        ];

        $this->authenticationController = new AuthenticatorController(Db::getInstance());
    }    
    
    public function init(): bool {
        $this->setLogger(new Logger());
        $this->disableCORSErrors();

        if ($this->checkForFile()) {
            return false;
        }

        return true;
    }    public function getUri(): string {
        if (!isset($this->uri)) {   
            $this->uri = strtok(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '?');
            $this->logger?->info("Request recieved: " . $this->getHTTPMethod() . ' ' . $this->uri);
        }

        return $this->uri; 
    }

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

    public function disableCORSErrors() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }


    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }    
    
    public function checkForFile(): bool
    {
        if (is_file(__DIR__ . '/../../public' . $this->getUri())) {
            $this->logger?->info("FILE: " . $this->getUri());
            return true;
        }

        return false;
    }    
    
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

    public function sendJsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }    
    
    public function handleAuthenticatedRoute($viewFile, $neededRole = null) {
        $this->checkLogin();

        if ($neededRole !== null && isset($_SESSION['role']) && $_SESSION['role'] !== $neededRole) {
            $this->logger?->error("Access denied for user with role {$_SESSION['role']} to $viewFile");
            http_response_code(403);
            include DIR_VIEWS . 'index.html.php';
        } else {
            $this->logger?->info("Redirecting authenticated user to $viewFile");
            include DIR_VIEWS . $viewFile;
        }
    }

    public function applyAuthMiddleware() {
        foreach ($this->protectedRoutes as $route) {
            if ($this->getUri() === $route) {
                $this->checkLogin();
                break;
            }
        }
    }

    private function checkLogin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            $this->logger?->info("User " . ($_SESSION['email'] ?? 'unknown') . " is logged in, proceeding with request.");
            return true;
        } else {
            $this->logger?->info("User is not logged in, redirecting to /login");
            include DIR_VIEWS . 'login.html.php';
            exit;
        }
    }

    public function getAuthenticationController(): AuthenticatorController {
        return $this->authenticationController;
    }

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

    public function getQueryParam(string $key, null|string $default = null) {
        return $_GET[$key] ?? $default;
    }

    public function getIntQueryParam(string $key, null|string $default = null): ?int {
        $value = $this->getQueryParam($key, $default);
        return $value !== null ? (int)$value : null;
    }

    public function isAuthenticated(): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

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
}