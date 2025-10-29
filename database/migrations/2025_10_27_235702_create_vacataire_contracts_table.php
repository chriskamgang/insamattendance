<?php

use App\Models\User;
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
        Schema::create('vacataire_contracts', function (Blueprint $table) {
            $table->id();

            // Relation avec l'utilisateur vacataire
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Numéro de contrat unique
            $table->string('contract_number', 50)->unique()->comment('Numéro unique du contrat');

            // Dates du contrat
            $table->date('start_date')->comment('Date de début du contrat');
            $table->date('end_date')->nullable()->comment('Date de fin du contrat');

            // Conditions financières
            $table->decimal('hourly_rate', 10, 2)->comment('Taux horaire (FCFA)');
            $table->integer('max_hours_per_month')->nullable()->comment('Quota maximum d\'heures par mois');

            // Informations supplémentaires
            $table->string('specialization', 255)->nullable()->comment('Matière/Spécialité enseignée');
            $table->enum('contract_type', ['initial', 'renewal', 'amendment'])->default('initial')->comment('Type de contrat');

            // Statut
            $table->enum('status', ['active', 'expired', 'terminated', 'renewed'])->default('active')->comment('Statut du contrat');
            $table->text('termination_reason')->nullable()->comment('Raison de la résiliation');
            $table->dateTime('terminated_at')->nullable()->comment('Date de résiliation');

            // Audit
            $table->foreignIdFor(User::class, 'created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreignIdFor(User::class, 'updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacataire_contracts');
    }
};
