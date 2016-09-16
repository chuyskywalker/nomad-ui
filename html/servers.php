<?php

require 'vendor/autoload.php';

$nomadBaseUrl = Nomad\Nomad::getAddress();
$servers = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/agent/servers'));

$requestlist = [];
foreach ($servers as $server) {
    $requestlist[str_replace(':4647', '', $server)] = 'http://'. str_replace('4647', '4646', $server) .'/v1/agent/self';
}
// turn this on to see what happens if a server is offline/non-responsive
//$requestlist['192.1.1.1'] = 'http://192.1.1.1:4646/v1/agent/self';

$serversInfos = Nomad\Nomad::multiRequest($requestlist);
foreach ($serversInfos as $idx => $serversInfo) {
    $serversInfos[$idx] = @json_decode($serversInfo);
}

$twig = Nomad\Nomad::getTwig();
echo $twig->render('servers.html.twig', array(
    'noinfo' => empty($servers),
    'servers' => $servers,
    'serverinfos' => $serversInfos,
    'crumbs' => [
        'Home' => 'index.php',
    ],
    'raw' => [
        'Servers' => json_encode($servers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        'ServerInfos' => json_encode($serversInfos, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    ],
));
