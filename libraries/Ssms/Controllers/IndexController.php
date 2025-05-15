<?php

namespace Ssms\Controllers;

use PDO;

class IndexController 
{
    private PDO $pdo;

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

            Logger::write('info', 'Fetched tests for user ID ' . $user_id . ': ' . json_encode($tests));

            // Get target data for each test
            foreach ($tests as &$test) {
                $stmt = $this->pdo->prepare("SELECT * FROM targets WHERE test_id = :test_id");
                $stmt->bindParam(':test_id', $test['id'], PDO::PARAM_INT);
                $stmt->execute();
                $targets = $stmt->fetchAll(PDO::FETCH_ASSOC);

                Logger::write('info', 'Fetched targets for test ID ' . $test['id'] . ': ' . json_encode($targets));

                // Add targets to the test data
                $test['targets'] = $targets;
            }

            // Return the tests
            return [
                'success' => true,
                'status' => 200,
                'data' => ['tests' => $tests]
            ];
            
        } catch (\PDOException $e) {
            return [
                'status' => 500,
                'data' => ['error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    }
}
