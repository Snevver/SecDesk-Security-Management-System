<?php

namespace Ssms\Controllers;
use PDO;
use Ssms\Logger;

class TargetController
{
    private PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    //-----------------------------------------------------
    // Fetch all target data from the database
    //-----------------------------------------------------
    public function getTargets($test_id = null)
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Check if user_id is set in the session
        if (!isset($_SESSION['user_id'])) {
            return [
                'status' => 400,
                'data' => ['error' => 'User ID is required']
            ];
        }

        if ($test_id !== null && !is_numeric($test_id)) {
            return [
                'status' => 400,
                'data' => ['error' => 'Invalid test ID']
            ];
        }

        $user_id = (int)$_SESSION['user_id'];
        $test_id = $test_id ?? null;        
          // Check if the test is owned by the logged in person or if the user is the pentester who created the test
        $stmt = $this->pdo->prepare("SELECT customer_id, pentester_id FROM tests WHERE id = :test_id");
        $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
        $stmt->execute();
        $test = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$test || ($test['customer_id'] !== $user_id && $test['pentester_id'] !== $user_id)) {
            Logger::write('error', 'Unauthorized access attempt to test ID ' . $test_id . ' by user ID ' . $user_id);
            return [
                'status' => 403,
                'data' => ['error' => 'Forbidden: You do not have access to this test']
            ];
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM targets WHERE test_id = :test_id");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->execute();
            $targets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            Logger::write('info', 'Fetched targets for test ID ' . $test_id . ': ' . json_encode($targets));

            return [
                'status' => 200,
                'data' => ['targets' => $targets]
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Database error']
            ];
        }
    }

    /**
     * Handle role-based target fetching for API endpoint
     */
    public function handleApiTargets($test_id = null)
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) session_start();

        $test_id = $test_id ?? (isset($_GET['test_id']) ? (int)$_GET['test_id'] : null);
        $userRole = $_SESSION['role'] ?? null;
        
        Logger::write('info', 'Fetching targets for user with role: ' . $userRole . ', Test ID: ' . $test_id);
        
        try {
            if ($userRole === 'pentester') {
                // Use DataController for pentester
                $dataController = new \Ssms\Controllers\DataController($this->pdo);
                return $dataController->getTargets($test_id);
            } elseif ($userRole === 'customer') {
                // Use TargetController for customer
                return $this->getTargets($test_id);
            } else {
                return [
                    'status' => 403,
                    'data' => ['error' => 'Access denied for role: ' . $userRole]
                ];
            }
        } catch (\Exception $e) {
            Logger::write('error', 'Error in handleApiTargets: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }

    public function deleteTarget($target_id)
    {
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

        // Check if the target exists
        $stmt = $this->pdo->prepare("SELECT test_id FROM targets WHERE id = :target_id");
        $stmt->bindParam(':target_id', $target_id, \PDO::PARAM_INT);
        $stmt->execute();
        $target = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$target) {
            Logger::write('error', 'Unauthorized access attempt to target ID ' . $target_id . ' by user ID ' . $user_id);
            return [
                'status' => 404,
                'data' => ['error' => 'Target not found']
            ];
        }

        // Check if the user has access to the test
        $stmt = $this->pdo->prepare("SELECT customer_id, pentester_id FROM tests WHERE id = :test_id");
        $stmt->bindParam(':test_id', $target['test_id'], \PDO::PARAM_INT);
        $stmt->execute();
        $test = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$test || ($test['customer_id'] !== $user_id && $test['pentester_id'] !== $user_id)) {
            Logger::write('error', 'Unauthorized access attempt to test ID ' . $target['test_id'] . ' by user ID ' . $user_id);
            return [
                'status' => 403,
                'data' => ['error' => 'Forbidden: You do not have access to this test']
            ];
        }

        try {
            // first delete all vulnerabilities associated with the target
            $stmt = $this->pdo->prepare("DELETE FROM vulnerabilities WHERE target_id = :target_id");
            $stmt->bindParam(':target_id', $target_id, \PDO::PARAM_INT);
            $stmt->execute();
            Logger::write('info', 'Deleted vulnerabilities for target ID ' . $target_id . ' by user ID ' . $user_id);
            
            // Delete the target
            $stmt = $this->pdo->prepare("DELETE FROM targets WHERE id = :target_id");
            $stmt->bindParam(':target_id', $target_id, \PDO::PARAM_INT);
            $stmt->execute();

            Logger::write('info', 'Target ID ' . $target_id . ' deleted successfully by user ID ' . $user_id);

            return [
                'status' => 200,
                'data' => ['message' => 'Target deleted successfully']
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error while deleting target ID ' . $target_id . ': ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Database error']
            ];
        }
    }

    public function addTarget($test_id) {
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

        // Validate test_id
        if (!is_numeric($test_id)) {
            return [
                'status' => 400,
                'data' => ['error' => 'Invalid test ID']
            ];
        }

        // Check if the test exists and the user has access to it
        $stmt = $this->pdo->prepare("SELECT customer_id, pentester_id FROM tests WHERE id = :test_id");
        $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
        $stmt->execute();
        $test = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$test || ($test['customer_id'] !== $user_id && $test['pentester_id'] !== $user_id)) {
            Logger::write('error', 'Unauthorized access attempt to test ID ' . $test_id . ' by user ID ' . $user_id);
            return [
                'status' => 403,
                'data' => ['error' => 'Forbidden: You do not have access to this test']
            ];
        }

        try {
            // Insert a new target into the database
            $stmt = $this->pdo->prepare("INSERT INTO targets (test_id) VALUES (:test_id)");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->execute();

            Logger::write('info', 'Target added for test ID ' . $test_id . ' by user ID ' . $user_id);

            return [
                'status' => 201,
                'data' => ['message' => 'Target added successfully']
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error while adding target for test ID ' . $test_id . ': ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Database error']
            ];
        }
    }
}