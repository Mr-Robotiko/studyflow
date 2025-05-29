<?php
require_once "system/user-classes/user.php";

class Login {
    private PDO $conn;
    private ?User $user = null;
    public string $alert = '';
    public string $popupTitle = '';

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function login(string $username, string $password_plain): bool {
        if (!preg_match('/^[a-zA-Z0-9_-]{1,50}$/', $username)) {
            $this->popupTitle = "Ungültiger Benutzername";
            $this->alert = "Nur Buchstaben, Zahlen, Unterstrich (_) und Bindestrich (-) erlaubt (max. 50 Zeichen).";
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9_-]{5,50}$/', $password_plain)) {
            $this->popupTitle = "Ungültiges Passwort";
            $this->alert = "Das Passwort muss zwischen 5 und 50 Zeichen lang sein und darf nur Buchstaben, Zahlen, _ und - enthalten.";
            return false;
        }

        $stmt = $this->conn->prepare(
            "SELECT Username, Password, Securitypassphrase, Name, Surname
             FROM user
             WHERE Username = :username
             LIMIT 1"
        );

        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password_plain, $row['Password'])) {
            $this->user = new User();
            $this->user->setUserName($row['Username']);
            $this->user->setPassword($row['Password']);
            $this->user->setSecurityPassphrase($row['Securitypassphrase']);
            $this->user->setName($row['Name']);
            $this->user->setSurname($row['Surname']);

            $_SESSION['eingeloggt'] = true;
            $_SESSION['user_data'] = [
                'username'           => $this->user->getUserName(),
                'name'               => $this->user->getName(),
                'surname'            => $this->user->getSurname(),
                'securityPassphrase' => $this->user->getSecurityPassphrase(),
            ];

            return true;
        }

        $this->popupTitle = "Login fehlgeschlagen";
        $this->alert = "Benutzername oder Passwort ist falsch.";
        return false;
    }
}
