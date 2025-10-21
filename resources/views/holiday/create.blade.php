@extends('master')

@section('title')
    Holiday
@stop
@section('page_title')
    Create
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.holiday.index'), 'button_text' => "Back to list"])
@stop

@section('js')
    <script src="{{ asset('admin/assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('admin/assets/validation/holiday.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Holiday Setup</h4>
                    <form id="holiday_submit" class="forms-sample" action="{{route('admin.holiday.store')}}" method="post">
                        @csrf
                        @include('holiday.action',['btn'=>"Save Holiday"])

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
