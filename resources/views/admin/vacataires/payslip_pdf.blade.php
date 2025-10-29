<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Paie - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #2c3e50;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table thead {
            background: #2c3e50;
            color: white;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        .summary-table {
            width: 100%;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .summary-table .label {
            font-weight: bold;
            background: #f8f9fa;
            width: 60%;
        }
        .summary-table .amount {
            text-align: right;
            width: 40%;
        }
        .total-row {
            background: #2c3e50 !important;
            color: white !important;
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #333;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 60px auto 10px auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>{{ $companyName }}</h1>
            <p>FICHE DE PAIE - VACATAIRE</p>
            <p>Période: {{ \Carbon\Carbon::create($payment->year, $payment->month)->locale('fr')->isoFormat('MMMM YYYY') }}</p>
        </div>

        <!-- Informations Employé et Entreprise -->
        <div class="info-section">
            <div class="info-column" style="padding-right: 10px;">
                <div class="info-box">
                    <h3>Informations Vacataire</h3>
                    <p><span class="info-label">Nom:</span> {{ $user->name }}</p>
                    <p><span class="info-label">Email:</span> {{ $user->email }}</p>
                    <p><span class="info-label">Mobile:</span> {{ $user->mobile }}</p>
                    <p><span class="info-label">Département:</span> {{ $user->getDepartment->title ?? 'N/A' }}</p>
                    <p><span class="info-label">Spécialité:</span> {{ $user->specialization ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="info-column" style="padding-left: 10px;">
                <div class="info-box">
                    <h3>Informations Contrat</h3>
                    <p><span class="info-label">Taux Horaire:</span> {{ number_format($payment->hourly_rate, 0, ',', ' ') }} FCFA</p>
                    <p><span class="info-label">Jours Travaillés:</span> {{ $payment->total_days_worked }}</p>
                    <p><span class="info-label">Total Heures:</span> {{ $payment->total_hours }} h</p>
                    <p><span class="info-label">Date Génération:</span> {{ $generatedDate }}</p>
                </div>
            </div>
        </div>

        <!-- Détail des Présences -->
        <h3 style="margin-top: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 5px;">Détail des Présences</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Arrivée</th>
                    <th>Départ</th>
                    <th>Heures Travaillées</th>
                    <th>Salaire Journalier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    @php
                        $hours = ($attendance->total_working_duration - ($attendance->total_lunch_duration ?? 0)) / 60;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($attendance->check_in)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</td>
                        <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : 'N/A' }}</td>
                        <td>{{ number_format($hours, 2) }} h</td>
                        <td>{{ number_format($attendance->daily_salary ?? 0, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Récapitulatif des Montants -->
        <h3 style="margin-top: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 5px;">Récapitulatif</h3>
        <table class="summary-table">
            <tr>
                <td class="label">Salaire Brut</td>
                <td class="amount">{{ number_format($payment->gross_salary, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($payment->deductions > 0)
            <tr>
                <td class="label">Déductions</td>
                <td class="amount">- {{ number_format($payment->deductions, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endif
            @if($payment->bonuses > 0)
            <tr>
                <td class="label">Bonus</td>
                <td class="amount">+ {{ number_format($payment->bonuses, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endif
            <tr class="total-row">
                <td class="label">SALAIRE NET À PAYER</td>
                <td class="amount">{{ number_format($payment->net_salary, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>

        @if($payment->admin_notes)
            <div class="info-box" style="margin-top: 20px;">
                <h3>Notes</h3>
                <p>{{ $payment->admin_notes }}</p>
            </div>
        @endif

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p><strong>Signature de l'Employeur</strong></p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p><strong>Signature du Vacataire</strong></p>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>Document généré le {{ $generatedDate }}</p>
            <p>{{ $companyName }} - Système de Gestion des Ressources Humaines</p>
        </div>
    </div>
</body>
</html>
