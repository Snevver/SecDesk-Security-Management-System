<?php

namespace App\Controllers\IndexController;

class IndexController 
{
    // Properties
    private \PDO $pdo;

    // Constructor
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Serve the index.html file
     * @return array
     */
    public function index()
    {
        // Serve the index.html file
        $indexPath = __DIR__ . '/../../public/index.html';
        
        if (file_exists($indexPath)) {
            $content = file_get_contents($indexPath);
            return [
                'status' => 200,
                'data' => ['content' => $content, 'type' => 'html']
            ];
        } else {
            return [
                'status' => 500,
                'data' => ['error' => 'Index file not found']
            ];
        }
    }
}
