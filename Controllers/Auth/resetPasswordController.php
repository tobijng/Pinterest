<?php

namespace Controllers\Auth;

// Load all dependencies using Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Imports:
 * resetPasswordModel: Used to reset the user's password in the database.
 * validatePasswordController: Used to validate the new password's format.
 */
use Models\Auth\resetPasswordModel;
use Controllers\Helpers\validatePasswordController;
use Config\Redis;

// Ensure this file can only be accessed through the API router
if (!defined('API_ACCESS')) {
    die(json_encode(["success" => false, "message" => "Unauthorized access."]));
}

/**
 * Controller responsible for handling password reset requests.
 */
class resetPasswordController {
    private $resetPasswordModel;

    /**
     * Constructor initializes the reset password model.
     */
    public function __construct() {
        $this->resetPasswordModel = new resetPasswordModel();
    }

    /**
     * Handles the password reset process.
     *
     * @return string JSON response indicating the success or failure of the password reset.
     */
    public function resetPassword(): string
    {
        // Set response content type to JSON
        header('Content-Type: application/json');

        // Retrieve JSON input from the request
        $inputJSON = file_get_contents("php://input");
        $data = json_decode($inputJSON, true);

        // Validate JSON decoding
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400); // Bad Request
            return json_encode(["success" => false, "message" => "Invalid JSON format"]);
        }

        // Validate required input fields (token and new password)
        if (empty($data['token']) || empty($data['new_password'])) {
            http_response_code(400); // Bad Request
            return json_encode(["success" => false, "message" => "Token and new password are required."]);
        }

        // Retrieve the reset token and new password
        $token = $data['token'];
        $newPassword = $data['new_password'];

        // Connect to Redis and check if the token exists and is valid
        $redis = Redis::getInstance()->getConnection();
        $userId = $redis->get("password_reset_token:$token");

        // Check if the token is invalid or expired
        if (!$userId) {
            http_response_code(400); // Bad Request
            return json_encode(["success" => false, "message" => "Invalid or expired token."]);
        }

        // Validate the new password using the password validation controller
        $validationResult = validatePasswordController::validate($newPassword);
        if (!$validationResult['success']) {
            http_response_code(400); // Bad Request
            return json_encode($validationResult);
        }

        // Attempt to update the user's password in the database
        $success = $this->resetPasswordModel->updatePassword($userId, $newPassword);

        // If password update is successful
        if ($success) {
            // Delete the reset token from Redis to prevent reuse
            $redis->del("password_reset_token:$token");

            http_response_code(200); // OK
            return json_encode([
                "success" => true,
                "message" => "Password has been successfully reset."
            ]);
        } else {
            // If password update fails
            http_response_code(500); // Internal Server Error
            return json_encode([
                "success" => false,
                "message" => "Error resetting the password. Please try again."
            ]);
        }
    }
}
