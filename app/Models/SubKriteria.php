<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected   $table = 'subkriteria',
        $primaryKey = 'id',
        $fillable = [
            'id',
            'kriteria_id',
            'kode_sub',
            'nama_subkriteria',
            'nilai',
        ];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kodeSub) {
            $kodeSub->kode_sub = self::generateSubKriteria();
        });
    }

    public static function generatesubKriteria()
    {
        // Ambil kode terakhir dari tabel sub-kriteria berdasarkan kode_sub
        $lastsubKriteria = self::orderBy('kode_sub', 'desc')->first();

        // Jika tidak ada data sebelumnya, mulai dari 'SUB-01'
        if (!$lastsubKriteria) {
            return 'SUB-01';
        }

        // Ambil angka dari kode terakhir dan konversi ke integer
        $lastNumber = intval(substr($lastsubKriteria->kode_sub, 4));

        // Tambahkan 1 untuk mendapatkan nomor baru
        $newNumber = $lastNumber + 1;

        // Buat kode baru dengan padding angka menjadi dua digit
        return 'SUB-' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    }
}
