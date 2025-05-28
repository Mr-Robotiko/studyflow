<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/user-classes/user.php";
require_once "system/database-classes/database.php";
require_once "system/login-classes/login-class.php";

define('SESSION_TIMEOUT', 600); // 10 Minuten Timeout

$popupTitle = '';
$alert = '';

try {
    $db = new Database("config/configuration.csv");
    $conn = $db->getConnection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST["username"] ?? '');
        $password = $_POST["password"] ?? '';

        $login = new Login($conn);

        if (empty($username) && empty($password)) {
            $popupTitle = "Fehlende Eingabe";
            $alert = "Bitte Benutzername und Passwort eingeben.";
        } elseif (empty($username)) {
            $popupTitle = "Benutzername fehlt";
            $alert = "Bitte gib deinen Benutzernamen ein.";
        } elseif (empty($password)) {
            $popupTitle = "Passwort fehlt";
            $alert = "Bitte gib dein Passwort ein.";
        } else {
            if ($login->login($username, $password)) {
                $_SESSION['LAST_ACTIVITY'] = time();
                header("Location: start.php");
                exit;
            } else {
                $popupTitle = $login->popupTitle;
                $alert = $login->alert;
            }
        }
    }

} catch (Exception $e) {
    $popupTitle = "Verbindungsfehler";
    $alert = "Fehler bei der Verbindung: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal | Login</title>
  <link rel="icon" href="images/Logo_favicon.png">
  <link rel="stylesheet" href="system/style/prelogin.css"/>
  <link rel="stylesheet" href="system/style/popup.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script defer src="system/javascript/popup.js"></script>
</head>
<body>

<header>
    <h1>Willkommen zurück bei StudyCal</h1>
</header>

<div class="main">
  <img src="images/Logo.png" alt="StudyCal Logo" />
  <p class="subtitle">Bitte melden Sie sich an</p>

  <form action="login.php" method="post">
    <div>
      <label for="username">Benutzername</label>
      <input class="input" type="text" id="username" name="username" placeholder="Benutzername eingeben" value="<?= htmlspecialchars($username ?? '') ?>" />
    </div>

    <div>
      <label for="password">Passwort</label>
      <input class="input" type="password" id="password" name="password" placeholder="Passwort eingeben" />
    </div>

    <div class="password-forgotten">
      <a href="password.php" class="forgot-password-link">Passwort vergessen?</a>
    </div>

    <div class="buttons">
      <button type="submit" class="btn">Einloggen</button>
      <a href="index.html" class="zurueck-button"><i class="fas fa-arrow-left"></i>Zurück</a>
    </div>
  </form>
</div>

<footer>
  &copy; 2025 StudyCal. Alle Rechte vorbehalten.
</footer>

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
