@extends('master')

@section('title')
    Attendance
@stop
@section('page_title')
    Edit
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.attendance.attendanceList'), 'button_text' => "Back to list"])
@stop
@section('js')
    <script src="{{ asset('admin/assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('admin/assets/validation/attendance.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Edit Attendance({{$_user->name}}) Time of {{$_date}}</h4>
                    <form id="attendance_submit" class="forms-sample" action="{{ route('admin.attendance.updateAttendanceDetail', [$_attendance->id]) }}" method="post">
                        <input type="hidden" name="_method" value="put" />
                        @csrf
                        <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label for="check_in" class="form-label"> Check In</label>
                                    <input type="time" id="check_in" class="form-control" name="check_in" value="{{ (($_attendance->check_in)? date('H:i',strtotime($_attendance->check_in)) :  old('check_in')) }}"  required>
                                    <span class="text-danger">{{ $errors->first('check_in') }}</span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="check_out" class="form-label"> Check Out</label>
                                    <input type="time" id="check_out" class="form-control" name="check_out" value="{{ (($_attendance->check_out)? date('H:i',strtotime($_attendance->check_out)) :  "") }}" @if($_attendance->check_out) required @endif>
                                    <span class="text-danger">{{ $errors->first('check_out') }}</span>
                                </div>
                                @if($enableLunchInOut)
                                    <div class="col-lg-6 mb-3">
                                        <label for="lunch_in" class="form-label"> Lunch Check In</label>
                                        <input type="time" id="lunch_in" class="form-control" name="lunch_in" value="{{ (($_attendance->lunch_in)? date('H:i',strtotime($_attendance->lunch_in)) :  "") }}" @if($_attendance->lunch_in) required @endif>
                                        <span class="text-danger">{{ $errors->first('lunch_in') }}</span>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="lunch_out" class="form-label"> Lunch Check Out</label>
                                        <input type="time" id="lunch_out" class="form-control" name="lunch_out" value="{{ (($_attendance->lunch_out)? date('H:i',strtotime($_attendance->lunch_out)) :  "") }}" @if($_attendance->lunch_out)) required @endif>
                                        <span class="text-danger">{{ $errors->first('lunch_out') }}</span>
                                    </div>
                                @endif
                                <div class="col-lg-12 mb-3">
                                    <label for="attendance_note" class="form-label"> Attendance Note</label>
                                    <input type="text" id="attendance_note" class="form-control" name="attendance_note" value="{{ (($_attendance ?? '')? $_attendance->attendance_note :  old('attendance_note')) }}" placeholder="Enter Attendance Note" >
                                    <span class="text-danger">{{ $errors->first('attendance_note') }}</span>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> Update</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
