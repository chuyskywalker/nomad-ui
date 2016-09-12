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

// sort allocations by createtime
usort($jobAllocationsInfo, function($a, $b) {
    if ($a->CreateTime == $b->CreateTime) {
        return 0;
    }
    return ($a->CreateTime < $b->CreateTime) ? 1 : -1;
});

// Find latest allocation(s) info, tie it in
foreach ($jobInfo->TaskGroups as $tgidx => $taskGroup) {

    $allocationsIds = [];
    for($i = 0; $i < $taskGroup->Count; $i++) {
        foreach ($jobAllocationsInfo as $ainfo) {
            // ex: loopy.loopy-group-two[0]
            if ($ainfo->Name == $jobInfo->Name . '.' . $taskGroup->Name . '[' . $i . ']') {
                $allocationsIds[] = $ainfo->ID;
                break;
            }
        }
    }
    $jobInfo->TaskGroups[$tgidx]->AllocationIDs = $allocationsIds;
}

// Generate a lovely list of all allocation events
$allocationHistory = [];
foreach ($jobAllocationsInfo as $ainfo) {
    foreach ($ainfo->TaskStates as $taskId => $taskInfo) {
        foreach ($taskInfo->Events as $eventInfo) {
            $allocationHistory[] = array_merge([
                'ID' => $ainfo->ID,
                'EvalID' => $ainfo->EvalID,
                'NodeID' => $ainfo->NodeID,
                'GroupStatus' => $ainfo->ClientStatus,
                'Name' => explode('.', $ainfo->Name, 2)[1],
                'Task' => $taskId,
            ], (array)$eventInfo);
        }
    }
}
usort($allocationHistory, function($a, $b) {
    if ($a['Time'] == $b['Time']) {
        return 0;
    }
    return ($a['Time'] < $b['Time']) ? 1 : -1;
});

$twig = Nomad\Nomad::getTwig();

echo $twig->render('job.html.twig', array(
    'shortid' => explode('-', $jobID)[0],
    'jobid' => $jobID,
    'noinfo' => empty($jobInfo),
    'job' => $jobInfo,
    'allocations' => $jobAllocationsInfo,
    'evaluations' => $jobEvaluationsInfo,
    'allocationHistory' => $allocationHistory,
));
