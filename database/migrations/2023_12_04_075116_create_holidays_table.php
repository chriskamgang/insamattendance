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
        Schema::create('holidays', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date');
            $table->boolean('is_active');
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
        Schema::dropIfExists('holidays');
    }
};
