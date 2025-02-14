<?php

namespace Controllers\Helpers;

// Load all dependencies using Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Controller responsible for validating the strength of a password.
 * This class contains the logic for checking if a password meets the required criteria.
 */
class validatePasswordController
{
    /**
     * Validates the provided password against predefined security rules.
     *
     * The password is checked to ensure it meets the following criteria:
     * - At least 8 characters in length
     * - Contains at least one number
     * - Contains at least one special character
     * - Contains at least one uppercase letter
     * - Contains at least one lowercase letter
     *
     * If the password passes all checks, it is considered valid.
     * If any check fails, a message is returned specifying which rule was violated.
     *
     * @param string $password The password to be validated.
     * @return array The validation result containing success status and an appropriate message.
     */
    public static function validate($password)
    {
        // Check if the password is at least 8 characters long
        if (strlen($password) < 8) {
            return ["success" => false, "message" => "Password must be at least 8 characters long."];
        }

        // Check if the password contains at least one digit
        if (!preg_match('/\d/', $password)) {
            return ["success" => false, "message" => "Password must contain at least one number."];
        }

        // Check if the password contains at least one special character
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            return ["success" => false, "message" => "Password must contain at least one special character."];
        }

        // Check if the password contains at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return ["success" => false, "message" => "Password must contain at least one uppercase letter."];
        }

        // Check if the password contains at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return ["success" => false, "message" => "Password must contain at least one lowercase letter."];
        }

        // If all checks pass, return a success message
        return ["success" => true, "message" => "Password is valid."];
    }
}
