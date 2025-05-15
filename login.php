<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/system/database-classes/database.php';
require_once __DIR__ . '/system/login-classes/login-class.php';

define('SESSION_TIMEOUT', 600);

try {
    $db = new Database(__DIR__ . '/configuration.csv');
    $conn = $db->connect();

    $loginHandler = new Login($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST["username"] ?? '');
        $password = $_POST["password"] ?? '';

        if ($loginHandler->login($username, $password)) {
            header("Location: start.php");
            exit;
        }
    }

    $alert = $loginHandler->alert ?? '';
    echo $alert;

} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script type="text/javascript" src="system/javascript/login.js"></script>
  <title>StudyCal - Login</title>
  <link rel="stylesheet" href="system/style/prelogin.css"/>
  <link rel="stylesheet" href="system/style/popup.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
    <h1>Willkommen zurück bei StudyCal</h1>
</header>

  <div class="main">
  
    <img src="images/Logo.png" alt="StudyCal Logo" />
    <p class="subtitle"><?php echo $alert?></p>

    <form action="login.php" method="post">
  <div>
    <label for="username">Benutzername</label>
    <input class="input" type="text" id="username" name="username" placeholder="Benutzername eingeben" required />
  </div>

  <div>
    <label for="password">Passwort</label>
    <input class="input" type="password" id="password" name="password" placeholder="Passwort eingeben" required />
  </div>

  <div class="password-forgotten">
    <a href="password.php" class="forgot-password-link">Passwort vergessen?</a>
  </div>

  <div class="buttons">
    <button onclick="input_to_var(event)" type="submit" class="btn">Einloggen</button>
    <a href="index.html" class="zurueck-button"><i class="fas fa-arrow-left"></i>Zurück</a>
  </div>

  <?php if (!empty($fehlermeldung)): ?>
    <p style="color: red; text-align: center;"><?= $fehlermeldung ?></p>
  <?php endif; ?>
</form>
  </div>

  <footer>
    &copy; 2025 StudyCal. Alle Rechte vorbehalten.
  </footer>

  <div id="customAlert" class="custom-popup-overlay">
  <div class="custom-popup">
    <h2 id="popupTitle">Benachrichtigung</h2> 
    <p id="alertMessage"></p>
    <button onclick="closeCustomAlert()">Schließen</button>
  </div>
</div>


</body>
</html>
