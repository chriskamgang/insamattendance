@extends('master')

@section('title')
    Department
@stop
@section('page_title')
    Edit
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.department.index'), 'button_text' => "Back to list"])
@stop
@section('js')
    <script src="{{ asset('assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/validation/department.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Department Setup</h4>
                    <form id="department_submit" class="forms-sample" action="{{ route('admin.department.update', [$_department->id]) }}" method="post">
                        <input type="hidden" name="_method" value="put" />
                        @csrf
                        @include('department.action',['btn'=>"Update Department"])
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
