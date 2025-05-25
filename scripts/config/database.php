<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// MySQL Configuration
function get_mysql_connection() {
    $mysql_host = 'localhost';
    $mysql_user = 'root';
    $mysql_password = '';
    $mysql_db = 'GymDB';

    try {
        $mysql_conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);
        
        if ($mysql_conn->connect_error) {
            throw new Exception("MySQL Connection failed: " . $mysql_conn->connect_error);
        }
        
        return $mysql_conn;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// MongoDB Configuration
function get_mongodb_connection() {
    require_once __DIR__ . '/../../vendor/autoload.php';

    try {
        // MongoDB connection string
        $mongodb_uri = "mongodb://localhost:27017";
        
        // Create new MongoDB client
        $mongodb_client = new MongoDB\Client($mongodb_uri);
        
        // Select database and collection
        $mongodb_db = $mongodb_client->GymDB;
        
        return $mongodb_db;
    } catch (Exception $e) {
        die("MongoDB Connection Error: " . $e->getMessage());
    }
}

// Function to execute MongoDB queries
function executeMongoQuery($filter = [], $options = []) {
    $mongodb_conn = get_mongodb_connection();
    try {
        $collection = $mongodb_conn->tickets;
        $cursor = $collection->find($filter, $options);
        
        // Convert BSON documents to arrays
        $results = [];
        foreach ($cursor as $document) {
            $doc = (array)$document;
            // Ensure comments are properly converted from BSON
            if (isset($doc['comments'])) {
                $doc['comments'] = array_map(function($comment) {
                    if ($comment instanceof MongoDB\Model\BSONDocument) {
                        return (object)$comment->getArrayCopy();
                    }
                    return $comment;
                }, (array)$doc['comments']);
            }
            $results[] = (object)$doc;
        }
        return $results;
    } catch (Exception $e) {
        die("MongoDB Query Error: " . $e->getMessage());
    }
}

// Function to execute MongoDB write operations
function executeMongoWrite($document) {
    $mongodb_conn = get_mongodb_connection();
    try {
        // Convert MongoDB\BSON\UTCDateTime to string for created_at
        $document['created_at'] = date('Y-m-d H:i:s');
        
        // Ensure correct schema
        $document = array_merge([
            'username' => '',
            'message' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'status' => true,
            'comments' => []
        ], $document);

        $collection = $mongodb_conn->tickets;
        return $collection->insertOne($document);
    } catch (Exception $e) {
        die("MongoDB Write Error: " . $e->getMessage());
    }
}

// Function to add a comment to a ticket
function addTicketComment($ticket_id, $comment) {
    $mongodb_conn = get_mongodb_connection();
    try {
        $collection = $mongodb_conn->tickets;
        return $collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($ticket_id)],
            ['$push' => ['comments' => (string)$comment]]
        );
    } catch (Exception $e) {
        die("MongoDB Comment Error: " . $e->getMessage());
    }
}
?> 