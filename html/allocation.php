<?php

require 'vendor/autoload.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
    $allocationID = $_GET['id'];
} else {
    return;
}

$nomadBaseUrl = Nomad\Nomad::getAddress();

$allocationRaw = @file_get_contents($nomadBaseUrl . '/v1/allocation/' . $allocationID);
$allocationInfo = @json_decode($allocationRaw);

// get node allocation is on's address
$nodeInfo = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/node/' . $allocationInfo->NodeID));

$allocationUrl = 'http://' . $nodeInfo->HTTPAddr . '/';

// find the std:err/out files
$allocLogs = json_decode(file_get_contents($allocationUrl . '/v1/client/fs/ls/' . $allocationID .'?path=/alloc/logs'));
$c = new Nomad\Crypt();
foreach ($allocLogs as $file) {
    if (stristr($file->Name, 'stderr')) {
        $stderr = $c->encrypt([
            'address' => $nodeInfo->HTTPAddr,
            'allocationid' => $allocationID,
            'path' => '/alloc/logs/'. $file->Name,
        ]);
    }
    elseif (stristr($file->Name, 'stdout')) {
        $stdout = $c->encrypt([
            'address' => $nodeInfo->HTTPAddr,
            'allocationid' => $allocationID,
            'path' => '/alloc/logs/'. $file->Name,
        ]);
    }
}

$twig = Nomad\Nomad::getTwig();

echo $twig->render('allocation.html.twig', array(
    'allocationid' => $allocationID,
    'noinfo' => empty($allocationInfo),
    'stderr' => $stderr,
    'stdout' => $stdout
));
