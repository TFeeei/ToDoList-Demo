<?php
class DatabaseHandler
{
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'fei2023';
    private $conn;

    function __construct()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            echo "Error: " . $this->conn->error;
        }
    }

    function getData()
    {
        $sql = "select * from posts";
        $result = mysqli_query($this->conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            echo "結果なし";
        }
    }

    function insertData($title, $content)
    {
        $createdAt = $this->getCurrentTime();
        $sql = "insert into posts values(default, '$title','$content', '$createdAt','$createdAt')";
        $this->executeSql($this->conn, $sql, "Data inserted successfully");
    }

    function updateData($todoId, $title, $content)
    {
        $updatedAt = $this->getCurrentTime();
        $sql = "update posts set title = '$title', content = '$content', updated_at ='$updatedAt' where ID = '$todoId'";
        $this->executeSql($this->conn, $sql, "Data updated successfully");
    }

    function deleteData($todoId)
    {
        $sql = "delete from posts where ID = '$todoId'";
        $this->executeSql($this->conn, $sql, "Data deleted successfully");
    }

    // 現在の時間を獲得する
    function getCurrentTime()
    {
        $currentTime = time();
        $currentTime = date('Y-m-d H:i:s', $currentTime);
        return $currentTime;
    }

    // SQlを実行する
    function executeSql($conn, $sql, $successMsg)
    {
        if ($conn->query($sql) === TRUE) {
            echo $successMsg;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    function __destruct()
    {
        mysqli_close($this->conn);
    }
}


$db = new DatabaseHandler();
$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? $_POST['action'] ?? null;


switch ($action) {
    case 'getData':
        $db->getData();
        break;
    case 'insertData':
        $db->insertData($data['title'], $data['content']);
        break;
    case 'updateData':
        $db->updateData($data['id'], $data['title'], $data['content']);
        break;
    case 'deleteData':
        $db->deleteData($data['id']);
        break;
}
