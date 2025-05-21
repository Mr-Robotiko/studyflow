<?php
require_once "system/user-classes/user.php";

class Login {
    private PDO $conn;
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
            $user = new User();
            $user->setUserName($row['Username']);
            $user->setPassword($row['Password']);
            $user->setSecurityPassphrase($row['Securitypassphrase']);
            $user->setName($row['Name']);
            $user->setSurname($row['Surname']);
            $user->setCalendarfile($row['Calendarfile'] ?? null);

            $_SESSION['eingeloggt'] = true;
            $_SESSION['user_data']  = [
                'username'           => $user->getUserName(),
                'name'               => $user->getName(),
                'surname'            => $user->getSurname(),
                'securityPassphrase' => $user->getSecurityPassphrase(),
                'calendarfile'       => $user->getCalendarfile(),
            ];

            return true;
        }

        $this->alert = 'Login fehlgeschlagen: Benutzername oder Passwort ist falsch.';
        return false;
    }
}
