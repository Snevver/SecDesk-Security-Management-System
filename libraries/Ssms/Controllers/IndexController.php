<?php

//======================================================================
// CUSTOMER DASHBOARD LOGIC
//======================================================================

namespace Ssms\Controllers;

use PDO;
use Ssms\Logger;

class IndexController 
{
    private PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    //-----------------------------------------------------
    // Fetch User Tests From Database
    //-----------------------------------------------------
    public function getCustomersTests() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user id is set in the session
        if (!isset($_SESSION['user_id'])) {
            Logger::write('error', 'User ID is not set in the session');
            return [
                'status' => 400,
                'data' => ['error' => 'User ID is required']
            ];
        }

        $user_id = (int)$_SESSION['user_id'];

        try {
            // Get test data
            $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched tests for user ID ' . $user_id . ': ' . json_encode($tests));

            return [
                'status' => 200,
                'data' => $tests
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
