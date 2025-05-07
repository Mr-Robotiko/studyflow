<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal - Registrierung</title>
  <script type="text/javascript" src="system/javascript/register.js"></script>
  <link rel="stylesheet" href="system/style/prelogin.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
  <h1>Registriere dich bei StudyCal</h1>
</header>

<div class="main">
  <p class="subtitle">Fülle das Formular aus, um dich zu registrieren</p>

  <form action="register.php" method="post">
    <!-- Vorname -->
    <div>
      <label for="vorname">Vorname</label>
      <input class="input" type="text" id="vorname" name="vorname" placeholder="Vorname eingeben" required />
    </div>

    <!-- Nachname -->
    <div>
      <label for="nachname">Nachname</label>
      <input class="input" type="text" id="nachname" name="nachname" placeholder="Nachname eingeben" required />
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
      <input class="input" type="password" id="passwordwdh" name="passwordwdh" placeholder="Passwort wiederholen" required />
    </div>

    <!-- Sicherheitsfrage -->
    <div>
      <label for="securityanswer">Sicherheitsfrage</label>
      <p style="margin-bottom: 10px; font-style: italic;">Wie lautet der Name deines ersten Haustiers?</p>
      <input class="input" type="text" id="securityanswer" name="securityanswer" placeholder="Antwort eingeben" required />
    </div>

    <!-- Button -->
    <div class="buttons">
      <button onclick="input_to_var()" type="submit" class="btn">Registrieren</button>
      <a href="index.html" class="zurueck-button" title="Zurück zur Startseite">
    <i class="fas fa-arrow-left"></i>Zurück
  </a>
    </div>
  </form>
</div>

<footer>
  &copy; 2025 StudyCal. Alle Rechte vorbehalten.
</footer>

</body>
</html>



<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/user-classes/user.php";

$alert = "";
$host = "localhost";
$dbname = "studycal";
$databaseUser = "Admin";
$pass = "rH!>|r'h6.XXlN.=2}A_#u[gxvhU3q;";

try {
  $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $databaseUser, $pass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  echo "Verbindung erfolgreich.<br>";

  $username = "TestUser_" . rand(1000, 9999);
  $name = "Max";
  $surname = "Mustermann";
  $securitypassphrase = "Affe";
  $calenderfile = null;
  $password_plain = "test1234";
  $password = password_hash($password_plain, PASSWORD_DEFAULT);

  $query = $conn->prepare("INSERT INTO user (Username, Name, Surname, Securitypassphrase, Calendarfile, Password)
                           VALUES (:username, :name, :surname, :securitypassphrase, :calenderfile, :password)");

  $query->bindParam(':username', $username);
  $query->bindParam(':name', $name);
  $query->bindParam(':surname', $surname);
  $query->bindParam(':securitypassphrase', $securitypassphrase);
  $query->bindParam(':calenderfile', $calenderfile);
  $query->bindParam(':password', $password);

  if ($query->execute()) {
      echo "Einfügen erfolgreich!";
  } else {
      echo "Fehler beim Einfügen:<br>";
      print_r($query->errorInfo());
  }

} catch (PDOException $e) {
  echo "PDO-Fehler: " . $e->getMessage();
}


      // if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //     $username = trim($_POST["username"] ?? '');
    //     $password = $_POST["password"] ?? '';
    //     $passwordrep = $_POST["passwordrep"] ?? '';
    //     $securitypassphrase = trim($_POST["securitypassphrase"] ?? '');

    //     $user = new User();
    //     $user->setUserName($username);
    //     $user->setPassword($password);
    //     $user->setSecurityPassphrase($securitypassphrase);

    //     if (empty($username) || empty($password) || empty($passwordrep) || empty($securitypassphrase)) {
    //         $alert = "Bitte alle Felder ausfüllen.";
    //     } elseif ($password !== $passwordrep) {
    //         $alert = "Passwörter stimmen nicht überein.";
    //     } else {
    //         // Prüfen ob Benutzername bereits existiert
    //         $check = $pdo->prepare("SELECT Username FROM user WHERE Username = :username");
    //         $check->execute(["username" => $username]);

    //         if ($check->fetch()) {
    //             $alert = "Benutzername bereits vergeben.";
    //         } else {
    //             $hash = password_hash($password, PASSWORD_DEFAULT);

    //             $stmt = $pdo->prepare("INSERT INTO user (Username, Password, Securitypassphrase) 
    //                                    VALUES (:username, :password, :securitypassphrase)");
    //             $stmt->execute([
    //                 "username" => $username,
    //                 "password" => $hash,
    //                 "securitypassphrase" => $securitypassphrase
    //             ]);

    //             $alert = "Registrierung erfolgreich. <a href='login.php'>Jetzt einloggen</a>";
    //         }
    //     }
    // }
?>


    

