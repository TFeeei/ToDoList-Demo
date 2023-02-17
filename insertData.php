<?


// データベースに接続する

$con = mysqli_connect('localhost', 'root', '', 'fei2023');

if (!$con) {
    echo "接続失敗";
}


// 获取vue发送的数据
$data = json_decode(file_get_contents("php://input"), true);


$title = $data['title'];
$content = $data['content'];

// 現在の時間を獲得する
$createdAt = time();
$createdAt = date('Y-m-d H:i:s', $createdAt);

// 插入数据
$sql = "insert into posts values(default, '$title','$content', '$createdAt','$createdAt')";

if ($con->query($sql) === TRUE) {
    echo "Data inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}

mysqli_close($con);
