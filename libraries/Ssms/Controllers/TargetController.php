<?php

//======================================================================
// TARGET CONTROLLER
//======================================================================


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
        $test_id = $test_id ?? null;        // Check if the test is owned by the logged in person
        $stmt = $this->pdo->prepare("SELECT customer_id FROM tests WHERE id = :test_id");
        $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
        $stmt->execute();
        $test = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$test || $test['customer_id'] !== $user_id) {
            Logger::write('warning', 'Unauthorized access attempt to test ID ' . $test_id . ' by user ID ' . $user_id);
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

    //-----------------------------------------------------
    // Fetch all vulnerability data from the database
    //-----------------------------------------------------
    function getVulnerabilities($target_id) {
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

        // Check if the target is owned by the logged in person
        $stmt = $this->pdo->prepare("SELECT test_id FROM targets WHERE id = :target_id");
        $stmt->bindParam(':target_id', $target_id, \PDO::PARAM_INT);
        $stmt->execute();
        $target = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$target) {
            Logger::write('warning', 'Unauthorized access attempt to target ID ' . $target_id . ' by user ID ' . $user_id);
            return [
                'status' => 404,
                'data' => ['error' => 'Target not found']
            ];
        }        // Check if the test is owned by the logged in person
        $stmt = $this->pdo->prepare("SELECT customer_id FROM tests WHERE id = :test_id");
        $stmt->bindParam(':test_id', $target['test_id'], \PDO::PARAM_INT);
        $stmt->execute();
        $test = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$test || $test['customer_id'] !== $user_id) {
            Logger::write('warning', 'Unauthorized access attempt to test ID ' . $target['test_id'] . ' by user ID ' . $user_id);
            return [
                'status' => 403,
                'data' => ['error' => 'Forbidden: You do not have access to this test']
            ];
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM vulnerabilities WHERE target_id = :target_id");
            $stmt->bindParam(':target_id', $target_id, \PDO::PARAM_INT);
            $stmt->execute();
            $vulnerabilities = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            Logger::write('info', 'Fetched vulnerabilities for target ID ' . $target_id . ': ' . json_encode($vulnerabilities));

            return [
                'status' => 200,
                'data' => ['vulnerabilities' => $vulnerabilities]
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Database error']
            ];
        }
    }
}