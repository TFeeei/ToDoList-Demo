<?php
require_once './DatabaseConnection.php';

class DatabaseHandler
{
    use DatabaseConnection;
    private $tbName = 'posts';

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

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows); // 取得したデータをJSON形式で出力
        } else {
            die("No results found.");
        }
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

    public function sortByUpDatedAtAsc()
    {
        $sql = "SELECT * FROM {$this->tbName} order by updated_at asc";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows); // 取得したデータをJSON形式で出力
        } else {
            die("No results found.");
        }
    }

    public function __destruct()
    {
        mysqli_close($this->dbConnection);
    }

}

$db = new DatabaseHandler();
$data = json_decode(file_get_contents("php://input"), true); // POSTされたJSONデータから配列へ変換
$action = $_GET['action'] ?? $_POST['action'] ?? null;

switch ($action) {
    case 'getData':
        $db->getData();
        break;
    case 'insertData':
        $db->insertData(filterXSS($data['title']), filterXSS($data['content']));
        break;
    case 'updateData':
        $db->updateData($data['id'], filterXSS($data['title']), filterXSS($data['content']));
        break;
    case 'deleteData':
        $db->deleteData($data['id']);
        break;
    case 'sortByUpDatedAtAsc':
        $db->sortByUpDatedAtAsc();
        break;
}

// XSS対策
function filterXSS($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
