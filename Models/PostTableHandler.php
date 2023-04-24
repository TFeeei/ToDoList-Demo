<?php

// クラッド操作
class PostTableHandler
{
    use DatabaseConnection;
    private $sortStrategy;

    public function __construct()
    {
        $this->getConnection();
    }

    public function getSortedData(SortStrategy $sortStrategy)
    {
        $this->sortStrategy = $sortStrategy;
        return $this->sortStrategy->getSortedData();
    }

    public function insert($title, $content)
    {
        $sql = "INSERT INTO {$this->tableName} VALUES(DEFAULT, ?, ?,DEFAULT,DEFAULT)";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bind_param("ss", $title, $content); // パラメータをバインド
        $stmt->execute();
        echo "Data inserted successfully.";
    }

    public function update($todoId, $title, $content)
    {
        $sql = "UPDATE {$this->tableName} SET title = ?, content = ? WHERE ID = ?";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $todoId);
        $stmt->execute();
        echo "Data updated successfully.";
    }

    public function delete($todoId)
    {
        $sql = "DELETE FROM {$this->tableName} WHERE ID = ?";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bind_param("i", $todoId);
        $stmt->execute();
        echo "Data deleted successfully.";
    }

    public function __destruct()
    {
        mysqli_close($this->dbConnection);
    }

}
