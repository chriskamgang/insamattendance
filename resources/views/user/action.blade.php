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
            <div class="col-lg-6 mb-3">
                <label for="monthly_salary" class="form-label"> Salaire Mensuel (FCFA)</label>
                <input type="number" step="0.01" id="monthly_salary" class="form-control" name="monthly_salary"
                       value="{{ (($_user ?? '')? $_user->monthly_salary :  old('monthly_salary')) }}" placeholder="Entrer le salaire mensuel" min="0">
                <span class="text-danger">{{ $errors->first('monthly_salary') }}</span>
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
        <div class="col-lg-6 mb-3">
            <label for="monthly_salary" class="form-label"> Salaire Mensuel (FCFA)</label>
            <input type="number" step="0.01" id="monthly_salary" class="form-control" name="monthly_salary"
                   value="{{ (($_user ?? '')? $_user->monthly_salary :  old('monthly_salary')) }}" placeholder="Entrer le salaire mensuel" min="0">
            <span class="text-danger">{{ $errors->first('monthly_salary') }}</span>
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
