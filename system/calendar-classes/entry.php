<?php
class Entry {
    private $title;
    private $description;
    private $startTime;
    private $endTime;

    public function __construct($title = '', $description = '', $startTime = '', $endTime = '') {
        $this->title = $title;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    // Getter und Setter
    public function getTitle() {
        return $this->title;
    }
    public function setTitle($title) {
        $this->title = $title;
    }

    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }

    public function getStartTime() {
        return $this->startTime;
    }
    public function setStartTime($startTime) {
        $this->startTime = $startTime;
    }

    public function getEndTime() {
        return $this->endTime;
    }
    public function setEndTime($endTime) {
        $this->endTime = $endTime;
    }
}
?>
