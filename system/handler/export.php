<?php
require_once '../database-classes/database.php';

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="benutzer.json"');

try {
    // Erstelle eine neue Instanz der Database-Klasse mit dem Pfad zur Konfigurationsdatei
    $db = new Database("../../config/configuration.csv");
    $conn = $db->getConnection();

    $stmt = $conn->query("SELECT Username, Name, Surname, Admin FROM user");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
