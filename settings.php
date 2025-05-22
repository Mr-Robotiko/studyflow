<?php
require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";

SessionManager::start();

$userData = SessionManager::getUserData();

if (!$userData) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StudyCal</title>
    <link rel="stylesheet" type="text/css" href="/system/style/main.css" />
    <script type="text/javascript" src="system/javascript/main.js"></script>
    <script src="system/javascript/inactivityTimer.js" defer></script>
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
                <a href="#" onclick="toggleDropdown(event, 'dropdown-profilbild')">
                  <img src="images/rusty.jpg" alt="Profilbild" class="profil-img" />
                </a>
                <div class="dropdown-content" id="dropdown-profilbild">
                  <a href="start.php">Kalender</a>
                  <a href="logout.php">Logout</a>
                </div>
              </li>
            </a>
          </li>
        </ul>
        <ul class="nav">
          <li><span class="arrow left"></span></li>
          <li><p>Kalenderwoche 42</p></li>
          <li><span class="arrow right"></span></li>
          <li><p>Username </p></li>
            <li>
              <button>
                Neuer Eintrag
              </button>
            </li> 
        </ul>
      </div>
      <div class="settings">
        <div class="lernideal">
          <h2>Einstellungen</h2>
          <label class="switch">
            <input type="checkbox" id="darkModeToggle" />
            <span class="s"></span>
          </label>
          <span id="mode-label">☀️</span>
          <p class="settings">Lernideal</p>
          <div class="slidecontainer"> 
            <input type="range" min="1" max="5" id="slider"/>
          </div>
          <div id="subtitle">30 min 45 min 60 min 120 min 180 min</div>
          <div> 
            <p>Zeitzone:</p>
            <input type="dateime-local" id="zeitzone"> 
          </div> 
          
          <button id="profilbild_aendern">Profilbild ändern</button>
          <button id="password_aendern">Passwort ändern</button>
          <button id="konto-loeschen">Konto löschen</button> 
          
          
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
