<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    //
    public $timestamps = false;
    protected   $table = 'kriteria',
                $primaryKey = 'id',
                $fillable = ['id','jenis','nama','nilai_bobot','created_at'];
}
