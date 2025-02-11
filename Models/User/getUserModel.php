<?php

namespace Models\User;

require_once __DIR__ . '/../../vendor/autoload.php';

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

use Config\Database;
use PDO;

class getUserModel {
    private $conn;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getUserById($userId) {

        $stmt = $this->conn->prepare("
        SELECT 
            u.user_id, 
            u.user_name,
            u.name, 
            u.last_name, 
            u.bio, 
            u.profilepictureurl, 
            u.birthdate, 
            p.pronoun AS pronoun, 
            c.country_name AS country, 
            u.created_at
        FROM users u
        LEFT JOIN countries c ON u.country_id = c.country_id
        LEFT JOIN pronouns p ON u.pronoun_id = p.pronoun_id
        WHERE u.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

