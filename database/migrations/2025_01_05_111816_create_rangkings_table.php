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
        Schema::create('rangkings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Receiver::class);
            $table->foreignIdFor(Penilaian::class);
            $table->integer('rangking');
            $table->integer('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rangkings');
    }
};
