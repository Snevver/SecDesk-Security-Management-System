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
                Logger::write('warning', 'Unauthorized test completion update attempt for test ID ' . $test_id . ' by user ID ' . $user_id);
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
    public function createTest() {

    }
}