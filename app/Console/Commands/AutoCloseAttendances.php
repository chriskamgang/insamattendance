<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCloseAttendances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-close {--date= : Date spécifique à traiter (format: Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ferme automatiquement les présences sans check-out à minuit (demi-journée pour permanents/semi-permanents)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('========================================');
        $this->info('FERMETURE AUTOMATIQUE DES PRÉSENCES');
        $this->info('========================================');
        $this->newLine();

        // Date à traiter (hier par défaut, car on exécute à minuit)
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'))->format('Y-m-d')
            : Carbon::yesterday()->format('Y-m-d');

        $this->info("Date traitée: {$targetDate}");
        $this->newLine();

        // Récupérer toutes les présences sans check-out pour la date cible
        $attendances = Attendance::with('user', 'user.getShift')
            ->whereDate('check_in', $targetDate)
            ->whereNull('check_out')
            ->where('is_auto_closed', false)
            ->get();

        if ($attendances->isEmpty()) {
            $this->info('✓ Aucune présence à fermer automatiquement.');
            return Command::SUCCESS;
        }

        $this->info("Présences trouvées: {$attendances->count()}");
        $this->newLine();

        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($attendances as $attendance) {
            $user = $attendance->user;

            // Exclure les vacataires
            if ($user->employee_type === 'vacataire') {
                $this->warn("⊘ {$user->name} (ID: {$user->id}) - VACATAIRE - Ignoré (pas de check-out = pas de salaire)");
                $skipped++;
                continue;
            }

            try {
                $shift = $user->getShift;

                if (!$shift) {
                    $this->error("✗ {$user->name} (ID: {$user->id}) - Pas de shift défini");
                    $errors++;
                    continue;
                }

                // Calculer la durée du shift en minutes
                $shiftStart = Carbon::createFromFormat('H:i', $shift->start);
                $shiftEnd = Carbon::createFromFormat('H:i', $shift->end);
                $totalShiftMinutes = $shiftEnd->diffInMinutes($shiftStart);

                // Demi-journée = 50% des heures du shift
                $halfDayMinutes = $totalShiftMinutes / 2;
                $halfDayHours = $halfDayMinutes / 60;

                // Récupérer le temps de pause déjeuner
                $lunchIn = Carbon::createFromFormat('Y-m-d H:i:s', ($attendance->lunch_in ?? $attendance->check_in));
                $lunchOut = Carbon::createFromFormat('Y-m-d H:i:s', ($attendance->lunch_out ?? $attendance->check_in));
                $totalLunchMinutes = $lunchOut->diffInMinutes($lunchIn);

                // Calculer le salaire normal d'une journée complète
                // Pour les permanents/semi-permanents, on suppose un salaire mensuel
                // qu'on divise par le nombre de jours travaillés
                // Pour simplifier, on utilise le daily_salary s'il existe déjà,
                // sinon on calcule à partir d'un salaire mensuel estimé

                // Salaire journalier normal (à ajuster selon votre logique métier)
                $dailySalaryFull = $user->salary ?? 0;

                // Si le salaire est mensuel, diviser par ~22 jours ouvrables
                if ($dailySalaryFull > 100000) {
                    $dailySalaryFull = $dailySalaryFull / 22;
                }

                // Demi-journée = 50% du salaire journalier
                $halfDaySalary = $dailySalaryFull / 2;

                // La pénalité est de 50% du salaire
                $penalty = $halfDaySalary;

                // Mettre à jour la présence
                $attendance->update([
                    'is_auto_closed' => true,
                    'attendance_status' => 'half_day',
                    'absence_penalty' => round($penalty, 2),
                    'total_working_duration' => $halfDayMinutes,
                    'total_lunch_duration' => $totalLunchMinutes,
                    'daily_salary' => round($halfDaySalary, 2),
                ]);

                $this->info("✓ {$user->name} (ID: {$user->id})");
                $this->line("  Shift: {$totalShiftMinutes}min → Demi-journée: {$halfDayMinutes}min");
                $this->line("  Salaire journalier normal: " . number_format($dailySalaryFull, 0) . " FCFA");
                $this->line("  Salaire demi-journée: " . number_format($halfDaySalary, 0) . " FCFA");
                $this->line("  Pénalité absence check-out: " . number_format($penalty, 0) . " FCFA");
                $this->newLine();

                $processed++;

            } catch (\Exception $e) {
                $this->error("✗ {$user->name} (ID: {$user->id}) - Erreur: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info('========================================');
        $this->info('RÉSUMÉ');
        $this->info('========================================');
        $this->info("Présences fermées: {$processed}");
        $this->info("Vacataires ignorés: {$skipped}");
        $this->info("Erreurs: {$errors}");
        $this->newLine();

        if ($processed > 0) {
            $this->info('✅ Fermeture automatique terminée!');
        }

        return Command::SUCCESS;
    }
}
