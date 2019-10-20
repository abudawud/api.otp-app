<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public static $loginData = array();

    public function response($content, $encrypted = false)
    {
        $key = Controller::$loginData['key'];

        $keyLife = env('OTP_KEY_LIFE');
        $keyHelper = ((int)(time() / $keyLife));

        if ($encrypted && env('APP_ENCRYPT')) {
            // START TO ENCRYPT DATA
            if ($key == '' || $keyHelper == null) {
                return response(['message' => 'Secret key or counter not set!'], 500);
            }

            $content = json_encode($content);

            if($content == false){
                return response(['message' => 'Failed to encode JSON!'], 500);
            }

            $encData = $this->encrypt($content, $key, $keyHelper);

            if ($encData[1] == false) {
                return response(['message' => 'Encrypt data failed!'], 500);
            }

            if(env('OTP_KEY_DEBUG')){
                $content = array(
                    'key' => $encData[0],
                    'data' => $encData[1]
                );
            }else{
                $content = ['data' => $encData[1]];
            }
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

        return array($key, $ciphertext);
    }
}
