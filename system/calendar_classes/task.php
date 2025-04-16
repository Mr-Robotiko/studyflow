<?php
    require_once "content.php";
    class Task {
        
        private $name;
        private $start;
        private $end;
        private $duration;
        private $Content;

        public function __construct($name = NULL, $start = NULL, $end = NULL, $duration = NULL, $Content = NULL) {
            $this->setName($name);
            $this->setStart($start);
            $this->setEnd($end);
            $this->setDuration($duration);
            $this->setContent($Content);
        }

        public function getName() {
            return $this->name;
        }

        public function getStart() {
            return $this->start;
        }

        public function getEnd() {
            return $this->end;
        }

        public function getDuration() {
            return $this->duration;
        }

        public function getContent() {
            return $this->Content;
        }

        public function setName($nameValue) {
            $this->name = $nameValue;
        }

        public function setStart($startValue) {
            $this->start = $startValue;
        }

        public function setEnd($endValue) {
            $this->end = $endValue;
        }

        public function setDuration($durationValue) {
            $this->duration = $durationValue;
        }

        public function setContent($ContentValue) {
            $this->Content = $ContentValue;
        }
    }
?>