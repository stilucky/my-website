<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Accept comma-separated validator addresses
$addresses = array_filter(
    array_map(fn($a) => preg_replace('/[^a-zA-Z0-9]/', '', trim($a)),
    explode(',', $_GET['validators'] ?? '')));

if (empty($addresses)) {
    echo '{"error":"missing validators param"}';
    exit;
}

$nodes = [
    'https://bootstrap1.pactus.org',
    'https://bootstrap2.pactus.org',
];

$results = [];
$curlOpts = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 6,
    CURLOPT_SSL_VERIFYPEER => true,
];

foreach ($addresses as $addr) {
    foreach ($nodes as $node) {
        $ch = curl_init($node . '/validator/address/' . $addr);
        curl_setopt_array($ch, $curlOpts);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($body && $code === 200) {
            $v = json_decode($body, true);
            if ($v) { $results[] = $v; break; }
        }
    }
}

echo json_encode(['validators' => $results]);
