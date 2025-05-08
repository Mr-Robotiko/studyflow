<?php
// Am Ende Löschen!!!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$alert = "Fülle das Formular aus, um dich zu registrieren";
$host = "localhost";
$dbname = "studycal";
$databaseUser = "Admin";
$pass = "rH!>|r'h6.XXlN.=2}A_#u[gxvhU3q;";

try {
  $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $databaseUser, $pass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  echo "Verbindung erfolgreich.<br>";

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Eingaben holen
    $username = trim($_POST["username"] ?? '');
    $name = trim($_POST["name"] ?? '');
    $surname = trim($_POST["surname"] ?? '');
    $password_plain = $_POST["password"] ?? '';
    $passwordrep = $_POST["passwordrep"] ?? '';
    $securitypassphrase = trim($_POST["securitypassphrase"] ?? '');
    $calenderfile = null;

    if (empty($username) || empty($password_plain) || empty($passwordrep) || empty($securitypassphrase) || empty($name) || empty($surname)) {
        $alert = "Bitte alle Felder ausfüllen.";
    } elseif ($password_plain !== $passwordrep) {
        $alert = "Passwörter stimmen nicht überein.";
    } else {
        $password = password_hash($password_plain, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT Username FROM user WHERE Username = :username");
        $check->execute(["username" => $username]);

        if ($check->fetch()) {
            $alert = "Benutzername bereits vergeben.";
        } else {
            $query = $conn->prepare("INSERT INTO user (Username, Name, Surname, Securitypassphrase, Calendarfile, Password)
                                     VALUES (:username, :name, :surname, :securitypassphrase, :calenderfile, :password)");

            $query->bindParam(':username', $username);
            $query->bindParam(':name', $name);
            $query->bindParam(':surname', $surname);
            $query->bindParam(':securitypassphrase', $securitypassphrase);
            $query->bindParam(':calenderfile', $calenderfile);
            $query->bindParam(':password', $password);

            if ($query->execute()) {
                $alert = "Einfügen erfolgreich!";
            } else {
                $alert = "Fehler beim Einfügen: ";
                print_r($query->errorInfo());
            }
        }
    }

    echo $alert;
}
} catch (PDOException $e) {
  echo "PDO-Fehler: " . $e->getMessage();
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



    

