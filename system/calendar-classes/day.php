<?php
require_once "entry.php";

class Day {
    private $date;
    private $entries = [];

    public function __construct($date = null) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getEntries() {
        return $this->entries;
    }

    // Einen neuen Eintrag hinzufügen
    public function addEntry(Entry $entry) {
        $this->entries[] = $entry;
    }
}
?>

