<?php

// this works for me. Sorry!
date_default_timezone_set('America/Los_Angeles');

$nomadBaseUrl = getenv('NOMAD_BASEURL') ?: 'http://127.0.0.1:4646';

function interlink($type, $val) {
    return '<a href="'.$type.'.php?id='.$val.'">' .
        ($type == 'job' ? $val : explode('-', $val)[0])
        .'</a>';
}

?><!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Nomad Info</title>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="static/bootstrap.min.css" />
        <link rel="stylesheet" href="static/bootstrap-theme-superhero.css">

        <script src="static/jquery.min.js"></script>
        <script src="static/bootstrap.min.js"></script>

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

            dl dd {
                margin-left: 2em;
            }
        </style>

    </head>
    <body role="document">
        <div class="container theme-showcase" role="main">

            <div class="page-header">
                <h1><?= $header ?: 'Nomad Info' ?><?php if (!empty($header)) { ?> <a href="/">Home</a><?php } ?></h1>
            </div>

