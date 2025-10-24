<?php

/**
 * Script to recalculate delay penalties and attendance status for all existing attendance records
 * This script should be run once to update all historical attendance data
 *
 * Usage: php recalculate_attendance_penalties.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "========================================\n";
echo "Recalculating Attendance Penalties\n";
echo "========================================\n\n";

// Get all attendances with check_in but missing delay calculation
$attendances = DB::table('attendances')
    ->whereNotNull('check_in')
    ->get();

echo "Found " . $attendances->count() . " attendance records to process.\n\n";

$updated = 0;
$skipped = 0;
$errors = 0;

foreach ($attendances as $attendance) {
    try {
        // Get user information
        $user = DB::table('users')->where('id', $attendance->user_id)->first();

        if (!$user) {
            echo "⚠️  Skipping attendance ID {$attendance->id} - User not found\n";
            $skipped++;
            continue;
        }

        // Get shift information
        $shift = DB::table('shifts')->where('id', $user->shift_id)->first();

        if (!$shift) {
            echo "⚠️  Skipping attendance ID {$attendance->id} - Shift not found for user {$user->name}\n";
            $skipped++;
            continue;
        }

        // Parse check-in time
        $checkInTime = Carbon::parse($attendance->check_in);

        // Parse shift start time
        $shiftStartTime = Carbon::createFromFormat('H:i', $shift->start);
        $shiftStartTime->setDate(
            $checkInTime->year,
            $checkInTime->month,
            $checkInTime->day
        );

        // Add grace period (after_start in minutes)
        $graceMinutes = $shift->after_start ?? 0;
        $shiftStartTimeWithGrace = $shiftStartTime->copy()->addMinutes($graceMinutes);

        // Get shift working hours (default 8)
        $shiftWorkingHours = $shift->working_hours ?? 8;

        // Get monthly salary
        $monthlySalary = $user->monthly_salary ?? 0;

        // Calculate delay and penalty
        $delayMinutes = 0;
        $penalty = 0;
        $status = 'on_time';
        $dailySalary = 0;

        if ($monthlySalary > 0) {
            $dailySalary = $monthlySalary / 30;
            $hourlySalary = $dailySalary / $shiftWorkingHours;
            $perMinuteSalary = $hourlySalary / 60;

            if ($checkInTime->greaterThan($shiftStartTimeWithGrace)) {
                $delayMinutes = $checkInTime->diffInMinutes($shiftStartTimeWithGrace);
                $status = 'late';
                $penalty = $delayMinutes * $perMinuteSalary;
                $penalty = round($penalty, 2);
            }

            $dailySalary = round($dailySalary - $penalty, 2);
        } else {
            // No salary defined, just calculate delay
            if ($checkInTime->greaterThan($shiftStartTimeWithGrace)) {
                $delayMinutes = $checkInTime->diffInMinutes($shiftStartTimeWithGrace);
                $status = 'late';
            }
        }

        // Update attendance record
        DB::table('attendances')
            ->where('id', $attendance->id)
            ->update([
                'delay_minutes' => $delayMinutes,
                'delay_penalty' => $penalty,
                'attendance_status' => $status,
                'daily_salary' => $dailySalary,
                'updated_at' => now(),
            ]);

        if ($delayMinutes > 0) {
            echo "✅ Updated attendance ID {$attendance->id} - User: {$user->name} - Date: {$checkInTime->format('Y-m-d')} - Delay: {$delayMinutes} min - Penalty: {$penalty} FCFA\n";
        } else {
            echo "✅ Updated attendance ID {$attendance->id} - User: {$user->name} - Date: {$checkInTime->format('Y-m-d')} - Status: On time\n";
        }

        $updated++;

    } catch (Exception $e) {
        echo "❌ Error processing attendance ID {$attendance->id}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n========================================\n";
echo "Recalculation Complete!\n";
echo "========================================\n";
echo "✅ Updated: {$updated}\n";
echo "⚠️  Skipped: {$skipped}\n";
echo "❌ Errors: {$errors}\n";
echo "========================================\n\n";

echo "You can now refresh the Payroll Report page to see the updated data.\n";
