<?php

namespace Nomad;

class Nomad {

    public static function getAddress() {
        return getenv('NOMAD_BASEURL') ?: 'http://127.0.0.1:4646';
    }

    public static function getTwig() {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../view');
        $twig = new \Twig_Environment($loader, array(
            'cache' => '/tmp',
            'debug' => true,
            'strict_variables' => true,
        ));;
        $twig->addFunction(new \Twig_SimpleFunction('nomadalink', ['Nomad\\Link', 'a'], ['is_safe' => ['html']]));
        $twig->addFunction(new \Twig_SimpleFunction('nomadelink', ['Nomad\\Link', 'e'], ['is_safe' => ['html']]));
        $twig->addFunction(new \Twig_SimpleFunction('nomadnlink', ['Nomad\\Link', 'n'], ['is_safe' => ['html']]));
        $twig->addFunction(new \Twig_SimpleFunction('nomadjlink', ['Nomad\\Link', 'j'], ['is_safe' => ['html']]));
        $twig->addFunction(new \Twig_SimpleFunction('dump', function($val) { return '<pre>' . print_r($val,1) . '</pre>'; }, ['is_safe' => ['html']]));
        return $twig;
    }

    // care of: http://www.phpied.com/simultaneuos-http-requests-in-php-with-curl/
    public static function multiRequest($data, $options = array()) {

      // array of curl handles
      $curly = array();
      // data to be returned
      $result = array();

      // multi handle
      $mh = curl_multi_init();

      // loop through $data and create curl handles
      // then add them to the multi-handle
      foreach ($data as $id => $d) {

        $curly[$id] = curl_init();

        $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
        curl_setopt($curly[$id], CURLOPT_URL,            $url);
        curl_setopt($curly[$id], CURLOPT_HEADER,         0);
        curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

        // post?
        if (is_array($d)) {
          if (!empty($d['post'])) {
            curl_setopt($curly[$id], CURLOPT_POST,       1);
            curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
          }
        }

        // extra options?
        if (!empty($options)) {
          curl_setopt_array($curly[$id], $options);
        }

        curl_multi_add_handle($mh, $curly[$id]);
      }

      // execute the handles
      $running = null;
      do {
        curl_multi_exec($mh, $running);
      } while($running > 0);


      // get content and remove handles
      foreach($curly as $id => $c) {
        $result[$id] = curl_multi_getcontent($c);
        curl_multi_remove_handle($mh, $c);
      }

      // all done
      curl_multi_close($mh);

      return $result;
    }


}