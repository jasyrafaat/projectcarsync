<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$mongoClient = new Client("mongodb://localhost:27017");
$collection = $mongoClient->carsynce->sensors;

$data = $collection->find([], ['sort' => ['timestamp' => -1], 'limit' => 10]);

$response = [];
foreach ($data as $row) {
    $response[] = [
        "_id" => (string) $row->_id,
        "speed" => $row->speed,
        "pressure" => $row->pressure,
        "temperature" => $row->temperature,
        "timestamp" => date('Y-m-d H:i:s', $row->timestamp->toDateTime()->getTimestamp())
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
