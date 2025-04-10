<?php
    class Day {
        include("task.php");
        
        private $day;
        private $Task;

        public function __construct($day = NULL, $Task = NULL) {
            $this->setDay($day);
            $this->setTask($Task);
        }

        public function getDay() {
            return $this->day;
        }

        public function getTask() {
            return $this->Task;
        }

        public function setDay($dayValue) {
            $this->day = $dayValue;
        }

        public function setTask($TaskValue) {
            $this->Task = $TaskValue;
        }

        public function __toString() {
            return "Day: {$this->day}\nTask: {$this->Task}";
        }
    }
?>
