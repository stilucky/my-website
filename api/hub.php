<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$addresses = array_filter(
    array_map(fn($a) => preg_replace('/[^a-zA-Z0-9]/', '', trim($a)),
    explode(',', $_GET['validators'] ?? '')));

if (empty($addresses)) {
    echo '{"error":"missing validators param"}';
    exit;
}

// Debug mode: ?debug=1
$debug = !empty($_GET['debug']);

$nodes = [
    'https://bootstrap1.pactus.org',
    'http://bootstrap1.pactus.org',
];

$results = [];
$logs    = [];

foreach ($addresses as $addr) {
    $got = false;
    foreach ($nodes as $node) {
        $url = $node . '/validator/address/' . $addr;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        $logs[] = ['url' => $url, 'code' => $code, 'err' => $err, 'body_len' => strlen((string)$body)];

        if ($body && $code === 200) {
            $v = json_decode($body, true);
            if ($v) { $results[] = $v; $got = true; break; }
        }
    }
    if (!$got) $logs[] = ['addr' => $addr, 'status' => 'failed'];
}

$out = ['validators' => $results];
if ($debug) $out['debug'] = $logs;
echo json_encode($out);
