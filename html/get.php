<?php

$nomadBaseUrl = getenv('NOMAD_BASEURL') ?: 'http://127.0.0.1:4646';

$url = false;

switch ($_GET['type']) {

    case 'nodes':
        $url = 'nodes';
        break;

    case 'node':
        if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
            $url = 'node/' . $_GET['id'];
        }
        break;

    case 'jobs':
        $url = 'jobs';
        break;

    case 'allocations':
        $url = 'allocations';
        break;

    case 'evaluations':
        $url = 'evaluations';
        break;
}

header('Content-type: application/json');

if (!$url) {
    echo json_encode(false);
} else {
    $d = file_get_contents($nomadBaseUrl . '/v1/' . $url);
    $j = json_decode($d);
    echo json_encode(['data' => $j]);
}
