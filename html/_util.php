<?php

// this works for me. Sorry!
date_default_timezone_set('America/Los_Angeles');

$nomadBaseUrl = getenv('NOMAD_BASEURL') ?: 'http://127.0.0.1:4646';

function interlink($type, $val) {
    return '<a href="'.$type.'.php?id='.$val.'">' .
        ($type == 'job' ? $val : explode('-', $val)[0])
        .'</a>';
}
