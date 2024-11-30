<?php

use App\Models\Calculate;
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
        Schema::create('calculate__receivers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Calculate::class);
            $table->foreignIdFor(Receiver::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculate__receivers');
    }
};
