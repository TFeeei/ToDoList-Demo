<?php

trait GetData
{
    public function getData($sortBy)
    {
        $sql = "SELECT * FROM {$this->tableName} order by {$sortBy} asc";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $this->jsonData[] = $row;
        }
        echo json_encode($this->jsonData); // 取得したデータをJSON形式で出力
    }
}

// 並び替えのインターフェイス
interface SortStrategy
{
    public function getSortedData();
}

// 具体的なクラス　作成日時の出力
class sortByCreatedAtAsc implements SortStrategy
{
    use DatabaseConnection;
    use GetData;

    private static $sortBy = "created_at";

    public function __construct()
    {
        $this->getConnection();
    }

    public function getSortedData()
    {
        return $this->getData(self::$sortBy);
    }
}

// 具体的なクラス 更新日時の出力
class sortByUpDatedAtAsc implements SortStrategy
{
    use DatabaseConnection;
    use GetData;

    private static $sortBy = "updated_at";

    public function __construct()
    {
        $this->getConnection();
    }

    public function getSortedData()
    {
        return $this->getData(self::$sortBy);
    }
}
