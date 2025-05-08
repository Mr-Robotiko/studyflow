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
    // Eingaben holen
    $username = trim($_POST['username'] ?? '');
    $securitypassphrase = trim($_POST['securitypassphrase'] ?? '');
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (empty($username) || empty($securitypassphrase) || empty($newPassword) || empty($confirmPassword)) {
      echo "<script>alert('Bitte alle Felder ausfüllen.'); window.history.back();</script>";
      exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Die Passwörter stimmen nicht überein.'); window.history.back();</script>";
        exit;
    }

    // Sicherheitsfrage prüfen
    $stmt = $pdo->prepare("SELECT Securitypassphrase FROM user WHERE Username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('Benutzer existiert nicht.'); window.history.back();</script>";
        exit;
    }

    if (strcasecmp(trim($user['Securitypassphrase']), $securitypassphrase) !== 0) {
        echo "<script>alert('Sicherheitsantwort ist falsch.'); window.history.back();</script>";
        exit;
    }

    // Neues Passwort speichern
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE user SET Password = :password WHERE Username = :username");

    if ($update->execute(['password' => $hashedPassword, 'username' => $username])) {
        echo "<script>alert('Passwort erfolgreich geändert.'); window.location.href='login.php';</script>";
    } 
    
    else {
        echo "<script>alert('Fehler beim Speichern des neuen Passworts.'); window.history.back();</script>";
    }

  }

} catch (PDOException $e) {
  echo "<script>alert('Verbindungsfehler zur Datenbank: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Passwort Wiederherstellung - StudyCal</title>
  <link rel="stylesheet" href="system/style/newpassword.css" />
  <script defer src="system/javascript/newpassword.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

  <div class="container">
    <h1>Passwort Wiederherstellung</h1>
    <p class="subtitle"><?php echo $alert?></p>

    <form method="POST" action="password.php">
      <div class="form-group">
        <label for="username">Benutzername</label>
        <input type="text" name="username" placeholder="Benutzername" required>
      </div>

      <div class="form-group">
        <label for="securitypassphrase">Wie lautet der Name deines ersten Haustiers?</label>
        <input type="text" name="securitypassphrase" placeholder="Antwort auf die Sicherheitsfrage" required>
      </div>

      <div class="form-group">
        <label for="newPassword">Neues Passwort</label>
        <input type="password" name="newPassword" placeholder="Neues Passwort" required>
      </div>

      <div class="form-group">
        <label for="confirmPassword">Passwort bestätigen</label>
        <input type="password" name="confirmPassword" placeholder="Passwort bestätigen" required>
      </div>

      <button type="submit" class="btn">Passwort ändern</button>
    </form>
  </div>
</body>
</html>