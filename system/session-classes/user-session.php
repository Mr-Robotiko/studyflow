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
        $this->user->setUserName($userData['username']);
        $this->user->setName($userData['name']);
        $this->user->setSurname($userData['surname']);
        $this->user->setSecurityPassphrase($userData['securityPassphrase'] ?? '');
        $this->user->setCalendarfile($userData['calendarfile'] ?? null);
    }

    public function getUser(): User {
        return $this->user;
    }
}
