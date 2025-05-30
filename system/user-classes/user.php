<?php
require_once 'system/database-classes/database.php';
    class User {
        private $id;
        private $name;
        private $surname;
        private $institution;
        private $Calendarfile; 
        private $securityPassphrase;
        private $userName;
        private $password;
        private $darkMode = false;
        private $lernideal = 2;


        public function __construct($id = null, $name = null, $surname = null, $institution = null, $Calendarfile = null, $securityPassphrase = null, $userName = null, $password = null, $darkMode = false, $lernideal = 2) {
            $this->setId($id);
            $this->setName($name);
            $this->setSurname($surname);
            $this->setInstitution($institution);
            $this->setCalendarfile($Calendarfile);
            $this->setSecurityPassphrase($securityPassphrase);
            $this->setUserName($userName);
            $this->setPassword($password);
            $this->setDarkMode($darkMode);
            $this->setLernideal($lernideal);
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

        public function getDarkMode() {
            return $this->darkMode;
        }
        
        public function setDarkMode($darkMode) {
            $this->darkMode = (bool)$darkMode;
        }
        
        public function getLernideal() {
            return $this->lernideal;
        }
        
        public function setLernideal($lernideal) {
            $this->lernideal = (int)$lernideal;
        }

        public function getId() {
            return $this->id;
        }
        
        public function setId($id) {
            $this->id = $id;
        }
        
        public function __toString() {
            return "Name: {$this->name} {$this->surname}, Institution: {$this->institution}, Calendar: {$this->Calendarfile}";
        }

        public function deleteFromDatabase() {

            $username = $this->getUserName();
            if (!$username) {
                throw new Exception("Benutzername fehlt");
            }
            
            $db = new Database("config/configuration.csv");
            $pdo = $db->getConnection();
        
            $stmt = $pdo->prepare("DELETE FROM user WHERE username = :username");
            return $stmt->execute(['username' => $username]);
        }

        public function saveSettingsToDatabase() {
            $db = new Database("config/configuration.csv");
            $pdo = $db->getConnection();
        
            $stmt = $pdo->prepare("UPDATE user SET dark_mode = :darkMode, lernideal = :lernideal WHERE username = :username");
            return $stmt->execute([
                'darkMode' => $this->getDarkMode() ? 1 : 0,
                'lernideal' => $this->getLernideal(),
                'username' => $this->getUserName()
            ]);
        }
        
        
        
    }
?>
