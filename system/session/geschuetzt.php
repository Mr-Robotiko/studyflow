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