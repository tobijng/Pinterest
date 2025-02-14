<?php
namespace Helpers;

// Autoload Composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';



use Config\Redis;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailHelper
{
    /**
     * Method to send a password reset email
     * This method generates a unique token, saves it to Redis,
     * and sends an email to the user with a reset link.
     *
     * @param string $email The user's email address to send the reset link
     * @param int $userId The user's unique ID
     * @return string Success or error message
     */
    public static function sendPasswordResetEmail($email, $userId)
    {
        // Generate a random 32-character token for password reset
        $token = bin2hex(random_bytes(16)); // Generates a 32-character token
        // Set the expiration time for the token (e.g., 1 hour)
        $expirationTime = 3600;  // 1 hour (in seconds)

        // Get the Redis connection instance
        $redis = Redis::getInstance()->getConnection();

        // Store the token in Redis with the user's ID, token as key
        // This ensures that we link the token to the user
        $redis->setex("password_reset_token:$token", $expirationTime, $userId);

        // Create the reset link to be sent in the email
        $resetLink = "http://localhost/reset-password.html?token=$token";

        // HTML template for the password reset email
        $template = "
        <html>
        <head>
            <title>Password Reset Request</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
                .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; }
                .header { text-align: center; background-color: #007bff; color: #fff; padding: 10px; border-radius: 5px 5px 0 0; }
                .footer { text-align: center; font-size: 12px; color: #999; margin-top: 20px; }
                .button { background-color: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Password Reset Request</h1>
                </div>
                <p>Hi there,</p>
                <p>We received a request to reset your password. If you didn't make this request, you can ignore this email.</p>
                <p>To reset your password, click the button below:</p>
                <p style='text-align: center;'>
                    <a href='$resetLink' class='button'>Reset Password</a>
                </p>
                <p>If you have any questions, feel free to reach out to us.</p>
                <p>Best regards,<br>Your Team</p>
                <div class='footer'>
                    <p>If you didn't request a password reset, please disregard this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Create a new PHPMailer instance to send the email
        $mail = new PHPMailer(true);

        try {
            // Configure the SMTP server (Gmail example)
            $mail->isSMTP();  // Set the email sending protocol to SMTP
            $mail->Host       = 'smtp.gmail.com';   // SMTP server (e.g., Gmail)
            $mail->SMTPAuth   = true;  // Enable SMTP authentication
            $mail->Username   = 'projektverteiltesystemeemail@gmail.com';  // Sender's email address
            $mail->Password   = 'tcxc himr kcda qtqk';  // SMTP password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
            $mail->Port       = 587;  // Port for TLS (587) or SSL (465)

            // Set the sender's email and recipient's email
            $mail->setFrom('projektverteiltesystemeemail@gmail.com', 'Password Reset Request');
            $mail->addAddress($email);  // Add recipient's email address

            // Set the email subject and body content (HTML email)
            $mail->isHTML(true);  // Enable HTML email content
            $mail->Subject = 'Password Reset';  // Subject line of the email
            $mail->Body    = $template;  // HTML body content (password reset template)
            $mail->AltBody = strip_tags($template);  // Plain-text version of the email content

            // Send the email
            $mail->send();
            return "Password reset email has been sent!";  // Return success message
        } catch (Exception $e) {
            // Return error message if something goes wrong
            return "Error sending email: {$mail->ErrorInfo}";
        }
    }
}
