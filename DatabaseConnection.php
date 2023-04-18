<?php

class DatabaseConnection
{
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'fei2023';
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            echo "Error: " . $this->conn->error;
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
