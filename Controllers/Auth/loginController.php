<?php
namespace Controllers\Auth;

require_once __DIR__ . '/../../vendor/autoload.php';

use Models\Auth\loginModel;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class loginController {
    private $loginModel;

    public function __construct() {
        $this->loginModel = new loginModel();
    }

    public function login() {
        // Setze den Content-Type auf JSON
        header('Content-Type: application/json');

        // JSON-Daten aus der Anfrage holen
        $inputJSON = file_get_contents("php://input");
        $data = json_decode($inputJSON, true);

        // Prüfen, ob das JSON korrekt dekodiert wurde
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Invalid JSON format"]);
        }

        // Validierung der Eingabedaten
        if (empty($data['username_or_email']) || empty($data['password'])) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Username/email and password are required."]);
        }

        // Authentifizierung im Model durchführen
        $user = $this->loginModel->verifyUser($data['username_or_email'], $data['password']);

        if ($user) {
            // Erfolgreiches Login
            http_response_code(200);
            return json_encode(["success" => true, "message" => "Login successful.", "data" => [
                    "user_id" => $user['user_id'],
                    "user_name" => $user['user_name']]
            ]);
        } else {
            http_response_code(401);
            return json_encode(["success" => false, "message" => "Invalid username or password."]);
        }
    }
}
