<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function send_json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

try {
    $db_file = __DIR__ . '/../../database/db.php';
    if (!file_exists($db_file)) {
        throw new Exception("Database connection file not found");
    }
    require_once $db_file;
    
    if (!isset($db) || !$db) {
        throw new Exception("Database connection failed: " . (isset($db) ? pg_last_error() : "Connection variable not set"));
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        send_json_response(['error' => 'Method not allowed'], 405);
    }
    
    $raw_data = file_get_contents('php://input');
    if (empty($raw_data)) {
        send_json_response(['error' => 'No data received'], 400);
    }
    
    $data = json_decode($raw_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        send_json_response(['error' => 'Invalid JSON: ' . json_last_error_msg()], 400);
    }
    
    if (empty($data['email']) || empty($data['password'])) {
        send_json_response(['error' => 'Email and password required'], 400);
    }
    
    $email = $data['email'];
    $password = $data['password'];
    
    $query = "SELECT id, email, password, role_id FROM users WHERE email = $1";
    $result = pg_query_params($db, $query, [$email]);
    
    if (!$result) {
        throw new Exception("Database query failed: " . pg_last_error($db));
    }
    
    if (pg_num_rows($result) === 0) {
        send_json_response(['error' => 'Invalid credentials'], 401);
    }
    
    $user = pg_fetch_assoc($result);
    
    if ($password !== $user['password']) {
        send_json_response(['error' => 'Invalid credentials'], 401);
    }
    
    // Get role name from the database
    $role_query = "SELECT name FROM roles WHERE id = $1";
    $role_result = pg_query_params($db, $role_query, [$user['role_id']]);
    $role_name = 'Unknown';
    
    if ($role_result && pg_num_rows($role_result) > 0) {
        $role_data = pg_fetch_assoc($role_result);
        $role_name = $role_data['name'];
    }
    
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['logged_in'] = true;
    $_SESSION['role_id'] = (int)$user['role_id'];
    $_SESSION['role'] = $role_name;
    
    send_json_response([
        'success' => true,
        'message' => 'Login successful',
        'email' => $user['email'],
        'redirect' => 'dashboard.html'
    ]);
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    
    send_json_response([
        'error' => 'Server error: ' . $e->getMessage(),
        'details' => 'Check server logs for more information'
    ], 500);
}
?>