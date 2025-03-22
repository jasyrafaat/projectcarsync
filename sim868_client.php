<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

$httpClient = new Client();
$apiEndpoint = 'http://localhost/sensor_api.php'; 

while (true) {
    $data = [
        'speed' => rand(0, 120),
        'pressure' => rand(20, 40),
        'temperature' => rand(15, 35)
    ];

    try {
        $response = $httpClient->post($apiEndpoint, ['json' => $data]);
        echo "Data sent to API: " . $response->getBody() . "\n";
    } catch (Exception $e) {
        echo "Failed to send data to API: " . $e->getMessage() . "\n";
    }

    sleep(1);
}
?>
