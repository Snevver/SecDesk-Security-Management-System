<?php

namespace Ssms\Controllers;
use Ssms\Logger;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
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
            $plainPassword = $this->generatePassword();
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
            $stmt->execute([
                'email' => $email,
                'password' => $hashedPassword,
                'role_id' => $roleId
            ]);
            $userId = $this->pdo->lastInsertId();
            Logger::write('info', 'Account created successfully with ID: ' . $userId);

            // Send welcome email with credentials
            $this->sendEmail($email, $plainPassword);            
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

    //-----------------------------------------------------
    // Get Customer Details
    //-----------------------------------------------------
    public function getCustomerDetails($customer_id) {
        try {
            if (!$customer_id) {
                Logger::write('error', 'Customer ID not provided');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Customer ID is required']
                ];
            }            
            
            // Fetch customer details
            $stmt = $this->pdo->prepare("SELECT id, email, creation_date FROM users WHERE id = :customer_id AND role_id = 1");
            $stmt->execute(['customer_id' => $customer_id]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                Logger::write('error', 'Customer not found: ' . $customer_id);
                return [
                    'status' => 404,
                    'data' => ['success' => false, 'error' => 'Customer not found']
                ];
            }

            Logger::write('info', 'Fetched customer details for ID: ' . $customer_id);

            return [
                'status' => 200,
                'data' => ['success' => true, 'customer' => $customer]
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        }
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
            Logger::write('error', 'System error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'System error: ' . $e->getMessage()]
            ];
        }
    }

    //-----------------------------------------------------
    // Get Customer Tests
    //-----------------------------------------------------
    public function getCustomerTests($customer_id) {
        try {
            if (!$customer_id) {
                Logger::write('error', 'Customer ID not provided');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Customer ID is required']
                ];
            }            
            
            // Fetch tests for specific customer
            $stmt = $this->pdo->prepare("
                SELECT t.id, t.customer_id, t.pentester_id, t.test_name, t.test_description, t.test_date, t.completed,
                       p.email as pentester_email
                FROM tests t
                LEFT JOIN users p ON t.pentester_id = p.id
                WHERE t.customer_id = :customer_id 
                ORDER BY t.test_date DESC
            ");
            $stmt->execute(['customer_id' => $customer_id]);
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched ' . count($tests) . ' tests for customer ID: ' . $customer_id);

            return [
                'status' => 200,
                'data' => ['success' => true, 'tests' => $tests]
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
    // Get Customer Targets
    //-----------------------------------------------------
    public function getCustomerTargets($customer_id) {
        try {
            if (!$customer_id) {
                Logger::write('error', 'Customer ID not provided');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Customer ID is required']
                ];
            }            
            
            // Fetch targets for all tests of specific customer
            $stmt = $this->pdo->prepare("
                SELECT t.id, t.test_id, t.target_name, t.target_description,
                       ts.test_name as test_name
                FROM targets t
                INNER JOIN tests ts ON t.test_id = ts.id
                WHERE ts.customer_id = :customer_id
                ORDER BY ts.test_date DESC, t.id DESC
            ");
            $stmt->execute(['customer_id' => $customer_id]);
            $targets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched ' . count($targets) . ' targets for customer ID: ' . $customer_id);

            return [
                'status' => 200,
                'data' => ['success' => true, 'targets' => $targets]
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
    // Get Customer Vulnerabilities
    //-----------------------------------------------------
    public function getCustomerVulnerabilities($customer_id) {
        try {
            if (!$customer_id) {
                Logger::write('error', 'Customer ID not provided');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Customer ID is required']
                ];
            }            
            
            // Fetch vulnerabilities for all targets of all tests of specific customer
            $stmt = $this->pdo->prepare("
                SELECT v.id, v.target_id, v.affected_entity, v.identifier, v.risk_statement, 
                       v.affected_component, v.residual_risk, v.classification, v.identified_controls, 
                       v.cvss_score, v.likelihood, v.cvssv3_code, v.location, 
                       v.vulnerabilities_description, v.reproduction_steps, v.impact, 
                       v.remediation_difficulty, v.recommendations, v.recommended_reading, 
                       v.response, v.solved, v.created_at,
                       t.target_name as target_name,
                       ts.test_name as test_name
                FROM vulnerabilities v
                INNER JOIN targets t ON v.target_id = t.id
                INNER JOIN tests ts ON t.test_id = ts.id
                WHERE ts.customer_id = :customer_id
                ORDER BY 
                    CASE 
                        WHEN v.cvss_score >= 9.0 THEN 1
                        WHEN v.cvss_score >= 7.0 THEN 2
                        WHEN v.cvss_score >= 4.0 THEN 3
                        ELSE 4
                    END,
                    v.created_at DESC
            ");
            $stmt->execute(['customer_id' => $customer_id]);
            $vulnerabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched ' . count($vulnerabilities) . ' vulnerabilities for customer ID: ' . $customer_id);

            return [
                'status' => 200,
                'data' => ['success' => true, 'vulnerabilities' => $vulnerabilities]
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
    // Get Pentester Details
    //-----------------------------------------------------
    public function getPentesterDetails($pentester_id) {
        try {
            if (!$pentester_id) {
                Logger::write('error', 'Pentester ID not provided');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Pentester ID is required']
                ];
            }

            // Fetch pentester details - pentesters have role_id = 2
            $stmt = $this->pdo->prepare("
                SELECT id, email, creation_date 
                FROM users 
                WHERE id = :pentester_id AND role_id = 2
            ");
            $stmt->execute(['pentester_id' => $pentester_id]);
            $pentester = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pentester) {
                Logger::write('error', 'Pentester not found with ID: ' . $pentester_id);
                return [
                    'status' => 404,
                    'data' => ['success' => false, 'error' => 'Pentester not found']
                ];
            }

            Logger::write('info', 'Fetched pentester details for ID: ' . $pentester_id);

            return [
                'status' => 200,
                'data' => ['success' => true, 'pentester' => $pentester]
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
    // Get Pentester Tests
    //-----------------------------------------------------
    public function getPentesterTests($pentester_id) {
        try {
            if (!$pentester_id) {
                Logger::write('error', 'Pentester ID not provided');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Pentester ID is required']
                ];
            }

            // Fetch all tests assigned to this pentester with customer information
            $stmt = $this->pdo->prepare("
                SELECT t.id, t.test_name, t.test_description, t.test_date, t.completed,
                       u.email as customer_email, u.id as customer_id
                FROM tests t
                INNER JOIN users u ON t.customer_id = u.id
                WHERE t.pentester_id = :pentester_id
                ORDER BY t.test_date DESC, t.id DESC
            ");
            $stmt->execute(['pentester_id' => $pentester_id]);
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched ' . count($tests) . ' tests for pentester ID: ' . $pentester_id);

            return [
                'status' => 200,
                'data' => ['success' => true, 'tests' => $tests]
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
    // Get Pentester Targets
    //-----------------------------------------------------
    public function getPentesterTargets($pentester_id) {
        try {
            if (!$pentester_id) {
                Logger::write('error', 'Pentester ID not provided');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Pentester ID is required']
                ];
            }

            // Fetch all targets for all tests of specific pentester
            $stmt = $this->pdo->prepare("
                SELECT tg.id, tg.target_name, tg.target_description, tg.test_id,
                       t.test_name, u.email as customer_email
                FROM targets tg
                INNER JOIN tests t ON tg.test_id = t.id
                INNER JOIN users u ON t.customer_id = u.id
                WHERE t.pentester_id = :pentester_id
                ORDER BY t.test_date DESC, tg.id ASC
            ");
            $stmt->execute(['pentester_id' => $pentester_id]);
            $targets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched ' . count($targets) . ' targets for pentester ID: ' . $pentester_id);

            return [
                'status' => 200,
                'data' => ['success' => true, 'targets' => $targets]
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
    // Get Pentester Vulnerabilities
    //-----------------------------------------------------
    public function getPentesterVulnerabilities($pentester_id) {
        try {
            if (!$pentester_id) {
                Logger::write('error', 'Pentester ID not provided');
                return [
                    'status' => 400,
                    'data' => ['success' => false, 'error' => 'Pentester ID is required']
                ];
            }

            // Fetch vulnerabilities for all targets of all tests of specific pentester
            $stmt = $this->pdo->prepare("
                SELECT v.id, v.target_id, v.affected_entity, v.identifier, v.risk_statement, 
                       v.affected_component, v.residual_risk, v.classification, v.identified_controls, 
                       v.cvss_score, v.likelihood, v.cvssv3_code, v.location, 
                       v.vulnerabilities_description, v.reproduction_steps, v.impact, 
                       v.remediation_difficulty, v.recommendations, v.recommended_reading, 
                       v.response, v.solved, v.created_at,
                       tg.target_name as target_name,
                       t.test_name as test_name,
                       u.email as customer_email
                FROM vulnerabilities v
                INNER JOIN targets tg ON v.target_id = tg.id
                INNER JOIN tests t ON tg.test_id = t.id
                INNER JOIN users u ON t.customer_id = u.id
                WHERE t.pentester_id = :pentester_id
                ORDER BY 
                    CASE 
                        WHEN v.cvss_score >= 9.0 THEN 1
                        WHEN v.cvss_score >= 7.0 THEN 2
                        WHEN v.cvss_score >= 4.0 THEN 3
                        ELSE 4
                    END,
                    v.created_at DESC
            ");
            $stmt->execute(['pentester_id' => $pentester_id]);
            $vulnerabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched ' . count($vulnerabilities) . ' vulnerabilities for pentester ID: ' . $pentester_id);

            return [
                'status' => 200,
                'data' => ['success' => true, 'vulnerabilities' => $vulnerabilities]
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
}