<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected  $table = 'kriteria';
    protected  $primaryKey = 'id';
    protected  $fillable = [
        'id',
        'kode_kriteria',
        'nama_kriteria',
        'bobot',
        'tipe',
        'created_at',
        'updated_at'
    ];

    public function subkriteria()
    {
        return $this->hasMany(Subkriteria::class, 'kriteria_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kode_kriteria) {
            $kode_kriteria->kode_kriteria = self::generateKodeKriteria();
        });
    }

    public static function generateKodeKriteria()
    {
        // Ambil kode terakhir dari tabel kriteria
        $lastKodeKriteria = self::orderBy('kode_kriteria', 'desc')->first();

        // Jika ada data sebelumnya, ambil nomor berikutnya
        if (!$lastKodeKriteria) {
            return 'C1'; // Mengambil nomor setelah C
        }
        $lastNumber = intval(substr($lastKodeKriteria->kode_kriteria, 1));

        $newNumber = $lastNumber + 1;
        // Buat kode baru
        return 'C' . str_pad($newNumber, 1, '0', $newNumber);
    }
}
