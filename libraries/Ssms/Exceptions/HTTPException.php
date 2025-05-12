<?php

declare(strict_types=1);
namespace Ssms\Exceptions;

class HTTPException extends \Exception
{
    readonly int $statusCode;

    public function __construct(string $message, int $statusCode = 500, string $file = "", int $line = 0)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->file = $file;
        $this->line = $line;
    }
}