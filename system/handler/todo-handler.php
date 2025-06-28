<?php
class TodoHandler {
    private PDO $pdo;
    private int $userId;

    public function __construct(PDO $pdo, int $userId) {
        $this->pdo = $pdo;
        $this->userId = $userId;
    }

    // Setzen der jeweiligen ToDos as fertig -> gelöscht
    public function markAsDone(int $tid): array {
        try {
            $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM todo WHERE TID = :tid AND UserID = :uid");
            $stmtCheck->execute([':tid' => $tid, ':uid' => $this->userId]);
            if ((int)$stmtCheck->fetchColumn() === 0) {
                return ['success' => false, 'error' => 'Kein To-Do gefunden oder kein Zugriff'];
            }

            $stmt = $this->pdo->prepare("UPDATE todo SET Checked = 1 WHERE TID = :tid AND UserID = :uid");
            $stmt->execute([':tid' => $tid, ':uid' => $this->userId]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Fehler beim Update: ' . $e->getMessage()];
        }
    }

    // Speichern eines neuen Todos in die DB
    public function saveNewTodo(string $text, string $enddate): array {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO todo (UserID, TName, TEnddate, Checked)
                VALUES (:userId, :tname, :tenddate, 0)
            ");
            $stmt->execute([
                ':userId' => $this->userId,
                ':tname' => $text,
                ':tenddate' => $enddate
            ]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Rückgabe der jewiligen Todos eines Users
    public function getTodos(): array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT TID, TName, TEnddate
                FROM todo
                WHERE UserID = :userId AND Checked = 0
                ORDER BY TEnddate ASC
            ");
            $stmt->execute([':userId' => $this->userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
