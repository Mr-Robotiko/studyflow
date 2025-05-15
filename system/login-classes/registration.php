<?php
class Registration {
    private $conn;
    public $alert = "Fülle das Formular aus, um dich zu registrieren";

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function handleRegistration(array $post) {
        $username = trim($post["username"] ?? '');
        $name = trim($post["name"] ?? '');
        $surname = trim($post["surname"] ?? '');
        $password_plain = $post["password"] ?? '';
        $passwordrep = $post["passwordrep"] ?? '';
        $securitypassphrase = trim($post["securitypassphrase"] ?? '');
        $calenderfile = null;

        if (empty($username) || empty($password_plain) || empty($passwordrep) || empty($securitypassphrase) || empty($name) || empty($surname)) {
            $this->alert = "Bitte alle Felder ausfüllen.";
            return;
        }

        if ($password_plain !== $passwordrep) {
            $this->alert = "Passwörter stimmen nicht überein.";
            return;
        }

        $password = password_hash($password_plain, PASSWORD_DEFAULT);

        $check = $this->conn->prepare("SELECT Username FROM user WHERE Username = :username");
        $check->execute(["username" => $username]);

        if ($check->fetch()) {
            $this->alert = "Benutzername bereits vergeben.";
            return;
        }

        $query = $this->conn->prepare("INSERT INTO user (Username, Name, Surname, Securitypassphrase, Calendarfile, Password)
                                      VALUES (:username, :name, :surname, :securitypassphrase, :calenderfile, :password)");

        $query->bindParam(':username', $username);
        $query->bindParam(':name', $name);
        $query->bindParam(':surname', $surname);
        $query->bindParam(':securitypassphrase', $securitypassphrase);
        $query->bindParam(':calenderfile', $calenderfile);
        $query->bindParam(':password', $password);

        if ($query->execute()) {
            $this->alert = "Einfügen erfolgreich!";
        } else {
            $this->alert = "Fehler beim Einfügen: " . implode(", ", $query->errorInfo());
        }
    }
}
