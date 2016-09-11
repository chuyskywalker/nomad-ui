<?php

namespace Nomad;

class Crypt {

    // Take any php object, string, whatever, and make it URL safe
    public function encrypt($mixed) {
        $value = serialize($mixed);
        $nonce = \Sodium\randombytes_buf(\Sodium\CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = \Sodium\crypto_secretbox($value, $nonce, $this->getKey());
        $safe = base64_encode(json_encode([
            'n' => bin2hex($nonce),
            'm' => bin2hex($ciphertext)
        ]));
        return $safe;
    }

    // Take what was done in encrypt and undo it
    public function decrypt($data) {
        $data = json_decode(base64_decode($data));
        $plaintext = \Sodium\crypto_secretbox_open(hex2bin($data->m), hex2bin($data->n), $this->getKey());
        if ($plaintext === false) {
            throw new Exception("Bad ciphertext");
        }
        return unserialize($plaintext);
    }

    // In order to not make people need to provide keys,
    // this generates one as needed
    private function getKey() {
        static $key;
        if (isset($key)) {
            return $key;
        }

        if (file_exists('/tmp/nomad.key')) {
            $key = base64_decode(file_get_contents('/tmp/nomad.key'));
            return $key;
        }

        $key = \Sodium\randombytes_buf(\Sodium\CRYPTO_SECRETBOX_KEYBYTES);
        file_put_contents('/tmp/nomad.key', base64_encode($key));
        return $key;
    }

}