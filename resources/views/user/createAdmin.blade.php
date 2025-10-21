@extends('master')

@section('title')
    User
@stop
@section('page_title')
    Create
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
    @endif
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">User Setup</h4>
                    <form id="user_submit" class="forms-sample" action="{{ route('admin.admin.saveAdmin') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="name" class="form-label"> name</label>
                                <input type="text" id="name" class="form-control" name="name"
                                       value="{{ (($_user ?? '')? $_user->name :  old('name')) }}" placeholder="Enter Title" required>
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="dob" class="form-label"> Date Of Birth</label>
                                <input type="date" id="dob" class="form-control" name="dob"
                                       value="{{ (($_user ?? '')? $_user->dob :  old('dob')) }}" required>
                                <span class="text-danger">{{ $errors->first('dob') }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="email" class="form-label"> Email</label>
                                <input type="email" id="email" class="form-control" name="email"
                                       value="{{ (($_user ?? '')? $_user->email :  old('email')) }}" placeholder="Enter Email" required>
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="mobile" class="form-label"> Mobile</label>
                                <input type="number" id="mobile" class="form-control" name="mobile"
                                       value="{{ (($_user ?? '')? $_user->mobile :  old('mobile')) }}" placeholder="Enter mobile" required>
                                <span class="text-danger">{{ $errors->first('mobile') }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="address" class="form-label"> Address</label>
                                <input type="text" id="address" class="form-control" name="address"
                                       value="{{ (($_user ?? '')? $_user->address :  old('address')) }}" placeholder="Enter Address" required>
                                <span class="text-danger">{{ $errors->first('address') }}</span>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> Create User</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
