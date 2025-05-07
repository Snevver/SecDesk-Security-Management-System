<?php

namespace DeepDiveAPI\resources;

class AuthenticatorController
{
    // Properties
    private \PDO $pdo;

    // Constructor
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

            file_put_contents("php://stdout", "Login request data: " . print_r($data, true) . "\n");

            // Validate the request body
            if (!isset($data['email']) || !isset($data['password'])) {
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Email and password required']
                ];
            }

            // Database authentication
            try {
                // Test database connection first
                $this->pdo->query("SELECT 1");
                
                // Authenticate the user with database using PDO
                $stmt = $this->pdo->prepare("SELECT id, email, password, role_id FROM users WHERE email = :email");
                $stmt->execute(['email' => $data['email']]);
                $user = $stmt->fetch();

                if ($user && $data['password'] === $user['password']) {
                    // Get role name
                    $role_stmt = $this->pdo->prepare("SELECT name FROM roles WHERE id = :role_id");
                    $role_stmt->execute(['role_id' => $user['role_id']]);
                    $role = $role_stmt->fetch();
                    $role_name = $role ? $role['name'] : 'Unknown';
                    
                    // Start session
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['role_id'] = (int)$user['role_id'];
                    $_SESSION['role'] = $role_name;
                    
                    return [
                        'status' => 200,
                        'data' => [
                            'success' => true,
                            'message' => 'Login successful',
                            'email' => $user['email'],
                            'role' => $role_name,
                            'redirect' => 'dashboard.html'
                        ]
                    ];
                } else {
                    return [
                        'status' => 401,
                        'data' => ['success' => false, 'error' => 'Invalid credentials']
                    ];
                }
            } catch (\Exception $dbError) {
                file_put_contents("php://stderr", "Database error in controller: " . $dbError->getMessage() . "\n");
                
                return [
                    'status' => 500,
                    'data' => [
                        'success' => false, 
                        'error' => 'Database connection error. Please try again later.',
                        'details' => $dbError->getMessage()
                    ]
                ];
            }
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
}