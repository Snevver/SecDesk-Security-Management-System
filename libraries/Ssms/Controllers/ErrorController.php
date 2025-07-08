<?php

declare(strict_types=1);

namespace Ssms\Controllers;

use Ssms\Exceptions\HTTPException;

class ErrorController
{
    public function __invoke(HTTPException $e): mixed
    {
        // Add an error check to see if the code is HTTP status code
        header("HTTP/1.1 {$e->statusCode} {$e->getMessage()}");
        include DIR_VIEWS . 'error.html.php';
        return 1;
    }
}