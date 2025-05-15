<?php
class SessionManager {
    const SESSION_TIMEOUT = 600; // 10 Minuten

    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["eingeloggt"]) || !$_SESSION["eingeloggt"]) {
            header("Location: login.php");
            exit;
        }

        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > self::SESSION_TIMEOUT)) {
            self::destroy();
            header("Location: login.php?timeout=1");
            exit;
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    public static function destroy() {
        session_unset();
        session_destroy();
    }

    public static function getUserData(): ?array {
        return $_SESSION['user_data'] ?? null;
    }
}
