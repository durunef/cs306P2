<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// MySQL Configuration
function get_mysql_connection() {
    static $mysql_conn = null;
    
    if ($mysql_conn === null) {
        $mysql_host = 'localhost';
        $mysql_user = 'root';
        $mysql_password = '';
        $mysql_db = 'GymDB';

        // Create MySQL connection
        $mysql_conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);

        // Check MySQL connection
        if ($mysql_conn->connect_error) {
            die("MySQL Connection failed: " . $mysql_conn->connect_error);
        }
    }

    return $mysql_conn;
}

// MongoDB Configuration
function get_mongodb_connection() {
    static $mongodb_conn = null;
    
    if ($mongodb_conn === null) {
        try {
            $mongodb_conn = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        } catch (MongoDB\Driver\Exception\Exception $e) {
            die("MongoDB Connection failed: " . $e->getMessage());
        }
    }

    return $mongodb_conn;
}

// Function to execute MongoDB queries
function executeMongoQuery($query) {
    $mongodb_conn = get_mongodb_connection();
    try {
        $result = $mongodb_conn->executeQuery("GymDB.tickets", $query);
        return $result->toArray();
    } catch (MongoDB\Driver\Exception\Exception $e) {
        die("MongoDB Query Error: " . $e->getMessage());
    }
}

// Function to execute MongoDB bulk operations
function executeMongoWrite($bulk) {
    $mongodb_conn = get_mongodb_connection();
    try {
        return $mongodb_conn->executeBulkWrite("GymDB.tickets", $bulk);
    } catch (MongoDB\Driver\Exception\Exception $e) {
        die("MongoDB Write Error: " . $e->getMessage());
    }
}
?> 