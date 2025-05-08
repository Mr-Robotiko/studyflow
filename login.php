<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/user-classes/user.php";

define('SESSION_TIMEOUT', 600);

$alert = "Bitte melde dich an, um fortzufahren";
$host = "localhost";
$dbname = "studycal";
$databaseUser = "Admin";
$pass = "rH!>|r'h6.XXlN.=2}A_#u[gxvhU3q;";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $databaseUser, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Verbindung erfolgreich.<br>";

    $username = trim($_POST["username"] ?? '');
    $password_plain = $_POST["password"] ?? '';

    if (empty($username) || empty($password_plain)) {
        $alert = "Bitte alle Felder ausfüllen.";
    } else {
        $query = $conn->prepare("SELECT * FROM user WHERE Username = :username");
        $query->execute(["username" => $username]);
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password_plain, $row['Password'])) {
            $user = new User();
            $user->setUserName($row['Username']);
            $user->setPassword($row['Password']);
            $user->setSecurityPassphrase($row['Securitypassphrase']);
            $user->setName($row['Name']);
            $user->setSurname($row['Surname']);
            $user->setCalendarfile($row['Calendarfile'] ?? null);

            $_SESSION['user_data'] = [
                'username' => $user->getUserName(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'securityPassphrase' => $user->getSecurityPassphrase(),
                'calendarfile' => $user->getCalendarfile()
            ];

            $_SESSION['eingeloggt'] = true;
            $_SESSION['LAST_ACTIVITY'] = time();

            header("Location: start.php");
            exit;
        } else {
            $alert = "Login fehlgeschlagen";
        }
    }

    echo $alert;
} catch (PDOException $e) {
    echo "PDO-Fehler: " . $e->getMessage();
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
    <button onclick="input_to_var()" type="submit" class="btn">Einloggen</button>
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

</body>
</html>
