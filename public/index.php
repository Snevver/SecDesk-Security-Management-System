<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log request information
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
file_put_contents("php://stdout", "Request received: $requestMethod $requestUri\n");

// Check if this is a direct request to index.php or the root
if ($requestUri == '/index.php' || $requestUri == '/' || $requestUri == '/SecDesk-project/public/') {
    include __DIR__ . '/index.html';
    exit;
}

// Load the router for API requests
require_once __DIR__ . '/../router/router.php';
