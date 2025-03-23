<?php
require 'vendor/autoload.php'; // تحميل مكتبة MongoDB

use MongoDB\Client;

// إعداد CORS للسماح بالاتصال من أي مصدر
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// السماح فقط بطلبات POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method. Only POST is allowed."]);
    exit;
}

// قراءة البيانات المرسلة من الهاردوير
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// التحقق من صحة البيانات المستلمة
if (!isset($data['speed'], $data['pressure'], $data['temperature'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required sensor data."]);
    exit;
}

try {
    // الاتصال بـ MongoDB باستخدام URI مباشر
    $mongoUri = "mongodb+srv://jasyrafaat:jasy2002@cluster0.ng0is.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";
    $mongoClient = new Client($mongoUri);

    // تحديد قاعدة البيانات والمجموعة
    $database = $mongoClient->selectDatabase('carsynce'); 
    $collection = $database->selectCollection('sensors'); 

    // إدخال البيانات إلى قاعدة البيانات
    $insertResult = $collection->insertOne([
        'speed' => (int) $data['speed'],
        'pressure' => (int) $data['pressure'],
        'temperature' => (int) $data['temperature'],
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Data inserted successfully.",
        "inserted_id" => (string) $insertResult->getInsertedId()
    ]);
} catch (Exception $e) {
    // تسجيل الخطأ في ملف log
    error_log("MongoDB Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

?>
