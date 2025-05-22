<?php
require_once "system/user-classes/user.php";

class Login {
    private PDO $conn;
    private ?User $user = null;
    public string $alert = '';

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function login(string $username, string $password_plain): bool {
        $stmt = $this->conn->prepare(
            "SELECT Username, Password, Securitypassphrase, Name, Surname, Calendarfile
             FROM user
             WHERE Username = :username
             LIMIT 1"
        );
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password_plain, $row['Password'])) {
            $this->user = new User();
            $this->user->setUserName($row['Username']);
            $this->user->setPassword($row['Password']);
            $this->user->setSecurityPassphrase($row['Securitypassphrase']);
            $this->user->setName($row['Name']);
            $this->user->setSurname($row['Surname']);
            $this->user->setCalendarfile($row['Calendarfile'] ?? null);

            $_SESSION['eingeloggt'] = true;
            $_SESSION['user_data'] = [
                'username'           => $this->user->getUserName(),
                'name'               => $this->user->getName(),
                'surname'            => $this->user->getSurname(),
                'securityPassphrase' => $this->user->getSecurityPassphrase(),
                'calendarfile'       => $this->user->getCalendarfile(),
            ];

            return true;
        }

        $this->alert = 'Login fehlgeschlagen: Benutzername oder Passwort ist falsch.';
        return false;
    }
}
