<?php

namespace Models\User;

require_once __DIR__ . '/../../vendor/autoload.php';

use Config\Database;
use PDO;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class deleteUserModel {
    private $conn;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function deleteUserById($userId) {
        // Löschen des Benutzers
        $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Gibt true zurück, wenn der User gelöscht wurde
        } else {
            return false; // Fehler beim Löschen
        }
    }
}

