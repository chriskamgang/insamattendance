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
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('delay_minutes')->default(0)->after('total_over_time_duration')->comment('Minutes de retard à l\'entrée');
            $table->decimal('delay_penalty', 10, 2)->default(0)->after('delay_minutes')->comment('Pénalité en FCFA pour retard');
            $table->decimal('daily_salary', 10, 2)->nullable()->after('delay_penalty')->comment('Salaire du jour après déduction');
            $table->enum('attendance_status', ['on_time', 'late', 'early_departure', 'absent'])->default('on_time')->after('daily_salary')->comment('Statut de présence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['delay_minutes', 'delay_penalty', 'daily_salary', 'attendance_status']);
        });
    }
};
