<?php
require 'vendor/autoload.php';
use MongoDB\Client;

header('Content-Type: application/json'); // تحديد نوع المحتوى كـ JSON

try {
    // الاتصال بـ MongoDB
    $mongo = new Client("mongodb+srv://jasyrafaat:jasy2002@cluster0.ng0is.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
    $collection = $mongo->carsynce->sensors;

    // جلب البيانات وتحويل الـ timestamp إلى تاريخ قابل للقراءة
    $documents = $collection->find([], ['sort' => ['timestamp' => -1]]); // ترتيب البيانات من الأحدث إلى الأقدم

    $data = [];
    foreach ($documents as $doc) {
        $data[] = [
            'speed' => $doc['speed'],
            'pressure' => $doc['pressure'],
            'temperature' => $doc['temperature'],
            'timestamp' => date('Y-m-d H:i:s', $doc['timestamp']->toDateTime()->getTimestamp()) // تحويل الـ timestamp
        ];
    }

    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
