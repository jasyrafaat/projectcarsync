<?php
require 'vendor/autoload.php';
use MongoDB\Client;

// CORS Headers (مش لازم للهاردوير لكن مش هيضر)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method. Only POST is allowed."]);
    exit;
}

// قراءة البيانات من json أو x-www-form-urlencoded
$input = file_get_contents("php://input");
$contentType = $_SERVER["CONTENT_TYPE"] ?? '';

if (stripos($contentType, 'application/json') !== false) {
    $data = json_decode($input, true);
} else {
    // ممكن تكون البيانات مبعوتة بصيغة form أو query
    $data = $_POST;
    if (empty($data)) {
        parse_str($input, $data);
    }
}

// التحقق من وجود البيانات المطلوبة
if (!isset($data['speed'], $data['pressure'], $data['temperature'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required sensor data."]);
    exit;
}

try {
    $mongoUri = getenv('MONGO_URI');
    if (!$mongoUri) {
        throw new Exception("MONGO_URI environment variable is not set.");
    }

    $mongoClient = new Client($mongoUri);
    $database = $mongoClient->selectDatabase('carsynce');
    $collection = $database->selectCollection('sensors');

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
    error_log("MongoDB Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}
?>
