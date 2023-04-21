<?php

// データベースの接続
trait DatabaseConnection
{
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'fei2023';
    private $tableName = 'posts';
    private $rows = [];

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

// クラッド操作
class PostTableHandler
{
    use DatabaseConnection;

    public function __construct()
    {
        $this->getConnection();
    }

    public function insertData($title, $content)
    {
        $sql = "INSERT INTO {$this->tableName} VALUES(DEFAULT, ?, ?,DEFAULT,DEFAULT)";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bind_param("ss", $title, $content); // パラメータをバインド
        $stmt->execute();
        echo "Data inserted successfully.";
    }

    public function updateData($todoId, $title, $content)
    {
        $sql = "UPDATE {$this->tableName} SET title = ?, content = ? WHERE ID = ?";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $todoId);
        $stmt->execute();
        echo "Data updated successfully.";
    }

    public function deleteData($todoId)
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

trait GetData
{
    public function getData($sortBy)
    {
        $sql = "SELECT * FROM {$this->tableName} order by {$sortBy} asc";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $this->rows[] = $row;
        }
        echo json_encode($this->rows); // 取得したデータをJSON形式で出力
    }
}

// 並び替えのインターフェイス
interface SortStrategy
{
    public function getSortedData();
}

// 具体的なクラス　作成日時の出力
class sortByCreatedAtAsc implements SortStrategy
{
    use DatabaseConnection;
    use GetData;

    private $sortBy = "created_at";

    public function __construct()
    {
        $this->getConnection();
    }

    public function getSortedData()
    {
        return $this->getData($this->sortBy);
    }
}

// 具体的なクラス 更新日時の出力
class sortByUpDatedAtAsc implements SortStrategy
{
    use DatabaseConnection;
    use GetData;

    private $sortBy = "updated_at";

    public function __construct()
    {
        $this->getConnection();
    }

    public function getSortedData()
    {
        return $this->getData($this->sortBy);
    }
}

class useSortBy
{
    private $sortStrategy;

    public function __construct(SortStrategy $sortStrategy)
    {
        $this->sortStrategy = $sortStrategy;
    }

    public function getSortedData()
    {
        return $this->sortStrategy->getSortedData();
    }
}
