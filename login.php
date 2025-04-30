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
  <link rel="stylesheet" href="/system/style/main.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<a href="index.html" class="zurueck-button" title="Zurück zur Startseite">
  <i class="fas fa-arrow-left"></i>
</a>
  <div class="container">
    <div class="logo">
      <img src="images/Logo.png" alt="StudyCal Logo" style="width: 20%; height: 20%;" />
    </div>
    <h1 class="title" style="height: 60px; text-align: center; line-height: 50px;">Willkommen zurück</h1>
    <form action="#" method="post" style="max-width: 400px; margin: 0 auto; text-align: left;">
      <div style="margin-bottom: 20px;">
        <label for="username" style="font-weight: bold; display: block; margin-bottom: 8px;">Benutzername</label>
        <input type="text" id="username" name="username" placeholder="Benutzername eingeben" required
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>
      <div style="margin-bottom: 20px;">
        <label for="password" style="font-weight: bold; display: block; margin-bottom: 8px;">Passwort</label>
        <input type="password" id="password" name="password" placeholder="Passwort eingeben" required
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>
      <div class="buttons">
        <button onclick="input_to_var()" type="submit" class="btn">Login</button>
        <?php if (!empty($fehlermeldung)) echo "<p style='color:red;'>$fehlermeldung</p>"; ?>
      </div>
    </form>
  </div>
  <?php if (!empty($hashedPassword)): ?>
        <h3>Gehashtes Passwort (SHA-256):</h3>
        <p style="word-break: break-all; font-family: monospace;"><?= htmlspecialchars($hashedPassword) ?></p>
    <?php endif; ?>
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