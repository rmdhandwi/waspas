<?php

namespace Database\Seeders\data;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // admin
        User::create(
            [
                'username' => 'admin',
                'nama' => Str::random(10),
                'tgl_lahir' => date('1995-06-07'),
                'jkel' => 'Perempuan',
                'email' => Str::random(10) . '@gmail.com',
                'foto_profil' => NULL,
                'password' => bcrypt('admin12345'),
                'no_telp' => '081234567801',
                'alamat' => NULL,
                'role' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
        );

        // Kepala User
        User::create([
            'username' => 'kepala',
            'nama' => Str::random(10),
            'tgl_lahir' => '1990-01-02',
            'jkel' => 'Laki-laki',
            'email' => Str::random(10) . '@gmail.com',
            'foto_profil' => null,
            'password' => bcrypt('kepala12345'),
            'no_telp' => '081234567802',
            'alamat' => null, // Use empty string if NULL is not allowed
            'role' => 'super_admin',
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ]);
    }
}
