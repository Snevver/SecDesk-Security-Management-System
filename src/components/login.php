<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log the request details
file_put_contents("php://stdout", "Login request received\n");
file_put_contents("php://stdout", "Request method: " . $_SERVER['REQUEST_METHOD'] . "\n");

function send_json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

try {
    // Log the execution flow
    file_put_contents("php://stdout", "Starting login process\n");
    
    $raw_data = file_get_contents('php://input');
    file_put_contents("php://stdout", "Raw data: " . $raw_data . "\n");
    
    $data = json_decode($raw_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        file_put_contents("php://stderr", "JSON Error: " . json_last_error_msg() . "\n");
        send_json_response(['error' => 'Invalid JSON: ' . json_last_error_msg()], 400);
    }
    
    // Connect to the database
    $db_file = __DIR__ . '/../../database/db.php';

    // Error handling
    if (!file_exists($db_file)) {
        file_put_contents("php://stderr", "Database file not found at: " . $db_file . "\n");
        throw new Exception("Database connection file not found");
    }
    require_once $db_file;
    
    // Get PDO connection
    $pdo = getPDO();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        send_json_response(['error' => 'Method not allowed'], 405);
    }
    
    if (empty($raw_data)) {
        send_json_response(['error' => 'No data received'], 400);
    }
    
    if (empty($data['email']) || empty($data['password'])) {
        send_json_response(['error' => 'Email and password required'], 400);
    }
    
    $email = $data['email'];
    $password = $data['password'];
    
    // Using PDO for queries - verify credentials against database
    $stmt = $pdo->prepare("SELECT id, email, password, role_id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        send_json_response(['error' => 'Invalid credentials'], 401);
        exit;
    }
    
    if ($password !== $user['password']) {
        send_json_response(['error' => 'Invalid credentials'], 401);
        exit;
    }
    
    // Get role name from the database using PDO
    $role_stmt = $pdo->prepare("SELECT name FROM roles WHERE id = :role_id");
    $role_stmt->execute(['role_id' => $user['role_id']]);
    $role = $role_stmt->fetch();
    $role_name = $role ? $role['name'] : 'Unknown';
    
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
    file_put_contents("php://stderr", "Login error: " . $e->getMessage() . "\n");
    
    send_json_response([
        'error' => 'Server error: ' . $e->getMessage(),
        'details' => 'Check server logs for more information'
    ], 500);
}
?>