<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";

SessionManager::start();

$data = SessionManager::getUserData();
if (!$data) {
    header("Location: login.php");
    exit;
}

$user = new User();
$user->setUserName($data['username']);
$user->setName($data['name']);
$user->setSurname($data['surname']);
$user->setSecurityPassphrase($data['securityPassphrase']);
$user->setCalendarfile($data['calendarfile'] ?? null);

$weekNumber = date('W');

$showEntryForm = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_entry_form']));

$days = $_SESSION['days'] ?? [];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>StudyCal</title>
  <link rel="stylesheet" href="system/style/main.css" />
  <style>
    .debug { background: #fee; padding: 0.5em; margin: 1em 0; border: 1px solid #f00; }
  </style>
</head>
<body>
  <div class="body">
    <div class="head">
      <ul class="header">
        <li><a href="#home" id="logo"><img src="images/logo.png" class="logo-img" alt="Logo"/></a></li>
        <li><a id="name">StudyCal</a></li>
        <li class="dropdown">
          <a href="#" onclick="toggleDropdown(event)">
            <img src="images/rusty.jpg" alt="Profilbild" class="profil-img"/>
          </a>
          <div class="dropdown-content" id="dropdown-menu">
            <a href="settings.html">Einstellungen</a>
            <a href="logout.php">Abmelden</a>
          </div>
        </li>
      </ul>
      <ul class="nav">
        <li><span class="arrow left"></span></li>
        <li><p>Kalenderwoche <?= htmlspecialchars($weekNumber) ?></p></li>
        <li><span class="arrow right"></span></li>
        <li><p>Schön, dich zu sehen, <?= htmlspecialchars($user->getUserName()) ?></p></li>
        <li>
          <form method="post" style="display:inline;">
            <button name="show_entry_form" value="1">Neuer Eintrag</button>
          </form>
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
                    <strong><?= htmlspecialchars($entry->getTitle()) ?></strong><br>
                    <?= nl2br(htmlspecialchars($entry->getDescription())) ?><br>
                    <small><?= htmlspecialchars($entry->getStartTime()) ?> – <?= htmlspecialchars($entry->getEndTime()) ?></small>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
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
          <input type="checkbox" id="Task1" /><label for="Task1"> Labor-Bericht</label><br/>
          <input type="checkbox" id="Task2" /><label for="Task2"> Zusammenfassung BWL</label><br/>
          <input type="checkbox" id="Task3" /><label for="Task3"> RSA-Verfahren</label><br/><br/>
        </div>
      </div>
    </div>
  </div>

  <div class="footer"><p>Datenschutz</p></div>

  <script src="system/javascript/main.js"></script>
</body>
</html>
