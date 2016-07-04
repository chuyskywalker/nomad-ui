<!doctype html>
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
            td.moreInfo {
                display: none;
            }

            td ul{
                padding: 0;
                padding-left: 10px;
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

$nodes = json_decode(file_get_contents($nomadBaseUrl . '/v1/nodes'));
$jobs = json_decode(file_get_contents($nomadBaseUrl . '/v1/jobs'));

$nodeInfos = [];
foreach ($nodes as $node) {
    $nodeInfos[$node->ID] = json_decode(file_get_contents($nomadBaseUrl . '/v1/node/' . $node->ID));
}

$jobInfos = [];
foreach ($jobs as $job) {
    $jobInfos[$job->ID] = json_decode(file_get_contents($nomadBaseUrl . '/v1/job/' . $job->ID));
}

?>

    <div class="container theme-showcase" role="main">
        <div class="page-header">
            <h1>Nomad Info</h1>
        </div>


            <p>@ <?= $nomadBaseUrl ?></p>
            
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
            
            <?php
            foreach ($nodeInfos as $nodeId => $nodeInfo) {
                ?>
            
                <tr>
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
            
                <?php
            }
            ?>
            
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
            
            <?php
            foreach ($jobInfos as $jobId => $jobInfo) {
                ?>
            
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
            
                <?php
            }
            ?>
            
                </tbody>
            </table>

        </div>

    </body>
</html>
