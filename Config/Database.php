<?php
namespace Config;  // Define the namespace for the Database class

require_once __DIR__ . '/../vendor/autoload.php';  // Include Composer autoloader

use Dotenv\Dotenv;  // Import Dotenv class for environment variable loading
use PDO;  // Import PDO for database connection
use PDOException;  // Import PDOException for handling connection errors

class Database {
    private static $instance = null;  // Static instance for the Singleton pattern
    private $conn;  // Variable to hold the PDO connection object

    /**
     * Private constructor to prevent direct instantiation of the Database class.
     * This ensures that only one instance of the Database connection is created (Singleton pattern).
     */
    private function __construct() {
        // Load environment variables from the .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../.env', 'Database_Access.txt');
        $dotenv->load();  // Load the .env file into the environment

        // Retrieve database credentials from environment variables
        $host = $_ENV['DB_HOST'];
        $db_name = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $driver = $_ENV['DB_DRIVER'];

        try {
            // Establish the database connection using PDO
            $this->conn = new PDO("$driver:host=$host;dbname=$db_name;charset=utf8", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set error mode to exception for debugging
        } catch (PDOException $exception) {
            // Create time stamp
            $timestamp = date("Y-m-d H:i:s");

            // Check, if log file is empty
            $logFile = __DIR__ . "/../logs/db_errors.log";
            if (filesize($logFile) == 0) {
                // If log file is empty, create a overview comment
                error_log("# Log File for Database Connection Errors\n", 3, $logFile);
                error_log("# Database connection errors are logged below:\n", 3, $logFile);
                error_log("# -----------------------------\n", 3, $logFile); // Dividing line
            }

            // Write the time stamp and the error into the log
            error_log("[$timestamp] DB Connection Error: " . $exception->getMessage() . PHP_EOL, 3, $logFile);

            // Return the error as a Json
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Database connection failed',
                'message' => $exception->getMessage(),
            ]);
            exit();
        }

    }

    /**
     * Static method to get the singleton instance of the Database class.
     * It ensures that only one instance of the Database connection is created.
     *
     * @return Database The singleton instance of the Database class.
     */
    public static function getInstance() {
        // If no instance exists, create one
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;  // Return the singleton instance
    }

    /**
     * Method to get the current database connection.
     *
     * @return PDO The PDO connection object.
     */
    public function getConnection() {
        return $this->conn;  // Return the PDO connection object
    }

    /**
     * Prevent cloning of the Database instance.
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
