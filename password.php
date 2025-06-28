<?php
require_once "system/login-classes/password.php";

$popupTitle = "";
$alert = "";
$username = "";
$securitypassphrase = "";

try {
    $resetHandler = new PasswordReset("config/configuration.csv");

    // Übernahme der Feldereingaben
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST['username'] ?? '';
        $securitypassphrase = $_POST['securitypassphrase'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        $result = $resetHandler->reset($username, $securitypassphrase, $newPassword, $confirmPassword);

        if ($result === "success") {
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 300);
                  </script>";
            exit;
        } else {
            $alert = $result;

            // Prüfung, ob alle Felder ausgefüllt wurden
            if ($result === "Bitte alle Felder ausfüllen.") {
                $popupTitle = "Fehlende Angaben";
            } elseif ($result === "Die Passwörter stimmen nicht überein.") {
                $popupTitle = "Passwortfehler";
            } elseif ($result === "Benutzer existiert nicht.") {
                $popupTitle = "Benutzerfehler";
            } elseif (str_contains($result, "Benutzername ist ungültig")) {
                $popupTitle = "Benutzername ungültig";
            } elseif (str_contains($result, "Neues Passwort ist ungültig")) {
                $popupTitle = "Passwort ungültig";
            } elseif ($result === "Sicherheitsantwort ist falsch.") {
                $popupTitle = "Sicherheitsfrage";
            } else {
                $popupTitle = "Fehlgeschlagen";
            }
        }
    }
} catch (Exception $e) {
    $popupTitle = "Datenbankfehler";
    $alert = "Verbindungsfehler: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal | Passwort Wiederherstellung</title>
  <link rel="icon" href="images/Logo_favicon.png">
  <link rel="stylesheet" href="system/style/newpassword.css" />
  <link rel="stylesheet" href="system/style/popup.css"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script defer src="system/javascript/popup.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <div class="container">
    <h1>Passwort Wiederherstellung</h1>
    <p class="subtitle">Passwort zurücksetzen</p>

    <form method="POST" action="password.php">
      <div class="form-group">
        <label for="username">Benutzername</label>
        <input type="text" name="username" placeholder="Benutzername" value="<?= htmlspecialchars($username) ?>">
      </div>

      <div class="form-group">
        <label for="securitypassphrase">Wie lautet der Name deines ersten Haustiers?</label>
        <input type="text" name="securitypassphrase" placeholder="Antwort auf die Sicherheitsfrage" value="<?= htmlspecialchars($securitypassphrase) ?>">
      </div>

      <div class="form-group">
        <label for="newPassword">Neues Passwort</label>
        <input type="password" name="newPassword" placeholder="Neues Passwort">
      </div>

      <div class="form-group">
        <label for="confirmPassword">Passwort bestätigen</label>
        <input type="password" name="confirmPassword" placeholder="Passwort bestätigen">
      </div>

      <div class="buttons">
        <button type="submit" class="btn">Passwort ändern</button>
        <a href="login.php" class="zurueck-button"><i class="fas fa-arrow-left"></i>Zurück</a>
      </div>
    </form>
  </div>

  <!-- Popup -->
  <div id="customAlert" class="custom-popup-overlay" style="display: none;">
    <div class="custom-popup">
      <h2 id="popupTitle">Benachrichtigung</h2> 
      <p id="alertMessage"></p>
      <button id="closePopup">Schließen</button>
    </div>
  </div>

  <!-- Übergabe PHP Fehler -->
  <div id="phpErrorMessage"
       data-title="<?= htmlspecialchars($popupTitle) ?>"
       data-message="<?= htmlspecialchars($alert) ?>"
       style="display:none;"></div>

</body>
</html>
