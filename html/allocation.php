<?php

require 'vendor/autoload.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
    $allocationID = $_GET['id'];
} else {
    return;
}

$nomadBaseUrl = Nomad\Nomad::getAddress();

// Get allocation info
$allocationInfo = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/allocation/' . $allocationID));

// Fetch the node info for where the allocation is _actually_ stored so we can get real files, not just meta data info
$nodeInfo = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/node/' . $allocationInfo->NodeID));

$allocationUrl = 'http://' . $nodeInfo->HTTPAddr . '/';
$c = new Nomad\Crypt();

$allocationPathBase = $allocationUrl . '/v1/client/fs/ls/' . $allocationID .'?path=';
if ($_GET['path']) {
    $querypath = $c->decrypt($_GET['path']);
    if ($querypath == '/') {
        $querypath = '';
    }
} else {
    $querypath = '';
}
$allocationPath = $allocationPathBase . $querypath;

// get pathQuery for parent
if ($querypath != '') {
    $parentPath = $c->encrypt(dirname($querypath));
} else {
    $parentPath = false;
}

$ls = @json_decode(@file_get_contents($allocationPath));
if ($ls) {
    foreach ($ls as $idx => $lsitem) {
        if ($lsitem->IsDir == true) {
            $ls[$idx]->pathQuery = $c->encrypt($querypath.'/'.$lsitem->Name);
            $ls[$idx]->pathQueryPlain = $querypath.'/'.$lsitem->Name;
        } else {
            $ls[$idx]->fileQuery = $c->encrypt($querypath.'/'.$lsitem->Name);
            $ls[$idx]->fileQueryPlain = $querypath.'/'.$lsitem->Name;
        }
    }
}

if ($_GET['file']) {
    $file = $c->decrypt($_GET['file']);
    $fileStream = $c->encrypt([
        'address' => $nodeInfo->HTTPAddr,
        'allocationid' => $allocationID,
        'path' => $file,
    ]);
} else {
    $file = $fileStream = false;
}

$twig = Nomad\Nomad::getTwig();

echo $twig->render('allocation.html.twig', array(
    'shortid' => explode('-', $allocationID)[0],
    'allocationid' => $allocationID,
    'noinfo' => empty($allocationInfo),
    'allocation' => $allocationInfo,
    'currentPath' => $c->encrypt($querypath),
    'filelist' => $ls,
    'file' => $file,
    'filestream' => $fileStream,
    'parent' => $parentPath,
));
