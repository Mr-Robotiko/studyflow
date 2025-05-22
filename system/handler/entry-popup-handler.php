<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../calendar-classes/day.php";

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
        $days[$startDate] = $day;
        $_SESSION['days'] = $days;
        $success = true;
    }

    $_SESSION['entry_success'] = $success;
    $_SESSION['entry_errors']  = $errors;

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'errors' => $errors]);
        exit;
    }

    header('Location: start.php');
    exit;
}
