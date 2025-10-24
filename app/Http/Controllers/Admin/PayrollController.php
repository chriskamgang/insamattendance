<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    /**
     * Display the payroll report
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Get filter parameters (default to current month/year)
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        // Build the start and end dates for the selected month
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate)); // Last day of the month

        // Calculate working days in the month (Monday-Saturday, excluding holidays)
        $workingDays = $this->calculateWorkingDays($startDate, $endDate);

        // Get all employees (user_type = 'employee')
        $employees = User::where('user_type', 'employee')
            ->with(['getDepartment', 'getShift'])
            ->get();

        $_payrollData = [];

        foreach ($employees as $employee) {
            // Get all attendances for this employee in the selected month
            $attendances = Attendance::where('user_id', $employee->id)
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->get();

            // Calculate totals
            $daysWorked = $attendances->count();
            $daysNotWorked = max(0, $workingDays - $daysWorked);
            $totalDelayMinutes = $attendances->sum('delay_minutes');
            $totalPenalties = $attendances->sum('delay_penalty');

            // Get monthly salary
            $monthlySalary = $employee->monthly_salary ?? 0;

            // Get justifications for this employee for this month
            $justification = DB::table('payroll_justifications')
                ->where('user_id', $employee->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            $justifiedDays = $justification ? $justification->justified_days : 0;
            $justifiedDelayMinutes = $justification ? $justification->justified_delay_minutes : 0;

            // Calculate unjustified days (days not worked - justified days)
            $unjustifiedDays = max(0, $daysNotWorked - $justifiedDays);

            // Calculate deduction for unjustified days
            $dailyRate = $workingDays > 0 ? $monthlySalary / $workingDays : 0;
            $deductionForAbsence = $unjustifiedDays * $dailyRate;

            // Calculate adjusted delay penalties (subtract penalties for justified delay minutes)
            $adjustedDelayPenalties = $totalPenalties;
            if ($totalDelayMinutes > 0 && $justifiedDelayMinutes > 0) {
                // Calculate penalty per minute
                $penaltyPerMinute = $totalPenalties / $totalDelayMinutes;
                // Subtract penalties for justified minutes
                $adjustedDelayPenalties = max(0, $totalPenalties - ($justifiedDelayMinutes * $penaltyPerMinute));
            }

            // Calculate final salary (monthly salary - adjusted delay penalties - absence deduction)
            $finalSalary = $monthlySalary - $adjustedDelayPenalties - $deductionForAbsence;

            // Only include employees with salary defined or who worked this month
            if ($monthlySalary > 0 || $daysWorked > 0) {
                $_payrollData[] = [
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'department' => $employee->getDepartment ? $employee->getDepartment->title : 'N/A',
                    'monthly_salary' => $monthlySalary,
                    'days_worked' => $daysWorked,
                    'days_not_worked' => $daysNotWorked,
                    'justified_days' => $justifiedDays,
                    'unjustified_days' => $unjustifiedDays,
                    'total_delay_minutes' => $totalDelayMinutes,
                    'justified_delay_minutes' => $justifiedDelayMinutes,
                    'total_penalties' => $adjustedDelayPenalties,
                    'absence_deduction' => $deductionForAbsence,
                    'final_salary' => max(0, $finalSalary), // Ensure final salary is not negative
                ];
            }
        }

        return view('payroll.index', compact('_payrollData', 'workingDays'));
    }

    /**
     * Calculate the number of working days in a month
     * Monday to Saturday (Saturday counts as half day)
     * Excludes Sundays and public holidays
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    private function calculateWorkingDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Get all holidays in this month
        $holidays = Holiday::where('is_active', 1)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->pluck('date')
            ->toArray();

        $workingDays = 0;

        // Loop through each day of the month
        while ($start->lte($end)) {
            $dayOfWeek = $start->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $currentDate = $start->format('Y-m-d');

            // Skip Sundays (0)
            if ($dayOfWeek === 0) {
                $start->addDay();
                continue;
            }

            // Skip public holidays
            if (in_array($currentDate, $holidays)) {
                $start->addDay();
                continue;
            }

            // Count working days
            // Saturday (6) counts as 0.5 day, Monday-Friday count as 1 day
            if ($dayOfWeek === 6) {
                $workingDays += 0.5; // Saturday = half day
            } else {
                $workingDays += 1; // Monday-Friday = full day
            }

            $start->addDay();
        }

        return $workingDays;
    }

    /**
     * Show detailed justification page for an employee
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function justifyDetails(Request $request)
    {
        $employeeEmail = $request->get('employee_email');
        $month = $request->get('month');
        $year = $request->get('year');

        // Get employee
        $employee = User::where('email', $employeeEmail)->first();

        if (!$employee) {
            return redirect()->route('admin.payroll.index')->with('error', 'Employé introuvable.');
        }

        // Build the start and end dates for the selected month
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        // Calculate working days
        $workingDays = $this->calculateWorkingDays($startDate, $endDate);

        // Get all working days in the month (excluding Sundays and holidays)
        $allWorkingDays = $this->getAllWorkingDaysInMonth($startDate, $endDate);

        // Get attendances for this employee
        $attendances = Attendance::where('user_id', $employee->id)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->keyBy(function($item) {
                return date('Y-m-d', strtotime($item->date));
            });

        // Get existing justifications
        $existingJustifications = DB::table('payroll_justifications')
            ->where('user_id', $employee->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // Prepare days data
        $daysData = [];
        foreach ($allWorkingDays as $day) {
            $dateKey = $day['date'];
            $dayData = [
                'date' => $dateKey,
                'day_name' => $day['day_name'],
                'is_saturday' => $day['is_saturday'],
                'status' => 'absent', // absent, late, present
                'check_in' => null,
                'check_out' => null,
                'delay_minutes' => 0,
                'delay_hours' => 0,
            ];

            if (isset($attendances[$dateKey])) {
                $attendance = $attendances[$dateKey];
                $dayData['check_in'] = $attendance->check_in;
                $dayData['check_out'] = $attendance->check_out;
                $dayData['delay_minutes'] = $attendance->delay_minutes ?? 0;
                $dayData['delay_hours'] = round($dayData['delay_minutes'] / 60, 2);

                if ($dayData['delay_minutes'] > 0) {
                    $dayData['status'] = 'late';
                } else {
                    $dayData['status'] = 'present';
                }
            }

            $daysData[] = $dayData;
        }

        return view('payroll.justify-details', compact(
            'employee',
            'month',
            'year',
            'workingDays',
            'daysData',
            'existingJustifications'
        ));
    }

    /**
     * Get all working days in a month with details
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getAllWorkingDaysInMonth($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Get all holidays
        $holidays = Holiday::where('is_active', 1)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->pluck('date')
            ->toArray();

        $workingDays = [];
        $dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

        while ($start->lte($end)) {
            $dayOfWeek = $start->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $currentDate = $start->format('Y-m-d');

            // Skip Sundays and holidays
            if ($dayOfWeek !== 0 && !in_array($currentDate, $holidays)) {
                $workingDays[] = [
                    'date' => $currentDate,
                    'day_name' => $dayNames[$dayOfWeek],
                    'is_saturday' => ($dayOfWeek === 6),
                ];
            }

            $start->addDay();
        }

        return $workingDays;
    }

    /**
     * Justify non-worked days for an employee
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function justify(Request $request)
    {
        $request->validate([
            'employee_email' => 'required|email|exists:users,email',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'justified_days' => 'required|numeric|min:0',
            'total_delay_minutes' => 'nullable|numeric|min:0',
            'justified_delay_minutes' => 'nullable|numeric|min:0',
            'justification_reason' => 'required|string|min:5',
        ]);

        // Get employee by email
        $employee = User::where('email', $request->employee_email)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employé introuvable.');
        }

        // Check if justification already exists for this month
        $existingJustification = DB::table('payroll_justifications')
            ->where('user_id', $employee->id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();

        if ($existingJustification) {
            // Update existing justification
            DB::table('payroll_justifications')
                ->where('id', $existingJustification->id)
                ->update([
                    'justified_days' => $request->justified_days,
                    'total_delay_minutes' => $request->total_delay_minutes ?? 0,
                    'justified_delay_minutes' => $request->justified_delay_minutes ?? 0,
                    'justification_reason' => $request->justification_reason,
                    'created_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            return redirect()->back()->with('success', 'Justification mise à jour avec succès!');
        } else {
            // Create new justification
            DB::table('payroll_justifications')->insert([
                'user_id' => $employee->id,
                'employee_email' => $request->employee_email,
                'month' => $request->month,
                'year' => $request->year,
                'justified_days' => $request->justified_days,
                'total_delay_minutes' => $request->total_delay_minutes ?? 0,
                'justified_delay_minutes' => $request->justified_delay_minutes ?? 0,
                'justification_reason' => $request->justification_reason,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Justification enregistrée avec succès!');
        }
    }

    /**
     * Apply deduction for unjustified absences
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyDeduction(Request $request)
    {
        $request->validate([
            'employee_email' => 'required|email|exists:users,email',
            'days_not_worked' => 'required|numeric|min:0',
            'deduction_amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        // Get employee by email
        $employee = User::where('email', $request->employee_email)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employé introuvable.'
            ], 404);
        }

        // Check if deduction already exists for this month
        $existingDeduction = DB::table('payroll_deductions')
            ->where('user_id', $employee->id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();

        if ($existingDeduction) {
            // Update existing deduction
            DB::table('payroll_deductions')
                ->where('id', $existingDeduction->id)
                ->update([
                    'days_not_worked' => $request->days_not_worked,
                    'deduction_amount' => $request->deduction_amount,
                    'created_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Déduction mise à jour avec succès!'
            ]);
        } else {
            // Create new deduction
            DB::table('payroll_deductions')->insert([
                'user_id' => $employee->id,
                'employee_email' => $request->employee_email,
                'month' => $request->month,
                'year' => $request->year,
                'days_not_worked' => $request->days_not_worked,
                'deduction_amount' => $request->deduction_amount,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Déduction appliquée avec succès!'
            ]);
        }
    }
}
