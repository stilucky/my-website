<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$address = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['address'] ?? '');
if (!$address) { echo '{}'; exit; }

$url = 'https://pactusscan.com/api/v1/hub/' . $address;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 8,
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
    CURLOPT_SSL_VERIFYPEER => true,
]);
$result = curl_exec($ch);
$code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo ($result && $code === 200) ? $result : '{"error":"upstream failed","code":' . $code . '}';
