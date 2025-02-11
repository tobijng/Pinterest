<?php

namespace Models\Auth;

namespace Models;

use Redis;

class RedisModel
{
    public static function storeResetToken($userId, $token)
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);

        // Speichert das Token für 1 Stunde
        $redis->setex("reset_token:$userId", 3600, $token);
    }

    public static function getResetToken($userId)
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);

        return $redis->get("reset_token:$userId");
    }
}
