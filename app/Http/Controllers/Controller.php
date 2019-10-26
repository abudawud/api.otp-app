<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    //
    public static $loginData = array(); 

    public static function Response($content, $privateKey = true, $status = 200, $encrypted = true)
    {
        $keyLife = env('OTP_KEY_LIFE');
        $keyHelper = ((int)(time() / $keyLife));

        if (env('APP_ENCRYPT_RESPONSE') && $encrypted) {
            // START TO ENCRYPT DATA
            if($privateKey){
                $key = Controller::$loginData['key'];
            }else{
                $key = env('APP_MAIN_KEY');
            }

            if ($key == '' || $keyHelper == null) {
                return response(['message' => 'Secret key or counter not set!'], 500);
            }

            $content = json_encode($content);

            if($content == false){
                return response(['message' => 'Failed to encode JSON!'], 500);
            }

            $encData = Controller::encrypt($content, $key, $keyHelper);

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

        $msg = array(
            'encrypted' => env('APP_ENCRYPT_RESPONSE') && $encrypted,
            'payload' => $content
        );

        return response($msg, $status);
    }

    private static function encrypt($plainText, $secretKey, $counter)
    {
        $key = hash_hmac('sha256', $counter, $secretKey);

        $ciphertext = openssl_encrypt($plainText, env('OTP_CIPHER'), $key);

        return array($key, $ciphertext);
    }
}
