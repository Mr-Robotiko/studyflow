<?php

class Database {
    private $host;
    private $dbname;
    private $user;
    private $pass;
    private $conn;

    public function __construct($credentialsPath) {
        if (!file_exists($credentialsPath)) {
            throw new Exception("Zugangsdaten-Datei nicht gefunden.");
        }

        $csv = array_map('str_getcsv', file($credentialsPath));
        $headers = array_map('trim', $csv[0]);
        $values = array_map('trim', $csv[1]);
        $data = array_combine($headers, $values);

        $this->host = $data['host'];
        $this->dbname = $data['dbname'];
        $this->user = $data['databaseUser'];
        $this->pass = $data['pass'];
    }

    public function connect() {
        if (!$this->conn) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $this->conn;
    }
}
