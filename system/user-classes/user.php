<?php
require_once "settings.php";
    class User {
        private $name;
        private $surname;
        private $institution;
        private $Calendarfile; 
        private $securityPassphrase;
        private $userName;
        private $password;
        private $Setting;

        public function __construct($name = null, $surname = null, $institution = null, $Calendarfile = null, $securityPassphrase = null, $userName = null, $password = null, $Setting = null) {
            $this->setName($name);
            $this->setSurname($surname);
            $this->setInstitution($institution);
            $this->setCalendarfile($Calendarfile);
            $this->setSecurityPassphrase($securityPassphrase);
            $this->setUserName($userName);
            $this->setPassword($password);
            $this->setSetting($Setting);
        }

        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getSurname() {
            return $this->surname;
        }

        public function setSurname($surname) {
            $this->surname = $surname;
        }

        public function getInstitution() {
            return $this->institution;
        }

        public function setInstitution($institution) {
            $this->institution = $institution;
        }

        public function getCalendarfile() {
            return $this->Calendarfile;
        }

        public function setCalendarfile($Calendarfile) {
            $this->Calendarfile = $Calendarfile;
        }

        public function getSecurityPassphrase() {
            return $this->securityPassphrase;
        }

        public function setSecurityPassphrase($securityPassphrase) {
            $this->securityPassphrase = $securityPassphrase;
        }

        public function getUserName() {
            return $this->userName;
        }

        public function setUserName($userName) {
            $this->userName = $userName;
        }

        public function getPassword() {
            return $this->password;
        }

        public function setPassword($password) {
            $this->password = $password;
        }

        public function getSetting() {
            return $this->Setting;
        }

        public function setSetting($Setting) {
            $this->Setting = $Setting;
        }

        public function __toString() {
            return "Name: {$this->name} {$this->surname}, Institution: {$this->institution}, Calendar: {$this->Calendarfile}";
        }
    }
?>
