<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";

SessionManager::start();

$weekNumber = 23; // Dynamisieren
$userData = SessionManager::getUserData();

if (!$userData) {
    header("Location: login.php");
    exit;
}

$user = new User();
$user->setUserName($userData['username']);
$user->setName($userData['name']);
$user->setSurname($userData['surname']);
$user->setSecurityPassphrase($userData['securityPassphrase'] ?? '');
$user->setCalendarfile($userData['calendarfile'] ?? null);
?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StudyCal</title>
    <link rel="icon" href="images/Logo.png">
    <link rel="stylesheet" type="text/css" href="system/style/main.css" />
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
            <a id="name"> StudyCal </a>
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
            </a>
          </li>
        </ul>
        <ul class="nav">
          <li><span class="arrow left"></span></li>
          <li><p>Kalenderwoche <?php echo $weekNumber?></p></li>
          <li><span class="arrow right"></span></li>
          <li><p>Schön, dich zu sehen, <?php echo $user->getUserName()?></p></li>
          <li>
            <a href="#" id="neuer_eintrag"> 
              <li class="drowpdown">
                <a href="#" id="neuer_eintrag" onclick="toggleDropdown(event, dropdown_eintrag)"> 
                  <button>
                    Neuer Eintrag
                  </button>
                </a>
                <div class="dropdown-content" id="dropdown-menu">
                  <a href="entry.html">Klausur</a>
                </div>
              </li>
            </a>
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
    <button id="konto-loeschen">Konto löschen</button>
    <br><br>
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
  </body>
</html>
