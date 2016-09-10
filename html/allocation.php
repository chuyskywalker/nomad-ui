<?php

$header = 'Allocation Info';
require '_header.php';

if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
    $allocationID = $_GET['id'];
} else {
    return;
}

$nomadBaseUrl = 'http://192.168.1.52:4646';

$allocationRaw = @file_get_contents($nomadBaseUrl . '/v1/allocation/' . $allocationID);
$allocationInfo = @json_decode($allocationRaw);

// get node allocation is on's address
$nodeInfo = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/node/' . $allocationInfo->NodeID));

$allocationUrl = 'http://' . $nodeInfo->HTTPAddr . '/';

?>

<?php if (empty($allocationInfo)) { ?>
<div class="alert alert-danger">
    <strong>Error</strong> Could not fetch job/node status(es)
</div>
<?php } else { ?>

<?php

// find ze logs!

$allocLogs = json_decode(file_get_contents($allocationUrl . '/v1/client/fs/ls/' . $allocationID .'?path=/alloc/logs'));
foreach ($allocLogs as $file) {
    if (stristr($file->Name, 'stderr')) {
        $stderr = $file->Name;
    }
    elseif (stristr($file->Name, 'stdout')) {
        $stdout = $file->Name;
    }
}
?>

<h2>stderr</h2>
<pre><?= htmlspecialchars (
    file_get_contents($allocationUrl . '/v1/client/fs/cat/' . $allocationID .'?path=/alloc/logs/' . $stderr)
) ?></pre>

<h2>stdout</h2>
<pre><?= htmlspecialchars (
    file_get_contents($allocationUrl . '/v1/client/fs/cat/' . $allocationID .'?path=/alloc/logs/' . $stdout)
) ?></pre>

<pre><?= print_r($allocationInfo) ; ?></pre>

<?php /*
<pre><?php
print_r(json_decode(file_get_contents($allocationUrl . '/v1/client/fs/ls/' . $allocationID .'?path=/')))
?></pre>

<pre><?php
print_r(json_decode(file_get_contents($allocationUrl . '/v1/client/fs/ls/' . $allocationID .'?path=/alloc')))
?></pre>

<pre><?php
print_r()
?></pre>

<pre><?php
print_r(file_get_contents($allocationUrl . '/v1/client/fs/cat/' . $allocationID .'?path=/alloc/logs/STD_OUT_FILE_HERE'))
?></pre>
*/ ?>

<?php } ?>