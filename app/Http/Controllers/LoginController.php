<?php

namespace App\Http\Controllers;
use App\ModelLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class LoginController extends Controller
{

    public function index(Request $request){
        $json = $request->input('json');
        $data = ModelLogin::where([
                ['nip', Arr::get($json, 'nip', 0)],
                ['pwd', Arr::get($json, 'pwd', 'N/A')]
            ])
                ->get();

        if(count($data)){
            $mdata = $data[0];
            $token = md5(time());
            $enckey = sha1(rand(0, time()));
            $chaceData = array(
                'token' => $token,
                'key' => $enckey
            );

            ModelLogin::where('nip', Arr::get($json, 'nip', 0))
                        ->update(['enckey' => $enckey]);

            Cache::put($mdata->nip, $chaceData);
            
            unset($mdata->pwd);
            $mdata->token = $token;
            $mdata->enckey = $enckey;
            return $this->response($mdata, false, 200);
        }else{
            return $this->response('message', false, 401);
        }
    }

    public function logout(Request $request){
        $data = $request->input('json');
        $token = Cache::pull(Arr::get($data, 'nip', 0));
        if($token == null){
            return $this->response(array('message' => 'Something went wrong'), 500);
        }else{
            return $this->response(array('message' => 'You are ready loged out!'), false);
        }
    }
}
