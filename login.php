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

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal - Login</title>
  <link rel="stylesheet" href="main.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="logo">
      <img src="images/Logo.png" alt="StudyCal Logo" style="width: 20%; height: 20%;" />
    </div>

    <h1 class="title" style="height: 60px; text-align: center; line-height: 60px;">Willkommen zurück</h1>


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
      </div>
    </form>
  </div>

  <?php if (!empty($hashedPassword)): ?>
        <h3>Gehashtes Passwort (SHA-256):</h3>
        <p style="word-break: break-all; font-family: monospace;"><?= htmlspecialchars($hashedPassword) ?></p>
    <?php endif; ?>
</body>
</html>
