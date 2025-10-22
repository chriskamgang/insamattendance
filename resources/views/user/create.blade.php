@extends('master')

@section('title')
    Employee
@stop
@section('page_title')
    Create
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.user.index'), 'button_text' => "Back User list"])
@stop
@section('js')
    <script src="{{ asset('assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/validation/user.js') }}"></script>
@stop
@section('content')
    @if( checkUserRole())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <marquee direction="left">
                    This is a demo version. Only 10 employees can be created in demo mode. Please delete employees from the list and try creating again. Thank You
                </marquee>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Employee Setup</h4>
                    <form id="user_submit" class="forms-sample" action="{{ route('admin.user.store') }}" method="post">
                        @csrf
                        @include('user.action',['btn'=>"Create Employee"])
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
