<?php

/**
 * This file handles:
 * - Serving the index.html file
 * - Fetching the user's tests from the database
 */
namespace Ssms\Controllers;

use PDO;

class IndexController 
{
    // Properties
    private PDO $pdo;

    // Constructor
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Fetch the user's tests from the database
     * @return array
     */
    public function getCustomersTests() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user_id is set in the session
        if (!isset($_SESSION['user_id'])) {
            return [
                'status' => 400,
                'data' => ['error' => 'User ID is required']
            ];
        }

        $user_id = (int)$_SESSION['user_id'];

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 200,
                'data' => $tests
            ];
        } catch (\PDOException $e) {
            return [
                'status' => 500,
                'data' => ['error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    }
}
