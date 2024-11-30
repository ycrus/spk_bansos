<?php

use App\Models\Criteria;
use App\Models\Program;
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
        Schema::create('program_criterias', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Program::class);
            $table->foreignIdFor(Criteria::class);
            $table->numeric('weight')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program__criterias');
    }
};
