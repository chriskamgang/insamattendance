@extends('master')

@section('title')
    User
@stop
@section('page_title')
    Edit
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.admin.listAdmin'), 'button_text' => "Back User list"])
@stop
@section('js')
    <script src="{{ asset('admin/assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('admin/assets/validation/user.js') }}"></script>
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
        @if($_user->user_type == 'admin')
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    Edits cannot be done in demo version. Please purchase the full code to unlock these features
                </div>
            </div>
        @endif
    @endif
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">User Setup</h4>
                    @if(checkUserPermission())
                        <form id="user_submit" class="forms-sample" action="{{ route('admin.admin.updateAdmin', [$_user->id]) }}" method="post">
                            <input type="hidden" name="_method" value="put" />
                            @csrf
                            @include('user.action',['btn'=>"Update Employee"])
                        </form>
                    @else
                        <form id="user_submit" class="forms-sample">
                            @csrf
                            @include('user.action',['btn'=>"Update Employee"])
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
