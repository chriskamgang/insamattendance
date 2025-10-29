<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="name" class="form-label"> name</label>
        <input type="text" id="name" class="form-control" name="name"
               value="{{ (($_user ?? '')? $_user->name :  old('name')) }}" placeholder="Enter Title" required>
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="dob" class="form-label"> Date Of Birth</label>
        <input type="date" id="dob" class="form-control" name="dob"
               value="{{ (($_user ?? '')? $_user->dob :  old('dob')) }}" required>
        <span class="text-danger">{{ $errors->first('dob') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="email" class="form-label"> Email</label>
        <input type="email" id="email" class="form-control" name="email"
               value="{{ (($_user ?? '')? $_user->email :  old('email')) }}" placeholder="Enter Email" required>
        <span class="text-danger">{{ $errors->first('email') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="mobile" class="form-label"> Mobile</label>
        <input type="number" id="mobile" class="form-control" name="mobile"
               value="{{ (($_user ?? '')? $_user->mobile :  old('mobile')) }}" placeholder="Enter mobile" required>
        <span class="text-danger">{{ $errors->first('mobile') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="address" class="form-label"> Address</label>
        <input type="text" id="address" class="form-control" name="address"
               value="{{ (($_user ?? '')? $_user->address :  old('address')) }}" placeholder="Enter Address" required>
        <span class="text-danger">{{ $errors->first('address') }}</span>
    </div>
    {{-- Type d'employé --}}
    <div class="col-lg-6 mb-3">
        <label for="employee_type" class="form-label"> Type d'Employé</label>
        <select id="employee_type" name="employee_type" class="form-select" required>
            @php $employee_type = (($_user ?? '')? $_user->employee_type : old('employee_type', 'permanent')) @endphp
            <option value="permanent" @if($employee_type == 'permanent') selected @endif>Permanent</option>
            <option value="semi_permanent" @if($employee_type == 'semi_permanent') selected @endif>Semi-Permanent</option>
            <option value="vacataire" @if($employee_type == 'vacataire') selected @endif>Vacataire</option>
        </select>
        <span class="text-danger">{{ $errors->first('employee_type') }}</span>
    </div>

    @if($_user ?? "" )
        @if($_user->user_type == 'employee')
            <div class="col-lg-6 mb-3">
                <label for="shift_id" class="form-label"> Shift</label>
                <select id="shift_id" name="shift_id" class="form-select" required>
                    <option >Select Shift</option>
                    @php $shift_id = (($_user ?? '')? $_user->shift_id : '') @endphp
                    @foreach($_shifts as $key => $shift)
                        <option value="{{$key}}" @if($shift_id == $key) selected @endif>{{ucfirst($shift)}}</option>
                    @endforeach
                </select>
                <span class="text-danger">{{ $errors->first('shift_id') }}</span>
            </div>
            <div class="col-lg-6 mb-3">
                <label for="department_id" class="form-label"> Department</label>
                <select id="department_id" name="department_id" class="form-select" required>
                    <option >Select Department</option>
                    @php $department_id = (($_user ?? '')? $_user->department_id : '') @endphp
                    @foreach($_department as $key => $department)
                        <option value="{{$key}}" @if($department_id == $key) selected @endif>{{ucfirst($department)}}</option>
                    @endforeach
                </select>
                <span class="text-danger">{{ $errors->first('department_id') }}</span>
            </div>

            {{-- Champs pour Permanent/Semi-Permanent --}}
            <div class="col-lg-6 mb-3 permanent-fields">
                <label for="monthly_salary" class="form-label"> Salaire Mensuel (FCFA)</label>
                <input type="number" step="0.01" id="monthly_salary" class="form-control" name="monthly_salary"
                       value="{{ (($_user ?? '')? $_user->monthly_salary :  old('monthly_salary')) }}" placeholder="Entrer le salaire mensuel" min="0">
                <span class="text-danger">{{ $errors->first('monthly_salary') }}</span>
            </div>

            {{-- Champs pour Vacataires --}}
            <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
                <label for="hourly_rate" class="form-label"> Taux Horaire (FCFA/heure) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" id="hourly_rate" class="form-control" name="hourly_rate"
                       value="{{ (($_user ?? '')? $_user->hourly_rate : old('hourly_rate')) }}" placeholder="Ex: 5000" min="0">
                <span class="text-danger">{{ $errors->first('hourly_rate') }}</span>
            </div>
            <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
                <label for="contract_start_date" class="form-label"> Date Début Contrat <span class="text-danger">*</span></label>
                <input type="date" id="contract_start_date" class="form-control" name="contract_start_date"
                       value="{{ (($_user ?? '')? $_user->contract_start_date : old('contract_start_date')) }}">
                <span class="text-danger">{{ $errors->first('contract_start_date') }}</span>
            </div>
            <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
                <label for="contract_end_date" class="form-label"> Date Fin Contrat</label>
                <input type="date" id="contract_end_date" class="form-control" name="contract_end_date"
                       value="{{ (($_user ?? '')? $_user->contract_end_date : old('contract_end_date')) }}">
                <span class="text-danger">{{ $errors->first('contract_end_date') }}</span>
                <small class="text-muted">Laissez vide pour contrat à durée indéterminée</small>
            </div>
            <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
                <label for="specialization" class="form-label"> Spécialité/Matière</label>
                <input type="text" id="specialization" class="form-control" name="specialization"
                       value="{{ (($_user ?? '')? $_user->specialization : old('specialization')) }}" placeholder="Ex: Mathématiques, Informatique...">
                <span class="text-danger">{{ $errors->first('specialization') }}</span>
            </div>
            <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
                <label for="max_hours_per_month" class="form-label"> Quota Max Heures/Mois</label>
                <input type="number" id="max_hours_per_month" class="form-control" name="max_hours_per_month"
                       value="{{ (($_user ?? '')? $_user->max_hours_per_month : old('max_hours_per_month')) }}" placeholder="Ex: 80" min="0">
                <span class="text-danger">{{ $errors->first('max_hours_per_month') }}</span>
                <small class="text-muted">Laissez vide pour aucune limite</small>
            </div>
        @endif
    @else
        <div class="col-lg-6 mb-3">
            <label for="shift_id" class="form-label"> Shift</label>
            <select id="shift_id" name="shift_id" class="form-select" required>
                <option value="">Select Shift</option>
                @foreach($_shifts as $key => $shift)
                    <option value="{{$key}}" >{{ucfirst($shift)}}</option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('shift_id') }}</span>
        </div>
        <div class="col-lg-6 mb-3">
            <label for="department_id" class="form-label"> Department</label>
            <select id="department_id" name="department_id" class="form-select" required>
                <option >Select Department</option>
                @foreach($_department as $key => $department)
                    <option value="{{$key}}">{{ucfirst($department)}}</option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('department_id') }}</span>
        </div>

        {{-- Champs pour Permanent/Semi-Permanent --}}
        <div class="col-lg-6 mb-3 permanent-fields">
            <label for="monthly_salary" class="form-label"> Salaire Mensuel (FCFA)</label>
            <input type="number" step="0.01" id="monthly_salary" class="form-control" name="monthly_salary"
                   value="{{ old('monthly_salary') }}" placeholder="Entrer le salaire mensuel" min="0">
            <span class="text-danger">{{ $errors->first('monthly_salary') }}</span>
        </div>

        {{-- Champs pour Vacataires --}}
        <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
            <label for="hourly_rate" class="form-label"> Taux Horaire (FCFA/heure) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" id="hourly_rate" class="form-control" name="hourly_rate"
                   value="{{ old('hourly_rate') }}" placeholder="Ex: 5000" min="0">
            <span class="text-danger">{{ $errors->first('hourly_rate') }}</span>
        </div>
        <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
            <label for="contract_start_date" class="form-label"> Date Début Contrat <span class="text-danger">*</span></label>
            <input type="date" id="contract_start_date" class="form-control" name="contract_start_date"
                   value="{{ old('contract_start_date') }}">
            <span class="text-danger">{{ $errors->first('contract_start_date') }}</span>
        </div>
        <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
            <label for="contract_end_date" class="form-label"> Date Fin Contrat</label>
            <input type="date" id="contract_end_date" class="form-control" name="contract_end_date"
                   value="{{ old('contract_end_date') }}">
            <span class="text-danger">{{ $errors->first('contract_end_date') }}</span>
            <small class="text-muted">Laissez vide pour contrat à durée indéterminée</small>
        </div>
        <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
            <label for="specialization" class="form-label"> Spécialité/Matière</label>
            <input type="text" id="specialization" class="form-control" name="specialization"
                   value="{{ old('specialization') }}" placeholder="Ex: Mathématiques, Informatique...">
            <span class="text-danger">{{ $errors->first('specialization') }}</span>
        </div>
        <div class="col-lg-6 mb-3 vacataire-fields" style="display: none;">
            <label for="max_hours_per_month" class="form-label"> Quota Max Heures/Mois</label>
            <input type="number" id="max_hours_per_month" class="form-control" name="max_hours_per_month"
                   value="{{ old('max_hours_per_month') }}" placeholder="Ex: 80" min="0">
            <span class="text-danger">{{ $errors->first('max_hours_per_month') }}</span>
            <small class="text-muted">Laissez vide pour aucune limite</small>
        </div>
    @endif
    @if( checkUserRole())
        @if(($_user ?? "") &&  $_user->user_type == 'employee')
                <div class="text-center">
                    <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{$btn}}</button>
                </div>
        @elseif((($_user ?? "") &&  $_user->user_type == 'admin'))

        @else
            <div class="text-center">
                <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{$btn}}</button>
            </div>
        @endif
    @else
        <div class="text-center">
            <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{$btn}}</button>
        </div>
    @endif
</div>

<script>
    // Toggle des champs selon le type d'employé
    document.addEventListener('DOMContentLoaded', function() {
        const employeeTypeSelect = document.getElementById('employee_type');
        const permanentFields = document.querySelectorAll('.permanent-fields');
        const vacataireFields = document.querySelectorAll('.vacataire-fields');

        function toggleFields() {
            const selectedType = employeeTypeSelect.value;

            if (selectedType === 'vacataire') {
                // Afficher champs vacataires, masquer champs permanents
                permanentFields.forEach(field => field.style.display = 'none');
                vacataireFields.forEach(field => field.style.display = 'block');

                // Rendre les champs vacataires requis
                document.getElementById('hourly_rate').setAttribute('required', 'required');
                document.getElementById('contract_start_date').setAttribute('required', 'required');

                // Retirer required des champs permanents
                document.getElementById('monthly_salary').removeAttribute('required');
            } else {
                // Afficher champs permanents, masquer champs vacataires
                permanentFields.forEach(field => field.style.display = 'block');
                vacataireFields.forEach(field => field.style.display = 'none');

                // Retirer required des champs vacataires
                document.getElementById('hourly_rate').removeAttribute('required');
                document.getElementById('contract_start_date').removeAttribute('required');

                // Rendre monthly_salary non requis (optionnel)
                // document.getElementById('monthly_salary').setAttribute('required', 'required');
            }
        }

        // Exécuter au chargement
        toggleFields();

        // Exécuter au changement
        employeeTypeSelect.addEventListener('change', toggleFields);
    });
</script>
