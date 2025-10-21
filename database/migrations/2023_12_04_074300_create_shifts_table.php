<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shifts', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('start');
            $table->string('end');
            $table->string('type')->comment('morning,day,evening,night');
            $table->boolean('is_early_check_in');
            $table->boolean('is_early_check_out');
            $table->string('before_start');
            $table->string('after_start');
            $table->string('before_end');
            $table->string('after_end');
            $table->boolean('is_active');
            $table->foreignIdFor(User::class, 'created_by')->nullable();
            $table->foreignIdFor(User::class, 'updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
