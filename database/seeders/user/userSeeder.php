<?php

namespace Database\Seeders\user;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            'username' => 'superadmin',
            'nama' => Str::random(10),
            'tgl_lahir' => date('1997-07-07'),
            'jkel' => 'Laki-laki',
            'email' => Str::random(10).'@gmail.com',
            'foto_profil' => NULL,
            'password' => Hash::make('superadmin'),
            'no_telp' => '081234567800',
            'alamat' => NULL,
            'role' => 'super_admin',
            'created_at' => Carbon::now(),
        ]);
    }
}
