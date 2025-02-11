<?php

namespace Controllers\Auth;

use Models\UserModel;
use Models\EmailModel;

class resetPasswordController
{
    public function resetPassword()
    {
        // Holen der POST-Daten
        $email = $_POST['email'];

        // Prüfen, ob die E-Mail in der Datenbank existiert
        $user = UserModel::getUserByEmail($email);

        if (!$user) {
            echo json_encode(["success" => false, "message" => "E-Mail nicht gefunden."]);
            return;
        }

        // Passwort zurücksetzen und Token generieren
        $resetToken = bin2hex(random_bytes(32));  // Ein zufälliges Token

        // Speichern des Tokens in Redis (oder in der Datenbank, je nach deiner Entscheidung)
        RedisModel::storeResetToken($user['id'], $resetToken);

        // E-Mail mit Passwort-Zurücksetzungslink senden
        $resetLink = "http://yourapp.com/reset_password?token=$resetToken";
        $emailModel = new EmailModel();
        $emailModel->sendPasswordResetEmail($email, $resetLink);

        echo json_encode(["success" => true, "message" => "E-Mail zum Zurücksetzen des Passworts wurde gesendet."]);
    }
}
