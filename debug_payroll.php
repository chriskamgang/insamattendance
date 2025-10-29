<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Attendance;

echo "========================================\n";
echo "DEBUG PAYROLL - STÉPHANE FOYET\n";
echo "========================================\n\n";

// Date du rapport
$month = date('m');
$year = date('Y');
$startDate = "$year-$month-01";
$endDate = date('Y-m-t', strtotime($startDate));

echo "Période: $startDate à $endDate\n\n";

// Trouver Stéphane Foyet
$user = User::where('name', 'LIKE', '%Foyet%')->first();

if (!$user) {
    echo "❌ Utilisateur Foyet non trouvé\n";
    exit(1);
}

echo "✓ Utilisateur trouvé:\n";
echo "  ID: {$user->id}\n";
echo "  Nom: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Type: {$user->user_type}\n";
echo "  Employee Type: " . ($user->employee_type ?? 'NULL') . "\n";
echo "  Salaire mensuel: {$user->monthly_salary} FCFA\n\n";

// Requête EXACTE du PayrollController
echo "📊 Requête attendances:\n";
$attendances = Attendance::where('user_id', $user->id)
    ->whereDate('date', '>=', $startDate)
    ->whereDate('date', '<=', $endDate)
    ->get();

echo "  Total enregistrements: " . $attendances->count() . "\n";
echo "  Dates: " . $attendances->pluck('date')->toJson() . "\n";
echo "  Dates uniques: " . $attendances->pluck('date')->unique()->count() . "\n\n";

// Calcul comme dans le controller
$daysWorked = $attendances->pluck('date')->unique()->count();
echo "🧮 Calcul final:\n";
echo "  Jours travaillés: $daysWorked\n\n";

// Détails des présences
if ($attendances->count() > 0) {
    echo "📋 Détails des présences:\n";
    foreach($attendances as $att) {
        echo "  - Date: {$att->date} | Check-in: " . ($att->check_in ?? 'NULL') . " | Check-out: " . ($att->check_out ?? 'NULL') . "\n";
    }
} else {
    echo "✓ Aucune présence trouvée (c'est normal)\n";
}

echo "\n========================================\n";
echo "Si 'Jours travaillés' = 0 ici mais = 24 dans l'interface,\n";
echo "alors il y a un problème de cache ou de code.\n";
echo "========================================\n";
