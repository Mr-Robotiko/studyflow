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
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="Studycal Login" content="Studycal Login">
</head>
<body>
    <h1>Willkommen zurück</h1>

    <form action="" method="post">
        <label for="username">Benutzername</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Passwort</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
        <button type="reset">Zurücksetzen</button>
    </form>

    <?php if (!empty($hashedPassword)): ?>
        <h3>Gehashtes Passwort (SHA-256):</h3>
        <p style="word-break: break-all; font-family: monospace;"><?= htmlspecialchars($hashedPassword) ?></p>
    <?php endif; ?>
</body>
</html>
