<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$mail = new PHPMailer(true);

try {
    // SMTP-Server Konfiguration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';   // SMTP-Server des Anbieters (z. B. Gmail)
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tobiasjung112@gmail.com';  // Deine Absender-Adresse
    $mail->Password   = '';  // SMTP-Passwort oder App-Passwort
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS-Verschlüsselung
    $mail->Port       = 587;  // Typischerweise 587 für TLS, 465 für SSL

    // Absender & Empfänger
    $mail->setFrom('tobiasjung112@gmail.com', 'Tobias Jung');
    $mail->addAddress('tobijung46@gmail.com', 'Tobi Name');

    // E-Mail-Inhalt
    $mail->isHTML(true);
    $mail->Subject = 'Test-E-Mail';
    $mail->Body    = 'Dies ist eine Testnachricht von <b>PHPMailer</b>.';
    $mail->AltBody = 'Dies ist eine Testnachricht von PHPMailer (Text-Version).';

    // E-Mail senden
    $mail->send();
    echo "E-Mail wurde gesendet!";
} catch (Exception $e) {
    echo "Fehler: {$mail->ErrorInfo}";
}

