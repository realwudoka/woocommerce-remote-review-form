<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestData = json_decode(file_get_contents('php://input'), true);

    $wp_url = $requestData['wp_url'];
    $consumer_key = $requestData['consumer_key'];
    $consumer_secret = $requestData['consumer_secret'];
    $endpoint = $requestData['endpoint'];
    $data = isset($requestData['data']) ? $requestData['data'] : [];
    $method = isset($requestData['method']) ? $requestData['method'] : 'POST';

    $url = $wp_url . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($consumer_key . ':' . $consumer_secret),
        'Content-Type: application/json'
    ]);

    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    header('Content-Type: application/json');
    http_response_code($httpCode);
    echo $response;
}
?>
