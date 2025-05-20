<?php
// MySQL Configuration
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_pass = '';
$mysql_db = 'GymDB';

try {
    // MySQL Connection
    $mysql_conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    if ($mysql_conn->connect_error) {
        throw new Exception("MySQL Connection failed: " . $mysql_conn->connect_error);
    }
    
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get MySQL connection
function get_mysql_connection() {
    global $mysql_conn;
    return $mysql_conn;
}

// MongoDB Configuration
$mongo_uri = 'mongodb://localhost:27017';
$mongo_db = 'GymDB';

try {
    $mongo_client = new MongoDB\Client($mongo_uri);
    $mongo_db = $mongo_client->selectDatabase($mongo_db);
} catch (Exception $e) {
    // Log MongoDB connection error but don't stop execution
    error_log("MongoDB Connection failed: " . $e->getMessage());
}

function get_mongo_db() {
    global $mongo_db;
    return $mongo_db;
}
?> 