<?php
require_once __DIR__ . '/db.php';

/**
 * Test function to check database connection
 */
function testConn() {
    global $db;
    
    try {
        if (pg_connection_status($db) === PGSQL_CONNECTION_OK) {
            echo "Database connection is working.\n";
        } else {
            echo "Database connection failed.\n Error: " . pg_last_error($db) . "\n";
        }
    } catch (Exception $error) {
        echo "Connection error: " . $error->getMessage() . "\n";
    }
}

testConn();
?>