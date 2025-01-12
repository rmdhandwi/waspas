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
        Schema::create('hasil_seleksi', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->float('skor_akhir');
            $table->integer('peringkat');
            $table->timestamps();
        });

        Schema::create('perhitungan', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->float('skor_akhir');
            $table->integer('peringkat');
            $table->string('status', 11);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hasil_seleksi');
    }
};
