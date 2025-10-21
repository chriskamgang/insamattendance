@extends('master')

@section('title')
     Update App Password
@stop
@section('page_title')
    Update
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.admin.listAdmin'), 'button_text' => "Back User list"])
@stop

@section('content')
    @if( checkUserRole())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <marquee direction="left">
                    Edits cannot be done in demo version. Please purchase the full code to unlock these features
                </marquee>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4"> Update App Password Setup</h4>
                    @if(checkUserRole())
                        <form id="user_submit" class="forms-sample">
                            @csrf
                            <div class="col-lg-6 mb-3">
                                <label for="app_password" class="form-label"> Password</label>
                                <input type="text" id="app_password" class="form-control" name="app_password" placeholder="Enter Password" value="{{ (($_user ?? '')? $_user->app_password :  old('app_password')) }}">
                                <span class="text-danger">{{ $errors->first('app_password') }}</span>
                            </div>
                        </form>
                    @else
                        <form id="user_submit" class="forms-sample" action="{{route('admin.admin.saveAppPassword', [$_user->id])}}" method="post">
                            @csrf
                            <div class="col-lg-6 mb-3">
                                <label for="app_password" class="form-label"> Password</label>
                                <input type="text" id="app_password" class="form-control" name="app_password" placeholder="Enter Password" value="{{ (($_user ?? '')? $_user->app_password :  old('app_password')) }}">
                                <span class="text-danger">{{ $errors->first('app_password') }}</span>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
