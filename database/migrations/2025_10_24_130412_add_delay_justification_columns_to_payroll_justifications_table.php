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
        Schema::table('payroll_justifications', function (Blueprint $table) {
            $table->float('total_delay_minutes')->default(0)->after('justified_days')->comment('Total des minutes de retard dans le mois');
            $table->float('justified_delay_minutes')->default(0)->after('total_delay_minutes')->comment('Nombre de minutes de retard justifiées');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_justifications', function (Blueprint $table) {
            $table->dropColumn(['total_delay_minutes', 'justified_delay_minutes']);
        });
    }
};
