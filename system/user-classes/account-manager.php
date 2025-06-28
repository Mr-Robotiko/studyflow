<?php

class AccountManager {
    private User $user;
    private PDO $conn;

    public function __construct(User $user, PDO $conn) {
        $this->user = $user;
        $this->conn = $conn;
    }

    // Löscht den Account
    public function deleteAccount(): bool {
        try {
            return $this->user->deleteFromDatabase();
        } catch (Exception $e) {
            throw new Exception("Fehler beim Löschen: " . $e->getMessage());
        }
    }

    // Überarbeitet die Einstellungen des jeweiligen Accounts
    public function updateUserSettings(int $mode, int $ILT): bool {
        $stmt = $this->conn->prepare("UPDATE user SET Mode = :mode, ILT = :ilt WHERE Username = :username");
        return $stmt->execute([
            ':mode'     => $mode,
            ':ilt'      => $ILT,
            ':username' => $this->user->getUserName()
        ]);
    }
}

?>
