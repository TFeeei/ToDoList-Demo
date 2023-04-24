<?php

// データベースの接続
trait DatabaseConnection
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $password = DB_PWD;
    private $databaseName = DB_NAME;
    private $tableName = TB_NAME;
    private $jsonData = [];

    private $dbConnection;

    public function getConnection()
    {
        try {
            $this->dbConnection = new mysqli($this->host, $this->user, $this->password, $this->databaseName);
            if ($this->dbConnection->connect_error) {
                echo "Error: " . $this->dbConnection->error;
            }
            return $this->dbConnection;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

}
