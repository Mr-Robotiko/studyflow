<?php

require_once __DIR__ . '/../database-classes/database.php';

class PasswordReset {
    private $pdo;

    public function __construct($configPath) {
        $db = new Database($configPath);
        $this->pdo = $db->getConnection();
    }

    public function reset($username, $securitypassphrase, $newPassword, $confirmPassword) {
        $username = trim($username);
        $securitypassphrase = trim($_POST['securitypassphrase'] ?? '');

        if (empty($username) || empty($securitypassphrase) || empty($newPassword) || empty($confirmPassword)) {
            return "Bitte alle Felder ausfüllen.";
        }

        if ($newPassword !== $confirmPassword) {
            return "Die Passwörter stimmen nicht überein.";
        }

        $stmt = $this->pdo->prepare("SELECT Securitypassphrase FROM user WHERE Username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return "Benutzer existiert nicht.";
        }

        if (!password_verify($securitypassphrase, $user['Securitypassphrase'])) {
            return "Sicherheitsantwort ist falsch.";
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $this->pdo->prepare("UPDATE user SET Password = :password WHERE Username = :username");

        if ($update->execute(['password' => $hashedPassword, 'username' => $username])) {
            return "success";
        }

        return "Fehler beim Speichern des neuen Passworts.";
    }
}
