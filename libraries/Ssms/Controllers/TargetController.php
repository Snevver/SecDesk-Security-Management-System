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
     * Fetch the targets for a specific test ID
     * @param int $test_id
     * @return array
     */
    public function getTargetsById(int $test_id) {
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
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    }
}
