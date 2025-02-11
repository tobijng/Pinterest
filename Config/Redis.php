<?php

namespace Config;  // Define the namespace for the Redis class

require_once __DIR__ . '/../vendor/autoload.php';  // Include Composer autoloader

use Dotenv\Dotenv;  // Import Dotenv class for environment variable loading
use Redis as RedisClient;  // Import the Redis class
use Exception;  // Import Exception class for handling errors

class Redis {
    private static $instance = null;  // Static instance for the Singleton pattern
    private $conn;  // Variable to hold the Redis connection object

    /**
     * Private constructor to prevent direct instantiation of the Redis class.
     * This ensures that only one instance of the Redis connection is created (Singleton pattern).
     */
    private function __construct() {
        // Load environment variables from the .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../.env', 'redis_Access.txt');
        $dotenv->load();  // Load the .env file into the environment

        // Retrieve Redis credentials from environment variables
        $host = $_ENV['REDIS_HOST'];
        $port = $_ENV['REDIS_PORT'];
        //$password = $_ENV['REDIS_PASSWORD'];  // Optional password

        try {
            // Create a new Redis client
            $this->conn = new RedisClient();

            // Connect to the Redis server
            $this->conn->connect($host, $port);

        } catch (Exception $exception) {
            // Create time stamp
            $timestamp = date("Y-m-d H:i:s");

            // Check, if log file is empty
            $logFile = __DIR__ . "/../logs/redis_errors.log";
            if (filesize($logFile) == 0) {
                // If log file is empty, create a header comment
                error_log("# Log File for Redis Connection Errors\n", 3, $logFile);
                error_log("# Redis connection errors are logged below:\n", 3, $logFile);
                error_log("# -----------------------------\n", 3, $logFile); // Dividing line
            }

            // Write the time stamp and the error into the log
            error_log("[$timestamp] Redis Connection Error: " . $exception->getMessage() . PHP_EOL, 3, $logFile);

            // Return the error as a Json
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Redis connection failed',
                'message' => $exception->getMessage(),
            ]);
            exit();
        }
    }

    /**
     * Static method to get the singleton instance of the Redis class.
     * It ensures that only one instance of the Redis connection is created.
     *
     * @return Redis The singleton instance of the Redis class.
     */
    public static function getInstance() {
        // If no instance exists, create one
        if (self::$instance === null) {
            self::$instance = new Redis();
        }
        return self::$instance;  // Return the singleton instance
    }

    /**
     * Method to get the current Redis connection.
     *
     * @return RedisClient The Redis connection object.
     */
    public function getConnection() {
        return $this->conn;  // Return the Redis connection object
    }

    /**
     * Prevent cloning of the Redis instance.
     * This ensures that no duplicate instances are created.
     */
    private function __clone() {
        throw new \Exception("Cloning a singleton is not allowed.");
    }

    /**
     * Prevent the instance from being unserialized.
     * This ensures that the singleton instance cannot be serialized.
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}

