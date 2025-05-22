<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$popupTitle = "";
$alert = "";

$host = "localhost";
$dbname = "studycal";
$databaseUser = "Admin";
$pass = "rH!>|r'h6.XXlN.=2}A_#u[gxvhU3q;";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $databaseUser, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['change_password'], $_POST['username'], $_POST['new_password'])) {
        $username = $_POST['username'];
        $newPassword = $_POST['new_password'];

        if (!preg_match('/^[a-zA-Z0-9_-]{5,50}$/', $newPassword)) {
            $popupTitle = "Ungültiges Passwort";
            $alert = "Passwort muss 5–50 Zeichen lang sein und darf nur Buchstaben, Zahlen, _ und - enthalten.";
        } else {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user SET Password = :password WHERE Username = :username");
            $stmt->execute([
                ':password' => $hashed,
                ':username' => $username
            ]);
            $popupTitle = "Passwort aktualisiert";
            $alert = "Das Passwort wurde erfolgreich geändert.";
        }
    }

    if (isset($_POST['toggle_admin'], $_POST['username'])) {
        $username = $_POST['username'];
        $stmt = $conn->prepare("SELECT is_admin FROM user WHERE Username = ?");
        $stmt->execute([$username]);
        $current = $stmt->fetchColumn();
        $new = $current ? 0 : 1;
        $stmt = $conn->prepare("UPDATE user SET is_admin = ? WHERE Username = ?");
        $stmt->execute([$new, $username]);
    }

    $stmt = $conn->query("SELECT Username, Name, Surname, is_admin FROM user");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
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
    <script src="system/javascript/popup.js"></script>
</head>
<body>

<h1>Adminverwaltung</h1>

<table>
    <thead>
        <tr>
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
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['Name']) ?></td>
                    <td><?= htmlspecialchars($user['Surname']) ?></td>
                    <td><?= htmlspecialchars($user['Username']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="username" value="<?= htmlspecialchars($user['Username']) ?>">
                            <input type="text" name="new_password" placeholder="Neues Passwort" required>
                            <button type="submit" name="change_password">Ändern</button>
                        </form>
                    </td>
                    <td><?= $user['is_admin'] ? 'Admin' : '—' ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="username" value="<?= htmlspecialchars($user['Username']) ?>">
                            <button type="submit" name="toggle_admin">
                                <?= $user['is_admin'] ? 'Admin entfernen' : 'Zum Admin machen' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">Keine Benutzer gefunden.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div style="text-align:center;">
    <button class="export-btn" onclick="exportJSON()">📁 Alle Benutzer als JSON exportieren</button>
</div>

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
