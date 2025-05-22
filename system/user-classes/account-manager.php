<?php

class AccountManager {
    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function deleteAccount(): bool {
        try {
            return $this->user->deleteFromDatabase();
        } catch (Exception $e) {
            throw new Exception("Fehler beim Löschen: " . $e->getMessage());
        }
    }
}
