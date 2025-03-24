<?php
// تضمين مكتبة MongoDB
require 'vendor/autoload.php';

// الاتصال بقاعدة البيانات
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->carsynce->sensorData; // تأكد من اسم المجموعة في MongoDB

// التحقق من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // قراءة البيانات المرسلة من الـ MQTT
    $data = json_decode(file_get_contents('php://input'), true);

    // التحقق من وجود البيانات المطلوبة
    if (isset($data['speed']) && isset($data['pressure']) && isset($data['temperature'])) {
        // إدخال البيانات في MongoDB
        $insertOneResult = $collection->insertOne([
            'speed' => $data['speed'],
            'pressure' => $data['pressure'],
            'temperature' => $data['temperature'],
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        // استجابة النجاح
        echo json_encode([
            'status' => 'success',
            'message' => 'Data inserted successfully!',
            'data' => $data,
            'inserted_id' => $insertOneResult->getInsertedId()
        ]);
    } else {
        // في حالة عدم وجود البيانات
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required data (speed, pressure, temperature)'
        ]);
    }
} else {
    // إذا كان الطلب ليس POST
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
}
?>
