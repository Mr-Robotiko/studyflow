<?php
require_once 'system/database-classes/database.php';

class User {
    private $id;
    private $name;
    private $surname;
    private $institution;
    private $Calendarfile; 
    private $securityPassphrase;
    private $userName;
    private $password;
    private int $darkMode = 0;      // 0 = Light, 1 = Dark
    private int $lernideal = 2;     // 0–4 (entspricht 30–180min)

    public function __construct($id = null, $name = null, $surname = null, $institution = null, $Calendarfile = null, $securityPassphrase = null, $userName = null, $password = null, $darkMode = 0, $lernideal = 2) {
        $this->setId($id);
        $this->setName($name);
        $this->setSurname($surname);
        $this->setInstitution($institution);
        $this->setCalendarfile($Calendarfile);
        $this->setSecurityPassphrase($securityPassphrase);
        $this->setUserName($userName);
        $this->setPassword($password);
        $this->setDarkMode($darkMode);
        $this->setLernideal($lernideal);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    public function getInstitution() {
        return $this->institution;
    }

    public function setInstitution($institution) {
        $this->institution = $institution;
    }

    public function getCalendarfile() {
        return $this->Calendarfile;
    }

    public function setCalendarfile($Calendarfile) {
        $this->Calendarfile = $Calendarfile;
    }

    public function getSecurityPassphrase() {
        return $this->securityPassphrase;
    }

    public function setSecurityPassphrase($securityPassphrase) {
        $this->securityPassphrase = $securityPassphrase;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    // Returns 1 (dark mode) or 0 (light mode)
    public function getDarkMode(): int {
        return $this->darkMode;
    }

    public function setDarkMode($darkMode): void {
        $this->darkMode = (int)$darkMode;
    }

    public function getLernideal(): int {
        return $this->lernideal;
    }

    public function setLernideal($lernideal): void {
        $this->lernideal = (int)$lernideal;
    }

    public function __toString() {
        return "Name: {$this->name} {$this->surname}, Institution: {$this->institution}, Calendar: {$this->Calendarfile}";
    }

    public function deleteFromDatabase(): bool {
        $username = $this->getUserName();
        if (!$username) {
            throw new Exception("Benutzername fehlt");
        }

        $db = new Database("config/configuration.csv");
        $pdo = $db->getConnection();

        // Abfgrage der UserID anhand des Nutzernamens
        $stmt = $pdo->prepare("SELECT UserID FROM user WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Benutzer nicht gefunden");
        }

        $userID = $user['UserID'];

        // EventIDs des Nutzers abfragen
        $stmt = $pdo->prepare("SELECT EventID FROM event WHERE UserID = :userID");
        $stmt->execute(['userID' => $userID]);
        $eventIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Lösche der EventIDs von jedem Event
        if (!empty($eventIDs)) {
            $inQuery = implode(',', array_fill(0, count($eventIDs), '?'));

            $stmt = $pdo->prepare("DELETE FROM entry WHERE EventID IN ($inQuery)");
            $stmt->execute($eventIDs);
        }

        // Löschen der Events
        $stmt = $pdo->prepare("DELETE FROM event WHERE UserID = :userID");
        $stmt->execute(['userID' => $userID]);


        // Löschen der ToDos
        $stmt = $pdo->prepare("DELETE FROM todo WHERE UserID = :userID");
        $stmt->execute(['userID' => $userID]);

        // Löschen des Benutzers
        $stmt = $pdo->prepare("DELETE FROM user WHERE username = :username");
        return $stmt->execute(['username' => $username]);
    }



    public function saveSettingsToDatabase(): bool {
        $db = new Database("config/configuration.csv");
        $pdo = $db->getConnection();
    
        $stmt = $pdo->prepare("UPDATE user SET Mode = :darkMode, ILT = :lernideal WHERE username = :username");
        return $stmt->execute([
            'darkMode' => $this->getDarkMode(),
            'lernideal' => $this->getLernideal(),
            'username' => $this->getUserName()
        ]);
    }

    public function loadUserDataFromDatabase(PDO $pdo): void {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE UserID = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->darkMode = $data['Mode'];
            $this->lernideal = $data['ILT'];
        }
    }

    public function getAutoLogoutTimer(): int {
        $db = new Database("config/configuration.csv");
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("SELECT AutoLogoutTimer FROM user WHERE UserID = :id");
        $stmt->execute(['id' => $this->id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['AutoLogoutTimer'] : 600;
    }

    public function getLernidealMinutes(): int {
        $mapping = [0 => 30, 1 => 60, 2 => 90, 3 => 120, 4 => 180];
        return $mapping[$this->lernideal] ?? 90; // Default = 90min
    }

}

?>
