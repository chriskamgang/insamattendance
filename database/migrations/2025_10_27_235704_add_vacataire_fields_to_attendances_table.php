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
            // Taux horaire au moment du scan (stocké pour historique)
            $table->decimal('hourly_rate', 10, 2)
                ->nullable()
                ->after('attendance_status')
                ->comment('Taux horaire au moment du pointage (FCFA)');

            // NOTE: daily_salary existe déjà dans la migration 2025_10_23_100001
            // Pas besoin de le créer à nouveau

            // Validation de la présence
            $table->boolean('is_validated')
                ->default(1)
                ->after('hourly_rate')
                ->comment('Présence validée par superviseur');

            $table->foreignIdFor(\App\Models\User::class, 'validated_by')
                ->nullable()
                ->after('is_validated');
            $table->foreign('validated_by')->references('id')->on('users');

            $table->dateTime('validated_at')
                ->nullable()
                ->after('validated_by')
                ->comment('Date de validation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn([
                'hourly_rate',
                // daily_salary reste (il était déjà là)
                'is_validated',
                'validated_by',
                'validated_at'
            ]);
        });
    }
};
