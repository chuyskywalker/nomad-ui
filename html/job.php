<?php

$header = 'Job Info';
require '_header.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-_]+$/', $_GET['id'])) {
    $jobID = $_GET['id'];
} else {
    return;
}

$jobRaw = @file_get_contents($nomadBaseUrl . '/v1/job/' . $jobID);
$jobInfo = @json_decode($jobRaw);

$jobAllocationsRaw = @file_get_contents($nomadBaseUrl . '/v1/job/' . $jobID . '/allocations');
$jobAllocationsInfo = @json_decode($jobAllocationsRaw);

$jobEvaluationsRaw = @file_get_contents($nomadBaseUrl . '/v1/job/' . $jobID . '/evaluations');
$jobEvaluationsInfo = @json_decode($jobEvaluationsRaw);


$constraintList = function($ci) {
    if (empty($ci)) {
        return 'None';
    }
    $a = [];
    foreach ($ci as $c) {
        $a[] = trim($c->LTarget . ' ' . $c->Operand . ' ' . $c->RTarget);
    }
    return implode("<br/>", $a);
};

?>

<?php if (empty($jobInfo)) { ?>
<div class="alert alert-danger">
    <strong>Error</strong> Could not fetch job/node status(es)
</div>
<?php } else { ?>

<h2>General Info</h2>
<table class="table table-bordered">
    <tbody>
        <?php

        $ds = [
            'ID' => $jobInfo->ID,
            'Name' => $jobInfo->Name,
            'Datacenter' => implode(', ', $jobInfo->Datacenters),
            'Type' => $jobInfo->Type,
            'Status' => $jobInfo->Status,
            'Priority' => $jobInfo->Priority,
            'Tasks Groups' => (function($jobInfo) {
                if (empty($jobInfo->TaskGroups)) {
                    return 'None';
                }
                $a = [];
                foreach ($jobInfo->TaskGroups as $tg) {
                    $a[] = $tg->Name;
                }
                return implode(", ", $a);
            })($jobInfo),
            'Tasks' => (function($jobInfo) {
                if (empty($jobInfo->TaskGroups)) {
                    return 'None';
                }
                $a = [];
                foreach ($jobInfo->TaskGroups as $tg) {
                    foreach ($tg->Tasks as $t) {
                        $a[] = $t->Name;
                    }
                }
                return implode(", ", $a);
            })($jobInfo),
            'Global Constraints' => $constraintList($jobInfo->Constraints),
            'Periodic' => $jobInfo->Periodic ?: 'No',
            'Update' => 'Stagger: ' . $jobInfo->Update->Stagger
                    . ', Parallel: ' . $jobInfo->Update->MaxParallel,
            'AllAtOnce' => $jobInfo->AllAtOnce ? 'Yes' : 'No',
        ];

        foreach ($ds as $k => $v) {

        ?>
        <tr>
            <th><?= $k ?></th>
            <td><?= $v ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h2>Tasks</h2>
<dl>
<?php foreach ($jobInfo->TaskGroups as $tg) { ?>
    <dt>Name</dt><dd><?= $tg->Name ?></dd>
    <dt>Count</dt><dd><?= $tg->Count ?></dd>
    <dt>Constraints</dt><dd><?= $constraintList($tg->Constraints) ?></dd>
    <dt>Task(s) Info</dt><dd>

        <dl>
        <?php foreach ($tg->Tasks as $t) { ?>
            <dt>Name</dt><dd><?= $t->Name ?></dd>
            <dt>Driver</dt><dd><?= $t->Driver ?></dd>
            <dt>Constraints</dt><dd><?= $constraintList($t->Constraints) ?></dd>
        <?php } ?>
        </dl>

    </dd>
<?php } ?>
</dl>

<h2>Allocations</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Created</th>
            <th>JobID</th>
            <th>NodeID</th>
            <th>Desired</th>
            <th>Current</th>
            <th>EvalID</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($jobAllocationsInfo as $allocation) { ?>
        <tr>
            <td><?= interlink('allocation', $allocation->ID) ?></td>
            <td nowrap><?= date('Y-m-d H:i:s', $allocation->CreateTime/1000000000) ?></td>
            <td><?= $allocation->JobID ?></td>
            <td><?= interlink('node', $allocation->NodeID) ?></td>
            <td><?= $allocation->DesiredStatus ?></td>
            <td><?= $allocation->ClientStatus ?></td>
            <td><?= interlink('evaluation', $allocation->EvalID) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h2>Evaluations</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>JobID</th>
            <th>Type</th>
            <th>Priority</th>
            <th>NodeID</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($jobEvaluationsInfo as $evalInfo) { ?>
        <tr>
            <td><?= interlink('evaluation', $evalInfo->ID) ?></td>
            <td><?= $evalInfo->JobID ?></td>
            <td><?= $evalInfo->Type ?></td>
            <td><?= $evalInfo->Priority ?></td>
            <td><?= interlink('node', $evalInfo->NodeID) ?></td>
            <td><?= $evalInfo->Status ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<pre><?= print_r($jobInfo) ; ?></pre>
<pre><?= print_r($jobAllocationsInfo) ; ?></pre>
<pre><?= print_r($jobEvaluationsInfo) ; ?></pre>

<?php } ?>