<?php
require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";

class UserSession {
    private User $user;

    public function __construct() {
        SessionManager::start();
        $userData = SessionManager::getUserData();
    
        if (!$userData) {
            header("Location: login.php");
            exit;
        }
    
        $this->user = new User();
    
        if (isset($userData['id'])) {
            $this->user->setId($userData['id']);
        }
    
        // 1. Lade DB-Daten basierend auf der UserID
        require_once "system/database-classes/database.php";
        $db = new Database(__DIR__ . "/../../config/configuration.csv");
        $pdo = $db->getConnection();
    
        $this->user->loadUserDataFromDatabase($pdo);
    
        // 2. Jetzt überschreibe die restlichen Werte aus der Session (z.B. Username, Name)
        $this->user->setUserName($userData['username']);
        $this->user->setName($userData['name']);
        $this->user->setSurname($userData['surname']);
        $this->user->setSecurityPassphrase($userData['securityPassphrase'] ?? '');
        $this->user->setCalendarfile($userData['calendarfile'] ?? null);
    
        // 3. Dark Mode aus Session oder DB (loadUserDataFromDatabase hat nur Lernideal & Mode gesetzt)
        if (isset($userData['mode'])) {
            $this->user->setDarkMode((bool)$userData['mode']);
        }
        
        // Lernideal ist jetzt aus DB geladen
    }
    

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }
    
}
