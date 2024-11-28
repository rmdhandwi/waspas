<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('periode')->onDelete('cascade');
            $table->string('nomor_kk', 25);
            $table->string('nama_kk', 25);
            $table->string('provinsi', 5);
            $table->string('kabupaten', 8);
            $table->string('kampung', 5);
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->string('asal_suku', 20);
            $table->string('pekerjaan', 50);
            $table->string('agama', 20);
            $table->integer('status');
            $table->string('jenis_kelamin', 1);
            $table->timestamps(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warga');
    }
};
