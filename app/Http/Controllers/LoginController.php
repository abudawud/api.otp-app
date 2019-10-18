<?php

namespace App\Http\Controllers;
use App\ModelLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(Request $request){
        $data = ModelLogin::where([
                ['nip', $request->input('nip')],
                ['pwd', $request->input('pwd')]
            ])
                ->get();

        if(count($data)){
            $mdata = $data[0];
            $token = md5(time());
            $chaceData = array(
                'token' => $token,
                'key' => $mdata->enckey
            );
            Cache::put($mdata->nip, $chaceData);
            
            unset($mdata->pwd, $mdata->enckey);
            $mdata->token = $token;
            return response ($mdata);
        }else{
            return response(array('message' => "invalid username/password!"), 401);
        }
    }

    public function logout(Request $request){
        $data = $request->input('json');
        $token = Cache::pull(Arr::get($data, 'nip', 0));
        if($token == null){
            return response(array('message' => 'Something went wrong'), 500);
        }else{
            return response(array('message' => 'You are ready loged out!'));
        }
    }
}
