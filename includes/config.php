<?php

// Configure session with enhanced settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);

session_save_path(sys_get_temp_dir());
error_reporting(E_ALL);

session_start();

