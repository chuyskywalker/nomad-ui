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


?>

<?php if (empty($jobInfo)) { ?>
<div class="alert alert-danger">
    <strong>Error</strong> Could not fetch job/node status(es)
</div>
<?php } else { ?>

<pre><?= print_r($jobInfo) ; ?></pre>
<pre><?= print_r($jobAllocationsInfo) ; ?></pre>
<pre><?= print_r($jobEvaluationsInfo) ; ?></pre>

<?php } ?>