<?php
//session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/user-classes/user.php";

//define('SESSION_TIMEOUT', 600);


$host = "localhost";
$dbname = "studycal";
$databaseUser = "Admin";
$pass = "rH!>|r'h6.XXlN.=2}A_#u[gxvhU3q;";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $databaseUser, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = trim($_POST["username"] ?? '');
    $password_plain = $_POST["password"] ?? '';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (empty($username) && empty($password_plain)) {
      $popupTitle = "Fehlerhafte Eingabe";
      $alert = "Bitte Benutzername und Passwort eingeben.";
  } elseif (empty($username)) {
      $popupTitle = "Benutzername fehlt";
      $alert = "Bitte gib deinen Benutzernamen ein.";
  } elseif (empty($password_plain)) {
      $popupTitle = "Passwort fehlt";
      $alert = "Bitte gib dein Passwort ein.";
  } else {
      $query = $conn->prepare("SELECT * FROM user WHERE Username = :username");
      $query->execute(["username" => $username]);
      $row = $query->fetch(PDO::FETCH_ASSOC);

      if (!$row) {
          $popupTitle = "Benutzer nicht gefunden";
          $alert = "Es existiert kein Benutzer mit diesem Namen.";
      } elseif (!password_verify($password_plain, $row['Password'])) {
          $popupTitle = "Falsches Passwort";
          $alert = "Das eingegebene Passwort ist nicht korrekt.";
      } else {
          $user = new User();
          $user->setUserName($row['Username']);
          $user->setPassword($row['Password']);
          $user->setSecurityPassphrase($row['Securitypassphrase']);
          $user->setName($row['Name']);
          $user->setSurname($row['Surname']);
          $user->setCalendarfile($row['Calendarfile'] ?? null);

            // $_SESSION['user_data'] = [
            //     'username' => $user->getUserName(),
            //     'name' => $user->getName(),
            //     'surname' => $user->getSurname(),
            //     'securityPassphrase' => $user->getSecurityPassphrase(),
            //     'calendarfile' => $user->getCalendarfile()
            // ];

            // $_SESSION['eingeloggt'] = true;
            // $_SESSION['LAST_ACTIVITY'] = time();
          header("Location: start.php");
          exit;
      }
  }
}

} catch (PDOException $e) {
    $alert = "PDO-Fehler: " . $e->getMessage();
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
