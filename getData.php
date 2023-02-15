<?

// データベースに接続する

$con = mysqli_connect('localhost', 'root', '', 'fei2023');

if (!$con) {
    echo "接続失敗";
}

$sql = "select * from posts";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    echo json_encode($rows);
} else {
    echo "結果なし";
}

mysqli_close($con);
