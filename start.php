<?php
// Fehleranzeigen aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ------------------ AJAX: To-Do als erledigt markieren ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_todo'])) {
    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/system/session-classes/session-manager.php';
    require_once __DIR__ . '/system/session-classes/user-session.php';
    require_once __DIR__ . '/system/user-classes/user.php';
    require_once __DIR__ . '/system/database-classes/database.php';
    require_once __DIR__ . '/system/handler/todo-handler.php';

    SessionManager::start();
    $userSession = new UserSession();
    $user = $userSession->getUser();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Nicht angemeldet']);
        exit;
    }

    $userId = $user->getId();

    try {
        $database = new Database(__DIR__ . '/config/configuration.csv');
        $pdo = $database->getConnection();
        $todoHandler = new TodoHandler($pdo, $userId);

        $tid = intval($_POST['TID'] ?? 0);
        if ($tid <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Ungültige TID']);
            exit;
        }

        $result = $todoHandler->markAsDone($tid);
        echo json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// ------------------ Normale Verarbeitung ------------------
require_once __DIR__ . "/system/session-classes/session-manager.php";
require_once __DIR__ . "/system/user-classes/user.php";
require_once __DIR__ . "/system/database-classes/database.php";
require_once __DIR__ . "/system/user-classes/account-manager.php";
require_once __DIR__ . "/system/session-classes/user-session.php";
require_once __DIR__ . "/system/calendar-classes/day.php";
require_once __DIR__ . "/system/calendar-classes/entry.php";
require_once __DIR__ . "/system/handler/calendar-handler.php";
require_once __DIR__ . "/system/handler/todo-handler.php";

SessionManager::start();
$userSession = new UserSession();
$user = $userSession->getUser();
$userId = $user->getId();

$database = new Database(__DIR__ . "/config/configuration.csv");
$pdo = $database->getConnection();

// Userdaten aktualisieren
$user->loadUserDataFromDatabase($pdo);
$accountManager = new AccountManager($user, $pdo);

$todoHandler = new TodoHandler($pdo, $userId);
$calendarHandler = new CalendarHandler($pdo, $userId);

$errorsTodo = [];
$todoSaved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---------------- To-Do speichern ----------------
    if (isset($_POST['save_todo'])) {
        $todoText = trim($_POST['todoText'] ?? '');
        $todoEnddate = trim($_POST['todoEnddate'] ?? '');

        if ($todoText === '') $errorsTodo[] = "Bitte gib eine Bezeichnung für das To-Do an.";
        if ($todoEnddate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $todoEnddate)) {
            $errorsTodo[] = "Bitte gib ein gültiges Fälligkeitsdatum (JJJJ-MM-TT) an.";
        }

        if (empty($errorsTodo)) {
            $result = $todoHandler->saveNewTodo($todoText, $todoEnddate);
            if ($result['success']) {
                $todoSaved = true;
            } else {
                $errorsTodo[] = $result['error'] ?? 'Unbekannter Fehler';
            }
        }
    }

    // ---------------- Einstellungen speichern ----------------
    if (isset($_POST['save_settings'])) {
        $litValue = intval($_POST['lit_value']);
        $darkMode = intval($_POST['dark_mode']);
        try {
            $stmt = $pdo->prepare("UPDATE user SET ILT = :ILT, Mode = :mode WHERE UserID = :userId");
            $stmt->execute([':ILT' => $litValue, ':mode' => $darkMode, ':userId' => $userId]);
            $user->loadUserDataFromDatabase($pdo);
        } catch (Exception $e) {
            echo "<p style='color:red;'>Fehler beim Speichern: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // ---------------- Konto löschen ----------------
    if (isset($_POST['delete_account'])) {
        try {
            if ($accountManager->deleteAccount()) {
                SessionManager::destroy();
                header("Location: login.php");
                exit;
            } else {
                echo "<p style='color:red;'>Konto konnte nicht gelöscht werden.</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // ---------------- Eintrag speichern ----------------
    if (isset($_POST['save_entry'])) {
        header('Content-Type: application/json');

        $eventName = trim($_POST['klausur'] ?? '');
        $beginDate = $_POST['anfangsdatum'] ?? '';
        $endDate = $_POST['endungsdatum'] ?? '';
        $note = trim($_POST['notizen'] ?? '');

        $errors = [];

        if ($eventName === '') $errors[] = "Titel fehlt.";
        if ($beginDate === '' || $endDate === '') {
            $errors[] = "Bitte Datum angeben.";
        } elseif ($beginDate > $endDate) {
            $errors[] = "Anfangsdatum liegt nach Enddatum.";
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO event (UserID, Eventname, Note, Begindate, Enddate, EventSeverity)
                    VALUES (:userId, :eventName, :note, :beginDate, :endDate, 0)
                ");
                $stmt->execute([
                    ':userId'    => $userId,
                    ':eventName' => $eventName,
                    ':note'      => $note,
                    ':beginDate' => $beginDate,
                    ':endDate'   => $endDate
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
                    $stmtEntry->execute([':eventId' => $eventId, ':dayDate' => $dt->format('Y-m-d')]);
                }
                header('Location: start.php');
                exit;
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        header('Location: start.php');
        exit;
    }
}

// ---------------- To-Dos & Kalenderdaten laden ----------------
$todos = $todoHandler->getTodos();
$today = date('Y-m-d');

$week = isset($_GET['week']) ? intval($_GET['week']) : intval(date('W'));
$year = date('Y');

$days = $calendarHandler->getWeekEntries($week, $year);
$weekNumber = $week;
$showEntryForm = isset($_POST['show_entry_form']);
$ideal = $user->getLernideal();
$lernzeitMinuten = [
    0 => "30 min",
    1 => "45 min",
    2 => "60 min",
    3 => "120 min",
    4 => "180 min",
];
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
  <script src="system/javascript/main.js" defer></script>
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
        <li>
          <a href="?week=<?= max(1, $weekNumber - 1) ?>" title="Vorherige Woche">
            <span class="arrow left"></span>
          </a>
        </li>
        <li><p>Kalenderwoche <?= $weekNumber ?></p></li>
        <li>
          <a href="?week=<?= min(53, $weekNumber + 1) ?>" title="Nächste Woche">
            <span class="arrow right"></span>
          </a>
        </li>
        <li><p>Schön, dich zu sehen, <?= htmlspecialchars($user->getUserName()) ?></p></li>
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
      <!-- Hauptbereich: Kalender + Today/To-Do -->
      <div class="kalender">
        <h2>Kalender</h2>
        <div class="kalender-grid">
          <?php if (empty($days)): ?>
            <div class="termin frei">Keine Einträge vorhanden</div>
          <?php else: ?>
            <?php foreach ($days as $date => $day): /** @var Day $day **/ ?>
              <div class="kalender-tag">
                <h3><?= htmlspecialchars(date("D", strtotime($date))) ?></h3>
                <?php foreach ($day->getEntries() as $entry): /** @var Entry $entry **/ ?>
                  <div class="termin">
                    <?= nl2br(htmlspecialchars($entry->getDescription())) ?><br />
                    <small><?= htmlspecialchars($entry->getStartTime()) ?> – <?= htmlspecialchars($entry->getEndTime()) ?></small>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="todaytodo">
        <div class="anstehend">
          <h1>Today</h1>
          <?php
          $today = date('Y-m-d');

          // --- Heutige Kalendereinträge anzeigen ---
          if (isset($days[$today]) && count($days[$today]->getEntries()) > 0) {
              foreach ($days[$today]->getEntries() as $entry): ?>
                <ul class="heute-entry">
                  <li class="heute-entry">
                    <?= nl2br(htmlspecialchars($entry->getDescription())) ?>
                  </li>
                </ul>
              <?php endforeach;
          } else {
              echo "<p>Keine Termine für heute.</p>";
          }
          ?>
        </div>
                <!-- Fehlermeldungen To-Do -->
        <?php if (!empty($errorsTodo)): ?>
          <div style="color: red; margin: 10px 0;">
            <?php foreach ($errorsTodo as $err): ?>
              <div><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="tasks" id="todoList">
          <div class="todo-wrapper">
            <?php if (empty($todos)): ?>
              <div id="todo-empty-info" style="text-align: center; color: #888; padding: 20px;">
                Aktuell keine To-Dos vorhanden
              </div>
            <?php endif; ?>

            <div id="all-todos" class="todo-prio-group">
              <?php if (!empty($todos)): ?>
                <h3 class="prio-label">To-Do Übersicht</h3>
                <div class="prio-items">
                  <?php foreach ($todos as $t): ?>
                    <div class="todo-item">
                      <input type="checkbox" class="todo-checkbox" data-id="<?= $t['TID'] ?>" />
                      <span class="todo-text"><?= htmlspecialchars($t['TName']) ?> (bis <?= htmlspecialchars($t['TEnddate']) ?>)</span>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <h3 class="prio-label" style="display: none;">📝 To-Übersicht</h3>
                <div class="prio-items"></div>
              <?php endif; ?>
            </div>

            <div style="text-align: right;">
              <button id="openTodoPopup">Neues To-Do</button>
            </div>
          </div>
        </div>
      </div>
      </div>

      <!-- Settings Form (anfangs versteckt) -->
      <div class="settings", style="display: none;">
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

            <!-- Adminbereich-Button (noch ohne Funktion) -->
            <button id="adminbereich-button">Adminbereich</button>

            <br /><br />
            <button onclick="showCalendar()">Zurück zum Kalender</button>
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

  <!-- 1) ENTRY-POPUP Overlay (muss exakt so stehen!) -->
  <div id="entryPopupOverlay" class="popup-overlay" style="display: none;">
    <div class="popup-content">
      <button id="closeEntryPopup">&times;</button>

      <h2>Neuer Eintrag</h2>

      <?php if ($success): ?>
        <p class="popup-success-text">Eintrag erfolgreich gespeichert!</p>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <ul class="popup-error-list">
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <!-- 2) ENTRY-Formular mit Grid: Start und End nebeneinander -->
      <form id="entryForm" method="post" action="start.php">
        <div class="popup-form-grid">
          <!-- Klausur (ganze Breite) -->
          <div class="form-section span-two">
            <label for="klausur" class="popup-form-label">Klausur</label>
            <input
              type="text"
              id="klausur"
              name="klausur"
              class="popup-form-input"
              value="<?= htmlspecialchars($_POST['klausur'] ?? '') ?>"
              required
            />
          </div>

          <!-- Anfangsdatum (links) -->
          <div class="form-section">
            <label for="anfangsdatum" class="popup-form-label">Anfangsdatum</label>
            <input
              type="date"
              id="anfangsdatum"
              name="anfangsdatum"
              class="popup-form-input"
              value="<?= htmlspecialchars($_POST['anfangsdatum'] ?? '') ?>"
              required
            />
          </div>

          <!-- Enddatum (rechts) -->
          <div class="form-section">
            <label for="endungsdatum" class="popup-form-label">Enddatum</label>
            <input
              type="date"
              id="endungsdatum"
              name="endungsdatum"
              class="popup-form-input"
              value="<?= htmlspecialchars($_POST['endungsdatum'] ?? '') ?>"
              required
            />
          </div>

          <!-- Notizen (ganze Breite) -->
          <div class="form-section span-two">
            <label for="notizen" class="popup-form-label">Notizen</label>
            <textarea
              id="notizen"
              name="notizen"
              class="popup-form-textarea"
              maxlength="1000"
            ><?= htmlspecialchars($_POST['notizen'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Submit (name="save_entry" muss unbedingt da stehen!) -->
        <button type="submit" name="save_entry" class="popup-submit-button">
          Speichern
        </button>
      </form>
    </div>
  </div>

  <!-- Overlay für To-Do-Popup  -->
  <div id="todoPopupOverlay" class="popup-overlay" style="display: none;">
    <div class="popup-content">
      <button id="closeTodoPopup">&times;</button>

      <h2>Neues To-Do</h2>

      <form id="todoForm" method="post" action="start.php">
        <!-- VERSTECKTES FELD zum Erkennen des Formular-Absendens -->
        <input type="hidden" name="save_todo" value="1" />

        <div class="popup-form-grid">
          <div class="form-section">
            <label for="todoText" class="popup-form-label">Aufgabe</label>
            <input
              type="text"
              id="todoText"
              name="todoText"
              class="popup-form-input"
              required
            />
          </div>
          <div class="form-section">
            <label for="todoEnddate" class="popup-form-label">Fälligkeitsdatum</label>
            <input
              type="date"
              id="todoEnddate"
              name="todoEnddate"
              class="popup-form-input"
              required
            />
          </div>
        </div>

        <button type="submit" class="popup-submit-button">Hinzufügen</button>
      </form>

      <!-- Falls du clientseitig Fehlermeldungen anzeigen willst: -->
      <div id="todoErrors" class="popup-error-list"></div>
    </div>
  </div>
</body>
</html>
