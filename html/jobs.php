<?php

require 'vendor/autoload.php';

$nomadBaseUrl = Nomad\Nomad::getAddress();
$info = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/jobs'));
$twig = Nomad\Nomad::getTwig();
echo $twig->render('jobs.html.twig', array(
    'noinfo' => empty($info),
    'jobs' => $info,
    'crumbs' => [
        'Home' => 'index.php',
    ],
    'raw' => [
        'Info' => json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    ],
));
