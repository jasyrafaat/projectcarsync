<?php
require 'vendor/autoload.php';
use MongoDB\Client;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); // تحديد نوع المحتوى كـ JSON

try {
    // جلب `MONGO_URI` من متغيرات البيئة
    $mongoUri = getenv('MONGO_URI');
    if (!$mongoUri) {
        throw new Exception("MONGO_URI environment variable is not set.");
    }

    // الاتصال بـ MongoDB
    $mongo = new Client($mongoUri);
    $collection = $mongo->carsynce->sensors;

    // جلب البيانات وترتيبها من الأحدث إلى الأقدم
    $documents = $collection->find([], ['sort' => ['timestamp' => -1]]);

    $data = [];
    foreach ($documents as $doc) {
        $data[] = [
            'speed' => $doc['speed'] ?? null,
            'pressure' => $doc['pressure'] ?? null,
            'temperature' => $doc['temperature'] ?? null,
            'timestamp' => isset($doc['timestamp']) 
                ? date('Y-m-d H:i:s', $doc['timestamp']->toDateTime()->getTimestamp()) 
                : null
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
