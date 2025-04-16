<?php
    require_once "todo.php";
        class Content {
        
        private $notes;
        private $deadline;
        private $start;
        private $ToDo;

        public function __construct($notes = NULL, $deadline = NULL, $start = NULL, $ToDo = NULL) {
            $this->setNotes($notes);
            $this->setDeadline($deadline);
            $this->setStart($start);
            $this->setToDo($ToDo);
        }

        public function getNotes() {
            return $this->notes;
        }

        public function getDeadline() {
            return $this->deadline;
        }

        public function getStart() {
            return $this->start;
        }

        public function getToDo() {
            return $this->ToDo;
        }

        public function setNotes($notesValue) {
            $this->notes = $notesValue;
        }

        public function setDeadline($deadlineValue) {
            $this->deadline = $deadlineValue;
        }

        public function setStart($startValue) {
            $this->start = $startValue;
        }

        public function setToDo($ToDo) {
            $this->ToDo = $ToDo;
        }

        public function __toString() {
            return "Notes: {$this->notes}\nDeadline: {$this->deadline}\nStart: {$this->start}\nToDo: " . get_class($this->ToDo);
        }
}
?>