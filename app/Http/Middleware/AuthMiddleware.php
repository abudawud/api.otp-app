<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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
        $timeForce = env('OTP_TIME_FORCE');
        $keyLife = env('OTP_KEY_LIFE');
        $factor = (int)($timeForce / 2);
        $keyHelper = ((int)(time() / $keyLife)) + $factor;

        if ($request->path() != 'login') { // PRIVATE ENCRYPTION
            $nip = $request->header('X-Auth-NIP');
            $token = $request->header('X-Auth-Token');

            $this->data = Cache::get($nip);
            if ($this->data == null || $this->data['token'] != $token) {
                return Controller::Response(array('message' => 'Broken token/expired!'), false, 401, false); // CANNOT DO ENCRYPTION!!
            }

            // START TO DECRYPT DATA
            $key = $this->data['key'];
            $iv = env('APP_IV_INIT');

            if ($key == '') {
                return Controller::Response(['message' => 'Secret key not set!'], false, 500, false); // CANNOT DO ENCRYPTION!!
            }

            if ($request->method() == 'POST' || $request->method() == 'PATCH') {
                $encData = $request->getContent();
                $rawData = false;

                if (env('APP_ENCRYPT_REQUEST')) {
                    while ($timeForce-- > 0) {
                        $rawData = $this->decrypt($encData, $key, $iv, --$keyHelper);
                        if ($rawData != false) {
                            break;
                        }
                    }

                    if ($rawData == false) {
                        return  Controller::Response(['message' => 'Decrypt data failed or OTP already expired!'], true, 500);
                    }
                } else {
                    $rawData = $encData;
                }

                $jsonData = json_decode($rawData, true);
                if ($jsonData == null) {
                    return  Controller::Response(['message' => 'Cannot Decode JSON Data!'], true, 500);
                }

                $request->merge(['json' => $jsonData]);
            }

            $mdata = array(
                'key' => $key,
                'keyHelper' => $keyHelper,
                'nip' => $nip,
            );

            Controller::$loginData = $mdata;
        } else { // GLOBAL ENCRYPTION
            if ($request->method() != 'POST') {
                return  Controller::Response(['message' => 'Method Not Allowed!'], false, 400);
            }

            $key = env('APP_MAIN_KEY');
            $iv = env('APP_IV_INIT');

            $encData = $request->getContent();
            $rawData = false;

            if (env('APP_ENCRYPT_REQUEST')) {
                while ($timeForce-- > 0) {
                    $rawData = $this->decrypt($encData, $key, $iv, --$keyHelper);
                    if ($rawData != false) {
                        break;
                    }
                }

                if ($rawData == false) {
                    return Controller::Response(['message' => 'Decrypt data failed or OTP already expired!'], false, 500);
                }
            } else {
                $rawData = $encData;
            }

            $jsonData = json_decode($rawData, true);
            if(is_string($jsonData)){
                $jsonData = json_decode($jsonData, true);
            }

            if ($jsonData == null) {
                return Controller::Response(['message' => 'Cannot Decode JSON Data!'], false, 500);
            }

            $request->merge(['json' => $jsonData]);
        }

        return $next($request);
    }

    function decrypt($ciphertext, $secretKey, $iv, $counter)
    {
        $key = hash_hmac('md5', $counter, hex2bin($secretKey), true);

        $plainText = openssl_decrypt($ciphertext, env('OTP_CIPHER'), $key, 0, hex2bin($iv) );

        return $plainText;
    }
}
