<?php
require_once __DIR__ . '/../calendar-classes/day.php';
require_once __DIR__ . '/../calendar-classes/entry.php';

class CalendarHandler {
    private PDO $pdo;
    private int $userId;

    public function __construct(PDO $pdo, int $userId) {
        $this->pdo = $pdo;
        $this->userId = $userId;
    }

    public function getWeekEntries(int $week, int $year): array {
        $startDate = new DateTime();
        $startDate->setISODate($year, $week);
        $endDate = clone $startDate;
        $endDate->modify('+6 days');

        $stmt = $this->pdo->prepare("
            SELECT entry.EntryID, event.Eventname, event.Begindate, event.Enddate, event.Note,
                   entry.Daydate, entry.Begintime AS StartTime, entry.Endtime AS EndTime
            FROM entry
            JOIN event ON entry.EventID = event.EventID
            WHERE entry.DayDate BETWEEN :startDate AND :endDate
              AND event.UserID = :userId
            ORDER BY entry.DayDate, entry.Begintime
        ");
        $stmt->execute([
            ':startDate' => $startDate->format('Y-m-d'),
            ':endDate' => $endDate->format('Y-m-d'),
            ':userId' => $this->userId
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $days = [];

        // Array mit Day-Objekten mit Datum als Schlüssel initialisieren
        for ($d = strtotime($startDate->format('Y-m-d')); $d <= strtotime($endDate->format('Y-m-d')); $d = strtotime('+1 day', $d)) {
            $date = date('Y-m-d', $d); // Richtiges Format für Datenbankabgleich
            $days[$date] = new Day($date);
        }


        // Einträge hinzufügen
        foreach ($results as $row) {
            $entry = new Entry(
                $row['EntryID'],
                $row['Eventname'],
                $row['StartTime'],
                $row['EndTime'],
                $row['Note']
            );
            // Sicherheitscheck, falls Datum nicht im Array ist
            if (isset($days[$row['Daydate']])) {
                $days[$row['Daydate']]->addEntry($entry);
            }
        }

        return $days;
    }
}
