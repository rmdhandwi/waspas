<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public $timestamps = false;
    protected   $table = 'users',
                $primaryKey = 'id',
                $fillable = ['id','username','nama','tgl_lahir','jkel','foto_profil','email','password','no_telp','alamat','remember_token','role','created_at'];
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
