<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_warga', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('nomor_kk',25);
            $table->string('nama_kk',25);
            $table->string('provinsi',5);
            $table->string('kabupaten',8);
            $table->string('kampung',5);
            $table->string('rt',5);
            $table->string('rw',5);
            $table->string('asal_suku',20);
            $table->string('pekerjaan',8);
            $table->string('agama',4);
            $table->string('jenis_kelamin',1);
            $table->string('sanitasi',25);
            $table->string('j_kloset',25);
            $table->string('t_limbah',25);
            $table->string('akses_air_minum',25);
            $table->string('status_rumah',25);
            $table->string('struktur_bangunan',25);
            $table->datetime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_warga');
    }
};
