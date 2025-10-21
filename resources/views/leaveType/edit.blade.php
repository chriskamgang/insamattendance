@extends('master')

@section('title')
    Leave Type
@stop
@section('page_title')
    Edit
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.leaveType.index'), 'button_text' => "Back to list"])
@stop
@section('js')
    <script src="{{ asset('admin/assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('admin/assets/validation/leaveType.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Leave Type Setup</h4>
                    <form id="leaveType_submit" class="forms-sample" action="{{ route('admin.leaveType.update', [$_leaveType->id]) }}" method="post">
                        <input type="hidden" name="_method" value="put" />
                        @csrf
                        @include('leaveType.action',['btn'=>"Update Leave Type"])
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
