<?php
// MySQL Configuration
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_pass = '';
$mysql_db = 'fitness_center';

// MongoDB Configuration
$mongo_uri = 'mongodb://localhost:27017';
$mongo_db = 'fitness_center';

try {
    // MySQL Connection
    $mysql_conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    if ($mysql_conn->connect_error) {
        throw new Exception("MySQL Connection failed: " . $mysql_conn->connect_error);
    }

    // MongoDB Connection
    $mongo_client = new MongoDB\Client($mongo_uri);
    $mongo_db = $mongo_client->selectDatabase($mongo_db);
    
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get MySQL connection
function get_mysql_connection() {
    global $mysql_conn;
    return $mysql_conn;
}

// Function to get MongoDB database
function get_mongo_db() {
    global $mongo_db;
    return $mongo_db;
}
?> 