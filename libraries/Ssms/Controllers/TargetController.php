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
        $test_id = $test_id ?? null;

        // Check if the test is owned by the logged in person
        $stmt = $this->pdo->prepare("SELECT user_id FROM tests WHERE id = :test_id");
        $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
        $stmt->execute();
        $test = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$test || $test['user_id'] !== $user_id) {
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
}