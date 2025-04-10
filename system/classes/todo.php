<?php
    class ToDo {
        private $name;

        public function __construct($name = NULL) {
            $this->setName($name);
        }

        public function getName() {
            return $this->name;
        }

        public function setName($nameValue) {
            $this->name = $nameValue;
        }
    }
?>