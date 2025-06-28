<?php
require_once 'system/session-classes/session-manager.php';
require_once 'system/session-classes/user-session.php';
require_once 'system/database-classes/database.php';
require_once __DIR__ . "/system/user-classes/user.php";

SessionManager::start();
$userSession = new UserSession();
$user = $userSession->getUser();

if (!$user) {
    header('Location: login.php');
    exit;
}

// Logout-Timer prüfen bei JEDEM Request
$timeoutLimit = $user->getAutoLogoutTimer() ?? 600;
if (isset($_SESSION['last_activity'])) {
    $inactive = time() - $_SESSION['last_activity'];
    if ($inactive > $timeoutLimit) {
        SessionManager::destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
}
$_SESSION['last_activity'] = time();

if (!$user || !method_exists($user, 'getId')) {
    header("Location: login.php");
    exit;
}

try {
    // Verbindung über Konfigurationsdatei herstellen
    $database = new Database("config/configuration.csv");
    $pdo = $database->getConnection();

    // Adminstatus prüfen
    $stmt = $pdo->prepare("SELECT admin FROM user WHERE UserID = :userId");
    $stmt->execute([':userId' => $user->getId()]);
    $isAdmin = (int)$stmt->fetchColumn();

    if ($isAdmin !== 1) {
        exit("Kein Zugriff!");
    }

    $popupTitle = "";
    $alert = "";

    // Regex-Templates für Eingaben
    if (isset($_POST['change_password'], $_POST['username'], $_POST['new_password'])) {
        $username = $_POST['username'];
        $newPassword = $_POST['new_password'];

        if (!preg_match('/^[a-zA-Z0-9_-]{5,50}$/', $newPassword)) {
            $popupTitle = "Ungültiges Passwort";
            $alert = "Passwort muss 5–50 Zeichen lang sein und darf nur Buchstaben, Zahlen, _ und - enthalten.";
        } else {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE user SET Password = :password WHERE Username = :username");
            $stmt->execute([
                ':password' => $hashed,
                ':username' => $username
            ]);
            $popupTitle = "Passwort aktualisiert";
            $alert = "Das Passwort wurde erfolgreich geändert.";
        }
    }

    // Setzen des Adminstatuses
    if (isset($_POST['toggle_admin'], $_POST['username'])) {
        $username = $_POST['username'];
        $stmt = $pdo->prepare("SELECT Admin FROM user WHERE Username = ?");
        $stmt->execute([$username]);
        $current = $stmt->fetchColumn();
        $new = $current ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE user SET Admin = ? WHERE Username = ?");
        $stmt->execute([$new, $username]);
    }

    // Alle User nehmen für die JSON exporte
    $stmt = $pdo->query("SELECT UserID, Username, Name, Surname, Admin FROM user");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $popupTitle = "Verbindungsfehler";
    $alert = "Fehler: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Adminverwaltung</title>
    <link rel="stylesheet" href="system/style/popup.css"/>
    <link rel="stylesheet" href="system/style/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="system/javascript/inactivityTimer.js" defer></script>
    <script>
        const sessionTimeout = <?= json_encode($user->getAutoLogoutTimer() ?? 600); ?>;
    </script>
    <script src="system/javascript/popup.js"></script>
</head>
<body>

<h1>Adminverwaltung</h1>

<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Vorname</th>
            <th>Benutzername</th>
            <th>Passwort ändern</th>
            <th>Adminstatus</th>
            <th>Aktion</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $accounts): ?>
                <tr>
                    <td><?= htmlspecialchars($accounts['UserID']) ?></td>
                    <td><?= htmlspecialchars($accounts['Name']) ?></td>
                    <td><?= htmlspecialchars($accounts['Surname']) ?></td>
                    <td><?= htmlspecialchars($accounts['Username']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="username" value="<?= htmlspecialchars($accounts['Username']) ?>">
                            <input type="text" name="new_password" placeholder="Neues Passwort" required>
                            <button type="submit" name="change_password">Ändern</button>
                        </form>
                    </td>
                    <td><?= $accounts['Admin'] ? 'Admin' : '—' ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="username" value="<?= htmlspecialchars($accounts['Username']) ?>">
                            <button type="submit" name="toggle_admin">
                                <?= $accounts['Admin'] ? 'Admin entfernen' : 'Zum Admin machen' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Keine Benutzer gefunden.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div style="text-align:center;">
    <button class="export-btn" onclick="exportJSON()">📁 Alle Benutzer als JSON exportieren</button>
</div>
<a href="start.php" class="zurueck-button"><i class="fas fa-arrow-left"></i>Zurück</a>

<script>
function exportJSON() {
    const link = document.createElement('a');
    link.href = 'system/handler/export.php';
    link.download = 'benutzer.json';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<div id="customAlert" class="custom-popup-overlay" style="display: none;">
  <div class="custom-popup">
    <h2 id="popupTitle">Benachrichtigung</h2> 
    <p id="alertMessage"></p>
    <button id="closePopup">Schließen</button>
  </div>
</div>

<div id="phpErrorMessage"
     data-title="<?= htmlspecialchars($popupTitle) ?>"
     data-message="<?= htmlspecialchars($alert) ?>"
     style="display:none;"></div>
</body>
</html>
