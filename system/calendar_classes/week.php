<?php
    require_once "day.php";
    class Week {
        
        private $Day;
        private $weekNumber;

        public function __construct($Day = NULL, $weekNumber = NULL) {
            $this->setDay($Day);
            $this->setWeekNumber($weekNumber);
        }

        public function getDay() {
            return $this->Day;
        }

        public function getWeekNumber() {
            return $this->weekNumber;
        }

        public function setDay($DayValue) {
            $this->Day = $DayValue;
        }

        public function setWeekNumber($weekNumber) {
            $this->weekNumber = $weekNumber;
        }
    }
?>