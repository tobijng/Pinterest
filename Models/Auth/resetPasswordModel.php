<?php

namespace Models\Auth;

require_once __DIR__ . '/../../vendor/autoload.php';

use Config\Database;
use PDO;
use PDOException;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class resetPasswordModel
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function updatePassword($userId, $newPassword): bool
    {
        try {
            // Neues Passwort hashen
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Passwort in der Datenbank aktualisieren
            $stmt = $this->conn->prepare("UPDATE users SET password = :password WHERE user_id = :id");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
