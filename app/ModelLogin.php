<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelLogin extends Model
{
   public $timestamps = false;
   protected $table = 'dosen'; //nama table yang kita buat lewat migration adalah todo
}