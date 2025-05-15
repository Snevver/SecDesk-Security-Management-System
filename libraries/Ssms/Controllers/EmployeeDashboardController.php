<?php

//======================================================================
// EMPLOYEE DASHBOARD LOGIC
//======================================================================

namespace Ssms\Controllers;

use Ssms\Logger;

class EmployeeDashboardController
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    //-----------------------------------------------------
    // Fetch All Customer Data From Database
    //-----------------------------------------------------
    public function getCustomers() {
        try {       
            // Fetch all customers from the database
            $stmt = $this->pdo->prepare("SELECT id, email FROM users where role_id = 1");
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Logger::write('info', 'Fetched customers: ' . json_encode($users));

            return [
                'status' => 200,
                'data' => ['success' => true, 'users' => $users]
            ];
        } catch (\PDOException $e) { 
            Logger::write('error', 'Database error: ' . $e->getMessage());       
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'Database error: ' . $e->getMessage()]
            ];
        } catch (\Exception $e) {   
            Logger::write('error', 'System error: ' . $e->getMessage());     
            return [
                'status' => 500,
                'data' => ['success' => false, 'error' => 'System error: ' . $e->getMessage()]
            ];
        }
    }
}