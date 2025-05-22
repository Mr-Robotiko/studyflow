<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";

SessionManager::start();
$userData = SessionManager::getUserData();

if ($userData) {
    $username = $userData['username'];
    $user = new User();
    $user->setUserName($username);

    $calendarPath = __DIR__ . "/data/calendar_{$username}.json";

    if (file_exists($calendarPath)) {
        $json = file_get_contents($calendarPath);

        // 📥 Speichern in DB
        if ($user->saveCalendarfileToDatabase($json)) {
            // 🧹 Löschen der lokalen Datei
            unlink($calendarPath);
        }
    }

    SessionManager::destroy();
}

header("Location: login.php");
exit;
