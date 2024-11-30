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
        Schema::create('receivers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik')->unique();
            $table->date('tanggal_lahir');
            $table->string('rt');
            $table->string('rw');
            $table->string('kelurahan');
            $table->string('pekerjaan');
            $table->string('penghasilan');
            $table->string('status_tempat_tinggal');
            $table->string('status_perkawinan');
            $table->string('jumlah_tanggungan');
            $table->string('keadaan_rumah');
            $table->boolean('disabilitas');
            $table->string('pendidikan');
            $table->string('fasilitas_mck');
            $table->string('bahan_bakar_harian');
            $table->string('kepemilikan_kendaraan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivers');
    }
};
