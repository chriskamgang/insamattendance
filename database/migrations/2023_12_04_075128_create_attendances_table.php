<?php

use App\Models\LeaveTypes;
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
        Schema::create('attendances', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->dateTime('lunch_in')->nullable();
            $table->dateTime('lunch_out')->nullable();

            $table->longText('check_in_image')->nullable();
            $table->longText('check_out_image')->nullable();
            $table->longText('lunch_in_image')->nullable();
            $table->longText('lunch_out_image')->nullable();

            $table->text('attendance_note');

            $table->float('total_working_duration');
            $table->float('total_lunch_duration');
            $table->float('total_over_time_duration');

            $table->string('attendance_type')->comment('USER or ADMIN');

            /*leave section */
            $table->boolean('is_on_leave')->default(0);
            $table->string('leave_note')->nullable();
            $table->string('leave_status')->default('approved');
            $table->foreignIdFor(LeaveTypes::class, 'leave_type_id')->nullable();
            $table->foreign('leave_type_id')->references('id')->on('leave_types');
            $table->foreignIdFor(User::class, 'leave_applied_by')->nullable();
            $table->foreign('leave_applied_by')->references('id')->on('users');
            $table->string('leave_group_code')->nullable();
            /*leave section */

            $table->foreignIdFor(User::class, 'shift_id')->nullable();
            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->foreignIdFor(User::class, 'updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
