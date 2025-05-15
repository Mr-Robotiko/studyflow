<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'system/database-classes/database.php';
require_once 'system/login-classes/registration.php';

try {
    $db = new Database(__DIR__ . '/configuration.csv');
    $conn = $db->connect();

    $registration = new Registration($conn);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $registration->handleRegistration($_POST);
    }

    $alert = $registration->alert;

} catch (Exception $e) {
    $alert = "Fehler: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal - Registrierung</title>
  <script type="text/javascript" src="system/javascript/register.js"></script>
  <link rel="stylesheet" href="system/style/prelogin.css">
  <link rel="stylesheet" href="system/style/popup.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
  <h1>Registriere dich bei StudyCal</h1>
</header>

<div class="main">
  <p class="subtitle"><?php echo $alert?></p>

  <form action="register.php" method="post">
    <!-- Vorname -->
    <div>
      <label for="vorname">Vorname</label>
      <input class="input" type="text" id="vorname" name="name" placeholder="Vorname eingeben" required />
    </div>

    <!-- Nachname -->
    <div>
      <label for="nachname">Nachname</label>
      <input class="input" type="text" id="nachname" name="surname" placeholder="Nachname eingeben" required />
    </div>

    <!-- Benutzername -->
    <div>
      <label for="username">Benutzername</label>
      <input class="input" type="text" id="username" name="username" placeholder="Benutzername eingeben" required />
    </div>

    <!-- Passwort -->
    <div>
      <label for="password">Passwort</label>
      <input class="input" type="password" id="password" name="password" placeholder="Passwort eingeben" required />
    </div>

    <!-- Passwort wiederholen -->
    <div>
      <label for="passwordwdh">Passwort wiederholen</label>
      <input class="input" type="password" id="passwordwdh" name="passwordrep" placeholder="Passwort wiederholen" required />
    </div>

    <!-- Sicherheitsfrage -->
    <div>
      <label for="securityanswer">Sicherheitsfrage</label>
      <p style="margin-bottom: 10px; font-style: italic;">Wie lautet der Name deines ersten Haustiers?</p>
      <input class="input" type="text" id="securityanswer" name="securitypassphrase" placeholder="Antwort eingeben" required />
    </div>

    <!-- Button -->
    <div class="buttons">
      <button onclick="input_to_var(event)" type="submit" class="btn">Registrieren</button>
      <a href="index.html" class="zurueck-button" title="Zurück zur Startseite">
    <i class="fas fa-arrow-left"></i>Zurück
  </a>
    </div>
  </form>
</div>

<div id="customAlert" class="custom-popup-overlay">
  <div class="custom-popup">
    <h2 id="popupTitle">Benachrichtigung</h2> 
    <p id="alertMessage"></p>
    <button onclick="closeCustomAlert()">Schließen</button>
  </div>
</div>

<footer>
  &copy; 2025 StudyCal. Alle Rechte vorbehalten.
</footer>

</body>
</html>



    

