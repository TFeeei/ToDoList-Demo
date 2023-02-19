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

    public function getData()
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
        // 現在の時間を獲得する
        $createdAt = time();
        $createdAt = date('Y-m-d H:i:s', $createdAt);

        $sql = "insert into posts values(default, '$title','$content', '$createdAt','$createdAt')";

        if ($this->conn->query($sql) === TRUE) {
            echo "Data inserted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
    }

    function updateData($todoId, $title, $content)
    {
        // 現在の時間を獲得する
        $updatedAt = time();
        $updatedAt = date('Y-m-d H:i:s', $updatedAt);
        $sql = "update posts set title = '$title', content = '$content', updated_at ='$updatedAt' where ID = '$todoId'";

        if ($this->conn->query($sql) === TRUE) {
            echo "Data updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
    }


    function deleteData($todoId)
    {
        $sql = "delete from posts where ID = '$todoId'";

        if ($this->conn->query($sql) === TRUE) {
            echo "Data deleted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
    }

    function __destruct()
    {
        mysqli_close($this->conn);
    }
}


$db = new DatabaseHandler();
$data = json_decode(file_get_contents("php://input"), true);



// if (isset($_GET['action'])) {
//     $action = $_GET['action'];
// } elseif (isset($_POST['action'])) {
//     $action = $_POST['action'];
// }
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
