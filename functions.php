<?php    
    // All project functions should be placed here

    // Database Configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'dct-ccs-finals');

    // Function to connect to the database
    function db_connection() {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check for connection errors
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        return $connection;
    }

    // Usage
    $connection = db_connection();

?>