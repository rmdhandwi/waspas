<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    //
    public $timestamps = false;
    protected   $table = 'kriteria',
        $primaryKey = 'id',
        $fillable = ['id', 'jenis', 'nama', 'nilai_bobot', 'created_at'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($jenis)
        {
            $jenis->jenis = self::generateJenisKriteria();
        });
    }

    public static function generateJenisKriteria()
    {
        // Ambil kode terakhir dari tabel kriteria
        $lastJenisKriteria = self::orderBy('jenis', 'desc')->first();
        
        // Jika ada data sebelumnya, ambil nomor berikutnya
        if (!$lastJenisKriteria) {
            return 'C1'; // Mengambil nomor setelah C
        }
        $lastNumber = intval(substr($lastJenisKriteria->jenis, 1));
        
        $newNumber = $lastNumber + 1;
        // Buat kode baru
        return 'C' . str_pad($newNumber, 1,'0',$newNumber);
    }
}
