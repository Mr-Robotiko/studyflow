<?php
require_once "system/database-classes/database.php";
require_once "system/calendar-classes/day.php";
require_once "system/calendar-classes/entry.php";

class CalendarManager {
    private User $user;
    private string $calendarFile;

    public function __construct(User $user) {
        $this->user = $user;
        $this->calendarFile = __DIR__ . '/../data/calendar_' . $this->user->getUserName() . '.json';
    }

    public function loadCalendarFromDatabase(): ?string {
        try {
            $db = new Database("config/configuration.csv");
            $pdo = $db->getConnection();

            $stmt = $pdo->prepare("SELECT calendarfile FROM user WHERE username = :username");
            $stmt->execute(['username' => $this->user->getUserName()]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($result['calendarfile'])) {
                return $result['calendarfile'];
            }
            return null;
        } catch (Exception $e) {
            throw new Exception("Fehler beim Laden der Kalenderdaten: " . $e->getMessage());
        }
    }

    public function saveCalendarToFile(string $calendarJson): void {
        $dir = __DIR__ . '/../data';
        
        // Verzeichnis prüfen und ggf. erstellen
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new Exception("Verzeichnis $dir konnte nicht erstellt werden.");
            }
        }
    
        // Pfad zur Datei setzen, falls nicht bereits gesetzt
        if (empty($this->calendarFile)) {
            $this->calendarFile = $dir . '/calendar_' . $this->user->getUserName() . '.json';
        }
    
        // Datei schreiben
        if (file_put_contents($this->calendarFile, $calendarJson) === false) {
            throw new Exception("Speichern der Kalenderdatei in {$this->calendarFile} fehlgeschlagen.");
        }
    }
    

    public function loadDaysFromFile(): array {
        $days = [];
        if (file_exists($this->calendarFile)) {
            $savedData = json_decode(file_get_contents($this->calendarFile), true);
            if (is_array($savedData)) {
                foreach ($savedData as $date => $entries) {
                    $day = new Day($date);
                    foreach ($entries as $entryData) {
                        $entry = new Entry(
                            $entryData['title'] ?? '',
                            $entryData['description'] ?? '',
                            $entryData['start'] ?? '',
                            $entryData['end'] ?? ''
                        );
                        $day->addEntry($entry);
                    }
                    $days[$date] = $day;
                }
            }
        }
        return $days;
    }

    public function saveDaysToFile(array $days): void {
        $savedDays = [];
        foreach ($days as $date => $dayObj) {
            $entries = [];
            foreach ($dayObj->getEntries() as $entryObj) {
                $entries[] = [
                    'title' => $entryObj->getTitle(),
                    'description' => $entryObj->getDescription(),
                    'start' => $entryObj->getStartTime(),
                    'end' => $entryObj->getEndTime()
                ];
            }
            $savedDays[$date] = $entries;
        }
        if (!is_dir(dirname($this->calendarFile))) {
            mkdir(dirname($this->calendarFile), 0777, true);
        }
        file_put_contents($this->calendarFile, json_encode($savedDays, JSON_PRETTY_PRINT));
    }

    public function handleEntryFormSubmission(array &$days, array &$errors, &$success): void {
        $title = trim($_POST['klausur'] ?? '');
        $description = trim($_POST['notizen'] ?? '');
        $startDate = $_POST['anfangsdatum'] ?? '';
        $endDate = $_POST['endungsdatum'] ?? '';

        if ($title && $startDate && $endDate) {
            $day = $days[$startDate] ?? new Day($startDate);
            $entry = new Entry($title, $description, "00:00", "00:00");
            $day->addEntry($entry);
            $days[$startDate] = $day;
            $this->saveDaysToFile($days);
            $success = true;
        } else {
            $errors[] = "Bitte alle Pflichtfelder ausfüllen.";
        }
    }

    public function getCalendarFilePath(): string {
        return $this->calendarFile;
    }
}
