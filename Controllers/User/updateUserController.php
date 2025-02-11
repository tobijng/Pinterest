<?php
namespace Controllers\User;

require_once __DIR__ . '/../../vendor/autoload.php';

use Models\User\updateUserModel;
use Models\User\validationUserModel;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class updateUserController {

    private $updateUserModel;
    private $userValidationModel; // Füge das UserValidationModel hinzu

    public function __construct() {
        $this->updateUserModel = new updateUserModel();
        $this->userValidationModel = new validationUserModel();
    }

    public function updateUser() {

        header('Content-Type: application/json');

        $inputJSON = file_get_contents("php://input");
        $data = json_decode($inputJSON, true);

        // JSON-Fehler prüfen
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Invalid JSON format"]);
        }

        // Überprüfen, ob user_id vorhanden ist
        if (!isset($data['user_id'])) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "User ID is required."]);

        }

        $userId = $data['user_id'];

        $userId = intval($userId);

        unset($data['user_id']); // Entferne user_id aus den Daten, da sie nicht verändert wird

        // Überprüfen, ob der Benutzer existiert, bevor das Update durchgeführt wird
        if (!$this->userValidationModel->checkUserIdExists($userId)) {
            http_response_code(404);
            return json_encode(["success" => false, "message" => "User not found."]);

        }

        // Führe das Update durch, wenn der Benutzer existiert
        $updateResult = $this->updateUserModel->updateById($userId, $data);

        if ($updateResult) {
            http_response_code(200);
            return json_encode(["success" => true, "message" => "Profile updated successfully."]);
        } else {
            http_response_code(500);
            return json_encode(["success" => false, "message" => "Profile update failed."]);
        }
    }
}
