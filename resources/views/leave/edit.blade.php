@extends('master')

@section('title')
    Leave Type
@stop
@section('page_title')
    Edit
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.leaveType.index'), 'button_text' => "Back Leave Type list"])
@stop
@section('js')
    <script src="{{ asset('assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/validation/leaveType.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Leave Type Setup</h4>
                    <form id="leaveType_submit" class="forms-sample" action="{{ route('admin.leave.update', ["leave_group_code"=>$_leave->leave_group_code]) }}" method="post">
                        <input type="hidden" name="_method" value="put" />
                        @csrf
                        @include('leave.action',['btn'=>"Update Leave"])
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
