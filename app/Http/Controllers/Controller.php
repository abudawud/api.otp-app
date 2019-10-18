<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public function response($content, $encrypted = false, $key = null, $counter = null)
    {
        if ($encrypted) {
            // START TO ENCRYPT DATA
            if ($key == '' || $counter == null) {
                return response(['message' => 'Secret key or counter not set!'], 500);
            }

            $content = json_encode($content);

            if($content == false){
                return response(['message' => 'Failed to encode JSON!'], 500);
            }

            $encData = $this->encrypt($content, $key, $counter);

            if ($encData == false) {
                return response(['message' => 'Encrypt data failed!'], 500);
            }

            $content = $encData;
        }

        return response($content);
    }

    private function encrypt($plainText, $secretKey, $counter)
    {
        $key = hash_hmac('sha256', $counter, $secretKey);

        $ciphertext = openssl_encrypt($plainText, env('OTP_CIPHER'), $key);

        return $ciphertext;
    }
}
