<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ModelNilai;

class NilaiController extends Controller
{

    public function index(Request $request){
        $data = ModelNilai::where('tugas1', 90)->get();
        return $this->response($data, true);
    }
}
