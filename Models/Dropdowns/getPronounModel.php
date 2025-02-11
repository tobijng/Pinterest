<?php

namespace Models\Dropdowns;

use Config\Database;
use PDO;
use PDOException;

require_once __DIR__ . '/../../vendor/autoload.php';

if (!defined('API_ACCESS')) {
    die('Direct access not permitted.');
}

class getPronounModel
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    // Private Methode - kann nur innerhalb der Klasse verwendet werden
    private function getAllPronouns(): array
    {
        try {
            $stmt = $this->conn->prepare("SELECT pronoun_id, pronoun FROM pronouns ORDER BY pronoun_id ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fehler in den Logs speichern
            //$this->logger->error('Fehler beim Abrufen der Länder: ' . $e->getMessage());
            return [];  // Leeres Array zurückgeben im Fehlerfall
        }
    }


    // Öffentliche Methode, die von außen genutzt werden kann
    public function fetchPronouns(): array
    {
        return $this->getAllPronouns();
    }

}