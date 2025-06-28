<?php
require_once "system/database-classes/database.php";
require_once "system/login-classes/registration.php";

$popupTitle = "";
$alert = "";
$registration = null;

// Eingabewerte für Re-Population vorbereiten
$name = "";
$surname = "";
$username = "";
$securitypassphrase = "";

try {
    $db = new Database("config/configuration.csv");
    $conn = $db->getConnection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Eingaben übernehmen
        $name = $_POST['name'] ?? '';
        $surname = $_POST['surname'] ?? '';
        $username = $_POST['username'] ?? '';
        $securitypassphrase = $_POST['securitypassphrase'] ?? '';

        $registration = new Registration($conn);
        $registration->handleRegistration($_POST);

        $popupTitle = $registration->popupTitle;
        $alert = $registration->alert;

        if ($alert === "Einfügen erfolgreich!") {
            $popupTitle = "Registrierung erfolgreich";
            $alert = "Viel Spaß mit StudyCal!";
        } elseif (str_contains($alert, "Fehler beim Einfügen")) {
            $popupTitle = "Registrierung fehlgeschlagen";
        } else {
            $popupTitle = "Eingabefehler";
        }
    }
} catch (Exception $e) {
    $popupTitle = "Verbindungsfehler";
    $alert = "Datenbankfehler: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal | Registrierung</title>
  <link rel="icon" href="images/Logo_favicon.png">
  <link rel="stylesheet" href="system/style/prelogin.css">
  <link rel="stylesheet" href="system/style/popup.css"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="system/javascript/popup.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
  <h1>Registriere dich bei StudyCal</h1>
</header>

<div class="main">
  
  <form action="register.php" method="post">
    <div>
      <label for="vorname">Vorname</label>
      <input class="input" type="text" id="vorname" name="name" placeholder="Vorname eingeben" value="<?= htmlspecialchars($name) ?>" />
    </div>
    <div>
      <label for="nachname">Nachname</label>
      <input class="input" type="text" id="nachname" name="surname" placeholder="Nachname eingeben" value="<?= htmlspecialchars($surname) ?>" />
    </div>
    <div>
      <label for="username">Benutzername</label>
      <input class="input" type="text" id="username" name="username" placeholder="Benutzername eingeben" value="<?= htmlspecialchars($username) ?>" />
    </div>
    <div>
      <label for="password">Passwort</label>
      <input class="input" type="password" id="password" name="password" placeholder="Passwort eingeben" />
    </div>
    <div>
      <label for="passwordwdh">Passwort wiederholen</label>
      <input class="input" type="password" id="passwordwdh" name="passwordrep" placeholder="Passwort wiederholen" />
    </div>
    <div>
      <label for="securityanswer">Sicherheitsfrage</label>
      <p style="margin-bottom: 10px; font-style: italic;">Wie lautet der Name deines ersten Haustiers?</p>
      <input class="input" type="text" id="securityanswer" name="securitypassphrase" placeholder="Antwort eingeben" value="<?= htmlspecialchars($securitypassphrase) ?>" />
    </div>

    <div class="buttons">
      <button type="submit" class="btn">Registrieren</button>
      <a href="index.html" class="zurueck-button"><i class="fas fa-arrow-left"></i>Zurück</a>
    </div>
  </form>
</div>

<div id="customAlert" class="custom-popup-overlay" style="display: none;">
  <div class="custom-popup">
    <h2 id="popupTitle">Benachrichtigung</h2> 
    <p id="alertMessage"></p>
    <button id="closePopup">Schließen</button>
  </div>
</div>

<!-- Datenübergabe an JS -->
<div id="phpErrorMessage"
     data-title="<?= htmlspecialchars($popupTitle) ?>"
     data-message="<?= htmlspecialchars($alert) ?>"
     style="display:none;"></div>

<footer>
  &copy; 2025 StudyCal. Alle Rechte vorbehalten.
</footer>

</body>
</html>
