@extends('master')

@section('title')
    Vacataires
@stop

@section('page_title')
    Liste des Vacataires
@stop

@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.user.create'), 'button_text' => "Ajouter Vacataire"])
@stop

@section('content')
    @php
        $search = $filters['search'] ?? null;
        $department_id = $filters['department_id'] ?? null;
        $contract_status = $filters['contract_status'] ?? null;
    @endphp

    {{-- Alertes --}}
    @if(!empty($alerts))
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Alertes</h6>
                        @foreach($alerts as $alert)
                            @if($alert['type'] == 'expiring_soon')
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i data-feather="alert-circle"></i> {{ $alert['message'] }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @elseif($alert['type'] == 'quota_warning')
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i data-feather="info"></i> {{ $alert['message'] }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="forms-sample mb-4" action="{{route('admin.vacataire.index')}}" method="get">
                        <div class="row align-items-center mt-3">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <input type="text" placeholder="Rechercher nom, email, spécialité..." class="form-control" name="search" value="{{$search}}"/>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <select id="department_id" name="department_id" class="form-select">
                                    <option value="">Tous les départements</option>
                                    @foreach($departments as $key => $department)
                                        <option value="{{$key}}" @if($department_id == $key) selected @endif>{{ucfirst($department)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 mb-2">
                                <select id="contract_status" name="contract_status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" @if($contract_status == 'active') selected @endif>Actif</option>
                                    <option value="expired" @if($contract_status == 'expired') selected @endif>Expiré</option>
                                    <option value="terminated" @if($contract_status == 'terminated') selected @endif>Terminé</option>
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-success form-control">Rechercher</button>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <a class="btn btn-block btn-primary" href="{{route('admin.vacataire.index')}}">Réinitialiser</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des vacataires --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <caption class="pb-0">Liste des Vacataires</caption>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Département</th>
                                <th>Spécialité</th>
                                <th>Taux Horaire</th>
                                <th>Heures Mois</th>
                                <th>Quota</th>
                                <th>Statut Contrat</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($vacataires->total() > 0)
                                @foreach($vacataires as $vacataire)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $vacataire->name }}</td>
                                        <td>{{ $vacataire->email }}</td>
                                        <td>{{ $vacataire->getDepartment->title ?? 'N/A' }}</td>
                                        <td>{{ $vacataire->specialization ?? 'N/A' }}</td>
                                        <td>{{ number_format($vacataire->hourly_rate, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            {{ $vacataire->monthly_stats['total_hours'] ?? 0 }} h
                                        </td>
                                        <td>
                                            @if($vacataire->max_hours_per_month)
                                                <span class="badge
                                                    @if(($vacataire->monthly_stats['quota_percentage'] ?? 0) >= 100) bg-danger
                                                    @elseif(($vacataire->monthly_stats['quota_percentage'] ?? 0) >= 90) bg-warning
                                                    @else bg-success
                                                    @endif">
                                                    {{ number_format($vacataire->monthly_stats['quota_percentage'] ?? 0, 1) }}%
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Sans limite</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($vacataire->contract_status == 'active')
                                                <span class="badge bg-success">Actif</span>
                                            @elseif($vacataire->contract_status == 'expired')
                                                <span class="badge bg-warning">Expiré</span>
                                            @else
                                                <span class="badge bg-danger">Terminé</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="d-flex justify-content-center list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{route('admin.vacataire.show', $vacataire->id)}}" title="Voir détails">
                                                        <em class="link-icon" data-feather="eye"></em>
                                                    </a>
                                                </li>
                                                <li class="me-2">
                                                    <a href="{{route('admin.user.edit', ['user' => $vacataire->id])}}" title="Modifier">
                                                        <em class="link-icon" data-feather="edit"></em>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-left" colspan="10">Aucun vacataire trouvé.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $vacataires->links('include.pagination') }}
@stop
