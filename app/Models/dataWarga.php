<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class dataWarga extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected   $table = 'warga';
    protected   $primaryKey = 'id';

    // deteksi kolom pada tabel dinamis
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    public function periode()
    {
        return $this->belongsTo(periode::class, 'tahun_id', 'id');
    }

    public function hasil()
    {
        return $this->hasMany(hasil::class, 'warga_id', 'id');
    }
}
