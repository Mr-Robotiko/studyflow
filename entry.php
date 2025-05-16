<?php
session_start()
$success = $success ?? false;
$errors = $errors ?? [];
?>

<div class="entry">
  <h2>Neuer Eintrag</h2>

  <?php if ($success): ?>
    <p style="color: green;">Eintrag erfolgreich gespeichert!</p>
  <?php endif; ?>

  <?php if ($errors): ?>
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
