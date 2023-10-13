<?php
declare(strict_types=1);

class RecordsStorage {
    private $db;
    private $databasePath = 'data/db.sqlite';

    public function __construct() {
        if (!file_exists($this->databasePath)) {
            $this->db = new SQLite3($this->databasePath);
            $this->createTable();
        } else {
            $this->db = new SQLite3($this->databasePath);
        }
    }

    private function createTable() {
        $query = "CREATE TABLE records (
            id INTEGER PRIMARY KEY,
            creation DATE,
            version INTEGER,
            prev_version_id INTEGER,
            record TEXT
        )";
        $this->db->exec($query);
    }

    public function getLatestRecords($limit = 10) {
        $query = "SELECT * FROM records ORDER BY id DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);

        $result = $stmt->execute();
        $records = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $records[] = $row;
        }

        return $records;
    }

    public function __destruct() {
        $this->db->close();
    }
}