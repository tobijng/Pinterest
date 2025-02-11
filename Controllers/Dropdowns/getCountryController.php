<?php

namespace Controllers\Dropdowns;

require_once __DIR__ . '/../../vendor/autoload.php';

use Models\Dropdowns\getCountryModel;

// Sicherstellen, dass die Datei nur über den API-Router geladen wird
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

class getCountryController {
    private $countryModel;

    public function __construct() {
        $this->countryModel = new getCountryModel();
    }

    public function getCountries() {
        header('Content-Type: application/json');

        $countries = $this->countryModel->fetchCountries();

        if (!empty($countries)) {
            return json_encode(["success" => true, "countries" => $countries]);
        } else {
            http_response_code(404);
            return json_encode(["success" => false, "message" => "No countries found."]);
        }
    }
}

