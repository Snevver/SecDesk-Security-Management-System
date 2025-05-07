<?php
session_start();

// Initialize the response array
$response = [];

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $response['logged_in'] = true;
    $response['email'] = $_SESSION['email'];
    $response['role'] = $_SESSION['role'];
    $response['role_id'] = $_SESSION['role_id'];
} else {
    $response['logged_in'] = false;
    $response['message'] = 'User not logged in';
}

header('Content-Type: application/json');
echo json_encode($response);
exit;