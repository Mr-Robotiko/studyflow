<?php
class Registration {
    private $conn;
    public $alert = "Fülle das Formular aus, um dich zu registrieren";
    public $popupTitle = "Fehler";

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function handleRegistration(array $post) {
        $username = trim($post["username"] ?? '');
        $name = trim($post["name"] ?? '');
        $surname = trim($post["surname"] ?? '');
        $password_plain = $post["password"] ?? '';
        $passwordrep = $post["passwordrep"] ?? '';
        $securitypassphrase_plain = trim($post["securitypassphrase"] ?? '');
        $admin = 0;
        $mode = 0;
        $ILT = 2;
        $DPS = 0;

        // Leere Felder
        if (empty($username) || empty($password_plain) || empty($passwordrep) || empty($securitypassphrase_plain) || empty($name) || empty($surname)) {
            $this->popupTitle = "Leere Felder";
            $this->alert = "Bitte alle Felder ausfüllen.";
            return;
        }

        // Zeichenprüfung Vorname & Nachname
        if (!preg_match('/^[a-zA-ZäöüÄÖÜß]{1,50}$/u', $name)) {
            $this->popupTitle = "Ungültiger Vorname";
            $this->alert = "Nur Buchstaben erlaubt, max. 50 Zeichen.";
            return;
        }

        if (!preg_match('/^[a-zA-ZäöüÄÖÜß]{1,50}$/u', $surname)) {
            $this->popupTitle = "Ungültiger Nachname";
            $this->alert = "Nur Buchstaben erlaubt, max. 50 Zeichen.";
            return;
        }

        // Benutzername: Buchstaben, Zahlen, _ und -, max. 50
        if (!preg_match('/^[a-zA-Z0-9_-]{1,50}$/', $username)) {
            $this->popupTitle = "Ungültiger Benutzername";
            $this->alert = "Nur Buchstaben, Zahlen, _ und - erlaubt, max. 50 Zeichen.";
            return;
        }

        // Passwort: min. 5, max. 50, nur Buchstaben/Zahlen/_/-
        if (!preg_match('/^[a-zA-Z0-9_-]{5,50}$/', $password_plain)) {
            $this->popupTitle = "Ungültiges Passwort";
            $this->alert = "5–50 Zeichen, nur Buchstaben, Zahlen, _ und - erlaubt.";
            return;
        }

        // Sicherheitsfrage: nur Buchstaben/Zahlen, max. 50
        if (!preg_match('/^[a-zA-Z0-9]{1,50}$/', $securitypassphrase_plain)) {
            $this->popupTitle = "Ungültige Sicherheitsantwort";
            $this->alert = "Nur Buchstaben und Zahlen erlaubt, max. 50 Zeichen.";
            return;
        }

        // Passwortgleichheit
        if ($password_plain !== $passwordrep) {
            $this->popupTitle = "Passwörter stimmen nicht überein";
            $this->alert = "Bitte gleiche Passwörter eingeben.";
            return;
        }

        // Hashing
        $password = password_hash($password_plain, PASSWORD_DEFAULT);
        $securitypassphrase = password_hash($securitypassphrase_plain, PASSWORD_DEFAULT);
        $calenderfile = ""; // Leerer Kalender-Dateiname zum Start

        // Benutzername bereits vergeben?
        $check = $this->conn->prepare("SELECT Username FROM user WHERE Username = :username");
        $check->execute(["username" => $username]);

        if ($check->fetch()) {
            $this->popupTitle = "Benutzername vergeben";
            $this->alert = "Der Benutzername ist bereits registriert.";
            return;
        }

        // INSERT-Query
        $query = $this->conn->prepare("INSERT INTO user 
            (Username, Name, Surname, Securitypassphrase, Password, Admin, DPS, Mode, ILT)
            VALUES (:username, :name, :surname, :securitypassphrase, :password, :Admin, :DPS, :Mode, :ILT)");
        
        $query->bindParam(':username', $username);
        $query->bindParam(':name', $name);
        $query->bindParam(':surname', $surname);
        $query->bindParam(':securitypassphrase', $securitypassphrase);
        $query->bindParam(':password', $password);
        $query->bindParam(':Admin', $admin);
        $query->bindParam(':DPS', $DPS);
        $query->bindParam(':Mode', $mode);
        $query->bindParam(':ILT', $ILT);
        

        // Ausführung & Rückmeldung
        if ($query->execute()) {
            $this->popupTitle = "Registrierung erfolgreich";
            $this->alert = "Einfügen erfolgreich!";
        } else {
            $this->popupTitle = "Fehler";
            $this->alert = "Fehler beim Einfügen: " . implode(", ", $query->errorInfo());
        }
    }
}
