<?php
// Get error info
$statusCode = $e->statusCode ?? 500;
$errorMessage = $e->getMessage() ?? 'An unexpected error occurred';

// Map status codes to messages
$statusMessages = [
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Access Forbidden',
    404 => 'Page Not Found',
    500 => 'Internal Server Error',
    503 => 'Service Unavailable'
];

$friendlyTitle = isset($statusMessages[$statusCode]) ? 
    "{$statusMessages[$statusCode]}" : 
    "Error";

// Descriptions
$descriptions = [
    400 => 'The request could not be understood or was missing required parameters.',
    401 => 'You need to log in to access this page.',
    403 => 'You don\'t have permission to access this resource.',
    404 => 'The page you\'re looking for doesn\'t exist or has been moved.',
    500 => 'Something went wrong on our end. Please try again later.',
    503 => 'The service is temporarily unavailable. Please try again later.'
];

$description = isset($descriptions[$statusCode]) ? 
    $descriptions[$statusCode] : 
    'An unexpected error occurred. Please try again later.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $statusCode ?> - <?= htmlspecialchars($friendlyTitle) ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #e74c3c;
            margin: 0;
            line-height: 1;
        }
        
        .error-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin: 1rem 0;
            font-weight: 600;
        }
        
        .error-description {
            color: #7f8c8d;
            margin: 1.5rem 0;
            line-height: 1.6;
        }
        
        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #6862ea;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #5a54d6;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #55e5a0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background-color: #4dd394;
            transform: translateY(-2px);
        }
        
        .logo {
            max-width: 200px;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 480px) {
            .error-container {
                padding: 2rem 1.5rem;
            }
            
            .error-code {
                font-size: 4rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">        
        <div class="error-code"><?= $statusCode ?></div>
        <h1 class="error-title"><?= htmlspecialchars($friendlyTitle) ?></h1>
        <p class="error-description"><?= htmlspecialchars($description) ?></p>
        
        <div class="action-buttons">
            <a href="/" class="btn btn-primary">Go Home</a>
            <button class="btn btn-secondary" onclick="window.history.back()">Go Back</button>
        </div>
    </div>
</body>
</html>