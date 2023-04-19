<?php

trait DatabaseConnection
{
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'fei2023';

    private $dbConnection;

    public function getConnection()
    {
        $this->dbConnection = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->dbConnection->connect_error) {
            echo "Error: " . $this->dbConnection->error;
        }
        return $this->dbConnection;
    }

}
