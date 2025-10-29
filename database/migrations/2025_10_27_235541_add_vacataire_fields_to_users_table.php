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
        Schema::table('users', function (Blueprint $table) {
            // Type d'employé : permanent, semi_permanent, vacataire
            $table->enum('employee_type', ['permanent', 'semi_permanent', 'vacataire'])
                ->default('permanent')
                ->after('user_type')
                ->comment('Type d\'employé');

            // Taux horaire pour les vacataires (en FCFA)
            $table->decimal('hourly_rate', 10, 2)
                ->nullable()
                ->after('monthly_salary')
                ->comment('Taux horaire pour vacataires (FCFA)');

            // Dates de contrat pour les vacataires
            $table->date('contract_start_date')
                ->nullable()
                ->after('hourly_rate')
                ->comment('Date de début du contrat');

            $table->date('contract_end_date')
                ->nullable()
                ->after('contract_start_date')
                ->comment('Date de fin du contrat');

            // Spécialité/Matière enseignée (pour vacataires)
            $table->string('specialization', 255)
                ->nullable()
                ->after('contract_end_date')
                ->comment('Matière/Spécialité enseignée');

            // Statut du contrat
            $table->enum('contract_status', ['active', 'expired', 'terminated'])
                ->default('active')
                ->after('specialization')
                ->comment('Statut du contrat vacataire');

            // Quota d'heures maximum par mois (optionnel)
            $table->integer('max_hours_per_month')
                ->nullable()
                ->after('contract_status')
                ->comment('Quota maximum d\'heures par mois');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_type',
                'hourly_rate',
                'contract_start_date',
                'contract_end_date',
                'specialization',
                'contract_status',
                'max_hours_per_month'
            ]);
        });
    }
};
