<?php

require 'vendor/autoload.php';

$nomadBaseUrl = Nomad\Nomad::getAddress();

$items = Nomad\Nomad::multiRequest([
    $nomadBaseUrl . '/v1/nodes',
    $nomadBaseUrl . '/v1/jobs',
    $nomadBaseUrl . '/v1/allocations',
    $nomadBaseUrl . '/v1/evaluations',
]);

$nodes = @json_decode($items[0]);
$jobs  = @json_decode($items[1]);
$allocs= @json_decode($items[2]);
$evals = @json_decode($items[3]);

$twig = Nomad\Nomad::getTwig();

echo $twig->render('index.html.twig', array(
    'noinfo' => empty($nodes),
    'baseurl' => $nomadBaseUrl,
    'nodes' => $nodes,
    'jobs' => $jobs,
    'allocations' => $allocs,
    'evaluations' => $evals,
));
