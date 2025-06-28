<?php
// Zerstört Session vom jeweiligen User
require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";

SessionManager::start();
$userData = SessionManager::getUserData();

if ($userData) {
    $username = $userData['username'];
    $user = new User();
    $user->setUserName($username);

    SessionManager::destroy();
}

// Redirection zur Login.php
header("Location: login.php");
exit;
