<?php

namespace Models\User;

require_once __DIR__ . '/../../vendor/autoload.php';

use Config\Database;
use PDOException;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class updateUserModel {

    private $conn;
    private $validationModel;

    public function __construct() {
        // Verbindung zur Datenbank aufbauen
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
        // Validierungsmodell instanziieren
        $this->validationModel = new validationUserModel();
    }

    /**
     * Aktualisiert die Benutzerdaten anhand der user_id
     *
     * @param int $userId Die ID des zu aktualisierenden Benutzers
     * @param array $data Ein assoziatives Array mit den zu ändernden Benutzerdaten
     * @return bool Gibt true zurück, wenn das Update erfolgreich war, andernfalls false
     */
    public function updateById($userId, $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $fields = [];
        $values = [];

        // Überprüfen, ob der Benutzername geändert wurde
        if (isset($data['user_name'])) {
            // Sicherstellen, dass der neue Benutzername einzigartig ist
            if ($this->validationModel->checkUserNameExists($data['user_name'])) {
                return false; // Benutzername bereits vergeben
            }
            $fields[] = "user_name = ?";
            $values[] = $data['user_name'];
        }

        // Weitere Felder hinzufügen und sichern
        foreach ($data as $key => $value) {
            if (in_array($key, ['profilepictureurl', 'bio', 'name', 'last_name', 'birthdate', 'pronoun_id', 'country_id'])) {
                // Vermeidung von SQL-Injection durch vorbereitete Statements
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        // Überprüfen, ob Felder zum Updaten existieren
        if (empty($fields)) {
            return false;
        }

        // user_id an das Ende der Werte anfügen
        $values[] = $userId;
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE user_id = ?";

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($values);

            // Transaktion abschließen
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            // Fehlerprotokollierung
            error_log('Fehler bei der Aktualisierung des Benutzers: ' . $e->getMessage());
            return false;
        }
    }

}
