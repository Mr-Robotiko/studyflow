<?php
    class Content{

        public function __construct($notes = NULL, $deadline = NULL, $start = NULL, $ToDo = NULL) {
            include('todo.php');
            setNotes($notes);
            setDeadline($deadline);
            setStart($start);
            setToDo($ToDo);
        }

        private $notes;
        private $deadline;
        private $start;
        private $ToDo;

        public function getNotes()
        {
            return $this->notes;
        }

        public function getDeadline()
        {
            return $this->deadline;
        }

        public function getStart()
        {
            return $this->start;
        }

        public function getToDo()
        {
            return $this->ToDo;
        }

        public function setNotes($notesValue)
        {
            $this->notes = $notesValue;
        }

        public function setDeadline($deadlineValue)
        {
            $this->notes = $deadlineValue;
        }

        public function setStart($startValue)
        {
            $this->notes = $startValue;
        }

        public function setToDo($ToDo)
        {
            $this->notes = $ToDo;
        }
}

?>