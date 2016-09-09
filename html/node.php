<?php

$header = 'Node Info';
require '_header.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
    $nodeID = $_GET['id'];
} else {
    return;
}

$nodeRaw = @file_get_contents($nomadBaseUrl . '/v1/node/' . $nodeID);
$nodeInfo = @json_decode($nodeRaw);

$nodeAllocationsRaw = @file_get_contents($nomadBaseUrl . '/v1/node/' . $nodeID . '/allocations');
$nodeAllocationsInfo = @json_decode($nodeAllocationsRaw);

$nodeStatsRaw = @file_get_contents('http://' . $nodeInfo->HTTPAddr . '/v1/client/stats');
$nodeStatsInfo = @json_decode($nodeStatsRaw);

?>

<?php if (empty($nodeInfo)) { ?>
<div class="alert alert-danger">
    <strong>Error</strong> Could not fetch job/node status(es)
</div>
<?php } else { ?>

<h2>General Info</h2>
<table class="table table-bordered">
    <tbody>
        <?php

        $ds = [
            'ID' => $nodeInfo->ID,
            'Name' => $nodeInfo->Name,
            'Datacenter' => $nodeInfo->Datacenter,
            'Address' => $nodeInfo->HTTPAddr,
            'Nomad Version' => $nodeInfo->Attributes->{'nomad.version'},
            'Processor' => $nodeInfo->Attributes->{'cpu.modelname'},
            'Cores' => $nodeInfo->Attributes->{'cpu.numcores'},
            'CPU Frequency' => number_format($nodeInfo->Attributes->{'cpu.frequency'},0),
            'Total Compute' => number_format($nodeInfo->Attributes->{'cpu.totalcompute'},0),
            'Ram (gb)' => number_format($nodeInfo->Resources->MemoryMB/1024,0),
            'Disk (gb)' => number_format($nodeInfo->Resources->DiskMB/1024,0),
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

<h2>Allocations (<?= count($nodeAllocationsInfo) ?>)</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Created</th>
            <th>JobID</th>
            <th>Desired</th>
            <th>Current</th>
            <th>EvalID</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($nodeAllocationsInfo as $allocation) { ?>
        <tr id="alloc-<?= $allocation->ID ?>">
            <td><?= interlink('allocation', $allocation->ID) ?></td>
            <td nowrap><?= date('Y-m-d H:i:s', $allocation->CreateTime/1000000000) ?></td>
            <td><?= $allocation->JobID ?></td>
            <td><?= $allocation->DesiredStatus ?></td>
            <td><?= $allocation->ClientStatus ?></td>
            <td><?= interlink('evaluation', $allocation->EvalID) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h2>System Metrics</h2>
<p></p>

<h3>CPU</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>CPU</th>
            <th>User</th>
            <th>System</th>
            <th>Idle</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($nodeStatsInfo->CPU as $cpu) { ?>
        <tr>
            <td><?= $cpu->CPU ?></td>
            <td><?= number_format($cpu->User, 2) ?></td>
            <td><?= number_format($cpu->System, 2) ?></td>
            <td><?= number_format($cpu->Idle, 2) ?></td>
            <td><?= number_format($cpu->Total, 2) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h3>Disk</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Mount</th>
            <th>Device</th>
            <th>Available (gb)</th>
            <th>Used (gb)</th>
            <th>inodes used</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($nodeStatsInfo->DiskStats as $disk) { ?>
        <tr>
            <td><?= $disk->Mountpoint ?></td>
            <td><?= $disk->Device ?></td>
            <td><?= number_format($disk->Available/1024/1024/1024, 2) ?></td>
            <td><?= number_format($disk->Used/1024/1024/1024, 2) ?>
               (<?= number_format($disk->UsedPercent, 2) ?>%)</td>
            <td><?= number_format($disk->InodesUsedPercent, 2) ?>%</td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h3>Ram</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Used</th>
            <th>Free</th>
            <th>Available</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= number_format($nodeStatsInfo->Memory->Used/1024/1024/1024, 2) ?></td>
            <td><?= number_format($nodeStatsInfo->Memory->Free/1024/1024/1024, 2) ?></td>
            <td><?= number_format($nodeStatsInfo->Memory->Available/1024/1024/1024, 2) ?></td>
            <td><?= number_format($nodeStatsInfo->Memory->Total/1024/1024/1024, 2) ?></td>
        </tr>
    </tbody>
</table>

<?php } ?>