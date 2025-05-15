<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$alert = "Bitte beantworte die Sicherheitsfrage, um fortzufahren.";
$host = "localhost";
$dbname = "studycal";
$dbuser = "Admin";
$dbpass = "rH!>|r'h6.XXlN.=2}A_#u[gxvhU3q;";

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $securitypassphrase = trim($_POST['securitypassphrase'] ?? '');
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (empty($username) || empty($securitypassphrase) || empty($newPassword) || empty($confirmPassword)) {
      $errorTitle = "Fehlende Angaben";
      $errorMessage = "Bitte alle Felder ausfüllen.";
    } elseif ($newPassword !== $confirmPassword) {
      $errorTitle = "Passwortfehler";
      $errorMessage = "Die Passwörter stimmen nicht überein.";
    } else {
      // Sicherheitsfrage prüfen
      $stmt = $pdo->prepare("SELECT Securitypassphrase FROM user WHERE Username = :username");
      $stmt->execute(['username' => $username]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        $errorTitle = "Benutzerfehler";
        $errorMessage = "Benutzer existiert nicht.";
      } elseif (strcasecmp(trim($user['Securitypassphrase']), $securitypassphrase) !== 0) {
        $errorTitle = "Sicherheitsfrage";
        $errorMessage = "Sicherheitsantwort ist falsch.";
      } else {
        // Neues Passwort speichern
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE user SET Password = :password WHERE Username = :username");

        if ($update->execute(['password' => $hashedPassword, 'username' => $username])) {
          echo "<script>
                  setTimeout(function() {
                window.location.href = 'login.php';
            }, 300);
                </script>";
          exit;
        } else {
          $errorTitle = "Fehlgeschlagen";
          $errorMessage = "Fehler beim Speichern des neuen Passworts.";
        }
      }
    }
  }

} catch (PDOException $e) {
  $errorTitle = "Datenbankfehler";
  $errorMessage = "Verbindungsfehler: " . $e->getMessage();
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
    <p class="subtitle"><?php echo $alert ?></p>

    <?php if (isset($errorMessage)): ?>
      <div id="phpErrorMessage" data-title="<?= htmlspecialchars($errorTitle) ?>" data-message="<?= htmlspecialchars($errorMessage) ?>" style="display: none;"></div>
    <?php endif; ?>

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
