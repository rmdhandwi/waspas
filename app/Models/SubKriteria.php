<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    //
    public $timestamps = false;
    protected   $table = 'sub_kriteria',
                $primaryKey = 'id',
                $fillable = ['id','jenis_sub','nama_sub','nilai_bobot','id_kriteria','created_at'];
}
