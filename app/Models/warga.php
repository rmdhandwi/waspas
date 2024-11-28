<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class warga extends Model
{
    use HasFactory;
    protected $table = 'warga';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // deteksi kolom pada tabel dinamis
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    public function subkriteria()
    {
        return $this->hasMany(Subkriteria::class, 'kriteria_id');
    }

    public function periode()
    {
        return $this->hasMany(periode::class, 'id');
    }
}
