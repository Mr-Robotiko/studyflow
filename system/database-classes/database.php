<?php

class Database {
    private $pdo;

    public function __construct($configPath) {
        if (!file_exists($configPath)) {
            throw new Exception("Zugangsdaten-Datei nicht gefunden.");
        }

        $csv = array_map('str_getcsv', file($configPath));
        $headers = array_map('trim', $csv[0]);
        $values = array_map('trim', $csv[1]);
        $credentials = array_combine($headers, $values);

        $host = $credentials['host'];
        $dbname = $credentials['dbname'];
        $user = $credentials['databaseUser'];
        $pass = $credentials['pass'];

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        $this->pdo = new PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection() {
        return $this->pdo;
    }
}
