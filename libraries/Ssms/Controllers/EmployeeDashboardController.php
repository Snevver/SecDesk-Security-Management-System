<?php

//======================================================================
// EMPLOYEE DASHBOARD CONTROLLER
//======================================================================

namespace Ssms\Controllers;

use Ssms\Logger;

class EmployeeDashboardController
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }    
    
    /**
     * Fetches all tests assigned to the pentester from the database.
     * 
     * @return array An associative array containing the status and data of the tests.
     */
    public function getEmployeeTests() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Check if user_id is set in the session
        if (!isset($_SESSION['user_id'])) {
            return [
                'status' => 400,
                'data' => ['error' => 'User ID is required']
            ];
        }

        $user_id = (int)$_SESSION['user_id'];

        try {
            // Fetch all tests assigned to this pentester
            $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE pentester_id = :user_id ORDER BY test_date DESC");
            $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $stmt->execute();
            $tests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched tests for pentester ID ' . $user_id . ': ' . json_encode($tests));

            // Get target data for each test
            foreach ($tests as $key => $test) {
                $stmt = $this->pdo->prepare("SELECT * FROM targets WHERE test_id = :test_id");
                $stmt->bindParam(':test_id', $test['id'], \PDO::PARAM_INT);
                $stmt->execute();
                $targets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                Logger::write('info', 'Fetched targets for test ID ' . $test['id'] . ': ' . json_encode($targets));

                // Add targets to the test data using array key
                $tests[$key]['targets'] = $targets;
            }            
            
            // Separate tests into completed and non-completed arrays
            $completedTests = [];
            $nonCompletedTests = [];
            
            foreach ($tests as $test) {
                if ($test['completed']) {
                    $completedTests[] = $test;
                } else {
                    $nonCompletedTests[] = $test;
                }
            }

            // Return the tests separated by completion status
            return [
                'success' => true,
                'status' => 200,
                'data' => [
                    'completedTests' => $completedTests,
                    'nonCompletedTests' => $nonCompletedTests
                ]
            ];
            
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    }

    //-----------------------------------------------------
    // Update test completion status
    //-----------------------------------------------------
    public function updateTestCompletion() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Check if user_id is set in the session
        if (!isset($_SESSION['user_id'])) {
            return [
                'status' => 400,
                'data' => ['error' => 'User ID is required']
            ];
        }

        // Get the request body
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);

        // Validate the request body
        if (!isset($data['test_id']) || !isset($data['completed'])) {
            return [
                'status' => 400,
                'data' => ['error' => 'Test ID and completion status are required']
            ];
        }

        $test_id = (int)$data['test_id'];
        $completed = (bool)$data['completed'];
        $user_id = (int)$_SESSION['user_id'];

        try {
            // Verify that the test belongs to this pentester
            $stmt = $this->pdo->prepare("SELECT pentester_id FROM tests WHERE id = :test_id");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->execute();
            $test = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$test || $test['pentester_id'] !== $user_id) {
                Logger::write('error', 'Unauthorized test completion update attempt for test ID ' . $test_id . ' by user ID ' . $user_id);
                return [
                    'status' => 403,
                    'data' => ['error' => 'Forbidden: You do not have access to this test']
                ];
            }
            
            $stmt = $this->pdo->prepare("UPDATE tests SET completed = :completed WHERE id = :test_id");
            $stmt->bindParam(':completed', $completed, \PDO::PARAM_BOOL);
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->execute();

            Logger::write('info', 'Updated test completion status for test ID ' . $test_id . ' to ' . ($completed ? 'completed' : 'in progress'));

            return [
                'status' => 200,
                'data' => ['success' => true, 'message' => 'Test completion status updated successfully']
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    }

    //-----------------------------------------------------
    // Create a new test
    //-----------------------------------------------------
    public function createTest($customerID) {
        $nullVar = null;

        $stmt = $this->pdo->prepare("INSERT INTO tests (test_name, test_description, customer_id, pentester_id) VALUES (:test_name, :test_description, :customer_id, :pentester_id)");
        $stmt->bindParam(':test_name', $nullVar, \PDO::PARAM_STR);
        $stmt->bindParam(':test_description', $nullVar, \PDO::PARAM_STR);
        $stmt->bindParam(':customer_id', $customerID, \PDO::PARAM_INT);
        $stmt->bindParam(':pentester_id', $_SESSION['user_id'], \PDO::PARAM_INT);
        $stmt->execute();
        
        $newTestID = $this->pdo->lastInsertId();

        Logger::write('info', 'Test created successfully for customer ID: ' . $customerID . ' with new test ID: ' . $newTestID);

        return [
                'status' => 201,
                'data' => ['success' => true, 'new_test_id' => $newTestID]
            ];
    }

    //-----------------------------------------------------
    // Delete a vulnerability
    //-----------------------------------------------------
    public function deleteVulnerability($vulnerabilityID) {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Check if user_id is set in the session
        if (!isset($_SESSION['user_id'])) {
            return [
                'status' => 400,
                'data' => ['error' => 'User ID is required']
            ];
        }

        $user_id = (int)$_SESSION['user_id'];

        try {
            // Verify that the vulnerability belongs to a target owned by this pentester
            $stmt = $this->pdo->prepare("SELECT target_id FROM vulnerabilities WHERE id = :vulnerability_id");
            $stmt->bindParam(':vulnerability_id', $vulnerabilityID, \PDO::PARAM_INT);
            $stmt->execute();
            $vulnerability = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$vulnerability) {
                Logger::write('error', 'Unauthorized vulnerability deletion attempt for vulnerability ID ' . $vulnerabilityID . ' by user ID ' . $user_id);
                return [
                    'status' => 404,
                    'data' => ['error' => 'Vulnerability not found']
                ];
            }

            // Check if the target belongs to a test owned by this pentester
            $stmt = $this->pdo->prepare("SELECT test_id FROM targets WHERE id = :target_id");
            $stmt->bindParam(':target_id', $vulnerability['target_id'], \PDO::PARAM_INT);
            $stmt->execute();
            $target = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$target) {
                Logger::write('error', 'Unauthorized target deletion attempt for target ID ' . $vulnerability['target_id'] . ' by user ID ' . $user_id);
                return [
                    'status' => 404,
                    'data' => ['error' => 'Target not found']
                ];
            }

            // Check if the test belongs to this pentester
            $stmt = $this->pdo->prepare("SELECT pentester_id FROM tests WHERE id = :test_id");
            $stmt->bindParam(':test_id', $target['test_id'], \PDO::PARAM_INT);
            $stmt->execute();
            $test = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$test || $test['pentester_id'] !== $user_id) {
                Logger::write('error', 'Unauthorized test deletion attempt for test ID ' . $target['test_id'] . ' by user ID ' . $user_id);
                return [
                    'status' => 403,
                    'data' => ['error' => 'Forbidden: You do not have access to this test']
                ];
            }
            
            // Delete the vulnerability
            $stmt = $this->pdo->prepare("DELETE FROM vulnerabilities WHERE id = :vulnerability_id");
            $stmt->bindParam(':vulnerability_id', $vulnerabilityID, \PDO::PARAM_INT);
            $stmt->execute();
            Logger::write('info', 'Vulnerability ID ' . $vulnerabilityID . ' deleted successfully by user ID ' . $user_id);

            // Send back to the edit page
            header('Location: /edit?test_id=' . $target['test_id']);
            
            return [
                'status' => 200,
                'data' => ['success' => true, 'message' => 'Vulnerability deleted successfully']
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    }

    //-----------------------------------------------------
    // Delete a test
    //-----------------------------------------------------
    public function deleteTest($testID) {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Check if user_id is set in the session
        if (!isset($_SESSION['user_id'])) {
            return [
                'status' => 400,
                'data' => ['error' => 'User ID is required']
            ];
        }

        $user_id = (int)$_SESSION['user_id'];

        try {
            // Verify that the test belongs to this pentester
            $stmt = $this->pdo->prepare("SELECT pentester_id FROM tests WHERE id = :test_id");
            $stmt->bindParam(':test_id', $testID, \PDO::PARAM_INT);
            $stmt->execute();
            $test = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$test || $test['pentester_id'] !== $user_id) {
                Logger::write('error', 'Unauthorized test deletion attempt for test ID ' . $testID . ' by user ID ' . $user_id);
                return [
                    'status' => 403,
                    'data' => ['error' => 'Forbidden: You do not have access to this test']
                ];
            }

            // Delete the test
            $stmt = $this->pdo->prepare("DELETE FROM tests WHERE id = :test_id");
            $stmt->bindParam(':test_id', $testID, \PDO::PARAM_INT);
            $stmt->execute();
            Logger::write('info', 'Test ID ' . $testID . ' deleted successfully by user ID ' . $user_id);

            // Redirect to the employee dashboard
            header('Location: /employee-dashboard');
            
            return [
                'status' => 200,
                'data' => ['success' => true, 'message' => 'Test deleted successfully']
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    }
}