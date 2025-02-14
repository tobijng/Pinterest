<?php

namespace Controllers\Dropdowns;

// Load all dependencies using Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Import:
 * getPronounModel: Used to fetch the list of pronouns from the database or other data sources.
 */
use Models\Dropdowns\getPronounModel;

// Ensure this file can only be accessed through the API router
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

/**
 * Controller responsible for handling requests to get a list of pronouns.
 */
class getPronounController {

    private $pronounModel;

    /**
     * Constructor initializes the pronoun model.
     */
    public function __construct() {
        // Instantiate the model that will handle fetching pronouns from the data source
        $this->pronounModel = new getPronounModel();
    }

    /**
     * Handles fetching and returning the list of pronouns.
     *
     * This method retrieves the pronouns from the model and returns them in a JSON format.
     * If the pronouns are successfully fetched, they will be included in the response.
     * If no pronouns are found, an error message will be returned.
     *
     * @return string JSON response containing the status of the request and the list of pronouns if found.
     */
    public function getPronoun() {
        // Set response content type to JSON to ensure consistency with API standards
        header('Content-Type: application/json');

        // Fetch the list of pronouns from the model
        $pronouns = $this->pronounModel->fetchPronouns();

        // Check if pronouns were successfully fetched
        if (!empty($pronouns)) {
            // Pronouns found, return them in a JSON response with success status
            return json_encode(["success" => true, "pronouns" => $pronouns]);
        } else {
            // No pronouns found, return a 404 error with an appropriate message
            http_response_code(404); // Not Found
            return json_encode(["success" => false, "message" => "No pronouns found."]);
        }
    }
}
