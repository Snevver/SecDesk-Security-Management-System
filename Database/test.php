<?php

require_once __DIR__ . '/db.php';

/**
 * Test function to get all users from the database
 * @return array Array of user
 */
function getUsers() {
    global $db;
    
    try {
        $query = "SELECT * FROM users";
        $result = pg_query($db, $query);
        
        if (!$result) {
            throw new Exception(pg_last_error($db));
        }
        
        $users = [];
        while ($row = pg_fetch_assoc($result)) {
            $users[] = $row;
        }
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $json_output = json_encode($users, JSON_PRETTY_PRINT);
        echo "Output:\n" . $json_output . "\n";
        
        return $users;
    } catch (Exception $error) {
        echo "Error fetching users: " . $error->getMessage() . "\n";
        throw $error;
    }
}

getUsers();
?>