<?php
namespace Controllers\Auth;

// Load all dependencies using Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Imports:
 * registerModel: Used to register the user in the Database
 * validationUserModel: Used to validate if user and email is already in the database
 */
use Models\Auth\registerModel;
use Models\User\validationUserModel;

// Ensure this file is accessed only via the API router
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

/**
 * Controller responsible for handling user register requests.
 */
class registerController {

    private $registerModel;
    private $validationModel;

    /**
     * Constructor initializes the login model and validation model.
     */
    public function __construct()
    {
        $this->registerModel = new registerModel();
        $this->validationModel = new validationUserModel();
    }

    /**
     * Handles user registration.
     *
     * @return string JSON response indicating success or failure.
     */
    public function register(): string
    {
        // Retrieve JSON data from request body
        $inputJSON = file_get_contents("php://input");
        $data = json_decode($inputJSON, true);

        // Validate JSON format
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Invalid JSON format"]);
        }

        // Validate user input
        $validationResult = $this->validateInput($data);
        if ($validationResult !== true) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => $validationResult]);
        }

        // Check if email or username already exists via the validation model
        $emailExists = $this->validationModel->checkEmailExists($data['email']);
        $userNameExists = $this->validationModel->checkUserNameExists($data['username']);

        // If email already exists return error
        if ($emailExists) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Email already exists."]);
        }

        // If user already exists return error
        if ($userNameExists) {
            http_response_code(400);
            return json_encode(["success" => false, "message" => "Username already exists."]);
        }

        // Register the user via the model
        $result = $this->registerModel->registerUser($data['email'], $data['username'], $data['password']);

        // Return response based on registration success
        if ($result) {
            // Registration successful
            http_response_code(201);
            return json_encode(["success" => true, "message" => "Registration successful."]);
        } else {
            // Registration failed
            http_response_code(500); // Internal Server Error
            return json_encode(["success" => false, "message" => "Registration failed."]);
        }
    }

    /**
     * Validates user input for registration.
     *
     * @param array $data The input data to validate.
     * @return true|string Returns true if valid, otherwise an error message.
     */
    private function validateInput(array $data)
    {
        // Ensure that all required fields are provided
        if (empty($data['email']) || empty($data['username']) || empty($data['password'])) {
            return "Email, username, and password are required.";
        }

        // Ensure email has the right format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }

        // Ensure the character length of the email is between 5 and 100
        if (strlen($data['email']) < 5 || strlen($data['email']) > 100) {
            return "Email must be between 5 and 100 characters long.";
        }

        // Ensure username doesn't contains special characters and is between 3 and 20 characters long
        if (!preg_match("/^[a-zA-Z0-9_-]{3,20}$/", $data['username'])) {
            return "Username must be between 3 and 20 characters long and contain only letters, numbers, dashes, or underscores.";
        }

        // Reserved usernames that cannot be used
        $reservedKeywords = ["admin", "root", "system", "test"];
        if (in_array(strtolower($data['username']), $reservedKeywords)) {
            return "The username is not available.";
        }

        // Ensure password is at least 8 characters long
        if (strlen($data['password']) < 8) {
            return "Password must be at least 8 characters long.";
        }

        // Ensure password contains at least one digit
        if (!preg_match('/\d/', $data['password'])) {
            return "Password must contain at least one number.";
        }

        // Ensure password contains at least one special character
        if (!preg_match('/[\W_]/', $data['password'])) {
            return "Password must contain at least one special character.";
        }

        return true;
    }
}