<?php
$url = "http://localhost/project/api.php";

$data = [
    "speed" => 30,
    "pressure" => 35,
    "temperature" => 22
];

$options = [
    "http" => [
        "header" => "Content-Type: application/json\r\n",
        "method" => "POST",
        "content" => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
echo $response;
?>
