<?php

namespace App\Controllers\AdminDashboardController;

class AdminDashboardController
{
    // Properties
    private \PDO $pdo;

    // Constructor
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Function to get all users
     * @return array
     */
    public function getCustomers() {
        try {
            // Fetch all users (only customers) from the database
            $stmt = $this->pdo->prepare("SELECT id, email FROM users where role_id = 1");
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return [
                'status' => 200,
                'data' => ['success' => true, 'users' => $users]
            ];
        } catch (\PDOException $e) {
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        }
    }

}