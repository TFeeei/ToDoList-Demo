<?


// データベースに接続する

$con = mysqli_connect('localhost', 'root', '', 'fei2023');

if (!$con) {
    echo "接続失敗";
}


// 获取vue发送的数据
$data = json_decode(file_get_contents("php://input"), true);


$todoId = $data['id'];

// 删除数据
$sql = "delete from posts where ID = '$todoId'";

if ($con->query($sql) === TRUE) {
    echo "Data deleted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}

mysqli_close($con);
