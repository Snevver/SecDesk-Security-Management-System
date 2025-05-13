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
            Logger::write('error', 'Test ID is not provided in the request');
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

            Logger::write('info', 'Fetched targets for test ID ' . $test_id . ': ' . json_encode($targets));
            return [
                'status' => 200,
                'data' => ['success' => true, 'targets' => $targets]
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    } 
}
