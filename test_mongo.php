<?php
require 'vendor/autoload.php'; // تحميل مكتبة MongoDB

$client = new MongoDB\Client("mongodb://localhost:27017");

try {
    $dbs = $client->listDatabases();
    echo "✅ اتصال ناجح بـ MongoDB!";
} catch (Exception $e) {
    echo "❌ فشل الاتصال: " . $e->getMessage();
}
?>
