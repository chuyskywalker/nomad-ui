<?php

$header = 'Nomad Info';
require '_header.php';

$nodesRaw = @file_get_contents($nomadBaseUrl . '/v1/nodes');
$jobsRaw  = @file_get_contents($nomadBaseUrl . '/v1/jobs');
$allocsRaw= @file_get_contents($nomadBaseUrl . '/v1/allocations');
$evalsRaw = @file_get_contents($nomadBaseUrl . '/v1/evaluations');

$nodes = @json_decode($nodesRaw);
$jobs  = @json_decode($jobsRaw);
$allocs= @json_decode($allocsRaw);
$evals = @json_decode($evalsRaw);

?>

<p>@ <?= $nomadBaseUrl ?></p>

<?php if (empty($nodes) || empty($jobs)) { ?>
<div class="alert alert-danger">
    <strong>Error</strong> Could not fetch job/node status(es)
</div>
<?php } ?>

<h2>Nodes</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Datacenter</th>
            <th>Drain</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($nodes as $nodeInfo) { ?>
        <tr id="node-<?= $nodeInfo->ID ?>">
            <td><?= interlink('node', $nodeInfo->ID) ?></td>
            <td><?= $nodeInfo->Name ?></td>
            <td><?= $nodeInfo->Datacenter ?></td>
            <td><?= $nodeInfo->Drain ?></td>
            <td><?= $nodeInfo->Status ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>


<h2>Jobs</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Priority</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($jobs as $jobInfo) { ?>
        <tr>
            <td><?= interlink('job', $jobInfo->ID) ?></td>
            <td><?= $jobInfo->Name ?></td>
            <td><?= $jobInfo->Type ?></td>
            <td><?= $jobInfo->Priority ?></td>
            <td><?= $jobInfo->Status ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

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
        <?php foreach ($allocs as $allocation) { ?>
        <tr id="alloc-<?= $allocation->ID ?>">
            <td><?= interlink('allocation', $allocation->ID) ?></td>
            <td nowrap><?= date('Y-m-d H:i:s', $allocation->CreateTime/1000000000) ?></td>
            <td><?= $allocation->JobID ?></td>
            <td><?= interlink('node', $allocation->NodeID) ?></td>
            <td><?= $allocation->DesiredStatus ?></td>
            <td><?= $allocation->ClientStatus ?></td>
            <td><?= interlink('eval', $allocation->EvalID) ?></td>
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
        <?php foreach ($evals as $evalInfo) { ?>
        <tr id="eval-<?= $evalInfo->ID ?>">
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
