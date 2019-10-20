<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelNilai extends Model
{
   public $timestamps = false;
   protected $table = 'nilai';
   protected $fillable = ['id_nilai', 'tugas1', 'tugas2', 'uts', 'uas', 'praktikum', 'nim', 'kode_kelas'];
}