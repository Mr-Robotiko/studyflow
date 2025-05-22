<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/system/login-classes/password.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $securitypassphrase = $_POST['securitypassphrase'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    try {
        $reset = new PasswordReset(__DIR__ . '/configuration.csv');
        $result = $reset->reset($username, $securitypassphrase, $newPassword, $confirmPassword);

        if ($result === "success") {
            echo "<script>alert('Passwort erfolgreich geändert.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('$result'); window.history.back();</script>";
        }
    } catch (Exception $e) {
        $msg = addslashes($e->getMessage());
        echo "<script>alert('Fehler: $msg'); window.history.back();</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Passwort Wiederherstellung - StudyCal</title>
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
        <input type="text" name="username" placeholder="Benutzername">
      </div>

      <div class="form-group">
        <label for="securitypassphrase">Wie lautet der Name deines ersten Haustiers?</label>
        <input type="text" name="securitypassphrase" placeholder="Antwort auf die Sicherheitsfrage">
      </div>

      <div class="form-group">
        <label for="newPassword">Neues Passwort</label>
        <input type="password" name="newPassword" placeholder="Neues Passwort">
      </div>

      <div class="form-group">
        <label for="confirmPassword">Passwort bestätigen</label>
        <input type="password" name="confirmPassword" placeholder="Passwort bestätigen">
      </div class="buttons">
      <button type="submit" class="btn">Passwort ändern</button>
      <a href="login.php" class="zurueck-button"><i class="fas fa-arrow-left"></i>Zurück</a>
    </form>
  </div>

  <div id="customAlert" class="custom-popup-overlay" style="display: none;">
  <div class="custom-popup">
    <h2 id="popupTitle">Benachrichtigung</h2> 
    <p id="alertMessage"></p>
    <button id="closePopup">Schließen</button>
  </div>
</div>

</body>
</html>
