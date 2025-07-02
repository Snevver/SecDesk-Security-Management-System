<?php
//======================================================================
// LOG IN AND LOG OUT CONTROLLER
//======================================================================

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

    //-----------------------------------------------------
    // Handle user log in
    //-----------------------------------------------------
    public function login()
    {
        try {
            // Get the request body
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);

            // Validate the request body
            if (!isset($data['email']) || !isset($data['password'])) throw new HTTPException('Email and password required', 400);

            // Database authentication
            try {
                // Authenticate the user with database using PDO
                $stmt = $this->pdo->prepare("SELECT id, email, password, role_id FROM users WHERE email = :email");
                $stmt->execute(['email' => $data['email']]);
                $user = $stmt->fetch();

                Logger::write('info', 'password: ' . $data['password'] . ' and ' . $user['password']    );

                if ($user && crypt($data['password'], $user['password']) === $user['password']) {
                    // Make sure session is started
                    if (session_status() === PHP_SESSION_NONE) session_start();

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
                    $_SESSION['redirect'] = '/';

                    Logger::write('info', "Login of " . $_SESSION['email'] . " successful! Redirecting user to " . $_SESSION['redirect']);
                    
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
                    Logger::write('error', "Login failed for " . $data['email'] . ": Invalid credentials");
                    throw new HTTPException('Invalid credentials', 401);
                }
            } catch (HTTPException $httpError) {
                throw $httpError;
            } catch (\PDOException $dbError) {
                Logger::write('error', "Database error: " . $dbError->getMessage());
                throw new HTTPException('Database connection error. Please try again later.', 500, $dbError->getFile(), $dbError->getLine());
            } catch (\Exception $dbError) {
                Logger::write('error', "Unexpected database error: " . $dbError->getMessage());
                throw new HTTPException('Database connection error. Please try again later.', 500, $dbError->getFile(), $dbError->getLine());
            }
        } catch (HTTPException $httpError) {
            $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
            Logger::write('error', "HTTP error: " . $httpError->getMessage() . " - " . $uri);
            return [
                'status' => $httpError->getCode(),
                'data' => [
                    'success' => false,
                    'error' => $httpError->getMessage()
                ]
            ];
        } catch (\Exception $e) {
            Logger::write('error', "Unexpected error: " . $e->getMessage());
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

    //-----------------------------------------------------
    // Handle user log out
    //-----------------------------------------------------
    public function logout()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        Logger::write('info', "Logging out user: " . $_SESSION['email']);

        // Destroy the session
        session_destroy();
        
        return [
            'status' => 200,
            'data' => ['success' => true, 'message' => 'Logout successful']
        ];
    }

    //-----------------------------------------------------
    // Check if user is logged in
    //-----------------------------------------------------
    public function isLoggedIn()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Check if user is logged in
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
            Logger::write('info', $_SESSION['email'] . " is logged in!");

            return [
                'status' => 200,
                'data' => [
                    'success' => true,
                    'message' => 'User is logged in',
                ]
            ];
        } else {
            Logger::write('info', "User is not logged in!");
            return [
                'status' => 401,
                'data' => ['success' => false, 'message' => 'User is not logged in']
            ];
        }
    }

    //-----------------------------------------------------
    // Check if pentester has access to test
    //-----------------------------------------------------
    public function doesUserHaveAccess()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        $testID = $_GET['test_id'] ?? null;

        if ($testID === null) {
            return [
                'status' => 400,
                'data' => [
                    'success' => false,
                    'message' => 'No test ID provided',
                ]
                ];
        } else {
            // Check if user is logged in
            if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
                return [
                    'status' => 401,
                    'data' => ['success' => false, 'message' => 'User is not logged in']
                ];
            }

            // Check if user is a pentester
            if ($_SESSION['role'] !== 'pentester') {
                return [
                    'status' => 403,
                    'data' => ['success' => false, 'message' => 'Access denied']
                ];
            }

            // Check if test ID belongs to the pentester
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tests WHERE id = :id AND pentester_id = :pentester_id");
            $stmt->execute(['id' => $testID, 'pentester_id' => $_SESSION['user_id']]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                return [
                    'status' => 200,
                    'data' => ['success' => true, 'message' => 'User has access to the test']
                ];
            } else {
                return [
                    'status' => 403,
                    'data' => ['success' => false, 'message' => 'Access denied to this test']
                ];
            }
        }
    }

    //-----------------------------------------------------
    // Change user password
    //-----------------------------------------------------
    public function changePassword()
    {
        try {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) session_start();

            // Check if user is logged in
            if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
                return [
                    'status' => 401,
                    'data' => ['success' => false, 'message' => 'User is not logged in']
                ];
            }

            // Get the request body
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);

            // Validate the request body
            if (!isset($data['currentPassword']) || !isset($data['newPassword'])) {
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'message' => 'Current password and new password are required']
                ];
            }

            $currentPassword = $data['currentPassword'];
            $newPassword = $data['newPassword'];
            $userId = $_SESSION['user_id'];

            // Validate new password strength
            if (strlen($newPassword) < 8) {
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'message' => 'New password must be at least 8 characters long']
                ];
            }

            // Check password strength
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $newPassword)) {
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'message' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number']
                ];
            }

            // Get current user password from database
            $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $user = $stmt->fetch();

            if (!$user) {
                return [
                    'status' => 404,
                    'data' => ['success' => false, 'message' => 'User not found']
                ];
            }

            // Verify current password using crypt for bcrypt hashes
            if (crypt($currentPassword, $user['password']) !== $user['password']) {
                Logger::write('error', 'Failed password change attempt for user ID ' . $userId . ': Invalid current password');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'message' => 'Current password is incorrect']
                ];
            }

            // Hash the new password using crypt with bcrypt salt
            $salt = '$2y$10$' . substr(str_replace('+', '.', base64_encode(random_bytes(16))), 0, 22);
            $hashedNewPassword = crypt($newPassword, $salt);
            
            // Update password in database
            $stmt = $this->pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
            $result = $stmt->execute([
                'new_password' => $hashedNewPassword,
                'user_id' => $userId
            ]);

            if ($result) {
                Logger::write('info', 'Password changed successfully for user ID ' . $userId);
                return [
                    'status' => 200,
                    'data' => ['success' => true, 'message' => 'Password changed successfully']
                ];
            } else {
                Logger::write('error', 'Failed to update password for user ID ' . $userId);
                return [
                    'status' => 500,
                    'data' => ['success' => false, 'message' => 'Failed to update password']
                ];
            }

        } catch (\PDOException $e) {
            Logger::write('error', 'Database error during password change: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'message' => 'Database error occurred']
            ];
        } catch (\Exception $e) {
            Logger::write('error', 'System error during password change: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'message' => 'System error occurred']
            ];
        }
    }

    /**
     * Handle edit route authorization and view rendering
     */
    public function handleEditRoute()
    {
        // Check if test_id is provided for editing existing test
        if (isset($_GET['test_id'])) {
            $accessResult = $this->doesUserHaveAccess();
            if ($accessResult['status'] == 200) {
                return [
                    'status' => 200,
                    'data' => ['view' => 'editTest.html.php', 'role' => 'pentester']
                ];
            } else {
                throw new HTTPException('Access denied', 403);
            }
        } else {
            // No test_id provided, this is for creating a new test
            return [
                'status' => 200,
                'data' => ['view' => 'editTest.html.php', 'role' => 'pentester']
            ];
        }
    }
}