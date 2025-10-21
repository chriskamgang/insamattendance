<?php

use App\Models\Department;
use App\Models\FaceIds;
use App\Models\Shift;
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
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('dob');
            $table->string('email')->unique();
            $table->string('mobile');
            $table->text('address');
            $table->text('image')->nullable();
            $table->string('password')->nullable();
            $table->string('user_type');
            $table->string('app_password')->nullable();

            $table->string('two_factor_code')->nullable();
            $table->dateTime('two_factor_expires_at')->nullable();
            $table->boolean('otp_verify_status')->default(0);

            $table->foreignIdFor(Department::class, 'department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments');

            $table->foreignIdFor(Shift::class, 'shift_id')->nullable();
            $table->foreign('shift_id')->references('id')->on('shifts');

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
