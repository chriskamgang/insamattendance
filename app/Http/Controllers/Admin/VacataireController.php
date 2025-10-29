<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\VacataireServices;
use App\Http\Services\VacatairePaymentServices;
use App\Models\Department;
use Illuminate\Http\Request;

class VacataireController extends Controller
{
    protected $vacataireServices;
    protected $paymentServices;

    public function __construct(
        VacataireServices $vacataireServices,
        VacatairePaymentServices $paymentServices
    ) {
        $this->vacataireServices = $vacataireServices;
        $this->paymentServices = $paymentServices;
    }

    /**
     * Liste de tous les vacataires
     */
    public function index(Request $request)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $filters = [
            'department_id' => $request->department_id,
            'contract_status' => $request->contract_status,
            'search' => $request->search,
        ];

        $vacataires = $this->vacataireServices->getAllVacataires($filters);
        $departments = Department::pluck('title', 'id');
        $alerts = $this->vacataireServices->getContractAlerts();

        return view('admin.vacataires.index', compact('vacataires', 'departments', 'alerts', 'filters'));
    }

    /**
     * Détails d'un vacataire
     */
    public function show($id, Request $request)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $vacataire = \App\Models\User::with(['getDepartment', 'getShift', 'vacataireContracts', 'monthlyPayments'])
            ->where('employee_type', 'vacataire')
            ->findOrFail($id);

        // Stats du mois sélectionné (ou mois en cours)
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $stats = $this->vacataireServices->getVacataireStats($id, $month, $year);

        // Historique des paies
        $payments = $this->paymentServices->getVacatairePaymentHistory($id, 6); // 6 derniers mois

        return view('admin.vacataires.show', compact('vacataire', 'stats', 'payments', 'month', 'year'));
    }

    /**
     * Renouveler le contrat d'un vacataire
     */
    public function renewContract(Request $request, $id)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $request->validate([
            'end_date' => 'required|date|after:today',
            'hourly_rate' => 'nullable|numeric|min:0',
            'max_hours_per_month' => 'nullable|integer|min:0',
        ]);

        try {
            $this->vacataireServices->renewContract(
                $id,
                $request->end_date,
                $request->hourly_rate,
                $request->max_hours_per_month
            );

            return redirect()->route('vacataire.show', $id)->with('success', 'Contrat renouvelé avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Terminer le contrat d'un vacataire
     */
    public function terminateContract(Request $request, $id)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->vacataireServices->terminateContract($id, $request->reason);

            return redirect()->route('vacataire.show', $id)->with('success', 'Contrat terminé avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Dashboard des paiements mensuels
     */
    public function paymentsIndex(Request $request)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $status = $request->status;

        $payments = $this->paymentServices->getMonthlyPayments($month, $year, $status);
        $stats = $this->paymentServices->getGlobalStats($month, $year);

        return view('admin.vacataires.payments.index', compact('payments', 'stats', 'month', 'year', 'status'));
    }

    /**
     * Générer les paies pour un mois donné
     */
    public function generatePayments(Request $request)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        try {
            $result = $this->paymentServices->generateAllMonthlyPayments($request->month, $request->year);

            $message = "Génération terminée : {$result['total']} paies créées";
            if (!empty($result['errors'])) {
                $message .= ", " . count($result['errors']) . " erreurs";
            }

            return redirect()->route('vacataire.payments.index', [
                'month' => $request->month,
                'year' => $request->year
            ])->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Valider une paie
     */
    public function validatePayment($id)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        try {
            $payment = $this->paymentServices->validatePayment($id, auth()->id());

            return redirect()->back()->with('success', 'Paie validée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Marquer une paie comme payée
     */
    public function markAsPaid(Request $request, $id)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $request->validate([
            'payment_method' => 'required|string|in:cash,bank_transfer,mobile_money,check',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        try {
            $payment = $this->paymentServices->markAsPaid($id, $request->payment_method, $request->payment_reference);

            return redirect()->back()->with('success', 'Paiement enregistré avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Annuler une paie
     */
    public function cancelPayment(Request $request, $id)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $payment = $this->paymentServices->cancelPayment($id, $request->reason);

            return redirect()->back()->with('success', 'Paie annulée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Modifier les ajustements d'une paie
     */
    public function updatePaymentAdjustments(Request $request, $id)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $request->validate([
            'deductions' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $payment = $this->paymentServices->updatePaymentAdjustments(
                $id,
                $request->deductions ?? 0,
                $request->bonuses ?? 0,
                $request->notes
            );

            return redirect()->back()->with('success', 'Ajustements mis à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Télécharger la fiche de paie en PDF
     */
    public function downloadPayslip($id)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        try {
            return $this->paymentServices->generatePayslipPDF($id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Exporter les paies en Excel
     */
    public function exportPayments(Request $request)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        try {
            $data = $this->paymentServices->exportMonthlyPaymentsExcel($month, $year);

            // Utiliser une bibliothèque d'export Excel (Laravel Excel recommandé)
            // Pour l'instant, retourner les données brutes
            return response()->json($data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Page de rapports et statistiques
     */
    public function reports(Request $request)
    {
        if (!checkUserPermission()) {
            abort(403);
        }

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $stats = $this->paymentServices->getGlobalStats($month, $year);
        $alerts = $this->vacataireServices->getContractAlerts();

        // Statistiques sur plusieurs mois pour graphiques
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->month,
                'year' => $date->year,
                'label' => $date->format('M Y'),
                'stats' => $this->paymentServices->getGlobalStats($date->month, $date->year)
            ];
        }

        return view('admin.vacataires.reports', compact('stats', 'alerts', 'monthlyData', 'month', 'year'));
    }
}
