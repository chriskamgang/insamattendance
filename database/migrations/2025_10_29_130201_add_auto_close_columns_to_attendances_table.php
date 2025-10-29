<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_auto_closed')->default(false)->after('attendance_type')
                ->comment('True si la présence a été fermée automatiquement à minuit sans check-out');
            $table->decimal('absence_penalty', 10, 2)->default(0)->after('is_auto_closed')
                ->comment('Montant de la déduction appliquée pour absence de check-out (50% du salaire journalier)');
        });

        // Modifier l'enum existant pour ajouter 'half_day' et 'incomplete'
        DB::statement("ALTER TABLE attendances MODIFY COLUMN attendance_status ENUM('on_time', 'late', 'early_departure', 'absent', 'incomplete', 'half_day') DEFAULT 'on_time' COMMENT 'Statut de présence'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['is_auto_closed', 'absence_penalty']);
        });

        // Restaurer l'enum original
        DB::statement("ALTER TABLE attendances MODIFY COLUMN attendance_status ENUM('on_time', 'late', 'early_departure', 'absent') DEFAULT 'on_time' COMMENT 'Statut de présence'");
    }
};
