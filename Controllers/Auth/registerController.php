<?php
namespace Controllers\Auth;

require_once __DIR__ . '/../../vendor/autoload.php';

use Models\Auth\registerModel;
use Models\User\validationUserModel;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class registerController {

    private $registerModel;
    private $validationModel;

    public function __construct()
    {
        $this->registerModel = new registerModel();
        $this->validationModel = new validationUserModel();
    }

    public function register()
    {
        // JSON-Daten aus der Anfrage holen
        $inputJSON = file_get_contents("php://input");
        $data = json_decode($inputJSON, true);

        // JSON-Fehler prüfen
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Invalid JSON format"]);
        }

        // **Validierung der Eingaben**
        $validationResult = $this->validateInput($data);
        if ($validationResult !== true) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => $validationResult]);
        }

        // **Überprüfung, ob der Benutzername oder die E-Mail bereits existieren**
        $emailExists = $this->validationModel->checkEmailExists($data['email']);
        $userNameExists = $this->validationModel->checkUserNameExists($data['username']);

        if ($emailExists) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Email already exists."]);
        }

        if ($userNameExists) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Username already exists."]);
        }

        // **Registrierung im Model aufrufen**
        $result = $this->registerModel->registerUser($data['email'], $data['username'], $data['password']);

        // **Antwort auf Basis des Model-Ergebnisses erstellen**
        if ($result) {
            http_response_code(201); // Created
            return json_encode(["success" => true, "message" => "Registration successful."]);
        } else {
            http_response_code(500); // Internal Server Error
            return json_encode(["success" => false, "message" => "Registration failed."]);
        }
    }

    private function validateInput($data)
    {
        if (empty($data['email']) || empty($data['username']) || empty($data['password'])) {
            return "Email, username, and password are required.";
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }

        if (strlen($data['email']) < 5 || strlen($data['email']) > 100) {
            return "Email must be between 5 and 100 characters long.";
        }

        if (!preg_match("/^[a-zA-Z0-9_-]{3,20}$/", $data['username'])) {
            return "Username must be between 3 and 20 characters long and contain only letters, numbers, dashes, or underscores.";
        }

        $reservedKeywords = ["admin", "root", "system", "test"];
        if (in_array(strtolower($data['username']), $reservedKeywords)) {
            return "The username is not available.";
        }

        if (strlen($data['password']) < 8) {
            return "Password must be at least 8 characters long.";
        }

        // Überprüfen, ob das Passwort mindestens eine Zahl enthält
        if (!preg_match('/\d/', $data['password'])) {
            return "Password must contain at least one number.";
        }

        // Überprüfen, ob das Passwort mindestens ein Sonderzeichen enthält
        if (!preg_match('/[\W_]/', $data['password'])) {
            return "Password must contain at least one special character.";
        }

        return true;
    }
}
