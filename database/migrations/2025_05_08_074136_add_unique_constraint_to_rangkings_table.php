<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('rangkings', function (Blueprint $table) {
        $table->unique(['penilaian_id', 'receiver_id']);
    });
}

public function down(): void
{
    Schema::table('rangkings', function (Blueprint $table) {
        $table->dropUnique(['rangkings_penilaian_id_receiver_id_unique']);
    });
}
};
