<?php

require 'vendor/autoload.php';

$nomadBaseUrl = Nomad\Nomad::getAddress();
$info = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/nodes'));
$twig = Nomad\Nomad::getTwig();
echo $twig->render('nodes.html.twig', array(
    'noinfo' => empty($info),
    'nodes' => $info,
));
