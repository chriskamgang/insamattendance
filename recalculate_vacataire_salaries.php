<?php

/**
 * Script de recalcul des salaires pour les vacataires
 *
 * Ce script recalcule le salaire journalier pour toutes les présences
 * des vacataires qui ont un check-out mais pas de salaire calculé.
 *
 * Usage: php recalculate_vacataire_salaries.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Attendance;

echo "========================================\n";
echo "RECALCUL DES SALAIRES VACATAIRES\n";
echo "========================================\n\n";

// Récupérer tous les vacataires
$vacataires = User::where('employee_type', 'vacataire')->get();

echo "Nombre de vacataires trouvés: " . $vacataires->count() . "\n\n";

$totalUpdated = 0;
$totalSkipped = 0;
$totalErrors = 0;

foreach ($vacataires as $vacataire) {
    echo "Traitement de: {$vacataire->name} (ID: {$vacataire->id})\n";
    echo "Taux horaire: " . number_format($vacataire->hourly_rate, 2) . " FCFA/h\n";

    if (!$vacataire->hourly_rate || $vacataire->hourly_rate <= 0) {
        echo "⚠️  ATTENTION: Taux horaire non défini ou invalide. Ignoré.\n\n";
        $totalSkipped++;
        continue;
    }

    // Récupérer toutes les présences avec check-out
    $attendances = Attendance::where('user_id', $vacataire->id)
        ->whereNotNull('check_out')
        ->get();

    echo "Présences à traiter: " . $attendances->count() . "\n";

    $updated = 0;

    foreach ($attendances as $attendance) {
        try {
            $totalWorkedInMinutes = $attendance->total_working_duration ?? 0;
            $totalLunchMinutes = $attendance->total_lunch_duration ?? 0;

            // Calcul des heures travaillées
            $hoursWorked = ($totalWorkedInMinutes - $totalLunchMinutes) / 60;

            // Calcul du salaire journalier
            $dailySalary = $hoursWorked * $vacataire->hourly_rate;

            // Mise à jour
            $attendance->hourly_rate = $vacataire->hourly_rate;
            $attendance->daily_salary = round($dailySalary, 2);
            $attendance->save();

            $updated++;

            echo "  ✓ Présence du " . \Carbon\Carbon::parse($attendance->check_in)->format('d/m/Y H:i') .
                 " - Heures: " . round($hoursWorked, 2) .
                 " - Salaire: " . number_format($attendance->daily_salary, 2) . " FCFA\n";

        } catch (\Exception $e) {
            echo "  ✗ Erreur: " . $e->getMessage() . "\n";
            $totalErrors++;
        }
    }

    $totalUpdated += $updated;
    echo "Mis à jour: {$updated} présences\n\n";
}

echo "========================================\n";
echo "RÉSUMÉ\n";
echo "========================================\n";
echo "Vacataires traités: " . $vacataires->count() . "\n";
echo "Présences mises à jour: {$totalUpdated}\n";
echo "Vacataires ignorés: {$totalSkipped}\n";
echo "Erreurs: {$totalErrors}\n";
echo "\n✅ Recalcul terminé!\n";
