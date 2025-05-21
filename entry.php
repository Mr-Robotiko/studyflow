<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "system/calendar-classes/entry.php";
require_once "system/calendar-classes/day.php";

$success = $_SESSION['entry_success'] ?? false;
$errors  = $_SESSION['entry_errors']  ?? [];
unset($_SESSION['entry_success'], $_SESSION['entry_errors']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_entry'])) {
    $title       = trim($_POST['klausur'] ?? '');
    $description = trim($_POST['notizen'] ?? '');
    $startDate   = $_POST['anfangsdatum'] ?? '';
    $endDate     = $_POST['endungsdatum'] ?? '';

    $errors = [];

    if ($title === '') {
        $errors[] = 'Bitte gib einen Titel (Klausur) ein.';
    }
    if ($startDate === '') {
        $errors[] = 'Bitte gib ein Anfangsdatum an.';
    }
    if ($endDate === '') {
        $errors[] = 'Bitte gib ein Enddatum an.';
    }
    if ($startDate && $endDate && strtotime($startDate) > strtotime($endDate)) {
        $errors[] = 'Das Anfangsdatum darf nicht nach dem Enddatum liegen.';
    }

    if (empty($errors)) {
        $entry = new Entry($title, $description, $startDate, $endDate);

        $days = $_SESSION['days'] ?? [];

        if (isset($days[$startDate]) && $days[$startDate] instanceof Day) {
            $day = $days[$startDate];
        } else {
            $day = new Day($startDate);
        }

        $day->addEntry($entry);
        $days[$startDate]        = $day;
        $_SESSION['days']        = $days;

        $success = true;
    }

    $_SESSION['entry_success'] = $success;
    $_SESSION['entry_errors']  = $errors;

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'errors' => $errors]);
        exit;
    }

    header('Location: start.php');
    exit;
}
?>

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
