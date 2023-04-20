<?php

require_once './DatabaseConnection.php';

// クラッド操作
class PostTableHandler
{
    use DatabaseConnection;
    private $tbName = 'posts';

    public function __construct()
    {
        $this->getConnection();
    }

    public function insertData($title, $content)
    {
        $sql = "INSERT INTO {$this->tbName} VALUES(DEFAULT, ?, ?,DEFAULT,DEFAULT)";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bind_param("ss", $title, $content); // パラメータをバインド
        $stmt->execute();
        echo "Data inserted successfully.";
    }

    public function updateData($todoId, $title, $content)
    {
        $sql = "UPDATE {$this->tbName} SET title = ?, content = ? WHERE ID = ?";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $todoId);
        $stmt->execute();
        echo "Data updated successfully.";
    }

    public function deleteData($todoId)
    {
        $sql = "DELETE FROM {$this->tbName} WHERE ID = ?";
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

// 並び替えのインターフェイス
interface SortStrategy
{
    public function getData();
}

// 具体的なクラス　作成日時の出力 
// (重複が多くて未改善)（そもそもこの設計あっているか？）
class sortByCreatedAtAsc implements SortStrategy
{
    use DatabaseConnection;

    private $tbName = 'posts';
    private $rows = [];

    public function __construct()
    {
        $this->getConnection();
    }

    public function getData()
    {

        $sql = "SELECT * FROM {$this->tbName} order by created_at asc";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $this->rows[] = $row;
        }
        echo json_encode($this->rows); // 取得したデータをJSON形式で出力
    }
}

// 具体的なクラス 更新日時の出力
class sortByUpDatedAtAsc implements SortStrategy
{
    use DatabaseConnection;
    private $tbName = 'posts';
    private $rows = [];

    public function __construct()
    {
        $this->getConnection();
    }

    public function getData()
    {
        $sql = "SELECT * FROM {$this->tbName} order by updated_at asc";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $this->rows[] = $row;
        }
        echo json_encode($this->rows); // 取得したデータをJSON形式で出力
    }
}

class useSortBy
{
    private $sortStrategy;

    public function __construct(SortStrategy $sortStrategy)
    {
        $this->sortStrategy = $sortStrategy;
    }

    public function getData()
    {
        return $this->sortStrategy->getData();
    }
}
