<?php

namespace Ssms\Controllers;

use Ssms\Exceptions\HTTPException;
use Ssms\Logger;

class AuthenticatorController
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Handle user login
     * @return array
     */
    public function login()
    {
        try {
            // Get the request body
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            
            Logger::write('info', "Login request data: " . print_r($data, true));

            // Validate the request body
            if (!isset($data['email']) || !isset($data['password'])) {
                throw new HTTPException('Email and password required', 400);
            }

            // Database authentication
            try {
                // Authenticate the user with database using PDO
                $stmt = $this->pdo->prepare("SELECT id, email, password, role_id FROM users WHERE email = :email");
                $stmt->execute(['email' => $data['email']]);
                $user = $stmt->fetch();

                if ($user && $data['password'] === $user['password']) {
                    // Make sure session is started
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    // Get role name
                    $role_stmt = $this->pdo->prepare("SELECT name FROM roles WHERE id = :role_id");
                    $role_stmt->execute(['role_id' => $user['role_id']]);
                    $role = $role_stmt->fetch();
                    $role_name = $role ? $role['name'] : 'Unknown';
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['role_id'] = (int)$user['role_id'];
                    $_SESSION['role'] = $role_name;

                    // log all session variables
                    Logger::write('info', "Session variables: " . print_r($_SESSION, true));

                    // Determine redirect URL based on role
                    if ($role_name === 'admin' || $role_name === 'employee') {
                        $_SESSION['redirect'] = '/employee-dashboard';
                    } else if ($role_name === 'customer') {
                        $_SESSION['redirect'] = '/';
                    } else {
                        throw new HTTPException('Unknown role', 401);
                    }

                    Logger::write('info', "Redirecting to: " . $_SESSION['redirect']);
                    
                    return [
                        'status' => 200,
                        'data' => [
                            'success' => true,
                            'message' => 'Login successful',
                            'email' => $user['email'],
                            'role' => $role_name,
                            'redirect' => $_SESSION['redirect']
                        ]
                    ];
                } else {
                    throw new HTTPException('Invalid credentials', 401);
                }
            } catch (\Exception $dbError) {
                Logger::write('error', "Database error: " . $dbError->getMessage());
                throw new HTTPException('Database connection error. Please try again later.', 500, $dbError->getFile(), $dbError->getLine());
            }
        } catch (HTTPException $httpError) {
            return [
                'status' => $httpError->getCode(),
                'data' => [
                    'success' => false,
                    'error' => $httpError->getMessage()
                ]
            ];
        } catch (\Exception $e) {
            file_put_contents("php://stderr", "General error in controller: " . $e->getMessage() . "\n");
            return [
                'status' => 500,
                'data' => [
                    'success' => false,
                    'error' => 'An unexpected error occurred',
                    'details' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Handle user logout
     * @return array
     */
    public function logout()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destroy the session
        session_destroy();

        return [
            'status' => 200,
            'data' => ['success' => true, 'message' => 'Logout successful']
        ];
    }

    /**
     * Check if user is logged in
     * @return array containing user data or error message
     */
    public function isLoggedIn()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Debug session info
        Logger::write('info', "Session variables: " . print_r($_SESSION, true));

        // Check if user is logged in
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            Logger::write('info', "User is logged in: " . $_SESSION['email']);
            return [
                'status' => 200,
                'data' => [
                    'success' => true,
                    'message' => 'User is logged in',
                ]
            ];
        } else {
            return [
                'status' => 401,
                'data' => ['success' => false, 'message' => 'User is not logged in']
            ];
        }
    }
}