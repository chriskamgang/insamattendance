<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Attendance;
use App\Models\VacataireContract;
use App\Models\VacataireMonthlyPayment;
use Carbon\Carbon;

class VacataireServices
{
    /**
     * Obtenir tous les vacataires avec leurs stats
     */
    public function getAllVacataires($filters = [])
    {
        $query = User::where('employee_type', 'vacataire')
            ->with(['getDepartment', 'getShift', 'activeContract']);

        // Filtres
        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['contract_status'])) {
            $query->where('contract_status', $filters['contract_status']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('specialization', 'like', '%' . $filters['search'] . '%');
            });
        }

        $vacataires = $query->orderBy('name')->paginate(20);

        // Ajouter les stats du mois en cours pour chaque vacataire
        $currentMonth = now()->month;
        $currentYear = now()->year;

        foreach ($vacataires as $vacataire) {
            $vacataire->monthly_stats = $this->getVacataireStats($vacataire->id, $currentMonth, $currentYear);
        }

        return $vacataires;
    }

    /**
     * Obtenir les statistiques d'un vacataire pour un mois donné
     */
    public function getVacataireStats($userId, $month, $year)
    {
        $user = User::find($userId);

        if (!$user || $user->employee_type !== 'vacataire') {
            return null;
        }

        // Récupérer toutes les présences du mois
        $attendances = Attendance::where('user_id', $userId)
            ->whereMonth('check_in', $month)
            ->whereYear('check_in', $year)
            ->where('is_validated', true)
            ->get();

        $totalHours = 0;
        $totalSalary = 0;
        $daysWorked = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->check_out) {
                // Convertir les minutes en heures
                $hours = ($attendance->total_working_duration - ($attendance->total_lunch_duration ?? 0)) / 60;
                $totalHours += $hours;
                $totalSalary += $attendance->daily_salary ?? 0;
                $daysWorked++;
            }
        }

        // Calcul du pourcentage du quota
        $quotaPercentage = 0;
        if ($user->max_hours_per_month > 0) {
            $quotaPercentage = ($totalHours / $user->max_hours_per_month) * 100;
        }

        return [
            'total_hours' => round($totalHours, 2),
            'total_salary' => round($totalSalary, 2),
            'days_worked' => $daysWorked,
            'hourly_rate' => $user->hourly_rate,
            'max_hours' => $user->max_hours_per_month,
            'quota_percentage' => round($quotaPercentage, 2),
            'quota_exceeded' => $user->max_hours_per_month > 0 && $totalHours > $user->max_hours_per_month,
        ];
    }

    /**
     * Renouveler le contrat d'un vacataire
     */
    public function renewContract($userId, $newEndDate, $newHourlyRate = null, $newMaxHours = null)
    {
        $user = User::find($userId);

        if (!$user || $user->employee_type !== 'vacataire') {
            throw new \Exception("Utilisateur non trouvé ou n'est pas un vacataire");
        }

        // Marquer l'ancien contrat comme renouvelé
        $oldContract = VacataireContract::where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if ($oldContract) {
            $oldContract->update([
                'status' => 'renewed',
                'end_date' => now()->toDateString(),
                'updated_by' => auth()->id(),
            ]);
        }

        // Créer le nouveau contrat
        $contractNumber = 'CNT-' . date('Y') . '-' . str_pad($userId, 5, '0', STR_PAD_LEFT) . '-R' . ($oldContract ? $oldContract->id : '1');

        $newContract = VacataireContract::create([
            'user_id' => $userId,
            'contract_number' => $contractNumber,
            'start_date' => now()->toDateString(),
            'end_date' => $newEndDate,
            'hourly_rate' => $newHourlyRate ?? $user->hourly_rate,
            'max_hours_per_month' => $newMaxHours ?? $user->max_hours_per_month,
            'specialization' => $user->specialization,
            'contract_type' => 'renewal',
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        // Mettre à jour l'utilisateur
        $user->update([
            'contract_end_date' => $newEndDate,
            'hourly_rate' => $newHourlyRate ?? $user->hourly_rate,
            'max_hours_per_month' => $newMaxHours ?? $user->max_hours_per_month,
            'contract_status' => 'active',
        ]);

        return $newContract;
    }

    /**
     * Terminer le contrat d'un vacataire
     */
    public function terminateContract($userId, $reason)
    {
        $user = User::find($userId);

        if (!$user || $user->employee_type !== 'vacataire') {
            throw new \Exception("Utilisateur non trouvé ou n'est pas un vacataire");
        }

        // Marquer le contrat actif comme terminé
        $activeContract = VacataireContract::where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if ($activeContract) {
            $activeContract->update([
                'status' => 'terminated',
                'termination_reason' => $reason,
                'terminated_at' => now(),
                'updated_by' => auth()->id(),
            ]);
        }

        // Mettre à jour l'utilisateur
        $user->update([
            'contract_status' => 'terminated',
        ]);

        return true;
    }

    /**
     * Vérifier et mettre à jour les contrats expirés
     */
    public function checkExpiredContracts()
    {
        $expiredUsers = User::where('employee_type', 'vacataire')
            ->where('contract_status', 'active')
            ->whereNotNull('contract_end_date')
            ->where('contract_end_date', '<', now()->toDateString())
            ->get();

        foreach ($expiredUsers as $user) {
            $user->update(['contract_status' => 'expired']);

            // Marquer aussi le contrat comme expiré
            VacataireContract::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);
        }

        return $expiredUsers->count();
    }

    /**
     * Obtenir les alertes pour les contrats
     */
    public function getContractAlerts()
    {
        $alerts = [];

        // Contrats qui expirent dans 30 jours
        $expiringSoon = User::where('employee_type', 'vacataire')
            ->where('contract_status', 'active')
            ->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->get();

        foreach ($expiringSoon as $user) {
            $daysLeft = now()->diffInDays($user->contract_end_date);
            $alerts[] = [
                'type' => 'expiring_soon',
                'user' => $user,
                'days_left' => $daysLeft,
                'message' => "Le contrat de {$user->name} expire dans {$daysLeft} jours"
            ];
        }

        // Vacataires qui dépassent leur quota
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $vacatairesWithQuota = User::where('employee_type', 'vacataire')
            ->where('contract_status', 'active')
            ->whereNotNull('max_hours_per_month')
            ->get();

        foreach ($vacatairesWithQuota as $user) {
            $stats = $this->getVacataireStats($user->id, $currentMonth, $currentYear);
            if ($stats['quota_percentage'] >= 90) {
                $alerts[] = [
                    'type' => 'quota_warning',
                    'user' => $user,
                    'percentage' => $stats['quota_percentage'],
                    'message' => "{$user->name} a atteint {$stats['quota_percentage']}% de son quota mensuel"
                ];
            }
        }

        return $alerts;
    }
}
