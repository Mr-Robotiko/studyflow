<?php
class SessionManager {
    private const SESSION_TIMEOUT = 600;

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }

        if (empty($_SESSION['eingeloggt']) || $_SESSION['eingeloggt'] !== true) {
            self::redirectToLogin();
        }

        if (!empty($_SESSION['LAST_ACTIVITY'])
            && (time() - (int)$_SESSION['LAST_ACTIVITY'] > self::SESSION_TIMEOUT)
        ) {
            self::destroy();
            header('Location: login.php?timeout=1');
            exit;
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    private static function redirectToLogin(): void {
        header('Location: login.php');
        exit;
    }

    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_unset();
        session_destroy();
    }
    public static function getUserData(): ?array {
        return $_SESSION['user_data'] ?? null;
    }
}
