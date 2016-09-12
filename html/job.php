<?php

require 'vendor/autoload.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-_]+$/', $_GET['id'])) {
    $jobID = $_GET['id'];
} else {
    return;
}

$nomadBaseUrl = Nomad\Nomad::getAddress();
$items = Nomad\Nomad::multiRequest([
    $nomadBaseUrl . '/v1/job/' . $jobID,
    $nomadBaseUrl . '/v1/job/' . $jobID . '/allocations',
    $nomadBaseUrl . '/v1/job/' . $jobID . '/evaluations',
]);
$jobInfo            = @json_decode($items[0]);
$jobAllocationsInfo = @json_decode($items[1]);
$jobEvaluationsInfo = @json_decode($items[2]);

$twig = Nomad\Nomad::getTwig();

echo $twig->render('job.html.twig', array(
    'shortid' => explode('-', $jobID)[0],
    'jobid' => $jobID,
    'noinfo' => empty($jobInfo),
    'job' => $jobInfo,
    'allocations' => $jobAllocationsInfo,
    'evaluations' => $jobEvaluationsInfo,
));
