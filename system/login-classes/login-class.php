<?php

class Login {
    private $conn;
    public $alert = "";

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function login(string $username, string $password): bool {
        if (empty($username) || empty($password)) {
            $this->alert = "Bitte alle Felder ausfüllen.";
            return false;
        }

        $query = $this->conn->prepare("SELECT * FROM user WHERE Username = :username");
        $query->execute(["username" => $username]);
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['Password'])) {
            require_once __DIR__ . '/../user-classes/user.php';

            $user = new User();
            $user->setUserName($row['Username']);
            $user->setPassword($row['Password']);
            $user->setSecurityPassphrase($row['Securitypassphrase']);
            $user->setName($row['Name']);
            $user->setSurname($row['Surname']);
            $user->setCalendarfile($row['Calendarfile'] ?? null);

            $_SESSION['user_data'] = [
                'username' => $user->getUserName(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'securityPassphrase' => $user->getSecurityPassphrase(),
                'calendarfile' => $user->getCalendarfile()
            ];

            $_SESSION['eingeloggt'] = true;
            $_SESSION['LAST_ACTIVITY'] = time();

            return true;
        }

        $this->alert = "Login fehlgeschlagen.";
        return false;
    }
}
