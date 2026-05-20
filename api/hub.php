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

function parseValidatorHtml(string $html): ?array {
    // Extract all <td>key</td><td>value</td> pairs
    preg_match_all('/<td>([^<]+)<\/td>\s*<td[^>]*>(?:<a[^>]*>)?([^<]+)(?:<\/a>)?<\/td>/i', $html, $m);
    if (empty($m[1])) return null;

    $data = [];
    foreach ($m[1] as $i => $key) {
        $data[trim($key)] = trim($m[2][$i]);
    }

    return [
        'address'            => $data['Address']            ?? '',
        'publicKey'          => $data['Public Key']         ?? '',
        'number'             => (int)($data['Number']       ?? 0),
        'stake'              => $data['Stake']              ?? '',
        'availabilityScore'  => (float)($data['Availability Score'] ?? 0),
        'lastBondingHeight'  => (int)($data['Last Bonding Height']  ?? 0),
        'unbondingHeight'    => (int)($data['Unbonding Height']     ?? 0),
        'isDelegated'        => isset($data['Delegate Owner']),
        'delegateOwner'      => $data['Delegate Owner']     ?? '',
        'delegateShare'      => $data['Delegate Share']     ?? '',
    ];
}

$results = [];

foreach ($addresses as $addr) {
    $url = 'https://bootstrap1.pactus.org/validator/address/' . $addr;
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0',
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body && $code === 200) {
        $v = parseValidatorHtml($body);
        if ($v && $v['address']) $results[] = $v;
    }
}

$out = ['validators' => $results];
if (!empty($_GET['debug'])) {
    // show raw HTML of first address for inspection
    $addr0 = array_values($addresses)[0];
    $ch = curl_init('https://bootstrap1.pactus.org/validator/address/' . $addr0);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10,CURLOPT_SSL_VERIFYPEER=>false]);
    $out['raw_html'] = curl_exec($ch);
    curl_close($ch);
}
echo json_encode($out);
