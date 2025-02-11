<?php

namespace Controllers\Dropdowns;

require_once __DIR__ . '/../../vendor/autoload.php';

use Models\Dropdowns\getPronounModel;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class getPronounController {

    private $pronounModel;

    public function __construct() {
        $this->pronounModel = new getPronounModel();
    }

    public function getPronoun() {
        header('Content-Type: application/json');

        $pronouns = $this->pronounModel->fetchPronouns();

        if (!empty($pronouns)) {
            return json_encode(["success" => true, "pronouns" => $pronouns]);
        } else {
            http_response_code(404);
            return json_encode(["success" => false, "message" => "No pronouns found."]);
        }
    }
}