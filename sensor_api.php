<?php
require 'vendor/autoload.php'; // تحميل مكتبة MongoDB

use MongoDB\Client;

// السماح فقط بطلبات POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// قراءة البيانات المرسلة من الهاردوير
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || !isset($data['speed']) || !isset($data['pressure']) || !isset($data['temperature'])) {
    echo json_encode(["status" => "error", "message" => "Invalid data received."]);
    exit;
}

try {
    // الاتصال بـ MongoDB على Railway
    $mongo = new Client("mongodb://admin:password@projectcarsync-production.up.railway.app:27017/carsynce");

    $collection = $mongo->carsynce->sensors;

    // إدخال البيانات إلى قاعدة البيانات
    $insertResult = $collection->insertOne([
        'speed' => (int) $data['speed'],
        'pressure' => (int) $data['pressure'],
        'temperature' => (int) $data['temperature'],
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ]);

    echo json_encode(["status" => "success", "message" => "Data inserted successfully.", "inserted_id" => (string) $insertResult->getInsertedId()]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
