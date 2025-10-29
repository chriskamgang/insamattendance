@extends('master')

@section('title')
    Rapports Vacataires
@stop

@section('page_title')
    Rapports et Statistiques
@stop

@section('action_button')
    <a href="{{route('admin.vacataire.index')}}" class="btn btn-outline-primary">
        <i data-feather="arrow-left"></i> Retour
    </a>
@stop

@section('content')
    {{-- Sélection de période --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="forms-sample mb-4" action="{{route('admin.vacataire.reports')}}" method="get">
                        <div class="row align-items-center mt-3">
                            <div class="col-xl-4 col-lg-4 col-md-4 mb-2">
                                <select id="month" name="month" class="form-select">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{$m}}" @if($month == $m) selected @endif>
                                            {{ \Carbon\Carbon::create(null, $m)->locale('fr')->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 mb-2">
                                <select id="year" name="year" class="form-select">
                                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                                        <option value="{{$y}}" @if($year == $y) selected @endif>{{$y}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-success form-control">Afficher</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques du mois sélectionné --}}
    <div class="row">
        <div class="col-md-12 mb-3">
            <h5>Période: {{ \Carbon\Carbon::create($year, $month)->locale('fr')->isoFormat('MMMM YYYY') }}</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Total Vacataires</h6>
                        <i data-feather="users" class="icon-lg text-muted"></i>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h3 class="mb-2">{{ $stats['total_vacataires'] }}</h3>
                            <p class="text-muted">Vacataires actifs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Total Heures</h6>
                        <i data-feather="clock" class="icon-lg text-muted"></i>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h3 class="mb-2">{{ number_format($stats['total_hours'], 2) }}</h3>
                            <p class="text-muted">Heures travaillées</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Moyenne Heures</h6>
                        <i data-feather="trending-up" class="icon-lg text-muted"></i>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h3 class="mb-2">{{ number_format($stats['average_hours'], 2) }}</h3>
                            <p class="text-muted">Heures/vacataire</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Coût Total</h6>
                        <i data-feather="dollar-sign" class="icon-lg text-muted"></i>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h3 class="mb-2">{{ number_format($stats['total_cost'], 0, ',', ' ') }}</h3>
                            <p class="text-muted">FCFA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statuts des paiements --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Répartition des Paiements</h6>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge bg-warning" style="width: 50px; height: 50px; font-size: 20px; display: flex; align-items: center; justify-content: center;">
                                        {{ $stats['pending_count'] }}
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">En Attente</h6>
                                    <p class="text-muted mb-0">Paies à valider</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge bg-info" style="width: 50px; height: 50px; font-size: 20px; display: flex; align-items: center; justify-content: center;">
                                        {{ $stats['validated_count'] }}
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">Validés</h6>
                                    <p class="text-muted mb-0">Paies validées</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge bg-success" style="width: 50px; height: 50px; font-size: 20px; display: flex; align-items: center; justify-content: center;">
                                        {{ $stats['paid_count'] }}
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">Payés</h6>
                                    <p class="text-muted mb-0">Paies effectuées</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Évolution sur 6 mois --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Évolution sur 6 Mois</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Période</th>
                                <th>Vacataires</th>
                                <th>Total Heures</th>
                                <th>Moyenne Heures</th>
                                <th>Coût Total</th>
                                <th>Moyenne Salaire</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($monthlyData as $data)
                                <tr>
                                    <td><strong>{{ $data['label'] }}</strong></td>
                                    <td>{{ $data['stats']['total_vacataires'] }}</td>
                                    <td>{{ number_format($data['stats']['total_hours'], 2) }} h</td>
                                    <td>{{ number_format($data['stats']['average_hours'], 2) }} h</td>
                                    <td>{{ number_format($data['stats']['total_cost'], 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($data['stats']['average_salary'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertes --}}
    @if(!empty($alerts))
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Alertes et Notifications</h6>
                        @foreach($alerts as $alert)
                            @if($alert['type'] == 'expiring_soon')
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i data-feather="alert-circle"></i> {{ $alert['message'] }}
                                    <a href="{{route('admin.vacataire.show', $alert['user']->id)}}" class="btn btn-sm btn-outline-warning ms-2">Voir</a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @elseif($alert['type'] == 'quota_warning')
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i data-feather="info"></i> {{ $alert['message'] }}
                                    <a href="{{route('admin.vacataire.show', $alert['user']->id)}}" class="btn btn-sm btn-outline-info ms-2">Voir</a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Actions rapides --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Actions Rapides</h6>
                    <div class="mt-3">
                        <a href="{{route('admin.vacataire.index')}}" class="btn btn-primary me-2">
                            <i data-feather="users"></i> Liste des Vacataires
                        </a>
                        <a href="{{route('admin.vacataire.payments.index')}}" class="btn btn-success me-2">
                            <i data-feather="dollar-sign"></i> Gestion des Paiements
                        </a>
                        <a href="{{route('admin.user.create')}}" class="btn btn-info me-2">
                            <i data-feather="user-plus"></i> Ajouter Vacataire
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
