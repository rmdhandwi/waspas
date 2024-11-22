<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dataWarga extends Model
{
    //
    public $timestamps = false;
    protected   $table = 'data_warga',
                $primaryKey = 'id',
                $fillable = ['id','nomor_kk','nama_kk','provinsi','kabupaten','kampung','rt','rw','asal_suku','pekerjaan','agama','jenis_kelamin','sanitasi','j_kloset','t_limbah','akses_air_minum','status_rumah','struktur_bangunan','created_at'];
}
