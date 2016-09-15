<?php

require 'vendor/autoload.php';

$nomadBaseUrl = Nomad\Nomad::getAddress();
$info = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/allocations'));
usort($info, function($a, $b) {
    if ($a->CreateTime == $b->CreateTime) {
        return 0;
    }
    return ($a->CreateTime < $b->CreateTime) ? 1 : -1;
});
$twig = Nomad\Nomad::getTwig();
echo $twig->render('allocations.html.twig', array(
    'noinfo' => empty($info),
    'allocations' => $info,
    'crumbs' => [
        'Home' => 'index.php',
    ],
    'raw' => [
        'Info' => json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    ],
));
