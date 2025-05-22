<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once "system/database-classes/database.php";
require_once "system/login-classes/login-class.php";

$fehlermeldung = '';

try {
    $db   = new Database(__DIR__ . '/configuration.csv');
    $conn = $db->getConnection();

    $login = new Login($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($login->login($username, $password)) {
            $_SESSION['eingeloggt'] = true;
            $_SESSION['user_data']  = [
                'username'           => $username,
                'name'               => method_exists($login,'getName')               ? $login->getName()               : '',
                'surname'            => method_exists($login,'getSurname')            ? $login->getSurname()            : '',
                'securityPassphrase' => method_exists($login,'getSecurityPassphrase') ? $login->getSecurityPassphrase() : '',
                'calendarfile'       => method_exists($login,'getCalendarfile')       ? $login->getCalendarfile()       : null,
            ];
            $_SESSION['LAST_ACTIVITY'] = time();

            session_write_close();
            header("Location: start.php");
            exit;
        } else {
            $fehlermeldung = $login->alert;
        }
    }
} catch (Exception $e) {
    $fehlermeldung = "Fehler: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal - Login</title>
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
    <p class="subtitle">Einloggen</p>

    <form action="login.php" method="post">
  <div>
    <label for="username">Benutzername</label>
    <input class="input" type="text" id="username" name="username" placeholder="Benutzername eingeben" required />
  </div>
  <img src="images/Logo.png" alt="StudyCal Logo" />

  <?php if (!empty($alert)): ?>
    <div id="phpErrorMessage" data-title="Login fehlgeschlagen" data-message="<?= htmlspecialchars($alert) ?>" style="display: none;"></div>
  <?php endif; ?>

  <form action="login.php" method="post">
    <div>
      <label for="username">Benutzername</label>
      <input class="input" type="text" id="username" name="username" placeholder="Benutzername eingeben" />
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

</body>
</html>
