<?php

//======================================================================
// EMPLOYEE DASHBOARD CONTROLLER
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

}