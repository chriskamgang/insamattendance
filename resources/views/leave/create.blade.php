@extends('master')

@section('title')
    Leave
@stop
@section('page_title')
    Create
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.leave.index'), 'button_text' => "Back to list"])
@stop

@section('js')
    <script src="{{ asset('assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/validation/leave.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Create Employee Leave</h4>
                    <form id="leave_submit" class="forms-sample" action="{{route('admin.leave.store')}}" method="post">
                        @csrf
                        @include('leave.action',['btn'=>"Save Leave"])
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
