<?php

namespace App\Http\Controllers;
use App\ViewJadwalDosen;

class JadwalController extends Controller
{
    public function index(){
        $nip = Controller::$loginData['nip'];

        $data = ViewJadwalDosen::where('nip', $nip)->get();
        return $this->Response($data);
    }

    public function get($id){
        $data = ViewJadwalDosen::where('id_jam', $id)->get();
        if(count($data)){
            return $this->response($data[0]);
        }else{
            return $this->Response(['message' => 'Data jadwal tidak ditemukan'], true, 404);
        }
    }
}
