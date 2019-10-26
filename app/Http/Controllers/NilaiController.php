<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ModelNilai;
use App\ViewNilaiDosen;

class NilaiController extends Controller
{
    public function index(){
        $nip = Controller::$loginData['nip'];

        $data = ViewNilaiDosen::where('nip', $nip)->get();
        return $this->response($data);
    }

    public function get($id){
        $data = ViewNilaiDosen::where('id_nilai', $id)->get();
        if(count($data)){
            return $this->Response($data[0]);
        }else{
            return $this->response(['message' => 'Data nilai tidak ditemukan'], true, 404 );
        }
    }

    public function store(Request $request){
        $data = $request->input('json');
        $model = new ModelNilai($data);
        $status = $model->save();
        
        if($status){
            return $this->Response(['message' => 'Nilai Berhasil Ditambah'], true, 201);
        }else{
            return $this->Response(['message' => 'Nilai Gagal Ditambah'], true, 500);
        }
    }

    public function update(Request $request, $id){
        $data = $request->input('json');
        $status = ModelNilai::where('id_nilai', $id)->update($data);
        if($status){
            return $this->Response(['message' => 'Nilai Berhasil Di-Update'], true, 202);
        }else{
            return $this->Response(['message' => 'Nilai gagal di-Update atau tidak ada perubahan data!'], true, 500);
        }
    }

    public function delete($id){
        $status = ModelNilai::where('id_nilai', $id)->delete();
        if($status){
            return $this->Response(['message' => 'Nilai Berhasil Dihapus'], true, 202);
        }else{
            return $this->response(['message' => 'Nilai Gagal Dihapus'],true, 500);
        }
    }
}
