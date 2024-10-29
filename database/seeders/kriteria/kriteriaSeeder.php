<?php

namespace Database\Seeders\kriteria;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class kriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('kriteria')->insert([
            'jenis' => 'C1',
            'nama' => 'Penghasilan per bulan',
            'nilai_bobot' => 20,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);
        DB::table('kriteria')->insert([
            'jenis' => 'C2',
            'nama' => 'Status Kepemilikan Rumah',
            'nilai_bobot' => 20,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);
        DB::table('kriteria')->insert([
            'jenis' => 'C3',
            'nama' => 'Struktur Bangunan Rumah',
            'nilai_bobot' => 20,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);
    }
}
