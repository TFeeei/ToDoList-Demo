<?php
require_once './Models/databaseConfig.php';
require_once './Models/DatabaseConnection.php';
require_once './Models/PostTableHandler.php';
require_once './Models/SortStrategy.php';

class APIController
{
    private $db;
    private $data;
    private $action;
    private $id;
    private $title;
    private $content;

    public function __construct()
    {
        $this->db = new PostTableHandler();
        $this->data = json_decode(file_get_contents("php://input"), true); // POSTされたJSONデータから配列へ変換
        $this->action = $_GET['action'] ?? $_POST['action'] ?? null;

        $this->id = $this->data['id'] ?? null;
        $this->title = $this->data['title'] ?? null;
        $this->content = $this->data['content'] ?? null;

    }

    private function filterXSS($filterData)
    {
        return htmlspecialchars($filterData, ENT_QUOTES, 'UTF-8');
    }

    private function validateData()
    {
        return isset($this->title, $this->content) && strlen($this->title) < 30;
    }

    public function handleRequest()
    {
        switch ($this->action) {
            case 'getData':
                $this->db->getSortedData(new sortByCreatedAtAsc());
                break;
            case 'insertData':
                if ($this->validateData()) {
                    $this->db->insert($this->filterXSS($this->title), $this->filterXSS($this->content));
                } else {
                    echo json_encode(['message' => '入力内容を確認してください。']);
                }
                break;
            case 'updateData':
                if ($this->validateData()) {
                    $this->db->update($this->id, $this->filterXSS($this->title), $this->filterXSS($this->content));
                } else {
                    echo json_encode(['message' => '入力内容を確認してください。']);
                }
                break;
            case 'deleteData':
                $this->db->delete($this->id);
                break;
            case 'sortByUpDatedAtAsc':
                $this->db->getSortedData(new sortByUpDatedAtAsc());
                break;
            default:
                echo json_encode(['message' => '不明エラー']);
                break;
        }
    }
}

$api = new APIController();
$api->handleRequest();
