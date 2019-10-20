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
        return $this->response($data, true);
    }

    public function get($id){
        $data = ViewNilaiDosen::where('id_nilai', $id)->get();
        if(count($data)){
            return $this->response($data[0], true);
        }else{
            return response(['message' => 'Data nilai tidak ditemukan'], 404);
        }
    }

    public function store(Request $request){
        $data = $request->input('json');
        $model = new ModelNilai($data);
        $status = $model->save();
        
        if($status){
            return response(['message' => 'Nilai Berhasil Ditambah'], 201);
        }else{
            return response(['message' => 'Nilai Gagal Ditambah'], 500);
        }
    }

    public function update(Request $request, $id){
        $data = $request->input('json');
        $status = ModelNilai::where('id_nilai', $id)->update($data);
        if($status){
            return response(['message' => 'Nilai Berhasil Di-Update'], 202);
        }else{
            return response(['message' => 'Nilai gagal di-Update atau tidak ada perubahan data!'], 500);
        }
    }

    public function delete($id){
        $status = ModelNilai::where('id_nilai', $id)->delete();
        if($status){
            return response(['message' => 'Nilai Berhasil Dihapus'], 202);
        }else{
            return response(['message' => 'Nilai Gagal Dihapus'], 500);
        }
    }
}
