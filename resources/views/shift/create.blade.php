@extends('master')

@section('title')
    Shift
@stop
@section('page_title')
    Create
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.shift.index'), 'button_text' => "Back to list"])
@stop

@section('js')
    <script src="{{ asset('assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/validation/shift.js') }}"></script>
    <script src="{{ asset('assets/numerical_checker.js') }}"></script>
    <script>
        $(document).ready(function () {
            $("#is_early_check_in").change(function () {
                var $input = $('#is_early_check_in');
                if ($input.prop('checked')) $("#before_start_div").hide();
                else $("#before_start_div").show();
            });
            $("#is_early_check_out").change(function () {
                var $input = $('#is_early_check_out');
                if ($input.prop('checked')) $("#before_end_div").hide();
                else $("#before_end_div").show();
            });
        });
    </script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Shift Setup</h4>
                    <form id="shift_submit" class="forms-sample" action="{{route('admin.shift.store')}}" method="post">
                        @csrf
                            @include('shift.action',['btn'=>"Save Shift"])
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
