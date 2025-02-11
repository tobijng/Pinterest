<?php
namespace Models\Auth;

require_once __DIR__ . '/../../vendor/autoload.php';

use Config\Database;
use PDO;

if (!defined('API_ACCESS')) {
    die('Direct access not permitted.');
}

class loginModel {
    private $conn;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function verifyUser($usernameOrEmail, $password) {
        // Abfrage zum Überprüfen des Benutzers anhand von user_name oder Email
        $stmt = $this->conn->prepare("SELECT user_id, user_name, password FROM users WHERE user_name = :usernameOrEmail OR email = :usernameOrEmail LIMIT 1");
        $stmt->bindParam(':usernameOrEmail', $usernameOrEmail, PDO::PARAM_STR);
        $stmt->execute();

        // Benutzer finden
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Wenn kein Benutzer gefunden wurde
        if (!$user) {
            return false;  // Benutzer existiert nicht
        }

        // Passwort überprüfen
        if (!password_verify($password, $user['password'])) {
            return false;  // Passwort ist falsch
        }

        // Erfolgreiche Authentifizierung
        return $user;  // Benutzer-Daten zurückgeben
    }
}
