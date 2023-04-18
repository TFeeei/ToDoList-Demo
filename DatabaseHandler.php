<?php
require_once './DatabaseConnection.php';

class DatabaseHandler
{
    private $conn;

    public function __construct(DatabaseConnection $dbConn)
    {
        $this->conn = $dbConn->getConnection();
    }

    public function getData()
    {
        $sql = "SELECT * FROM posts";
        $stmt = $this->conn->prepare($sql);
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
        $createdAt = $this->getCurrentTime(); // データの挿入時間を取得

        $sql = "INSERT INTO posts VALUES(DEFAULT, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $title, $content, $createdAt, $createdAt); // パラメータをバインド
        $stmt->execute();
        echo "Data inserted successfully.";
    }

    public function updateData($todoId, $title, $content)
    {
        $updatedAt = $this->getCurrentTime(); // データの更新時間を取得

        $sql = "UPDATE posts SET title = ?, content = ?, updated_at = ? WHERE ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $title, $content, $updatedAt, $todoId);
        $stmt->execute();
        echo "Data updated successfully." . $updatedAt;
    }

    public function deleteData($todoId)
    {
        $sql = "DELETE FROM posts WHERE ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $todoId);
        $stmt->execute();
        echo "Data deleted successfully.";
    }

    // 現在の時間を取得する
    private function getCurrentTime()
    {
        date_default_timezone_set('Asia/Tokyo');
        $currentTime = time();
        $currentTime = date('Y-m-d H:i:s', $currentTime);
        return $currentTime;
    }

    public function __destruct()
    {
        mysqli_close($this->conn);
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
}

// XSS対策
function filterXSS($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
