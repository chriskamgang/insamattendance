@extends('master')

@section('title')
    Holiday
@stop
@section('page_title')
    Edit
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.holiday.index'), 'button_text' => "Back Holiday list"])
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
                    <form id="holiday_submit" class="forms-sample" action="{{ route('admin.holiday.update', [$_holiday->id]) }}" method="post">
                        <input type="hidden" name="_method" value="put" />
                        @csrf
                        @include('holiday.action',['btn'=>"Update Holiday"])
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
