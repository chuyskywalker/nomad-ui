<?php

// this works for me. Sorry!
date_default_timezone_set('America/Los_Angeles');

?><!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Nomad Info</title>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="bootstrap.min.css" />
        <link rel="stylesheet" href="bootstrap-theme-superhero.css">

        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>

        <style>
            * {
                font-family: monospace;
                font-size: 14px;
            }
            td.moreInfo {
                display: none;
            }

            td ul{
                padding: 0;
                padding-left: 10px;
            }
            tr:target {
                background: rgba(16, 255, 0, 0.15);
            }
            .table > thead > tr > th,
            .table > tbody > tr > th,
            .table > tfoot > tr > th,
            .table > thead > tr > td,
            .table > tbody > tr > td,
            .table > tfoot > tr > td {
                padding: 2px 8px;
            }
        </style>

        <script>
            $(function(){
                $("button.moreInfo").click(function(){
                    $(this).closest('tr').next('tr').toggle();
                });
            });
        </script>

    </head>
    <body role="document">

<?php

$nomadBaseUrl = getenv('NOMAD_BASEURL') ?: 'http://127.0.0.1:4646';

$nodesRaw = @file_get_contents($nomadBaseUrl . '/v1/nodes');
$jobsRaw  = @file_get_contents($nomadBaseUrl . '/v1/jobs');
$allocsRaw= @file_get_contents($nomadBaseUrl . '/v1/allocations');
$evalsRaw = @file_get_contents($nomadBaseUrl . '/v1/evaluations');

$nodes = @json_decode($nodesRaw);
$jobs  = @json_decode($jobsRaw);
$allocs= @json_decode($allocsRaw);
$evals = @json_decode($evalsRaw);

// echo '<pre>' . print_r($evals,1) . '</pre>';

$nodeInfos = [];
foreach ((array)$nodes as $node) {
    $nodeInfos[$node->ID] = json_decode(file_get_contents($nomadBaseUrl . '/v1/node/' . $node->ID));
}

$jobInfos = [];
foreach ((array)$jobs as $job) {
    $jobInfos[$job->ID] = json_decode(file_get_contents($nomadBaseUrl . '/v1/job/' . $job->ID));
}

function interlink($type, $val) {
    return '<a href="#'.$type.'-'.$val.'">' . explode('-', $val)[0] .'</a>';
}

?>

        <div class="container theme-showcase" role="main">
            <div class="page-header">
                <h1>Nomad Info</h1>
            </div>

            <p>@ <?= $nomadBaseUrl ?></p>

            <?php if (empty($nodeInfos) || empty($jobInfos)) { ?>
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
                        <th>Address</th>
                        <th>CPU</th>
                        <th>Memory(MB)</th>
                        <th>Disk(MB)</th>
                        <th>Status</th>
                        <th>More Info</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($nodeInfos as $nodeId => $nodeInfo) { ?>

                    <tr id="node-<?= $nodeInfo->ID ?>">
                        <td><?= $nodeInfo->ID ?></td>
                        <td><?= $nodeInfo->Name ?></td>
                        <td><?= $nodeInfo->HTTPAddr ?></td>
                        <td><?= $nodeInfo->Resources->CPU ?></td>
                        <td><?= $nodeInfo->Resources->MemoryMB ?></td>
                        <td><?= $nodeInfo->Resources->DiskMB ?></td>
                        <td><?= $nodeInfo->Status ?></td>
                        <td><button class="moreInfo btn btn-default btn-xs">Full&nbsp;Info</button></td>
                    </tr>
                    <tr style="display: none">
                        <td colspan=99>
                            <pre><?= json_encode($nodeInfo, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) ?></pre>
                        </td>
                    </tr>

                    <?php } ?>

                </tbody>
            </table>


            <h2>Jobs</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <!--<th>Name</th>-->
                        <th>Type</th>
                        <th>Status</th>
                        <th>Task Layout</th>
                        <th>More Info</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($jobInfos as $jobId => $jobInfo) { ?>

                    <tr>
                        <td><?= $jobInfo->ID ?></td>
                        <!--<td><?= $jobInfo->Name ?></td>-->
                        <td><?= $jobInfo->Type ?></td>
                        <td><?= $jobInfo->Status ?></td>
                        <td><?

                        echo "<ul>";
                        foreach ($jobInfo->TaskGroups as $g) {
                            echo "<li>". $g->Name ." (". $g->Count ."x)</li><ul>";
                            foreach($g->Tasks as $task) {
                                switch ($task->Driver) {
                                    case 'docker':
                                        echo '<li>'. $task->Name . ':&nbsp;docker('. $task->Config->image .')</li>';
                                        break;
                                    default:
                                        echo '<li>'. $task->Name . ':&nbsp;'. $task->Driver .'</li>';
                                }
                            }
                            echo "</ul>";
                        }
                        echo "</ul>";

                        ?></td>
                        <td><button class="moreInfo btn btn-default btn-xs">Full&nbsp;Info</button></td>
                    </tr>
                    <tr style="display: none">
                        <td colspan=99>
                            <pre><?= json_encode($jobInfo, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) ?></pre>
                        </td>
                    </tr>

                    <?php } ?>

                </tbody>
            </table>

            <h2>Allocations</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Created</th>
                        <th>JobID</th>
                        <th>NodeID</th>
                        <th>Desired</th>
                        <th>Current</th>
                        <th>EvalID</th>
                        <th>Task Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allocs as $allocation) { ?>
                    <tr id="alloc-<?= $allocation->ID ?>">
                        <td nowrap><?= date('Y-m-d H:i:s', $allocation->CreateTime/1000000000) ?></td>
                        <td><?= $allocation->JobID ?></td>
                        <td><?= interlink('node', $allocation->NodeID) ?></td>
                        <td><?= $allocation->DesiredStatus ?></td>
                        <td><?= $allocation->ClientStatus ?></td>
                        <td><?= interlink('eval', $allocation->EvalID) ?></td>
                        <td><button class="moreInfo btn btn-default btn-xs">Task&nbsp;Details</button></td>
                    </tr>
                    <tr style="display: none">
                        <td colspan=99>
                        <?php foreach ($allocation->TaskStates as $TaskID => $task) { ?>
                            Task: <b><?= $TaskID ?></b>, State: <b><?= $task->State ?></b></br>
                            <?php foreach ($task->Events as $event) { ?>
                            <?= date('Y-m-d H:i:s', $event->Time/1000000000) ?>:
                            <?= $event->Type ?>
                            <?= $event->DriverError ?>
                            <?= $event->RestartReason ?>
                            <?= $event->Message ?>
                            <?= $event->KillError ?>
                            <?= $event->DownloadError ?>
                            <?= $event->ValidationError ?>
                            <br/>
                            <?php } ?>
                        <?php } ?>
                        </td>
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
                        <th>More Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evals as $evalInfo) { ?>
                    <tr id="eval-<?= $evalInfo->ID ?>">
                        <td><?= $evalInfo->ID ?></td>
                        <td><?= $evalInfo->JobID ?></td>
                        <td><?= $evalInfo->Type ?></td>
                        <td><?= $evalInfo->Priority ?></td>
                        <td><?= interlink('node', $evalInfo->NodeID) ?></td>
                        <td><?= $evalInfo->Status ?></td>
                        <td><button class="moreInfo btn btn-default btn-xs">Full&nbsp;Info</button></td>
                    </tr>
                    <tr style="display: none">
                        <td colspan=99>
                            <pre><?= json_encode($evalInfo, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) ?></pre>
                        </td>
                    </tr>
                    <?php } ?>

                </tbody>
            </table>

        </div>

    </body>
</html>
