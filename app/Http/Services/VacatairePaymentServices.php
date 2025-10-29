<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Attendance;
use App\Models\VacataireMonthlyPayment;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class VacatairePaymentServices
{
    /**
     * Générer la paie mensuelle pour un vacataire
     */
    public function generateMonthlyPayment($userId, $month, $year)
    {
        $user = User::find($userId);

        if (!$user || $user->employee_type !== 'vacataire') {
            throw new \Exception("Utilisateur non trouvé ou n'est pas un vacataire");
        }

        // Vérifier si la paie existe déjà
        $existingPayment = VacataireMonthlyPayment::where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existingPayment) {
            throw new \Exception("La paie pour ce mois existe déjà");
        }

        // Récupérer toutes les présences validées du mois
        $attendances = Attendance::where('user_id', $userId)
            ->whereMonth('check_in', $month)
            ->whereYear('check_in', $year)
            ->where('is_validated', true)
            ->whereNotNull('check_out') // Seulement les présences complètes
            ->get();

        $totalHours = 0;
        $totalSalary = 0;
        $daysWorked = 0;

        foreach ($attendances as $attendance) {
            // Convertir les minutes en heures
            $hours = ($attendance->total_working_duration - ($attendance->total_lunch_duration ?? 0)) / 60;
            $totalHours += $hours;
            $totalSalary += $attendance->daily_salary ?? 0;
            $daysWorked++;
        }

        // Créer l'enregistrement de paie
        $payment = VacataireMonthlyPayment::create([
            'user_id' => $userId,
            'month' => $month,
            'year' => $year,
            'total_hours' => round($totalHours, 2),
            'total_days_worked' => $daysWorked,
            'hourly_rate' => $user->hourly_rate,
            'gross_salary' => round($totalSalary, 2),
            'deductions' => 0,
            'bonuses' => 0,
            'net_salary' => round($totalSalary, 2),
            'status' => 'pending',
        ]);

        return $payment;
    }

    /**
     * Générer les paies pour tous les vacataires actifs
     */
    public function generateAllMonthlyPayments($month, $year)
    {
        $vacataires = User::where('employee_type', 'vacataire')
            ->where('contract_status', 'active')
            ->get();

        $generated = [];
        $errors = [];

        foreach ($vacataires as $vacataire) {
            try {
                $payment = $this->generateMonthlyPayment($vacataire->id, $month, $year);
                $generated[] = [
                    'user' => $vacataire,
                    'payment' => $payment
                ];
            } catch (\Exception $e) {
                $errors[] = [
                    'user' => $vacataire,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'generated' => $generated,
            'errors' => $errors,
            'total' => count($generated),
        ];
    }

    /**
     * Valider une paie
     */
    public function validatePayment($paymentId, $adminId)
    {
        $payment = VacataireMonthlyPayment::findOrFail($paymentId);

        if ($payment->status !== 'pending') {
            throw new \Exception("Cette paie ne peut pas être validée (statut: {$payment->status})");
        }

        $payment->update([
            'status' => 'validated',
            'validated_by' => $adminId,
            'validated_at' => now(),
        ]);

        return $payment;
    }

    /**
     * Marquer une paie comme payée
     */
    public function markAsPaid($paymentId, $paymentMethod, $reference = null)
    {
        $payment = VacataireMonthlyPayment::findOrFail($paymentId);

        if ($payment->status !== 'validated') {
            throw new \Exception("Cette paie doit être validée avant d'être marquée comme payée");
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference,
        ]);

        return $payment;
    }

    /**
     * Annuler une paie
     */
    public function cancelPayment($paymentId, $reason)
    {
        $payment = VacataireMonthlyPayment::findOrFail($paymentId);

        if ($payment->status === 'paid') {
            throw new \Exception("Une paie déjà payée ne peut pas être annulée");
        }

        $payment->update([
            'status' => 'cancelled',
            'admin_notes' => $reason,
        ]);

        return $payment;
    }

    /**
     * Ajouter des déductions ou bonus
     */
    public function updatePaymentAdjustments($paymentId, $deductions = 0, $bonuses = 0, $notes = null)
    {
        $payment = VacataireMonthlyPayment::findOrFail($paymentId);

        if ($payment->status === 'paid') {
            throw new \Exception("Une paie déjà payée ne peut pas être modifiée");
        }

        $newNetSalary = $payment->gross_salary - $deductions + $bonuses;

        $payment->update([
            'deductions' => $deductions,
            'bonuses' => $bonuses,
            'net_salary' => round($newNetSalary, 2),
            'admin_notes' => $notes,
        ]);

        return $payment;
    }

    /**
     * Obtenir toutes les paies pour un mois donné
     */
    public function getMonthlyPayments($month, $year, $status = null)
    {
        $query = VacataireMonthlyPayment::with('user', 'validator')
            ->where('month', $month)
            ->where('year', $year);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obtenir l'historique des paies d'un vacataire
     */
    public function getVacatairePaymentHistory($userId, $limit = null)
    {
        $query = VacataireMonthlyPayment::where('user_id', $userId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Générer PDF de fiche de paie
     */
    public function generatePayslipPDF($paymentId)
    {
        $payment = VacataireMonthlyPayment::with('user')->findOrFail($paymentId);

        if ($payment->status !== 'paid' && $payment->status !== 'validated') {
            throw new \Exception("La fiche de paie ne peut être générée que pour les paies validées ou payées");
        }

        // Récupérer les détails des présences
        $attendances = Attendance::where('user_id', $payment->user_id)
            ->whereMonth('check_in', $payment->month)
            ->whereYear('check_in', $payment->year)
            ->where('is_validated', true)
            ->whereNotNull('check_out')
            ->orderBy('check_in')
            ->get();

        $data = [
            'payment' => $payment,
            'user' => $payment->user,
            'attendances' => $attendances,
            'companyName' => config('app.name', 'DigitalHR'),
            'generatedDate' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('admin.vacataires.payslip_pdf', $data);

        return $pdf->download('fiche_paie_' . $payment->user->name . '_' . $payment->month . '_' . $payment->year . '.pdf');
    }

    /**
     * Export Excel pour comptabilité
     */
    public function exportMonthlyPaymentsExcel($month, $year)
    {
        $payments = $this->getMonthlyPayments($month, $year);

        $data = [];
        $data[] = ['Nom', 'Email', 'Département', 'Heures', 'Taux Horaire', 'Salaire Brut', 'Déductions', 'Bonus', 'Salaire Net', 'Statut'];

        foreach ($payments as $payment) {
            $data[] = [
                $payment->user->name,
                $payment->user->email,
                $payment->user->getDepartment->title ?? 'N/A',
                $payment->total_hours,
                $payment->hourly_rate,
                $payment->gross_salary,
                $payment->deductions,
                $payment->bonuses,
                $payment->net_salary,
                ucfirst($payment->status),
            ];
        }

        // Totaux
        $data[] = [];
        $data[] = [
            'TOTAL',
            '',
            '',
            $payments->sum('total_hours'),
            '',
            $payments->sum('gross_salary'),
            $payments->sum('deductions'),
            $payments->sum('bonuses'),
            $payments->sum('net_salary'),
            '',
        ];

        return $data;
    }

    /**
     * Obtenir les statistiques globales
     */
    public function getGlobalStats($month, $year)
    {
        $payments = $this->getMonthlyPayments($month, $year);

        return [
            'total_vacataires' => $payments->count(),
            'total_hours' => round($payments->sum('total_hours'), 2),
            'total_cost' => round($payments->sum('net_salary'), 2),
            'average_hours' => $payments->count() > 0 ? round($payments->avg('total_hours'), 2) : 0,
            'average_salary' => $payments->count() > 0 ? round($payments->avg('net_salary'), 2) : 0,
            'pending_count' => $payments->where('status', 'pending')->count(),
            'validated_count' => $payments->where('status', 'validated')->count(),
            'paid_count' => $payments->where('status', 'paid')->count(),
        ];
    }
}
