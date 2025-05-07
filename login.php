<?php
session_start();

define('SESSION_TIMEOUT', 600); // 10 min

if (isset($_SESSION["eingeloggt"]) && $_SESSION["eingeloggt"] === true) {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit;
    }
    $_SESSION['LAST_ACTIVITY'] = time();
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
    <p class="subtitle">Bitte melde dich an, um fortzufahren</p>

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
    <a href="passwort.html" class="forgot-password-link">Passwort vergessen?</a>
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

<?php
$hashedPassword = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    $hashedPassword = hash("sha256", $password);

    echo "Benutzer: $username<br>";
    echo "Gehashtes Passwort: $hashedPassword";
}
?>

<?php
session_start();

define('SESSION_TIMEOUT', 600); // 10 Minuten

$host = 'localhost';
$db = 'studycal_db';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$fehlermeldung = "";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    die("Verbindung fehlgeschlagen: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM benutzer WHERE username = :username");
    $stmt->execute(["username" => $username]);
    $nutzer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nutzer && password_verify($password, $nutzer["passwort"])) {
        $_SESSION["eingeloggt"] = true;
        $_SESSION["nutzername"] = $nutzer["username"];
        $_SESSION['LAST_ACTIVITY'] = time(); // Timeout-Start
        header("Location: geschuetzt.php");
        exit;
    } else {
        $fehlermeldung = "Benutzername oder Passwort ist falsch.";
    }
}
?>