<?php
namespace Controllers\Auth;

// Load all dependencies using Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Import:
 * loginModel: Used to login a user via a Database match based on the provided data
 */
use Models\Auth\loginModel;

// Ensure this file can only be accessed through the API router
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

/**
 * Controller responsible for handling user login requests.
 */
class loginController {
    private $loginModel;

    /**
     * Constructor initializes the login model.
     */
    public function __construct() {
        $this->loginModel = new loginModel();
    }

    /**
     * Handles user authentication.
     *
     * @return string JSON response containing authentication status and user data if successful.
     */
    public function login(): string
    {
        // Set response content type to JSON
        header('Content-Type: application/json');

        // Retrieve JSON input from the request
        $inputJSON = file_get_contents("php://input");
        $data = json_decode($inputJSON, true);

        // Validate JSON decoding
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Invalid JSON format"]);
        }

        // Validate required input fields
        if (empty($data['username_or_email']) || empty($data['password'])) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Username/email and password are required."]);
        }

        // Attempt to authenticate user via the login model
        $user = $this->loginModel->verifyUser($data['username_or_email'], $data['password']);

        if ($user) {
            // Authentication successful
            http_response_code(200);
            return json_encode([
                "success" => true,
                "message" => "Login successful.",
                "data" => [
                    "user_id" => $user['user_id'],
                    "user_name" => $user['user_name']
                ]
            ]);
        } else {
            // Authentication failed
            http_response_code(401);
            return json_encode(["success" => false, "message" => "Invalid username or password."]);
        }
    }
}