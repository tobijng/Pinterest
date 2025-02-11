<?php

namespace Controllers\User;

require_once __DIR__ . '/../../vendor/autoload.php';

use Models\User\getUserModel;
use Models\User\validationUserModel;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class getUserController {
    private $userModel;
    private $userValidationModel;

    public function __construct() {
        $this->userModel = new getUserModel();
        $this->userValidationModel = new validationUserModel();
    }

    public function getUser($userId) {

        // Setze den Content-Type auf JSON
        header('Content-Type: application/json');

        // Prüfe, ob die ID vorhanden ist
        if (!is_numeric($userId)) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "User ID must be a number."]);
        }

        // Sicherstellen das userID eine Ganzzahl ist
        $userId = intval($userId);

        // Überprüfen, ob der Benutzer existiert, bevor das Update durchgeführt wird
        if (!$this->userValidationModel->checkUserIdExists($userId)) {
            http_response_code(404);
            return json_encode(["success" => false, "message" => "User not found."]);
        }

        // Versuchen, User bei userID zu bekommen
        $user = $this->userModel->getUserById($userId);

        if ($user) {
            return json_encode(["success" => true, "user" => $user]);
        } else {
            http_response_code(404);
            return json_encode(["success" => false, "message" => "User not found."]);
        }
    }
}
