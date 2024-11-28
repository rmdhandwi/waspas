<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class periode extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'periode';
    protected $fillable = [
        'id',
        'tahun',
        'created_at',
        'updated_at'
    ];

    public function warga()
    {
        return $this->belongsTo(warga::class, 'periode_id');
    }
}
