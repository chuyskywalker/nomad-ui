<?php

require 'vendor/autoload.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
    $evaluationID = $_GET['id'];
} else {
    return;
}

$nomadBaseUrl = Nomad\Nomad::getAddress();
$items = Nomad\Nomad::multiRequest([
    $nomadBaseUrl . '/v1/evaluation/' . $evaluationID,
    $nomadBaseUrl . '/v1/evaluation/' . $evaluationID . '/allocations',
]);
$evaluationInfo           = @json_decode($items[0]);
$evaluationAllocationInfo = @json_decode($items[1]);

$twig = Nomad\Nomad::getTwig();
echo $twig->render('evaluation.html.twig', array(
    'shortid' => explode('-', $evaluationID)[0],
    'evaluationid' => $evaluationID,
    'noinfo' => empty($evaluationInfo),
    'evaluation' => $evaluationInfo,
    'allocations' => $evaluationAllocationInfo,
));