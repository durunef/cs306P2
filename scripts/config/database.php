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
?> 