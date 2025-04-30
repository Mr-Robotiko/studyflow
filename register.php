<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal - Registrierung</title>
  <script type="text/javascript" src="system/javascript/register.js"></script>
  <link rel="stylesheet" href="/system/style/main.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="display: flex; justify-content: center; flex-direction: column; min-height: 100vh;">
  <a href="index.html" class="zurueck-button" title="Zurück zur Startseite">
    <i class="fas fa-arrow-left"></i>
  </a>
  <div class="container">

    <h1 class="title" style="height: 60px; text-align: center; line-height: 50px;">Willkommen bei StudyCal</h1>

    <form action="#" method="post" style="max-width: 500px; margin: 0 auto; text-align: left;">
      <!-- Benutzername -->
      <div style="margin-bottom: 20px;">
        <label for="username" style="font-weight: bold; display: block; margin-bottom: 8px;">Benutzername</label>
        <input type="text" id="username" name="username" placeholder="Benutzername eingeben" required 
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>

      <!-- Passwort -->
      <div style="margin-bottom: 20px;">
        <label for="password" style="font-weight: bold; display: block; margin-bottom: 8px;">Passwort</label>
        <input type="password" id="password" name="password" placeholder="Passwort eingeben" required 
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>

      <!-- Passwort wiederholen -->
      <div style="margin-bottom: 20px;">
        <label for="passwordwdh" style="font-weight: bold; display: block; margin-bottom: 8px;">Passwort wiederholen</label>
        <input type="password" id="passwordwdh" name="passwordwdh" placeholder="Passwort wiederholen" required 
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>

      <!-- Sicherheitsfrage -->
      <div style="margin-bottom: 20px;">
        <label for="securityanswer" style="font-weight: bold; display: block; margin-bottom: 8px;">Sicherheitsfrage:</label>
        <p style="margin-bottom: 10px; font-style: italic;">Wie lautet der Name deines ersten Haustiers?</p>
        <input type="text" id="securityanswer" name="securityanswer" placeholder="Antwort auf Sicherheitsfrage eingeben" required 
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>

      <!-- Button -->
      <div class="buttons" style="justify-content: center;">
        <button onclick="input_to_var()" type="submit" class="btn">Registrieren</button>
      </div>
    </form>
  </div>
</body>
</html>



<?php
require_once("/system/user_classes/user.php");

$alert = "";
$host = "localhost";
$dbname = "studycal";
$databaseUser = "Admin";
$pass = "rH!>|r'h6.XXlN.=2\"}A_#u[gxvhU3q;";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $databaseUser, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST["username"] ?? '');
        $password = $_POST["password"] ?? '';
        $passwordrep = $_POST["passwordrep"] ?? '';
        $securitypassphrase = trim($_POST["securitypassphrase"] ?? '');

        $user = new User();
        $user->setUserName($username);
        $user->setPassword($password);
        $user->setSecurityPassphrase($securitypassphrase);

        if (empty($username) || empty($password) || empty($passwordrep) || empty($securitypassphrase)) {
            $alert = "Bitte alle Felder ausfüllen.";
        } elseif ($password !== $passwordrep) {
            $alert = "Passwörter stimmen nicht überein.";
        } else {
            // Prüfen ob Benutzername bereits existiert
            $check = $pdo->prepare("SELECT Username FROM user WHERE Username = :username");
            $check->execute(["username" => $username]);

            if ($check->fetch()) {
                $alert = "Benutzername bereits vergeben.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO user (Username, Password, Securitypassphrase) 
                                       VALUES (:username, :password, :securitypassphrase)");
                $stmt->execute([
                    "username" => $username,
                    "password" => $hash,
                    "securitypassphrase" => $securitypassphrase
                ]);

                $alert = "Registrierung erfolgreich. <a href='login.php'>Jetzt einloggen</a>";
            }
        }
    }
} catch (PDOException $e) {
    $alert = "Fehler bei der Datenbankverbindung: " . $e->getMessage();
}
?>
