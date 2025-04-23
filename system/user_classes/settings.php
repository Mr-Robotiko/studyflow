<?php
class Setting {
    private $ilt;
    private $mode;
    private $dpslink;
    private $timezone; 
    private $profilePicture;

    public function __construct($ilt = null, $mode = null, $dpslink = null, $timezone = null, $profilePicture = null) {
        $this->setIlt($ilt);
        $this->setMode($mode);
        $this->setDpslink($dpslink);
        $this->setTimezone($timezone);
        $this->setProfilePicture($profilePicture);
    }

    public function getIlt() {
        return $this->ilt;
    }

    public function setIlt($ilt) {
        $this->ilt = $ilt;
    }

    public function getMode() {
        return $this->mode;
    }

    public function setMode($mode) {
        $this->mode = $mode;
    }

    public function getDpslink() {
        return $this->dpslink;
    }

    public function setDpslink($dpslink) {
        $this->dpslink = $dpslink;
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function setTimezone($timezone) {
        $this->timezone = $timezone;
    }

    public function getProfilePicture() {
        return $this->profilePicture;
    }

    public function setProfilePicture($profilePicture) {
        $this->profilePicture = $profilePicture;
    }

    public function __toString() {
        return "Mode: {$this->mode}, Timezone: {$this->timezone}, DPS Link: {$this->dpslink}";
    }
}
?>
