<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$hub = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['hub'] ?? '');
if (!$hub) { echo '{"error":"missing hub param"}'; exit; }

// Mapping hub reward address → on-chain delegate_owner account address
$hubMap = [
    'pc1rpwp6zcqx3vw76y4j2hj69m3kf4agk37zc26nf6' => 'pc1z24smayvvyalglr9sfpyz0yscdn38fh0p5hud3k',
];
$delegateOwner = $hubMap[$hub] ?? $hub;

$curlOpts = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 12,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
];

$openSlots = [];
$page = 1;

do {
    $url = "https://pactusscan.com/api/v1/validators?delegate_owner={$delegateOwner}&page={$page}";
    $ch  = curl_init($url);
    curl_setopt_array($ch, $curlOpts);
    $body  = curl_exec($ch);
    $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$body || $code !== 200) break;
    $data  = json_decode($body, true);
    $list  = $data['validators'] ?? [];
    $total = (int)($data['total'] ?? 0);
    $pages = (int)($data['pages'] ?? 1);

    // Nếu không được filter (trả về toàn bộ 9000+ validators) thì dừng
    if ($total > 500) break;

    foreach ($list as $v) {
        if (($v['is_delegated'] ?? true) === false && !empty($v['address'])) {
            $openSlots[] = [
                'address'   => $v['address'],
                'publicKey' => $v['public_key'] ?? '',
            ];
        }
    }
    $page++;
} while ($page <= $pages);

// Fallback: nếu pactusscan không filter được, dùng SLOTS cứng từ bootstrap1
if (empty($openSlots)) {
    // Trả về rỗng để JS dùng cached SLOTS
    echo json_encode(['validators' => [], 'fallback' => true]);
    exit;
}

echo json_encode(['validators' => $openSlots]);
