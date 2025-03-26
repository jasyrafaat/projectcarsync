<?php
require 'vendor/autoload.php'; // تحميل مكتبة MongoDB
use MongoDB\Client;

// إعداد CORS للسماح بالاتصال من أي مصدر
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json'); // تحديد نوع المحتوى كـ JSON

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
    // جلب `MONGO_URI` من متغيرات البيئة
    $mongoUri = getenv('MONGO_URI');
    if (!$mongoUri) {
        throw new Exception("MONGO_URI environment variable is not set.");
    }

    // الاتصال بـ MongoDB
    $mongoClient = new Client($mongoUri);
    $database = $mongoClient->selectDatabase('carsynce');
    $collection = $database->selectCollection('sensors');

    // إدخال البيانات إلى قاعدة البيانات
    $insertResult = $collection->insertOne([
        'speed' => (int) $data['speed'],
        'pressure' => (int) $data['pressure'],
        'temperature' => (int) $data['temperature'],
        'timestamp' => new MongoDB\BSON\UTCDateTime() // حفظ التوقيت
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Data inserted successfully.",
        "inserted_id" => (string) $insertResult->getInsertedId()
    ]);
} catch (Exception $e) {
    error_log("MongoDB Error: " . $e->getMessage()); // تسجيل الخطأ

    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}
?>
