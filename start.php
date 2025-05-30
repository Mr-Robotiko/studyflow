<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "system/session-classes/session-manager.php";
require_once "system/user-classes/user.php";
require_once "system/database-classes/database.php";
require_once "system/calendar-classes/day.php";
require_once "system/calendar-classes/entry.php";

require_once "system/user-classes/account-manager.php";

require_once "system/session-classes/user-session.php";

$lernzeitMinuten = [
  0 => "30 min",
  1 => "45 min",
  2 => "60 min",
  3 => "120 min",
  4 => "180 min",
];

SessionManager::start();

$userSession = new UserSession();
$user = $userSession->getUser();
$userId = $user->getId();
$ideal = $user->getLernideal();

try {
  $configPath = __DIR__ . "/config/configuration.csv";
  $database = new Database($configPath);
  $pdo = $database->getConnection();
} catch (Exception $e) {
  die("Fehler bei der Datenbankverbindung: " . htmlspecialchars($e->getMessage()));
}

$accountManager = new AccountManager($user, $pdo);

$errors = [];
$success = false;

// -------------------- NEU: Kalenderwoche aus GET oder aktuelle ------------------
$week = isset($_GET['week']) ? intval($_GET['week']) : intval(date('W'));
$year = date('Y');

// Start und Ende der Woche bestimmen (Montag bis Sonntag)
$startDate = new DateTime();
$startDate->setISODate($year, $week);
$endDate = clone $startDate;
$endDate->modify('+6 days');

$startDateStr = $startDate->format('Y-m-d');
$endDateStr = $endDate->format('Y-m-d');

// -------------------- NEU: Einträge aus DB laden für den Zeitraum ------------------
$stmt = $pdo->prepare("
    SELECT 
        entry.EntryID, 
        event.Eventname, 
        event.Begindate, 
        event.Enddate, 
        event.Note, 
        entry.Daydate,
        entry.Begintime AS StartTime,
        entry.Endtime AS EndTime
    FROM entry
    JOIN event ON entry.EventID = event.EventID
    WHERE entry.DayDate BETWEEN :startDate AND :endDate
    AND event.UserID = :userId
    ORDER BY entry.DayDate, entry.Begintime
");

$stmt->execute([':startDate' => $startDateStr, ':endDate' => $endDateStr, ':userId' => $userId]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------- NEU: Day-Objekte für jeden Tag erstellen ------------------
$days = [];
for ($d = strtotime($startDateStr); $d <= strtotime($endDateStr); $d = strtotime('+1 day', $d)) {
    $date = date('Y-m-d', $d);
    $days[$date] = new Day($date);
}

// -------------------- NEU: Entries zu den Tagen hinzufügen ------------------
foreach ($results as $row) {
  $entry = new Entry(
      $row['EntryID'],
      $row['Eventname'],  // korrekter Spaltenname
      $row['StartTime'],
      $row['EndTime'],
      $row['Note']        // 'Note' statt 'Description'
  );
  $days[$row['Daydate']]->addEntry($entry);
}


// -------------------- DEIN BEHÄNDLING DER FORMULARE ---------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  if (isset($_POST['save_settings'])) {
    $litValue = intval($_POST['lit_value']);
    $darkMode = intval($_POST['dark_mode']);

    try {
      $stmt = $pdo->prepare("UPDATE user SET ILT = :ILT, Mode = :mode WHERE UserID = :userId");
      $stmt->bindParam(':ILT', $litValue, PDO::PARAM_INT);
      $stmt->bindParam(':mode', $darkMode, PDO::PARAM_INT);
      $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();
  
      $user->loadUserDataFromDatabase($pdo);
      $ideal = $litValue;
  
      echo "<p style='color:green;'>Einstellungen wurden gespeichert.</p>";
    } catch (Exception $e) {
      echo "<p style='color:red;'>Fehler beim Speichern der Einstellungen: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
  }

  if (isset($_POST['delete_account'])) {
      try {
          if ($accountManager->deleteAccount()) {
              SessionManager::destroy();
              header("Location: login.php");
              exit;
          } else {
              echo "<p style='color:red;'>Löschen fehlgeschlagen. Bitte versuche es erneut.</p>";
          }
      } catch (Exception $e) {
          echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
      }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_entry'])) {
    header('Content-Type: application/json');

    $errors = [];
    $success = false;

    $eventName = trim($_POST['klausur'] ?? '');
    $beginDate = $_POST['anfangsdatum'] ?? '';
    $endDate = $_POST['endungsdatum'] ?? '';
    $note = trim($_POST['notizen'] ?? '');

    if (empty($eventName)) {
        $errors[] = "Bitte gib einen Titel für den Eintrag an.";
    }
    if (empty($beginDate) || empty($endDate)) {
        $errors[] = "Bitte gib Anfangs- und Enddatum an.";
    } elseif ($beginDate > $endDate) {
        $errors[] = "Das Anfangsdatum darf nicht nach dem Enddatum liegen.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
            INSERT INTO event (UserID, Eventname, Note, Begindate, Enddate, EventSeverity)
            VALUES (:userId, :eventName, :note, :beginDate, :endDate, 0)
            ");
      
            $stmt->execute([
                ':userId' => $userId,
                ':eventName' => $eventName,
                ':note' => $note,
                ':beginDate' => $beginDate,
                ':endDate' => $endDate
            ]);
            $eventId = $pdo->lastInsertId();

            $period = new DatePeriod(
                new DateTime($beginDate),
                new DateInterval('P1D'),
                (new DateTime($endDate))->modify('+1 day')
            );

            $stmtEntry = $pdo->prepare("
                INSERT INTO entry (EventID, DayDate, Begintime, Endtime)
                VALUES (:eventId, :dayDate, '00:00:00', '23:59:59')
            ");

            foreach ($period as $dt) {
                $dayDate = $dt->format('Y-m-d');
                $stmtEntry->execute([
                    ':eventId' => $eventId,
                    ':dayDate' => $dayDate
                ]);
            }

            $success = true;
            exit;
        } catch (Exception $e) {
            $errors[] = "Fehler beim Speichern des Eintrags: " . $e->getMessage();
        }
    }

    // Falls Fehler aufgetreten
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}


}

$weekNumber = $week; // damit es in der Anzeige passt
$showEntryForm = isset($_POST['show_entry_form']);
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
  <body class="<?= $user->getDarkMode() ? 'dark-mode' : '' ?>">
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
        <!-- Settings Form -->
        <div class="settings" style="display: none;">
          <div class="lernideal">
            <h2>Einstellungen</h2>
            <form method="POST" action="start.php" onsubmit="updateSettingsHiddenFields();">
              <div>
                <label class="switch">
                  <input type="checkbox" id="darkModeToggle" <?= ($user->getDarkMode() ? 'checked' : '') ?> />
                  <span class="s"></span>
                </label>
                <span id="mode-label"><?= $user->getDarkMode() ? "🌙" : "☀️" ?></span>

                <p class="settings">Lernideal</p>
                <div class="slidecontainer">
                  <div class="slider-container">
                    <input
                      type="range"
                      min="0" max="4"
                      value="<?= htmlspecialchars($user->getLernideal() ?? 2) ?>"
                      step="1"
                      class="slider"
                      id="study-slider"
                    />
                  </div>
                  <div class="slider-labels">
                    <span>30 min</span>
                    <span>45 min</span>
                    <span>60 min</span>
                    <span>120 min</span>
                    <span>180 min</span>
                  </div>
                </div>
                <p id="selected-duration-label">Lernzeit: <span id="selected-duration"><?= $lernzeitMinuten[$ideal] ?></span></p>
                <!-- Hidden inputs -->
                <input type="hidden" name="lit_value" id="lit_value" value="<?= htmlspecialchars($user->getLernideal() ?? 2) ?>">
                <input type="hidden" name="dark_mode" id="dark_mode_value" value="<?= $user->getDarkMode() ? 1 : 0 ?>">
                <button type="submit" name="save_settings">Einstellungen speichern</button>
              </div>
            </form>

            <!-- Buttons -->
            <div>
              <!-- Passwort ändern -->
              <button id="password_aendern"><a href="password.php">Passwort ändern</a></button>

              <!-- Konto löschen: eigenes Form -->
              <form method="POST" onsubmit="return confirm('Bist du sicher, dass du dein Konto löschen willst?');">
                <button type="submit" name="delete_account" id="konto-loeschen">Konto löschen</button>
              </form>

              <br /><br />
              <button onclick="showCalendar()">Zurück zum Kalender</button>
            </div>
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

