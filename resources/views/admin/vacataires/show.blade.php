@extends('master')

@section('title')
    Vacataire - {{ $vacataire->name }}
@stop

@section('page_title')
    Détails Vacataire
@stop

@section('action_button')
    <a href="{{route('admin.vacataire.index')}}" class="btn btn-outline-primary">
        <i data-feather="arrow-left"></i> Retour à la liste
    </a>
@stop

@section('content')
    {{-- Informations personnelles --}}
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Informations Personnelles</h6>
                    <div class="mt-3">
                        <p><strong>Nom:</strong> {{ $vacataire->name }}</p>
                        <p><strong>Email:</strong> {{ $vacataire->email }}</p>
                        <p><strong>Mobile:</strong> {{ $vacataire->mobile }}</p>
                        <p><strong>Département:</strong> {{ $vacataire->getDepartment->title ?? 'N/A' }}</p>
                        <p><strong>Spécialité:</strong> {{ $vacataire->specialization ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Contrat Actuel</h6>
                    <div class="mt-3">
                        <p><strong>Taux Horaire:</strong> {{ number_format($vacataire->hourly_rate, 0, ',', ' ') }} FCFA/h</p>
                        <p><strong>Date Début:</strong> {{ $vacataire->contract_start_date ? \Carbon\Carbon::parse($vacataire->contract_start_date)->format('d/m/Y') : 'N/A' }}</p>
                        <p><strong>Date Fin:</strong> {{ $vacataire->contract_end_date ? \Carbon\Carbon::parse($vacataire->contract_end_date)->format('d/m/Y') : 'Indéterminée' }}</p>
                        <p><strong>Quota Max:</strong> {{ $vacataire->max_hours_per_month ?? 'Sans limite' }} h/mois</p>
                        <p>
                            <strong>Statut:</strong>
                            @if($vacataire->contract_status == 'active')
                                <span class="badge bg-success">Actif</span>
                            @elseif($vacataire->contract_status == 'expired')
                                <span class="badge bg-warning">Expiré</span>
                            @else
                                <span class="badge bg-danger">Terminé</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Statistiques - {{ \Carbon\Carbon::create($year, $month)->locale('fr')->isoFormat('MMMM YYYY') }}</h6>
                    <div class="mt-3">
                        <p><strong>Jours Travaillés:</strong> {{ $stats['days_worked'] }}</p>
                        <p><strong>Total Heures:</strong> {{ $stats['total_hours'] }} h</p>
                        <p><strong>Total Salaire:</strong> {{ number_format($stats['total_salary'], 0, ',', ' ') }} FCFA</p>
                        @if($vacataire->max_hours_per_month)
                            <p><strong>Quota:</strong>
                                <span class="badge
                                    @if($stats['quota_percentage'] >= 100) bg-danger
                                    @elseif($stats['quota_percentage'] >= 90) bg-warning
                                    @else bg-success
                                    @endif">
                                    {{ number_format($stats['quota_percentage'], 1) }}%
                                </span>
                            </p>
                            @if($stats['quota_exceeded'])
                                <div class="alert alert-danger">
                                    <i data-feather="alert-triangle"></i> Quota mensuel dépassé !
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions de gestion --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Gestion du Contrat</h6>
                    <div class="mt-3">
                        @if($vacataire->contract_status == 'active')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#renewContractModal">
                                <i data-feather="refresh-cw"></i> Renouveler Contrat
                            </button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#terminateContractModal">
                                <i data-feather="x-circle"></i> Terminer Contrat
                            </button>
                        @else
                            <p class="text-muted">Le contrat n'est pas actif. Il ne peut pas être renouvelé ou terminé.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historique des contrats --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Historique des Contrats</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Numéro Contrat</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Taux Horaire</th>
                                <th>Type</th>
                                <th>Statut</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($vacataire->vacataireContracts as $contract)
                                <tr>
                                    <td>{{ $contract->contract_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</td>
                                    <td>{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ number_format($contract->hourly_rate, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ ucfirst($contract->contract_type) }}</td>
                                    <td>
                                        @if($contract->status == 'active')
                                            <span class="badge bg-success">Actif</span>
                                        @elseif($contract->status == 'expired')
                                            <span class="badge bg-warning">Expiré</span>
                                        @elseif($contract->status == 'renewed')
                                            <span class="badge bg-info">Renouvelé</span>
                                        @else
                                            <span class="badge bg-danger">Terminé</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Aucun contrat trouvé.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historique des paiements --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Historique des Paiements</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Période</th>
                                <th>Heures</th>
                                <th>Jours</th>
                                <th>Salaire Brut</th>
                                <th>Déductions</th>
                                <th>Bonus</th>
                                <th>Salaire Net</th>
                                <th>Statut</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->month_name }} {{ $payment->year }}</td>
                                    <td>{{ $payment->total_hours }} h</td>
                                    <td>{{ $payment->total_days_worked }}</td>
                                    <td>{{ number_format($payment->gross_salary, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($payment->deductions, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($payment->bonuses, 0, ',', ' ') }} FCFA</td>
                                    <td><strong>{{ number_format($payment->net_salary, 0, ',', ' ') }} FCFA</strong></td>
                                    <td>
                                        @if($payment->status == 'paid')
                                            <span class="badge bg-success">Payé</span>
                                        @elseif($payment->status == 'validated')
                                            <span class="badge bg-info">Validé</span>
                                        @elseif($payment->status == 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @else
                                            <span class="badge bg-danger">Annulé</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">Aucun paiement trouvé.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Renouveler Contrat --}}
    <div class="modal fade" id="renewContractModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.vacataire.renew', $vacataire->id)}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Renouveler le Contrat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Nouvelle Date de Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="hourly_rate" class="form-label">Nouveau Taux Horaire (optionnel)</label>
                            <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" placeholder="Laisser vide pour garder {{ $vacataire->hourly_rate }}">
                        </div>
                        <div class="mb-3">
                            <label for="max_hours_per_month" class="form-label">Nouveau Quota Max (optionnel)</label>
                            <input type="number" class="form-control" id="max_hours_per_month" name="max_hours_per_month" placeholder="Laisser vide pour garder {{ $vacataire->max_hours_per_month }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Renouveler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Terminer Contrat --}}
    <div class="modal fade" id="terminateContractModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.vacataire.terminate', $vacataire->id)}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Terminer le Contrat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Raison de la Terminaison <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Terminer le Contrat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
