<?php

require 'vendor/autoload.php';

$nomadBaseUrl = Nomad\Nomad::getAddress();
$info = @json_decode(@file_get_contents($nomadBaseUrl . '/v1/evaluations'));

usort($info, function($a, $b) {
    if ($a->CreateIndex == $b->CreateIndex) {
        return 0;
    }
    return ($a->CreateIndex < $b->CreateIndex) ? 1 : -1;
});

$twig = Nomad\Nomad::getTwig();
echo $twig->render('evaluations.html.twig', array(
    'noinfo' => empty($info),
    'evaluations' => $info,
));
