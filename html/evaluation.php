<?php

$header = 'Evaluation Info';
require '_header.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
    $evaluationID = $_GET['id'];
} else {
    return;
}

$evaluationRaw = @file_get_contents($nomadBaseUrl . '/v1/evaluation/' . $evaluationID);
$evaluationInfo = @json_decode($evaluationRaw);

$evaluationAllocationRaw = @file_get_contents($nomadBaseUrl . '/v1/evaluation/' . $evaluationID . '/allocations');
$evaluationAllocationInfo = @json_decode($evaluationAllocationRaw);

?>

<?php if (empty($evaluationInfo)) { ?>
<div class="alert alert-danger">
    <strong>Error</strong> Could not fetch job/node status(es)
</div>
<?php } else { ?>

<pre><?= print_r($evaluationInfo) ; ?></pre>
<pre><?= print_r($evaluationAllocationInfo) ; ?></pre>

<?php } ?>