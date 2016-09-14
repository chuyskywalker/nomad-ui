<?php

require 'vendor/autoload.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
    $nodeID = $_GET['id'];
} else {
    return;
}

$nomadBaseUrl = Nomad\Nomad::getAddress();

$nodeInfo = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/node/' . $nodeID));
$nodeAllocationsInfo = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/node/' . $nodeID . '/allocations'));
$nodeStatsInfo = @json_decode(@file_get_contents('http://' . $nodeInfo->HTTPAddr . '/v1/client/stats'));

// summarize allocated resources
$resSum = [
    'CPU' => 0,
    'MemoryMB' => 0,
    'DiskMB' => 0,
];
foreach ($nodeAllocationsInfo as $alloc) {
    $resSum['CPU'] += $alloc->Resources->CPU;
    $resSum['MemoryMB'] += $alloc->Resources->MemoryMB;
    $resSum['DiskMB'] += $alloc->Resources->DiskMB;
}

$twig = Nomad\Nomad::getTwig();

echo $twig->render('node.html.twig', array(
    'nodeid' => $nodeID,
    'shortid' => explode('-',$nodeID)[0],
    'noinfo' => empty($nodeInfo),
    'nodeinfo' => $nodeInfo,
    'allocations' => $nodeAllocationsInfo,
    'stats' => $nodeStatsInfo,
    'allocated' => $resSum,
));
