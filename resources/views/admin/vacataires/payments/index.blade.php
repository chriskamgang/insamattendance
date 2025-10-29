@extends('master')

@section('title')
    Paiements Vacataires
@stop

@section('page_title')
    Gestion des Paiements Mensuels
@stop

@section('action_button')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePaymentsModal">
        <i data-feather="plus"></i> Générer Paies du Mois
    </button>
@stop

@section('content')
    {{-- Statistiques --}}
    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Vacataires</h6>
                    <h3 class="mt-3">{{ $stats['total_vacataires'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Heures</h6>
                    <h3 class="mt-3">{{ number_format($stats['total_hours'], 2) }} h</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Coût Total</h6>
                    <h3 class="mt-3">{{ number_format($stats['total_cost'], 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">En Attente</h6>
                    <h3 class="mt-3 text-warning">{{ $stats['pending_count'] }}</h3>
                    <p class="text-muted mb-0">Validés: {{ $stats['validated_count'] }} | Payés: {{ $stats['paid_count'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="forms-sample mb-4" action="{{route('admin.vacataire.payments.index')}}" method="get">
                        <div class="row align-items-center mt-3">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <select id="month" name="month" class="form-select">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{$m}}" @if($month == $m) selected @endif>
                                            {{ \Carbon\Carbon::create(null, $m)->locale('fr')->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <select id="year" name="year" class="form-select">
                                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                                        <option value="{{$y}}" @if($year == $y) selected @endif>{{$y}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <select id="status" name="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="pending" @if($status == 'pending') selected @endif>En attente</option>
                                    <option value="validated" @if($status == 'validated') selected @endif>Validé</option>
                                    <option value="paid" @if($status == 'paid') selected @endif>Payé</option>
                                    <option value="cancelled" @if($status == 'cancelled') selected @endif>Annulé</option>
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-success form-control">Filtrer</button>
                            </div>
                            <div class="col-lg-1 col-md-3 col-sm-6 mb-2">
                                <a class="btn btn-block btn-primary" href="{{route('admin.vacataire.payments.index')}}">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des paiements --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">
                            Paiements - {{ \Carbon\Carbon::create($year, $month)->locale('fr')->isoFormat('MMMM YYYY') }}
                        </h6>
                        <a href="{{route('admin.vacataire.payments.export', ['month' => $month, 'year' => $year])}}"
                           class="btn btn-sm btn-outline-success">
                            <i data-feather="download"></i> Exporter Excel
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Vacataire</th>
                                <th>Département</th>
                                <th>Jours</th>
                                <th>Heures</th>
                                <th>Taux</th>
                                <th>Brut</th>
                                <th>Déductions</th>
                                <th>Bonus</th>
                                <th>Net</th>
                                <th>Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $payment->user->name }}</td>
                                    <td>{{ $payment->user->getDepartment->title ?? 'N/A' }}</td>
                                    <td>{{ $payment->total_days_worked }}</td>
                                    <td>{{ $payment->total_hours }} h</td>
                                    <td>{{ number_format($payment->hourly_rate, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($payment->gross_salary, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($payment->deductions, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($payment->bonuses, 0, ',', ' ') }}</td>
                                    <td><strong>{{ number_format($payment->net_salary, 0, ',', ' ') }}</strong></td>
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
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($payment->status == 'pending')
                                                    <li>
                                                        <button class="dropdown-item" onclick="openAdjustmentsModal({{ $payment->id }}, {{ $payment->deductions }}, {{ $payment->bonuses }})">
                                                            <i data-feather="edit-2"></i> Ajustements
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form action="{{route('admin.vacataire.payments.validate', $payment->id)}}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i data-feather="check"></i> Valider
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($payment->status == 'validated')
                                                    <li>
                                                        <button class="dropdown-item" onclick="openMarkPaidModal({{ $payment->id }})">
                                                            <i data-feather="dollar-sign"></i> Marquer Payé
                                                        </button>
                                                    </li>
                                                @endif
                                                @if($payment->status == 'paid' || $payment->status == 'validated')
                                                    <li>
                                                        <a href="{{route('admin.vacataire.payments.payslip', $payment->id)}}" class="dropdown-item">
                                                            <i data-feather="file-text"></i> Télécharger Fiche
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($payment->status != 'paid')
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="openCancelModal({{ $payment->id }})">
                                                            <i data-feather="x"></i> Annuler
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">Aucun paiement trouvé pour cette période.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Générer Paies --}}
    <div class="modal fade" id="generatePaymentsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.vacataire.payments.generate')}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Générer les Paies Mensuelles</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="gen_month" class="form-label">Mois <span class="text-danger">*</span></label>
                            <select id="gen_month" name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{$m}}" @if(now()->month == $m) selected @endif>
                                        {{ \Carbon\Carbon::create(null, $m)->locale('fr')->isoFormat('MMMM') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="gen_year" class="form-label">Année <span class="text-danger">*</span></label>
                            <select id="gen_year" name="year" class="form-select" required>
                                @for($y = now()->year; $y >= now()->year - 2; $y--)
                                    <option value="{{$y}}" @if(now()->year == $y) selected @endif>{{$y}}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i data-feather="info"></i> Cette action va générer les paies pour tous les vacataires actifs ayant des présences validées pour la période sélectionnée.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Générer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Ajustements --}}
    <div class="modal fade" id="adjustmentsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="adjustmentsForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajustements de Paie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="deductions" class="form-label">Déductions (FCFA)</label>
                            <input type="number" step="0.01" class="form-control" id="deductions" name="deductions" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="bonuses" class="form-label">Bonus (FCFA)</label>
                            <input type="number" step="0.01" class="form-control" id="bonuses" name="bonuses" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Marquer Payé --}}
    <div class="modal fade" id="markPaidModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="markPaidForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Marquer comme Payé</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Méthode de Paiement <span class="text-danger">*</span></label>
                            <select id="payment_method" name="payment_method" class="form-select" required>
                                <option value="cash">Espèces</option>
                                <option value="bank_transfer">Virement Bancaire</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="check">Chèque</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payment_reference" class="form-label">Référence</label>
                            <input type="text" class="form-control" id="payment_reference" name="payment_reference">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Confirmer Paiement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Annuler --}}
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="cancelForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Annuler le Paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Raison de l'Annulation <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="cancel_reason" name="reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-danger">Annuler le Paiement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAdjustmentsModal(paymentId, deductions, bonuses) {
            document.getElementById('adjustmentsForm').action = "{{ url('admin/vacataires/payments') }}/" + paymentId + "/adjustments";
            document.getElementById('deductions').value = deductions;
            document.getElementById('bonuses').value = bonuses;
            new bootstrap.Modal(document.getElementById('adjustmentsModal')).show();
        }

        function openMarkPaidModal(paymentId) {
            document.getElementById('markPaidForm').action = "{{ url('admin/vacataires/payments') }}/" + paymentId + "/mark-paid";
            new bootstrap.Modal(document.getElementById('markPaidModal')).show();
        }

        function openCancelModal(paymentId) {
            document.getElementById('cancelForm').action = "{{ url('admin/vacataires/payments') }}/" + paymentId + "/cancel";
            new bootstrap.Modal(document.getElementById('cancelModal')).show();
        }
    </script>
@stop
