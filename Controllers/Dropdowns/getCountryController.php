<?php

namespace Controllers\Dropdowns;

// Load all dependencies using Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Import:
 * getCountryModel: Used to fetch the list of countries from the database or other data sources.
 */
use Models\Dropdowns\getCountryModel;

// Ensure this file can only be accessed through the API router
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

/**
 * Controller responsible for handling requests to get a list of countries.
 */
class getCountryController {
    private $countryModel;

    /**
     * Constructor initializes the country model.
     */
    public function __construct() {
        // Instantiate the model that will handle country data fetching
        $this->countryModel = new getCountryModel();
    }

    /**
     * Handles fetching and returning the list of countries.
     *
     * This method retrieves the countries from the model and returns them in a JSON format.
     * If the countries are successfully fetched, it returns them in the response.
     * Otherwise, it returns an error message.
     *
     * @return string JSON response containing the status of the request and the list of countries if found.
     */
    public function getCountries() {
        // Set response content type to JSON for consistency with API standards
        header('Content-Type: application/json');

        // Fetch the list of countries from the model
        $countries = $this->countryModel->fetchCountries();

        // Check if countries were successfully fetched
        if (!empty($countries)) {
            // Countries found, return them as a JSON response with success status
            return json_encode(["success" => true, "countries" => $countries]);
        } else {
            // No countries found, return a 404 error with an appropriate message
            http_response_code(404); // Not Found
            return json_encode(["success" => false, "message" => "No countries found."]);
        }
    }
}
