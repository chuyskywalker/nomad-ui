<?php

require 'vendor/autoload.php';

if ($_GET['id'] && filter_var($_GET['id'], FILTER_VALIDATE_IP) !== false) {
    $serverID = $_GET['id'];
} else {
    return;
}

$serverInfo = @json_decode(@file_get_contents('http://' . $serverID . ':4646/v1/agent/self'));

$twig = Nomad\Nomad::getTwig();
echo $twig->render('server.html.twig', array(
    'noinfo' => empty($serverInfo),
    'serverid' => $serverID,
    'serverinfo' => $serverInfo,
    'crumbs' => [
        'Home' => 'index.php',
        'Servers' => 'servers.php',
    ],
    'raw' => [
        'serverInfo' => json_encode($serverInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    ],
));
