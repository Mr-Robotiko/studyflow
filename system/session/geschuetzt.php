<?php
    session_start();
    if (!isset($_SESSION["eingeloggt"]) || $_SESSION["eingeloggt"] !== true) {
        header("Location: ../../login.php");
        exit;
}
?>
<!DOCTYPE html>
<html lang="de">
    <head><meta charset="UTF-8"><title>Geschützte Seite</title></head>
    <body>
        <h2>Hallo, <?= htmlspecialchars($_SESSION["nutzername"]) ?>!</h2>
        <p>Diese Seite ist geschützt.</p>
        <p><a href="logout.php">Logout</a></p>
    </body>
</html>