<?php
require 'vendor/autoload.php'; // تحميل مكتبة MongoDB

use MongoDB\Client;

try {
    $mongoClient = new Client("mongodb+srv://jasyrafaat:jasy2002@cluster0.ng0is.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");

    $database = $mongoClient->selectDatabase('carsynce');
    $collection = $database->selectCollection('sensors');

    echo "✅ Successfully connected to MongoDB Atlas!";
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
