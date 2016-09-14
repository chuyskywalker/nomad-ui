<?php

require 'vendor/autoload.php';

$nomadBaseUrl = Nomad\Nomad::getAddress();

$items = Nomad\Nomad::multiRequest([$nomadBaseUrl . '/v1/nodes']);
$nodes = @json_decode($items[0]);

$requestlist = [];
foreach ($nodes as $node) {
    $requestlist[] = $nomadBaseUrl . '/v1/node/' . $node->ID . '';
    $requestlist[] = $nomadBaseUrl . '/v1/node/' . $node->ID . '/allocations';
}

$infos = Nomad\Nomad::multiRequest($requestlist);

$clusterStats = [
    'available' => [
        'CPU'      => 0,
        'MemoryMB' => 0,
        'DiskMB'   => 0,
    ],
    'allocated' => [
        'CPU'      => 0,
        'MemoryMB' => 0,
        'DiskMB'   => 0,
    ]
];

$nodeStats = [];
foreach ($nodes as $nodeInfo) {
    $nodeStats[$nodeInfo->ID] = [
        'available' => [
            'CPU'      => 0,
            'MemoryMB' => 0,
            'DiskMB'   => 0,
        ],
        'allocated' => [
            'CPU'      => 0,
            'MemoryMB' => 0,
            'DiskMB'   => 0,
        ]
    ];
}

foreach ($infos as $info) {
    $info = json_decode($info);
    if (isset($info->HTTPAddr)) {
        // node info
        $clusterStats['available']['CPU'] += $info->Resources->CPU;
        $clusterStats['available']['MemoryMB'] += $info->Resources->MemoryMB;
        $clusterStats['available']['DiskMB'] += $info->Resources->DiskMB;

        $nodeStats[$info->ID]['available']['CPU'] = $info->Resources->CPU;
        $nodeStats[$info->ID]['available']['MemoryMB'] = $info->Resources->MemoryMB;
        $nodeStats[$info->ID]['available']['DiskMB'] = $info->Resources->DiskMB;
    } else {
        // node allocations
        foreach ($info as $alloc) {
            $clusterStats['allocated']['CPU'] += $alloc->Resources->CPU;
            $clusterStats['allocated']['MemoryMB'] += $alloc->Resources->MemoryMB;
            $clusterStats['allocated']['DiskMB'] += $alloc->Resources->DiskMB;

            $nodeStats[$alloc->NodeID]['allocated']['CPU'] += $alloc->Resources->CPU;
            $nodeStats[$alloc->NodeID]['allocated']['MemoryMB'] += $alloc->Resources->MemoryMB;
            $nodeStats[$alloc->NodeID]['allocated']['DiskMB'] += $alloc->Resources->DiskMB;
        }
    }
}


$twig = Nomad\Nomad::getTwig();
echo $twig->render('index.html.twig', array(
    'noinfo' => false,
    'baseurl' => $nomadBaseUrl,
    'nodes' => $nodes,
    'cluster' => $clusterStats,
    'nodesums' => $nodeStats,
));
