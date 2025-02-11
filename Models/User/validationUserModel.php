<?php

namespace Models\User;

require_once __DIR__ . '/../../vendor/autoload.php';

use Config\Database;


// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class validationUserModel {

    private $conn;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    // Überprüfen, ob die user_id bereits existiert
    public function checkUserIdExists($userId) {
        $sql = "SELECT COUNT(*) FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);

        $result = $stmt->fetchColumn();
        return $result > 0;
    }

    // Überprüfen, ob der Benutzername bereits vergeben ist
    public function checkUserNameExists($userName) {
        $sql = "SELECT COUNT(*) FROM users WHERE user_name = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userName]);

        $result = $stmt->fetchColumn();
        return $result > 0;
    }

    // Überprüfen, ob die E-Mail bereits vergeben ist
    public function checkEmailExists($email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);

        $result = $stmt->fetchColumn();
        return $result > 0;
    }
}
