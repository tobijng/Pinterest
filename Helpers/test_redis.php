<?php


// Script for generating a redis entry for test purpose

require_once __DIR__ . '/../vendor/autoload.php'; // Falls du Composer nutzt

use Config\Redis;

try {
    // Benutzer-ID, für die der Token generiert wird
    $userId = 8;

    // Generiere ein zufälliges 32-stelliges Token
    $token = bin2hex(random_bytes(16));

    // Setze die Gültigkeitsdauer des Tokens (z. B. 1 Stunde)
    $expirationTime = 3600;

    // Verbindung zu Redis
    $redis = Redis::getInstance()->getConnection();

    // Token in Redis mit der User-ID speichern
    $redis->setex("password_reset_token:$token", $expirationTime, $userId);

    echo json_encode([
        "success" => true,
        "message" => "Token generated successfully.",
        "token" => $token
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error generating token: " . $e->getMessage()
    ]);
}
