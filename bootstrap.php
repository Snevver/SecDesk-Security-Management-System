<?php

declare(strict_types=1);

// Configure application
define('APP_ROOT', realpath(__DIR__) . DIRECTORY_SEPARATOR);
const DIR_PUBLIC = APP_ROOT . 'public' . DIRECTORY_SEPARATOR;
const DIR_VIEWS = APP_ROOT . 'views' . DIRECTORY_SEPARATOR;
const DIR_CONTROLLERS = APP_ROOT . 'libraries' . DIRECTORY_SEPARATOR . 'Ssms' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR;
const DIR_INCLUDES = APP_ROOT . 'includes' . DIRECTORY_SEPARATOR;
const DIR_DATABASE = APP_ROOT . 'libraries' . DIRECTORY_SEPARATOR . 'Ssms' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR;

// Require files
require_once DIR_INCLUDES . 'config.php';
require_once APP_ROOT . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once DIR_INCLUDES . 'errorHandler.php';