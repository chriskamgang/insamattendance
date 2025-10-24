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
        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('includes_saturday')->default(false)->after('is_active')->comment('Indique si le shift inclut le samedi');
            $table->time('saturday_end_time')->nullable()->after('includes_saturday')->comment('Heure de fin pour le samedi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['includes_saturday', 'saturday_end_time']);
        });
    }
};
