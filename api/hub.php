<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// All known validators for this hub — keyed by address
$KNOWN = [
    'pc1pmklncfdqc8dqqf56tvk2upd7exgjs9zq2k693p' => 'public1p5zeyj4538neyff7fvr0ze5vndfy73ru72y98l49296dnr0el9vkl9exmnphx33we48v9wph3acmyyqlj3jsec2t4z4pd4vtrpd4dh76kkrq4nultafq2nd7ysrkp0rvh9292wx6pu8gfwvugcswrfq4mlqvcce46',
    'pc1pamhkjej7qmys7w729n92v5ep4v0uj6938vt4d5' => 'public1pjf5an7hacww5khxymr8lsa3qfesgr27rv3lts8yl8ryfewxm83yxwxxvjz7tj75czlrxkv57uu5nxr933tskgjjl6p6utawkphnxtv3j5caft2pm0828ufngctgx2vwkwtghsf9xjm47dy5w494hnc2w4ua6snsn',
    'pc1py6kcs6yzzvy33p76fdw9fyercrpxquen6mpvn4' => 'public1p3mscn52mtkeqxywyldqgqc4u92p3han7vdgr4ylkkphq55cunxx7es3mgj9z2ywa98nqu2y6avlsxr2k5vta9vmpg490m7r9e4vq0dy0yl7ek79380xcm47rj8dv8gwruzcjtjnnmtk7xwfsehzwm44dkvttx5r9',
    'pc1pl63czl2l0xadh9vrxztaadjw744sf7z7putkfa' => 'public1pj3dz2qfnnxmhspwarrajs6knq70vzt0vp3hjmnzfdefut9peh0kg7jpq2qapk6tff5jmtw8g5fteuqz8rl05dz3kulyjndngt9csz9rtk2t7gwaf3jcydwdpmyl30xqqw52wcy4vuty7u3t22sc5mlkutgrz4j89',
    'pc1puvwzsfh52r0ly9zhcvsnsl3elmza2yux0wd6hw' => 'public1p3ash7knt5wpq4ypd7j5f7g0ydc45uanngy9cvr9utgh448g6udnj3p6p6xlzs63m43eafgp30d887y3nc3wl6lsu98zyfljh0dav7vhv2kmas4xyegv56rqzlpe6mz0ffpfurewv7840q7uu4685ew7auuluxtww',
    'pc1puh3g6hallnnefjmnvhcctvxk362t6xnumkc07u' => 'public1p3lgtphplh45kfur3jgvkrpm6rwfxgrmgsyekgpl6zacct8kp6u7659fzdar8tk2vcrslj73ku4xdcqaz9kalmy80zy0wvvgax3ytej390uwu5js8fk06ckd9ggjgzvvzv7vxl7f9l8hfx0ukg2am354hhufw8wdm',
    'pc1p2f97wt366uerq828zvtvgmulxuyhvwzujgu9ss' => 'public1psqc8pyj6hkq3qqqu7u3nzf642fkyccrpx03hgyfem6attdsp5xt83hng3khkpaq0z2t84pdq70vlwrkynjan9x3578407un9ndf0tzf494g3306cjw4endsrvfe86d7khejy00lhmfnwgter868dnq35lulgu6xc',
    'pc1pjedfuvwkzc7psh9lya09zaml0n3039smculv2m' => 'public1p4quchtk70vtc8ea8gn3l6juk9qwvqu2333p2slf6afjlk2duauzfps8hyrst6kqmke68q8h9ep8gyzpk68vm8wyatt9znx2xfuvf9llc3asc68jfwm7jxtnzutcr4q9weawrsq30u5prc232v8fvhjcgtu2ufsxy',
    'pc1pagndrhug3kdc60uthwgmc2py3p4nw6mlzx5j5x' => 'public1pjkqe4v9y5rj8vhcmgj9p6sx07q0gzfk403w7u9e8cf6pnmnu6n87qwsu4t5ntyv8xrhhzuej2htd6y8vs9ahffdwr6k4etd2u23pyp2shk6csnq7l3yd9a3haq5hqr5w24zg5zt6wegqf0fhkarkj6q3lcu6080r',
    'pc1pazrndf4jgn0vg8fpjz3ywtk3qyhc0zg9uk9spm' => 'public1p3pdk43v2vxxcp9hp370v0erknxackye74qkqmwem9dtu5fw7rnqw4qy4h7g28n8hz8mgq40dsu3hwrjffxul0ksdruaqfe5mgdrel4xxkltkn0ftmdw34tgd7alydwq9en28r4juwzxkepw2lz48nrlw4vxwggz9',
    'pc1p2g3p754z9xu9v099cm87uwcclxgr40xcedywmx' => 'public1ps7ndf247f26rnyng6hznujk8dehhmz5j2xa4dten2qmcz7ym5vhrg6wfg6w2xwt3wu6h935uk2k069dy0ayr8v6gp5ke7yhdzxadtj5atls9z5uawzrn72qheflugc6v0zfdns7x2w5k7cdwmf4asg0rqg0n9dlu',
    'pc1pp6g94d3y58y336t6h4lxpzlua29k8z8sl7t9h0' => 'public1p4jdpjd4m363g5tyrdtrtnm0shg70z55883p20e8ez59mqg0ytj07mz2l9udzmn5rgpp2ner7k4fq7zw5mpx4pdp9mp3490rdu96zngzx8pdxm6ydd5kz2mvarxuqmnscc6nz27j9h70fl9nt49nxnylrgqyxhtrx',
    'pc1prn3tvxs93tn566a2652fsjw4qewufl2drzx3r3' => 'public1psnrl5ldjxyf9ztzvru2rz079j7ugmdgwpqermvgpwycjlmucah0mlq3pq9jwqyjjr5w8h4xmjhme7yjmm2djwkwxntfqqqzdgyfw9r94awhtspspfch6tn4lupqxqpna0wpfww3xqa42307h8gasvu9chyygs5ft',
    'pc1p3zw4lt7ysgp33aq2y4nkrwyd5sa0ad3e8upadm' => 'public1phyklsn4n3xcfcm336d89nnlulhy7gpmcxmyhunauehaw5qu72tu6cdmaanujymlhcxxnpfmgsz3njyzjup5n0gkm9vnny20k9wyefmt6khm4p5u25lxe8dpmggjsnj5aqv73j0gmj8s3hn2hz58cf46njc4dumph',
    'pc1pv5pf9tuap0ahhq4rmjswmupcws7wvny9rtldjh' => 'public1p5c7rjw7azer9q9759ryy7lee0hcnltr73e9ntlycxj4ntg89dsdrzf043tk5wutg4c3qu2hvl8tvs95zp683arktqdfz6pxx943hv9exj9x44wggpxh2ftkhnvazwgjaswfj4g6953fmwp3kd3wlyfetfguma8t8',
];

$curlBase = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
];

// Query pactusscan individually for each known validator (parallel)
$mh      = curl_multi_init();
$handles = [];

foreach (array_keys($KNOWN) as $addr) {
    $ch = curl_init("https://pactusscan.com/api/v1/validators/{$addr}");
    curl_setopt_array($ch, $curlBase);
    curl_multi_add_handle($mh, $ch);
    $handles[$addr] = $ch;
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

$openSlots  = [];
$gotResults = false;

foreach ($handles as $addr => $ch) {
    $body = curl_multi_getcontent($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);

    if (!$body || $code !== 200) continue;
    $gotResults = true;

    $v = json_decode($body, true);
    // pactusscan may wrap in {validator:{...}} or return the object directly
    if (isset($v['validator'])) $v = $v['validator'];

    $isDelegated = $v['is_delegated'] ?? $v['IsDelegated'] ?? null;

    // Keep only if explicitly NOT delegated
    if ($isDelegated === false || $isDelegated === 'false' || $isDelegated === 0) {
        $openSlots[] = ['address' => $addr, 'publicKey' => $KNOWN[$addr]];
    }
}

curl_multi_close($mh);

// If API responded but returned empty — all slots delegated
if ($gotResults && empty($openSlots)) {
    echo json_encode(['validators' => [], 'fallback' => false]);
    exit;
}

// If API did not respond at all — return full hardcoded list as cached fallback
if (empty($openSlots)) {
    foreach ($KNOWN as $addr => $pubkey) {
        $openSlots[] = ['address' => $addr, 'publicKey' => $pubkey];
    }
    echo json_encode(['validators' => $openSlots, 'fallback' => true]);
    exit;
}

echo json_encode(['validators' => $openSlots]);
