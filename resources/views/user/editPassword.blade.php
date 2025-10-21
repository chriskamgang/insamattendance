@extends('master')

@section('title')
    Update Password
@stop
@section('page_title')
    Create
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
                    <h4 class="mb-4">Update Password Setup</h4>
                    @if(checkUserRole())
                        <form id="user_submit" class="forms-sample">
                            <div class="col-lg-6 mb-3">
                                <label for="password" class="form-label"> Password</label>
                                <input type="text" id="password" class="form-control" name="password" placeholder="Enter Password">
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            </div>
                        </form>
                    @else
                        <form id="user_submit" class="forms-sample" action="{{route('admin.admin.savePassword', [$_user->id])}}" method="post">
                        @csrf
                            <div class="col-lg-6 mb-3">
                                <label for="password" class="form-label"> Password</label>
                                <input type="text" id="password" class="form-control" name="password" placeholder="Enter Password">
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="link-icon" data-feather="plus"></i>
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
