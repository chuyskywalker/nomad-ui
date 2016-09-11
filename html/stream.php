<?php

require '_util.php';

// if ($_GET['id'] && preg_match('/^[a-zA-Z0-9-]+$/', $_GET['id'])) {
//     $allocationID = $_GET['id'];
// } else {
//     return json_encode(false);
// }

// $allocationInfo = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/allocation/' . $allocationID));
// $nodeInfo = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/node/' . $allocationInfo->NodeID));


// nomadaddress, allocationid, path, offset, limit

$info = json_decode(base64_decode($_GET['fileid']));

echo file_get_contents(
    'http://' . $info->address . '/v1/client/fs/readat/' . $info->allocationid
    .'?path='. $info->path . '&offset='. $_GET['offset'] .'&limit=' . $_GET['limit']
    );

