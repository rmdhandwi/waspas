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
    
    public function kriteria()
    {
        return $this->hasMany(Kriteria::class, 'id_kriteria');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subJenis) {
            $subJenis->jenis_sub = self::generateSubKriteria();
        });
    }

    public static function generatesubKriteria()
    {
        // Ambil kode terakhir dari tabel kriteria
        $lastsubKriteria = self::orderBy('jenis_sub', 'desc')->first();

        // Jika ada data sebelumnya, ambil nomor berikutnya
        if (!$lastsubKriteria) {
            return 'SUB-01'; // Mengambil nomor setelah C
        }
        $lastNumber = intval(substr($lastsubKriteria->jenis_sub, 4));

        $newNumber = $lastNumber + 1;
        // Buat kode baru
        return 'SUB-' . str_pad($newNumber, 2, '0', $newNumber);
    }
}
