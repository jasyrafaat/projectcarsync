<?php
require 'vendor/autoload.php';

use MongoDB\Client;

try {
    $mongoUri = getenv("MONGO_URI");
    if (!$mongoUri) {
        throw new Exception("MongoDB URI not set in environment variables.");
    }

    $client = new Client($mongoUri);
    $database = $client->selectDatabase('carsynce');

    echo json_encode(["status" => "success", "message" => "Connected to MongoDB successfully."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
