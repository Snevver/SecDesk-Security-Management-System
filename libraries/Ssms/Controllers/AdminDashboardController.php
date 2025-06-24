<?php

namespace Ssms\Controllers;
use Ssms\Logger;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AdminDashboardController
{
    private PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    //-----------------------------------------------------
    // Fetch All Customers From Database
    //-----------------------------------------------------
    public function getCustomers() {
        try {       
            // Fetch all customers from the database
            $stmt = $this->pdo->prepare("SELECT id, email FROM users where role_id = 1");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched customers: ' . json_encode($users));

            return [
                'status' => 200,
                'data' => ['success' => true, 'users' => $users]
            ];
        } catch (\PDOException $e) { 
            Logger::write('error', 'Database error: ' . $e->getMessage());       
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        } catch (\Exception $e) {   
            Logger::write('error', 'System error: ' . $e->getMessage());     
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'System error: ' . $e->getMessage()]
            ];
        }
    }

    //-----------------------------------------------------
    // Fetch All Employees From Database
    //-----------------------------------------------------
    public function getEmployees() {
        try {
            // Fetch all employees from the database
            $stmt = $this->pdo->prepare("SELECT id, email FROM users where role_id = 2");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched employees: ' . json_encode($users));

            return [
                'status' => 200,
                'data' => ['success' => true, 'users' => $users]
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        } catch (\Exception $e) {
            Logger::write('error', 'System error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'System error: ' . $e->getMessage()]
            ];
        }
    }

    //-----------------------------------------------------
    // Fetch All Admins From Database
    //-----------------------------------------------------
    public function getAdmins() {
        try {
            // Fetch all admins from the database
            $stmt = $this->pdo->prepare("SELECT id, email FROM users where role_id = 3");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched admins: ' . json_encode($users));

            return [
                'status' => 200,
                'data' => ['success' => true, 'users' => $users]
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        } catch (\Exception $e) {
            Logger::write('error', 'System error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'System error: ' . $e->getMessage()]
            ];
        }
    }
    
    // -----------------------------------------------------
    // Create a new account of specified type
    // -----------------------------------------------------
    public function createNewAccount($data) {
        try {
            // Handle both old format (for backward compatibility) and new format
            if (is_array($data) && isset($data['accountType']) && isset($data['email'])) {
                // New format: array with accountType and email
                $typeOfAccount = $data['accountType'];
                $email = $data['email'];
            } else if (is_string($data)) {
                // Old format: just email string, assume customer
                $typeOfAccount = 'customer';
                $email = $data;
            } else {
                Logger::write('error', 'Invalid data format for createNewAccount');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Invalid data format']
                ];
            }

            Logger::write('info', 'Creating new account of type: ' . $typeOfAccount . ' for email: ' . $email);
        
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Logger::write('error', 'Invalid email format: ' . $email);
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Invalid email format']
                ];
            }
            
            // Check if user already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                Logger::write('error', 'User already exists: ' . $email);
                return [
                    'status' => 409,
                    'data' => ['success' => false, 'error' => 'User with this email already exists']
                ];
            }

            switch($typeOfAccount) {
                case 'customer':
                    $roleId = 1; // Customer role
                    break;
                case 'employee':
                    $roleId = 2; // Employee role
                    break;
                case 'admin':
                    $roleId = 3; // Admin role
                    break;
                default:
                    Logger::write('error', 'Invalid account type: ' . $typeOfAccount);
                    return [
                        'status' => 400,
                        'data' => ['success' => false, 'error' => 'Invalid account type']
                    ];
            }

            // Create user
            $stmt = $this->pdo->prepare("INSERT INTO users (email, password, role_id) VALUES (:email, :password, :role_id)");
            $password = $this->generatePassword();
            $stmt->execute([
                'email' => $email,
                'password' => $password,
                'role_id' => $roleId
            ]);
            $userId = $this->pdo->lastInsertId();
            Logger::write('info', 'Account created successfully with ID: ' . $userId);

            // Send welcome email with credentials
            $this->sendEmail($email, $password);            
            return [
                    'status' => 201,
                    'data' => [
                        'success' => true, 
                        'message' => ucfirst($typeOfAccount) . ' created successfully',
                        'user_id' => $userId,
                        'email' => $email,
                        'role' => $typeOfAccount
                    ]
                ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error creating account: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        } catch (\Exception $e) {
            Logger::write('error', 'System error creating account: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'System error: ' . $e->getMessage()]
            ];
        }
    }

    //-----------------------------------------------------
    // Generate a random password
    //-----------------------------------------------------
    public function generatePassword() {
        return bin2hex(random_bytes(16));
    }    
    
    //-----------------------------------------------------
    // Send email to user with their credentials
    //-----------------------------------------------------
    public function sendEmail($email, $password) {
        Logger::write('info', 'Sending email to ' . $email . ' with password: ' . $password);
        
        try {
            // Load email configuration
            $emailConfig = include __DIR__ . '/../../../includes/email-config.php';
            
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = $emailConfig['smtp_host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $emailConfig['smtp_username'];
            $mail->Password   = $emailConfig['smtp_password'];
            $mail->SMTPSecure = $emailConfig['smtp_encryption'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $emailConfig['smtp_port'];

            // Recipients
            $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
            $mail->addAddress($email);
            $mail->addReplyTo($emailConfig['reply_to']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your SecDesk Account Credentials';
            $mail->Body    = $this->getEmailTemplate($email, $password);
            $mail->AltBody = "Welcome to SecDesk!\n\nYour account has been created successfully.\n\nEmail: {$email}\nPassword: {$password}\n\nPlease log in and change your password.\n\nBest regards,\nSecDesk Team";

            $mail->send();
            Logger::write('info', 'Email sent successfully to ' . $email);
            return true;
            
        } catch (Exception $e) {
            Logger::write('error', 'Email sending failed: ' . $mail->ErrorInfo);
            return false;
        }
    }

    //-----------------------------------------------------
    // Get HTML email template
    //-----------------------------------------------------
    private function getEmailTemplate($email, $password) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Welcome to SecDesk</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .credentials { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #7f8c8d; }
                .button { display: inline-block; padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to SecDesk</h1>
                    <p>Security Management System</p>
                </div>
                <div class='content'>
                    <h2>Your Account Has Been Created</h2>
                    <p>Hello,</p>
                    <p>Your SecDesk account has been successfully created. Below are your login credentials:</p>
                    
                    <div class='credentials'>
                        <strong>Email:</strong> {$email}<br>
                        <strong>Password:</strong> {$password}
                    </div>
                    
                    <p><strong>Important:</strong> For security reasons, please log in and change your password immediately.</p>
                    
                    <p style='text-align: center;'>
                        <a href='#' class='button'>Log In to SecDesk</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>&copy; 2025 SecDesk Security Management System</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    public function deleteUser($userId) {
        try {
            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            if (!$stmt->fetch()) {
                Logger::write('error', 'User not found: ' . $userId);
                return [
                    'status' => 404,
                    'data' => ['success' => false, 'error' => 'User not found']
                ];
            }

            // Delete user
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            Logger::write('info', 'User deleted successfully: ' . $userId);

            return [
                'status' => 200,
                'data' => ['success' => true, 'message' => 'User deleted successfully']
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error deleting user: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        } catch (\Exception $e) {
            Logger::write('error', 'System error deleting user: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'System error: ' . $e->getMessage()]
            ];
        }
    }
}