<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class perhitungan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'perhitungan';
    protected $primaryKey = 'id';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    public function warga()
    {
        return $this->belongsTo(dataWarga::class, 'warga_id', 'id');
    }


}
