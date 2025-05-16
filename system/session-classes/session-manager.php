<?php
class SessionManager {
    // Timeout in Sekunden (z. B. 600 = 10 Minuten)
    private const SESSION_TIMEOUT = 600;

    /**
     * Initialisiert die Session mit sicheren Cookie-Parametern,
     * prüft Login-Status und Inaktivität und setzt LAST_ACTIVITY.
     */
    public static function start(): void {
        // Sichere Cookie-Parameter setzen, bevor session_start() aufgerufen wird
        session_set_cookie_params([
            'lifetime' => 0,           // bis Browser-Schließen
            'path'     => '/',         
            'domain'   => '',          // leer = aktuelle Domain
            'secure'   => isset($_SERVER['HTTPS']), // nur über HTTPS
            'httponly' => true,        // JS-Zugriff verbieten
            'samesite' => 'Lax',       // CSRF-Risiko minimieren
        ]);

        // Session starten, falls noch keine aktiv ist
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Login prüfen
        if (empty($_SESSION['eingeloggt']) || $_SESSION['eingeloggt'] !== true) {
            self::redirectToLogin();
        }

        // Timeout prüfen
        if (!empty($_SESSION['LAST_ACTIVITY'])
            && (time() - (int)$_SESSION['LAST_ACTIVITY'] > self::SESSION_TIMEOUT)
        ) {
            self::destroy();
            // Mit Timeout-Parameter zurück ins Login
            header('Location: login.php?timeout=1');
            exit;
        }

        // LAST_ACTIVITY aktualisieren
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    /**
     * Leitet zur Login-Seite weiter und beendet das Skript.
     */
    private static function redirectToLogin(): void {
        header('Location: login.php');
        exit;
    }

    /**
     * Zerstört die Session vollständig.
     */
    public static function destroy(): void {
        // alle Session-Daten löschen
        $_SESSION = [];
        // Cookie löschen
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

    /**
     * Liefert die User-Daten, wie sie beim Login gespeichert wurden.
     */
    public static function getUserData(): ?array {
        return $_SESSION['user_data'] ?? null;
    }
}
