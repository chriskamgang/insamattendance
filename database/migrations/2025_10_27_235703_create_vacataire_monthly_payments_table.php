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
        Schema::create('vacataire_monthly_payments', function (Blueprint $table) {
            $table->id();

            // Relation avec le vacataire
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Période de paiement
            $table->tinyInteger('month')->comment('Mois (1-12)');
            $table->integer('year')->comment('Année');

            // Calculs automatiques
            $table->decimal('total_hours', 10, 2)->default(0)->comment('Total heures travaillées');
            $table->integer('total_days_worked')->default(0)->comment('Nombre de jours travaillés');
            $table->decimal('hourly_rate', 10, 2)->comment('Taux horaire du mois (FCFA)');

            // Montants
            $table->decimal('gross_salary', 10, 2)->comment('Salaire brut (heures × taux)');
            $table->decimal('deductions', 10, 2)->default(0)->comment('Retenues éventuelles');
            $table->decimal('bonuses', 10, 2)->default(0)->comment('Primes éventuelles');
            $table->decimal('net_salary', 10, 2)->comment('Salaire net à payer');

            // Statut et validation
            $table->enum('status', ['pending', 'validated', 'paid', 'cancelled'])->default('pending')->comment('Statut du paiement');
            $table->foreignIdFor(User::class, 'validated_by')->nullable();
            $table->foreign('validated_by')->references('id')->on('users');
            $table->dateTime('validated_at')->nullable()->comment('Date de validation');
            $table->dateTime('paid_at')->nullable()->comment('Date de paiement');
            $table->string('payment_method', 50)->nullable()->comment('Méthode de paiement (virement, espèces, chèque)');
            $table->string('payment_reference', 100)->nullable()->comment('Référence du paiement');

            // Notes
            $table->text('notes')->nullable()->comment('Notes générales');
            $table->text('admin_notes')->nullable()->comment('Notes administratives');

            $table->timestamps();

            // Index unique pour éviter les doublons
            $table->unique(['user_id', 'month', 'year'], 'unique_user_month_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacataire_monthly_payments');
    }
};
