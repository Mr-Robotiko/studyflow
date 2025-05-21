<?php
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
    <script src="system/javascript/main.js"></script>
    <style>
      .show {
        display: block;
      }
    </style>
  </body>
</html>
