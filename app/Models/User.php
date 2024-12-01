<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable =
    [
        'id',
        'nama',
        'username',
        'password',
        'role',
        'created_at'
    ];
    protected $hidden = [
        'password',
    ];
}
