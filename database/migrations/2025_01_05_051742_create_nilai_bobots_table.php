<?php

use App\Models\Penilaian;
use App\Models\Receiver;
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
        Schema::create('nilai_bobots', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Receiver::class);
            $table->foreignIdFor(Penilaian::class);
            $table->integer('umur');
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_bobots');
    }
};
