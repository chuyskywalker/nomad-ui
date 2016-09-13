<?php

require 'vendor/autoload.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
    $nodeID = $_GET['id'];
} else {
    return;
}

$nomadBaseUrl = Nomad\Nomad::getAddress();

$nodeRaw = @file_get_contents($nomadBaseUrl . '/v1/node/' . $nodeID);
$nodeInfo = @json_decode($nodeRaw);

$nodeAllocationsRaw = @file_get_contents($nomadBaseUrl . '/v1/node/' . $nodeID . '/allocations');
$nodeAllocationsInfo = @json_decode($nodeAllocationsRaw);

$nodeStatsRaw = @file_get_contents('http://' . $nodeInfo->HTTPAddr . '/v1/client/stats');
$nodeStatsInfo = @json_decode($nodeStatsRaw);

$twig = Nomad\Nomad::getTwig();

echo $twig->render('node.html.twig', array(
    'nodeid' => $nodeID,
    'shortid' => explode('-',$nodeID)[0],
    'noinfo' => empty($nodeInfo),
    'nodeinfo' => $nodeInfo,
    'allocations' => $nodeAllocationsInfo,
    'stats' => $nodeStatsInfo,
));
