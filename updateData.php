<?


// データベースに接続する

$con = mysqli_connect('localhost', 'root', '', 'fei2023');

if (!$con) {
    echo "接続失敗";
}


// 获取vue发送的数据
$data = json_decode(file_get_contents("php://input"), true);


$todoId = $data['id'];
$title = $data['title'];
$content = $data['content'];

// 获取现在时间
$updatedAt = time();
$updatedAt = date('Y-m-d H:i:s', $updatedAt);

// 插入数据
$sql = "update posts set title = '$title', content = '$content', updated_at ='$updatedAt' where ID = '$todoId'";

if ($con->query($sql) === TRUE) {
    echo "データが更新されました。";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}

mysqli_close($con);
