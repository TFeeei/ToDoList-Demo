<?php

include './PostTableHandler.php';

$db = new PostTableHandler();
$data = json_decode(file_get_contents("php://input"), true); // POSTされたJSONデータから配列へ変換
$action = $_GET['action'] ?? $_POST['action'] ?? null;

$sortByCreatedAtAsc = new useSortBy(new sortByCreatedAtAsc());
$sortByUpDatedAtAsc = new useSortBy(new sortByUpDatedAtAsc());

switch ($action) {
    case 'getData':
        $sortByCreatedAtAsc->getData();
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
        $sortByUpDatedAtAsc->getData();
        break;
}

// XSS対策
function filterXSS($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
