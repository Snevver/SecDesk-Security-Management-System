<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configure session
ini_set('session.gc_maxlifetime', 3600);
session_save_path(sys_get_temp_dir());
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

// Log request information
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
file_put_contents("php://stdout", "Request received: $requestMethod $requestUri\n");

// Check if this is a direct request to index.php or the root
if ($requestUri == '/index.php' || $requestUri == '/' || $requestUri == '/SecDesk-Security-Management-System/public/') {
    include __DIR__ . '/index.html';
    exit;
}

// Load the router for API requests
require_once __DIR__ . '/../router/router.php';
