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
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID de l\'employé');
            $table->string('employee_email')->comment('Email de l\'employé');
            $table->tinyInteger('month')->comment('Mois (1-12)');
            $table->smallInteger('year')->comment('Année');
            $table->float('days_not_worked')->comment('Nombre de jours non travaillés');
            $table->decimal('deduction_amount', 10, 2)->comment('Montant de la déduction');
            $table->unsignedBigInteger('created_by')->comment('Admin qui a appliqué la déduction');
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'month', 'year']);
            $table->index('employee_email');

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_deductions');
    }
};
