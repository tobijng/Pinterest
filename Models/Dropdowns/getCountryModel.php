<?php
namespace Models\Dropdowns;

require_once __DIR__ . '/../../vendor/autoload.php';

use Config\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PDO;
use PDOException;

if (!defined('API_ACCESS')) {
    die('Direct access not permitted.');
}

class getCountryModel {
    private $conn;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();

        // Logger initialisieren
        $this->logger = new Logger('country_model_logger');
        // FileHandler hinzufügen (logs werden in eine Datei geschrieben)
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/country_model.log', Level::Debug));


    }

    // Private Methode - kann nur innerhalb der Klasse verwendet werden
    private function getAllCountries(): array
    {
        try {
            $stmt = $this->conn->prepare("SELECT country_id, country_name FROM countries ORDER BY country_name ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fehler in den Logs speichern
            $this->logger->error('Fehler beim Abrufen der Länder: ' . $e->getMessage());
            return [];  // Leeres Array zurückgeben im Fehlerfall
        }
    }


    // Öffentliche Methode, die von außen genutzt werden kann
    public function fetchCountries(): array
    {
        return $this->getAllCountries();
    }
}
