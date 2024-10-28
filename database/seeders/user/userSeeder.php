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
        //super admin
        // DB::table('users')->insert([
        //     'username' => 'superadmin',
        //     'nama' => Str::random(10),
        //     'tgl_lahir' => date('1997-07-07'),
        //     'jkel' => 'Laki-laki',
        //     'email' => Str::random(10).'@gmail.com',
        //     'foto_profil' => NULL,
        //     'password' => Hash::make('superadmin'),
        //     'no_telp' => '081234567800',
        //     'alamat' => NULL,
        //     'role' => 'super_admin',
        //     'created_at' => Carbon::now(),
        // ]);

        // admin
        DB::table('users')->insert([
            'username' => 'admin01',
            'nama' => Str::random(10),
            'tgl_lahir' => date('1995-06-07'),
            'jkel' => 'Perempuan',
            'email' => Str::random(10).'@gmail.com',
            'foto_profil' => NULL,
            'password' => Hash::make('admin01'),
            'no_telp' => '081234567801',
            'alamat' => NULL,
            'role' => 'admin',
            'created_at' => Carbon::now(),
        ]);
        DB::table('users')->insert([
            'username' => 'admin02',
            'nama' => Str::random(10),
            'tgl_lahir' => date('1990-01-02'),
            'jkel' => 'Laki-laki',
            'email' => Str::random(10).'@gmail.com',
            'foto_profil' => NULL,
            'password' => Hash::make('admin02'),
            'no_telp' => '081234567802',
            'alamat' => NULL,
            'role' => 'admin',
            'created_at' => Carbon::now(),
        ]);
    }
}
