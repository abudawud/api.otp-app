<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public static $key;
    public static $keyHelper;

    public function response($content, $encrypted = false)
    {
        if ($encrypted) {
            // START TO ENCRYPT DATA
            if ($this::$key == '' || $this::$keyHelper == null) {
                return response(['message' => 'Secret key or counter not set!'], 500);
            }

            $content = json_encode($content);

            if($content == false){
                return response(['message' => 'Failed to encode JSON!'], 500);
            }

            $encData = $this->encrypt($content, $this::$key, $this::$keyHelper);

            if ($encData == false) {
                return response(['message' => 'Encrypt data failed!'], 500);
            }

            $content = $encData;
        }

        return response($content);
    }

    public function test(){
        return "ts";
    }

    private function encrypt($plainText, $secretKey, $counter)
    {
        $key = hash_hmac('sha256', $counter, $secretKey);

        $ciphertext = openssl_encrypt($plainText, env('OTP_CIPHER'), $key);

        return $ciphertext;
    }
}
