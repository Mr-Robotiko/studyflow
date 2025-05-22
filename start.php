<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";
require_once "system/database-classes/database.php";

// Beispielstruktur – ersetzen mit realem Laden aus Datei/DB:
require_once "system/calendar-classes/day.php";
require_once "system/calendar-classes/entry.php";

SessionManager::start();

$weekNumber = date('W');
$userData = SessionManager::getUserData();

$showEntryForm = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_entry_form']));

// ✅ Fehlervermeidung: Initialisierung
$success = false;
$errors = [];

if (!$userData) {
    header("Location: login.php");
    exit;
}

// Kalenderdaten laden (hier mit Dummy-Daten als Beispiel)
$days = [];

// Dummy-Eintrag (optional entfernen, wenn du später dynamisch lädst)
$day = new Day("2025-05-22");
$entry = new Entry("Mathe Klausur", "Kapitel 1-3 wiederholen", "10:00", "12:00");
$day->addEntry($entry);
$days["2025-05-22"] = $day;

$user = new User();
$user->setUserName($userData['username']);
$user->setName($userData['name']);
$user->setSurname($userData['surname']);
$user->setSecurityPassphrase($userData['securityPassphrase'] ?? '');
$user->setCalendarfile($userData['calendarfile'] ?? null);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) { 
  try {
      if ($user->deleteFromDatabase()) {
          SessionManager::destroy();
          header("Location: login.php");
          exit;
      } else {
          echo "<p style='color:red;'>Löschen fehlgeschlagen. Bitte versuche es erneut.</p>";
      }
  } catch (Exception $e) {
      echo "<p style='color:red;'>Fehler beim Löschen: " . htmlspecialchars($e->getMessage()) . "</p>";
  }
}

?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StudyCal</title>
    <link rel="icon" href="images/Logo_favicon.png" />
    <link rel="stylesheet" type="text/css" href="system/style/main.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="system/javascript/inactivityTimer.js" defer></script>
    <script src="system/javascript/main.js"></script>
  </head>
  <body>
    <div class="body">
      <div class="head">
        <ul class="header">
          <li>
            <a href="start.php" id="logo">
              <img src="images/logo.png" alt="Logo" class="logo-img" />
            </a>
          </li>
          <li>
            <a id="name" href="#">StudyCal</a>
          </li>
          <li class="dropdown">
            <a href="#" onclick="toggleDropdown(event, 'dropdown-profilbild')">
              <img src="images/rusty.jpg" alt="Profilbild" class="profil-img" />
            </a>
            <div class="dropdown-content" id="dropdown-profilbild">
              <a href="#" onclick="showSettings(event)">Einstellungen</a>
              <a href="#" onclick="showCalendar()">Kalender</a>
              <a href="logout.php">Logout</a>
            </div>
          </li>
        </ul>
        <ul class="nav">
          <li><span class="arrow left"></span></li>
          <li><p>Kalenderwoche <?php echo $weekNumber ?></p></li>
          <li><span class="arrow right"></span></li>
          <li><p>Schön, dich zu sehen, <?php echo $user->getUserName() ?></p></li>
          <li class="dropdown">
            <button id="openEntryPopup">Neuer Eintrag</button>
            <div class="dropdown-content" id="dropdown-menu">
              <a href="entry.html">Klausur</a>
            </div>
          </li>
        </ul>
      </div>

      <?php if ($showEntryForm): ?>
      <div class="entry">
        <?php require_once "entry.php"; ?>
      </div>
      <?php else: ?>
      <div class="kalender">
        <h2>Kalender</h2>
        <div class="kalender-grid">
          <?php if (empty($days)): ?>
          <div class="termin frei">Keine Einträge vorhanden</div>
          <?php else: ?>
          <?php foreach ($days as $date => $day): /** @var Day $day **/ ?>
          <div class="kalender-tag">
            <h3><?= htmlspecialchars($date) ?></h3>
            <?php foreach ($day->getEntries() as $entry): /** @var Entry $entry **/ ?>
            <div class="termin">
              <strong><?= htmlspecialchars($entry->getTitle()) ?></strong><br />
              <?= nl2br(htmlspecialchars($entry->getDescription())) ?><br />
              <small><?= htmlspecialchars($entry->getStartTime()) ?> – <?= htmlspecialchars($entry->getEndTime()) ?></small>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="settings" style="display: none;">
        <div class="lernideal">
          <h2>Einstellungen</h2>

          <label class="switch">
            <input type="checkbox" id="darkModeToggle" />
            <span class="s"></span>
          </label>
          <span id="mode-label">☀️</span>

          <p class="settings">Lernideal</p>
          <div class="slidecontainer">
            <input type="range" min="1" max="5" id="slider" />
          </div>
          <div id="subtitle">30 min 45 min 60 min 120 min 180 min</div>

          <div>
            <p>Zeitzone:</p>
            <input type="datetime-local" id="zeitzone" />
          </div>
          <button id="password_aendern"><a href="password.php">Passwort ändern</a></button>
          <form method="POST" onsubmit="return confirm('Bist du sicher, dass du dein Konto löschen willst?');">
            <button type="submit" name="delete_account" id="konto-loeschen">Konto löschen</button>
          </form>
          <br /><br />
          <button onclick="showCalendar()">Zurück zum Kalender</button>
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
      <?php endif; ?>
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

