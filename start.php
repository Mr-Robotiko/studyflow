<?php
// start.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1) SessionManager laden und Session prüfen (Login + Timeout)
require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";

SessionManager::start();

// 2) User-Daten sicher aus Session holen
$data = SessionManager::getUserData();
if (!$data) {
    // Fallback, sollte durch start() aber nie nötig sein
    header("Location: login.php");
    exit;
}

// 3) User-Objekt befüllen
$user = new User();
$user->setUserName($data['username']);
$user->setName($data['name']);
$user->setSurname($data['surname']);
$user->setSecurityPassphrase($data['securityPassphrase']);
$user->setCalendarfile($data['calendarfile'] ?? null);

// 4) Kalenderwoche ermitteln
$weekNumber = date('W');

// 5) Steuert, ob das Formular für neuen Eintrag angezeigt wird
$showEntryForm = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_entry_form']));
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>StudyCal</title>
  <link rel="stylesheet" href="system/style/main.css" />
</head>
<body>
  <div class="body">
    <div class="head">
      <ul class="header">
        <li>
          <a href="#home" id="logo">
            <img src="images/logo.png" alt="Logo" class="logo-img" />
          </a>
        </li>
        <li><a id="name">StudyCal</a></li>
        <li class="dropdown">
          <a href="#" id="profilbild" onclick="toggleDropdown(event)">
            <img src="images/rusty.jpg" alt="Profilbild" class="profil-img" />
          </a>
          <div class="dropdown-content" id="dropdown-menu">
            <a href="settings.html">Einstellungen</a>
          </div>
        </li>
      </ul>

      <ul class="nav">
        <li><span class="arrow left"></span></li>
        <li><p>Kalenderwoche <?php echo $weekNumber ?></p></li>
        <li><span class="arrow right"></span></li>
        <li><p>Schön, dich zu sehen, <?php echo htmlspecialchars($user->getUserName()) ?></p></li>
        <li>
          <form method="post" style="display:inline;">
            <button type="submit" name="show_entry_form" value="1">Neuer Eintrag</button>
          </form>
        </li>
      </ul>
    </div>

    <?php if ($showEntryForm): ?>
      <!-- Formular für neuen Eintrag anzeigen (entry.php einbinden) -->
      <div class="entry">
        <?php require_once "entry.php"; ?>
      </div>
    <?php else: ?>
      <!-- Kalender anzeigen -->
      <div class="kalender">
        <h2>Kalender</h2>
        <div class="kalender-grid">
          <div class="termin">Mathe-Vorlesung (Lieben Wir)<br /><small>08:00 - 09:30</small></div>
          <div class="termin">BWL-Seminar (Super spannend)<br /><small>10:00 - 11:30</small></div>
          <div class="termin">Lernsession<br /><small>14:00 - 15:30</small></div>
          <div class="termin frei">Frei</div>
          <div class="termin frei">Frei</div>
          <div class="termin">Abgabe Projekt<br /><small>23:59</small></div>
          <div class="termin frei">Frei</div>
        </div>
      </div>
    <?php endif; ?>

    <div class="todaytodo">
      <div class="anstehend">
        <h1>Today</h1>
        <p>Content wird nicht eingedeutscht, Alexa!</p>
      </div>
      <div class="todo">
        <h1>To Do</h1>
        <div class="tasks">
          <input type="checkbox" id="Task1" name="Task1" value="Task" />
          <label for="Task1"> Labor-Bericht</label><br />
          <input type="checkbox" id="Task2" name="Task2" value="Task" />
          <label for="Task2"> Zusammenfassung BWL</label><br />
          <input type="checkbox" id="Task3" name="Task3" value="Task" />
          <label for="Task3"> RSA-Verfahren</label><br /><br />
        </div>
      </div>
    </div>
  </div>

  <div class="footer">
    <p>Datenschutz</p>
  </div>

  <script src="system/javascript/main.js"></script>
  <style>
    .show {
      display: block;
    }
  </style>
</body>
</html>
