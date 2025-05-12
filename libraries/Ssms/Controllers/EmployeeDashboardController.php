<?php

/**
 * This file handles:
 * - Fetching all users from the database
 */

namespace Ssms\Controllers;

class EmployeeDashboardController
{
    // Properties
    private \PDO $pdo;

    // Constructor
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Function to get all customers
     * @return array
     */
    public function getCustomers() {
    try {       
        // Fetch all users (only customers) from the database
        $stmt = $this->pdo->prepare("SELECT id, email FROM users where role_id = 1");
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Log the result
        file_put_contents("php://stdout", "Query returned " . count($users) . " users\n");

        return [
            'status' => 200,
            'data' => ['success' => true, 'users' => $users]
        ];
    } catch (\PDOException $e) {
        // Log the full error with SQL state
        file_put_contents("php://stderr", "PDO Error in getCustomers: " . $e->getMessage() . "\n");
        file_put_contents("php://stderr", "SQL State: " . $e->getCode() . "\n");
        file_put_contents("php://stderr", "Trace: " . $e->getTraceAsString() . "\n");
        
        return [
            'status' => 500,
            'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
        ];
    } catch (\Exception $e) {
        // Catch any other exceptions
        file_put_contents("php://stderr", "General Error in getCustomers: " . $e->getMessage() . "\n");
        
        return [
            'status' => 500,
            'data' => ['success' => false, 'error' => 'System error: ' . $e->getMessage()]
        ];
    }
}
}