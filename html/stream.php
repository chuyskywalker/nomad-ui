<?php

require 'vendor/autoload.php';

// nomadaddress, allocationid, path, offset, limit

$info = (new Nomad\Crypt())->decrypt($_GET['fileid']);

header('Content-type: text/plain');

echo file_get_contents(
    'http://' . $info['address'] . '/v1/client/fs/readat/' . $info['allocationid']
    .'?path='. $info['path'] . '&offset='. $_GET['offset'] .'&limit=' . $_GET['limit']
    );

