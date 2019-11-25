<?php
namespace App\Http\Controllers;
use App\ViewNilaiKelas;
use App\Http\Controllers\Controller;
use App\ViewNilaiDosen;

class KelasController extends Controller
{
    public function index(){
        $nip = Controller::$loginData['nip'];

        $data = ViewNilaiKelas::where('nip', $nip)->get();
        return $this->response($data);
    }

    public function nilaiKelas($id){
        $data = ViewNilaiDosen::where('kode_kelas', $id)->get();
        return $this->response($data);
    }
}
?>