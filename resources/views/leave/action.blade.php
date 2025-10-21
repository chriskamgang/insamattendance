<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="leave_type_id" class="form-label"> Select Leave Type</label>
        <select id="leave_type_id" name="leave_type_id" class="form-select" required>
            <option>Select Leave Type</option>
            @php $selectedLeaveType = (($_leave ?? '')? $_leave->leave_type_id : '')  @endphp
            @foreach($_leaveTypes as $key => $leaveType)
                <option value="{{$key}}"
                        @if($selectedLeaveType === $key) selected @endif>{{ucfirst($leaveType)}}</option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('leave_type_id') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="user_id" class="form-label"> Select Employee</label>
        <select id="user_id" name="user_id" class="form-select" required>
            <option>Select Employee</option>
            @php $_selectedUser = (($_leave ?? '')? $_leave->user_id : '') @endphp
            @foreach($_users as $key => $user)
                <option value="{{$key}}" @if($_selectedUser === $key) selected @endif>{{ucfirst($user)}}</option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('user_id') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="from_date" class="form-label"> Start Date</label>
        <input type="date" id="from_date" class="form-control" name="from_date"
               value="{{ (($_leave ?? '')? $_leave->from_date :  old('from_date')) }}" required>
        <span class="text-danger">{{ $errors->first('from_date') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="to_date" class="form-label"> End Date</label>
        <input type="date" id="to_date" class="form-control" name="to_date"
               value="{{ (($_leave ?? '')? $_leave->to_date :  old('to_date')) }}" required>
        <span class="text-danger">{{ $errors->first('to_date') }}</span>
    </div>
    <div class="col-lg-12 mb-3">
        <label for="reason" class="form-label"> Reason</label>
        <textarea id="reason" class="form-control" name="reason" rows="5"
                  required>{!! (($_leave ?? '' )? $_leave->leave_note :  old('reason')) !!}</textarea>
        <span class="text-danger">{{ $errors->first('reason') }}</span>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{$btn}}</button>
    </div>
</div>
