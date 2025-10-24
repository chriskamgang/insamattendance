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
        Schema::create('payroll_justifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID de l\'employé');
            $table->string('employee_email')->comment('Email de l\'employé');
            $table->tinyInteger('month')->comment('Mois (1-12)');
            $table->smallInteger('year')->comment('Année');
            $table->float('justified_days')->comment('Nombre de jours justifiés');
            $table->text('justification_reason')->comment('Raison de la justification');
            $table->unsignedBigInteger('created_by')->comment('Admin qui a créé la justification');
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
        Schema::dropIfExists('payroll_justifications');
    }
};
