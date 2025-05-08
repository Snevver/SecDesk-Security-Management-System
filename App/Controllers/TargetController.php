<?php

/**
 * This file handles:
 * - Fetching the given tests targets from the database
 */
namespace App\Controllers\TargetController;

use PDO;

class TargetController 
{
    // Properties
    private PDO $pdo;

    // Constructor
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Fetch the tests targets from the database
     * @return array
     */
    public function getTargets() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if test_id is provided
        if (!isset($_GET['id'])) {
            return [
                'status' => 400,
                'data' => ['success' => false, 'error' => 'Test ID is required']
            ];
        }

        $test_id = (int)$_GET['id'];

        // Fetch the tests targets from the database
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM targets WHERE test_id = :test_id");
            $stmt->bindParam(':test_id', $test_id, PDO::PARAM_INT);
            $stmt->execute();
            $targets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 200,
                'data' => ['success' => true, 'targets' => $targets]
            ];
        } catch (\PDOException $e) {
            return [
                'status' => 500,
                'data' => ['error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    } 
}
