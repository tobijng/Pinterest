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

class registerModel {

    private $conn;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function registerUser($email, $userName, $password) {
        try {
            // **Transaktion starten**
            $this->conn->beginTransaction();

            // **Nutzer in die Datenbank einfügen**
            if ($this->insertUser($email, $userName, $password)) {
                // **Transaktion abschließen**
                $this->conn->commit();
                return true; // Registrierung erfolgreich
            } else {
                // **Rollback, wenn Einfügen des Nutzers fehlschlägt**
                $this->conn->rollBack();
                return false; // Fehler beim Einfügen
            }

        } catch (PDOException $e) {
            // **Fehler bei der Datenbankoperation, Rollback**
            $this->conn->rollBack();
            $this->logError("Database error: " . $e->getMessage());
            return false; // Fehler bei der Datenbankoperation
        }
    }

    private function insertUser($email, $userName, $password) {
        try {
            // **Passwort hashen**
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // **SQL-Statement vorbereiten**
            $stmt = $this->conn->prepare("INSERT INTO users (email, user_name, password) VALUES (:email, :user_name, :password)");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':user_name', $userName, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

            // **SQL ausführen**
            return $stmt->execute(); // Gibt true zurück, wenn erfolgreich
        } catch (PDOException $e) {
            $this->logError("Database error: " . $e->getMessage());
            return false;
        }
    }

    private function logError($message) {
        $logFile = __DIR__ . "/../logs/register.log";
        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
