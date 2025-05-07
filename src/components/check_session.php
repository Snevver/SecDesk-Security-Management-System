<?php
session_start();

// Set JSON content type
header('Content-Type: application/json');

// Check for active session
if (isset($_SESSION['user_id']) && isset($_SESSION['email']) && isset($_SESSION['logged_in'])) {
    echo json_encode([
        'logged_in' => true,
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role'] ?? 'unknown',
        'user_id' => $_SESSION['user_id']
    ]);
} else {
    echo json_encode([
        'logged_in' => false
    ]);
}
?>