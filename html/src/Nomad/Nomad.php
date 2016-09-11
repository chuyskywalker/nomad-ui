<?php

namespace Nomad;

class Nomad {

    public static function getAddress() {
        return getenv('NOMAD_BASEURL') ?: 'http://127.0.0.1:4646';
    }

    public static function getTwig() {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../view');
        $twig = new \Twig_Environment($loader, array(
            'cache' => '/tmp',
            'debug' => true,
            'strict_variables' => true,
        ));;
        $twig->addFunction(new \Twig_SimpleFunction('nomandalink', ['Nomad\\Link', 'a'], ['is_safe' => ['html']]));
        $twig->addFunction(new \Twig_SimpleFunction('nomandelink', ['Nomad\\Link', 'e'], ['is_safe' => ['html']]));
        $twig->addFunction(new \Twig_SimpleFunction('nomandnlink', ['Nomad\\Link', 'n'], ['is_safe' => ['html']]));
        return $twig;
    }

}