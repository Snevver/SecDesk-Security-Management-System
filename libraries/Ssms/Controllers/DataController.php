<?php

namespace Ssms\Controllers;

use Ssms\Logger;

class DataController
{
    public function __construct(private \PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getTestData($test_id) {
        try {

            Logger::write('info', 'Fetching test data for test ID: ' . $test_id . " of type " . gettype($test_id));

            // Prepare and execute the SQL statement to fetch test data
            $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE id = :test_id");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            // Fetch the test data
            $testData = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($testData) {
                Logger::write('info', 'Fetched test data for test ID ' . $test_id . ': ' . json_encode($testData));
                return [
                    'status' => 200,
                    'data' => $testData
                ];
            } else {
                Logger::write('warning', 'No test found for test ID ' . $test_id);
                return [
                    'status' => 404,
                    'data' => ['error' => 'Test not found']
                ];
            }
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }

    public function updateTest($test_id, $title, $description) {
        try {
            Logger::write('info', 'Updating test with ID: ' . $test_id);

            // Prepare and execute the SQL statement to update the test
            $stmt = $this->pdo->prepare("UPDATE tests SET test_name = :title, test_description = :description WHERE id = :test_id");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, \PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                Logger::write('info', 'Test updated successfully for test ID ' . $test_id);
                return [
                    'status' => 200,
                    'data' => ['success' => true]
                ];
            } else {
                Logger::write('warning', 'No changes made for test ID ' . $test_id);
                return [
                    'status' => 304,
                    'data' => ['success' => false, 'message' => 'No changes made']
                ];
            }
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }
}