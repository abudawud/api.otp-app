<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private $data = array();
    public function handle($request, Closure $next)
    {
        error_reporting(~E_WARNING);
        if ($request->path() != 'login') {
            $nip = $request->header('X-Auth-NIP');
            $token = $request->header('X-Auth-Token');

            $this->data = Cache::get($nip);
            if ($this->data == null || $this->data['token'] != $token) {
                return response(array('message' => 'Broken token/expired!'), 401);
            }

            // START TO DECRYPT DATA
            $key = $this->data['key'];
            if ($key == '') {
                return response(['message' => 'Secret key not set!'], 500);
            }

            $encData = $request->getContent();            
            $timeForce = env('OTP_TIME_FORCE');
            $keyLife = env('OTP_KEY_LIFE');
            $factor = (int)($timeForce / 2);
            $keyHelper = ((int)(time() / $keyLife)) + $factor;
            
            if($request->method() != 'GET'){
                $rawData = false;
                while($timeForce-- > 0){
                    $rawData = $this->decrypt($encData, $key, --$keyHelper);
                    if($rawData != false){
                        break;
                    }
                }

                if ($rawData == false) {
                    return response(['message' => 'Decrypt data failed or OTP already expired!'], 500);
                }

                $jsonData = json_decode($rawData, true);
                if($jsonData == null){
                    return response(['message' => 'Cannot Decode JSON Data!'], 500);
                }

                $request->merge(['json' => $jsonData]);
            }

            Controller::$key = $key;
            Controller::$keyHelper = $keyHelper;
        }

        return $next($request);
    }

    function decrypt($ciphertext, $secretKey, $counter)
    {
        $key = hash_hmac('sha256', $counter, $secretKey);

        $plainText = openssl_decrypt($ciphertext, env('OTP_CIPHER'), $key);

        return $plainText;
    }
}
