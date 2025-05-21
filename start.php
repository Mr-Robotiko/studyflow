<?php
require_once "system/handlers/entry-popup-handler.php";
//session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/user-classes/user.php";

$today = new DateTime();            
$weekNumber = $today->format("W");  


// define('SESSION_TIMEOUT', 600); // 10 Minuten

// if (!isset($_SESSION["eingeloggt"]) || !$_SESSION["eingeloggt"]) {
//     header("Location: login.php");
//     exit;
// }

// // Session Timeout prüfen
// if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT)) {
//     session_unset();
//     session_destroy();
//     header("Location: login.php?timeout=1");
//     exit;
// }
// $_SESSION['LAST_ACTIVITY'] = time();

// // User aus Session-Daten neu erstellen
// $data = $_SESSION['user_data'];
$user = new User();
$user->setUserName($data['username']);
$user->setName($data['name']);
$user->setSurname($data['surname']);
$user->setSecurityPassphrase($data['securityPassphrase']);
$user->setCalendarfile($data['calendarfile'] ?? null);

// Jetzt kannst du damit arbeiten
echo "Willkommen, " . $user->getName() . " " . $user->getSurname();
?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StudyCal</title>
    <link rel="stylesheet" type="text/css" href="system/style/main.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="system/javascript/main.js"></script>
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
          <li>
            <a id="name"> StudyCal </a>
          </li>
          <li>
            <a href="#home" id="profilbild">
              <li class="dropdown">
                <a href="#" id="profilbild" onclick="toggleDropdown(event, dropdown_profilbild)">
                  <img
                    src="images/rusty.jpg"
                    alt="Profilbild"
                    class="profil-img"
                  />
                </a>
                <div class="dropdown-content" id="dropdown-menu">
                  <a href="settings.html">Einstellungen</a>
                </div>
              </li>
            </a>
          </li>
        </ul>
        <ul class="nav">
          <li><span class="arrow left"></span></li>
          <li><p>Kalenderwoche <?php echo $weekNumber?></p></li>
          <li><span class="arrow right"></span></li>
          <li><p>Schön, dich zu sehen, <?php echo $user->getUserName()?></p></li>
          <li class="dropdown">
            <button id="openEntryPopup">Neuer Eintrag</button>
            <div class="dropdown-content" id="dropdown-menu">
             <a href="entry.html">Klausur</a>
        </div>
      </li>
        </ul>
      </div>

      <div class="kalender">
        <h2>Kalender</h2>
        <div class="kalender-grid">
          <div class="termin">
            Mathe-Vorlesung (Lieben Wir)<br /><small>08:00 - 09:30</small>
          </div>
          <div class="termin">
            BWL-Seminar (Super spannend)<br /><small>10:00 - 11:30</small>
          </div>
          <div class="termin">
            Lernsession<br /><small>14:00 - 15:30</small>
          </div>
          <div class="termin frei">Frei</div>
          <div class="termin frei">Frei</div>
          <div class="termin">Abgabe Projekt<br /><small>23:59</small></div>
          <div class="termin frei">Frei</div>
        </div>
      </div>
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
      <a href="datenschutz.html"> 
        <p>Datenschutz</p>
      </a> 
    </div>
    <style>
      .show {
        display: block;
      }
    </style>

    <!-- Overlay für Entry-Popup mit PHP-Formular -->
<div id="entryPopupOverlay" class="popup-overlay" style="display: none;">
  <div class="popup-content">
    <button id="closeEntryPopup">&times;</button>
    
    <div class="entry">
      <h2>Neuer Eintrag</h2>

      <?php if ($success): ?>
        <p style="color: green;">Eintrag erfolgreich gespeichert!</p>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <ul style="color: red;">
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <form id="entryForm" method="post" action="start.php">
        <div class="klausur-grid">
          <div class="klausur">
            <label for="klausur">Klausur</label><br />
            <input
              type="text"
              id="klausur"
              name="klausur"
              class="entry_h"
              value="<?= htmlspecialchars($_POST['klausur'] ?? '') ?>"
              required
            />
          </div>
          <div class="dates">
            <label for="anfangsdatum">Anfangsdatum:</label><br />
            <input
              type="date"
              id="anfangsdatum"
              name="anfangsdatum"
              class="entry_be"
              value="<?= htmlspecialchars($_POST['anfangsdatum'] ?? '') ?>"
              required
            /><br />
            <label for="endungsdatum">Endungsdatum:</label><br />
            <input
              type="date"
              id="endungsdatum"
              name="endungsdatum"
              class="entry_end"
              value="<?= htmlspecialchars($_POST['endungsdatum'] ?? '') ?>"
              required
            />
          </div>
          <div class="notes">
            <label for="notizen">Notizen:</label><br />
            <textarea
              id="notizen"
              name="notizen"
              rows="4"
              maxlength="1000"
              class="notes"
            ><?= htmlspecialchars($_POST['notizen'] ?? '') ?></textarea>
          </div>
        </div>
        <button type="submit" name="save_entry" value="1">Speichern</button>
      </form>
    </div>
  </div>
</div>

  </body>
</html>
