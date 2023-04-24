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

    public function __construct()
    {
        $this->db = new PostTableHandler();
        $this->data = json_decode(file_get_contents("php://input"), true); // POSTされたJSONデータから配列へ変換
        $this->action = $_GET['action'] ?? $_POST['action'] ?? null;
    }

    private function filterXSS($filterData)
    {
        return htmlspecialchars($filterData, ENT_QUOTES, 'UTF-8');
    }

    public function handleRequest()
    {
        switch ($this->action) {
            case 'getData':
                $this->db->getSortedData(new sortByCreatedAtAsc());
                break;
            case 'insertData':
                $this->db->insert($this->filterXSS($this->data['title']), $this->filterXSS($this->data['content']));
                break;
            case 'updateData':
                $this->db->update($this->data['id'], $this->filterXSS($this->data['title']), $this->filterXSS($this->data['content']));
                break;
            case 'deleteData':
                $this->db->delete($this->data['id']);
                break;
            case 'sortByUpDatedAtAsc':
                $this->db->getSortedData(new sortByUpDatedAtAsc());
                break;
        }
    }
}

$api = new APIController();
$api->handleRequest();
