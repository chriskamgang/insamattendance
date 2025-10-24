@extends('master')

@section('title')
    Payroll
@stop
@section('page_title')
    Rapport Mensuel
@stop

@section('action_button')
@stop

@section('content')
    @php
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
    @endphp

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="forms-sample mb-4" action="{{route('admin.payroll.index')}}" method="get">
                        <div class="row align-items-center mt-3">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <select name="month" class="form-select" required>
                                    <option value="">Sélectionner Mois</option>
                                    <option value="01" @if($month == '01') selected @endif>Janvier</option>
                                    <option value="02" @if($month == '02') selected @endif>Février</option>
                                    <option value="03" @if($month == '03') selected @endif>Mars</option>
                                    <option value="04" @if($month == '04') selected @endif>Avril</option>
                                    <option value="05" @if($month == '05') selected @endif>Mai</option>
                                    <option value="06" @if($month == '06') selected @endif>Juin</option>
                                    <option value="07" @if($month == '07') selected @endif>Juillet</option>
                                    <option value="08" @if($month == '08') selected @endif>Août</option>
                                    <option value="09" @if($month == '09') selected @endif>Septembre</option>
                                    <option value="10" @if($month == '10') selected @endif>Octobre</option>
                                    <option value="11" @if($month == '11') selected @endif>Novembre</option>
                                    <option value="12" @if($month == '12') selected @endif>Décembre</option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <select name="year" class="form-select" required>
                                    <option value="">Sélectionner Année</option>
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                        <option value="{{$y}}" @if($year == $y) selected @endif>{{$y}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-success form-control">Filtrer</button>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <a class="btn btn-block btn-primary" href="{{route('admin.payroll.index')}}">Réinitialiser</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Rapport Payroll - {{date('F Y', strtotime("$year-$month-01"))}}</h4>
                    <p class="text-muted mb-3">Jours ouvrables dans le mois: <strong>{{$workingDays}}</strong> (Lundi-Samedi, Samedi = demi-journée)</p>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Employé</th>
                                <th>Département</th>
                                <th>Salaire Mensuel</th>
                                <th class="text-center">Jours Travaillés</th>
                                <th class="text-center">Jours Non Travaillés</th>
                                <th class="text-center">Total Retards (min)</th>
                                <th class="text-end">Pénalités (FCFA)</th>
                                <th class="text-end">Salaire Final (FCFA)</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($_payrollData) > 0)
                                @php
                                    $totalSalaries = 0;
                                    $totalPenalties = 0;
                                    $totalFinal = 0;
                                @endphp
                                @foreach($_payrollData as $index => $payroll)
                                    @php
                                        $totalSalaries += $payroll['monthly_salary'];
                                        $totalPenalties += $payroll['total_penalties'];
                                        $totalFinal += $payroll['final_salary'];
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $payroll['name'] }}</strong>
                                            <br><small class="text-muted">{{ $payroll['email'] }}</small>
                                        </td>
                                        <td>{{ $payroll['department'] }}</td>
                                        <td>{{ number_format($payroll['monthly_salary'], 0, ',', ' ') }} FCFA</td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $payroll['days_worked'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $payroll['days_not_worked'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge @if($payroll['total_delay_minutes'] > 0) bg-warning @else bg-success @endif">
                                                {{ $payroll['total_delay_minutes'] }} min
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-danger">
                                                {{ number_format($payroll['total_penalties'], 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">
                                                {{ number_format($payroll['final_salary'], 0, ',', ' ') }} FCFA
                                            </strong>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#justifyModal"
                                                    data-employee-name="{{ $payroll['name'] }}"
                                                    data-employee-email="{{ $payroll['email'] }}"
                                                    data-days-not-worked="{{ $payroll['days_not_worked'] }}"
                                                    data-total-delay-minutes="{{ $payroll['total_delay_minutes'] }}"
                                                    data-month="{{ $month }}"
                                                    data-year="{{ $year }}">
                                                <i class="mdi mdi-file-document"></i> Justifier
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success"
                                                    onclick="applyDeduction('{{ $payroll['email'] }}', {{ $payroll['days_not_worked'] }}, {{ $payroll['monthly_salary'] }}, {{ $workingDays }})">
                                                <i class="mdi mdi-cash-minus"></i> Appliquer
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>TOTAUX:</strong></td>
                                    <td><strong>{{ number_format($totalSalaries, 0, ',', ' ') }} FCFA</strong></td>
                                    <td colspan="4"></td>
                                    <td class="text-end">
                                        <strong class="text-danger">{{ number_format($totalPenalties, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($totalFinal, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="text-center" colspan="9">Aucune donnée trouvée pour cette période.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Justification -->
    <div class="modal fade" id="justifyModal" tabindex="-1" aria-labelledby="justifyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="justifyModalLabel">Justifier les jours non travaillés</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="justifyForm" method="POST" action="{{route('admin.payroll.justify')}}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Employé:</strong></label>
                            <p id="employee_name_display" class="text-muted"></p>
                            <input type="hidden" name="employee_email" id="employee_email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Période:</strong></label>
                            <p id="period_display" class="text-muted"></p>
                            <input type="hidden" name="month" id="justify_month">
                            <input type="hidden" name="year" id="justify_year">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Jours non travaillés:</strong></label>
                            <p id="days_not_worked_display" class="text-danger fw-bold"></p>
                        </div>
                        <div class="mb-3">
                            <label for="justified_days" class="form-label">Nombre de jours à justifier <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="justified_days" name="justified_days" min="0" step="0.5" required>
                            <small class="text-muted">Entrez le nombre de jours à justifier (ex: 1, 2, 2.5)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Total retard:</strong></label>
                            <p id="total_delay_display" class="text-warning fw-bold"></p>
                            <input type="hidden" name="total_delay_minutes" id="total_delay_minutes">
                        </div>
                        <div class="mb-3">
                            <label for="justified_delay_minutes" class="form-label">Minutes de retard à justifier</label>
                            <input type="number" class="form-control" id="justified_delay_minutes" name="justified_delay_minutes" min="0" step="1" value="0">
                            <small class="text-muted">Entrez le nombre de minutes de retard à justifier (ex: 30, 60, 120)</small>
                        </div>
                        <div class="mb-3">
                            <label for="justification_reason" class="form-label">Motif de justification <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="justification_reason" name="justification_reason" rows="4" required placeholder="Ex: Maladie, Congé autorisé, Formation, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer la justification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal Justification - populate data when opened
        document.addEventListener('DOMContentLoaded', function() {
            const justifyModal = document.getElementById('justifyModal');

            justifyModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const employeeName = button.getAttribute('data-employee-name');
                const employeeEmail = button.getAttribute('data-employee-email');
                const daysNotWorked = button.getAttribute('data-days-not-worked');
                const totalDelayMinutes = parseInt(button.getAttribute('data-total-delay-minutes')) || 0;
                const month = button.getAttribute('data-month');
                const year = button.getAttribute('data-year');

                // Month names in French
                const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                const monthName = monthNames[parseInt(month) - 1];

                // Convert minutes to hours and minutes
                const hours = Math.floor(totalDelayMinutes / 60);
                const minutes = totalDelayMinutes % 60;
                let delayDisplay = '';
                if (hours > 0 && minutes > 0) {
                    delayDisplay = hours + 'h ' + minutes + 'min (' + totalDelayMinutes + ' minutes)';
                } else if (hours > 0) {
                    delayDisplay = hours + 'h (' + totalDelayMinutes + ' minutes)';
                } else {
                    delayDisplay = totalDelayMinutes + ' minutes';
                }

                // Populate modal fields
                document.getElementById('employee_name_display').textContent = employeeName + ' (' + employeeEmail + ')';
                document.getElementById('employee_email').value = employeeEmail;
                document.getElementById('period_display').textContent = monthName + ' ' + year;
                document.getElementById('justify_month').value = month;
                document.getElementById('justify_year').value = year;
                document.getElementById('days_not_worked_display').textContent = daysNotWorked + ' jour(s)';
                document.getElementById('total_delay_display').textContent = delayDisplay;
                document.getElementById('total_delay_minutes').value = totalDelayMinutes;

                // Set max value for justified days and delay minutes
                document.getElementById('justified_days').setAttribute('max', daysNotWorked);
                document.getElementById('justified_delay_minutes').setAttribute('max', totalDelayMinutes);
                document.getElementById('justified_delay_minutes').value = 0;
            });
        });

        // Apply Deduction function
        function applyDeduction(email, daysNotWorked, monthlySalary, workingDays) {
            if (daysNotWorked <= 0) {
                alert('Aucun jour non travaillé à déduire pour cet employé.');
                return;
            }

            // Calculate deduction
            const dailyRate = monthlySalary / workingDays;
            const deduction = dailyRate * daysNotWorked;
            const finalSalary = monthlySalary - deduction;

            const message = `Voulez-vous appliquer la déduction pour ${daysNotWorked} jour(s) non travaillé(s)?\n\n` +
                          `Salaire mensuel: ${monthlySalary.toLocaleString()} FCFA\n` +
                          `Taux journalier: ${Math.round(dailyRate).toLocaleString()} FCFA\n` +
                          `Déduction: ${Math.round(deduction).toLocaleString()} FCFA\n` +
                          `Salaire final: ${Math.round(finalSalary).toLocaleString()} FCFA`;

            if (confirm(message)) {
                // Submit deduction to server
                fetch('{{route('admin.payroll.apply-deduction')}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        employee_email: email,
                        days_not_worked: daysNotWorked,
                        deduction_amount: Math.round(deduction),
                        month: '{{$month}}',
                        year: '{{$year}}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Déduction appliquée avec succès!');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de l\'application de la déduction.');
                });
            }
        }
    </script>
@stop
