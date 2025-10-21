@extends('master')

@section('title')
    Employee
@stop
@section('page_title')
    List
@stop

@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.user.create'), 'button_text' => "Create Employee"])
@stop
@section('js')
    <script src="{{ asset('admin/assets/changeStatus.js') }}"></script>
@stop
@section('content')
    @if( checkUserRole())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <marquee direction="left">
                    This is a demo version. Only 10 employees can be created in demo mode. Please delete employees from
                    the list and try creating again. Thank You
                </marquee>
            </div>
        </div>
    @endif
    @php
        $search = $_GET['search'] ?? null;
        $department_id = $_GET['department_id'] ?? null;
        $shift_id = $_GET['shift_id'] ?? null;
    @endphp
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="forms-sample mb-4" action="{{route('admin.user.index' )}}" method="get">
                        <div class="row align-items-center mt-3">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <input type="text" placeholder="Search" class="form-control" name="search" value="{{$search}}"/>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <select id="shift_id" name="shift_id" class="form-select" required>
                                    <option value="">Select Shift</option>
                                    @foreach($_shifts as $key => $shift)
                                        <option value="{{$key}}" @if($shift_id == $key) selected @endif>{{ucfirst($shift)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-2">
                                <select id="department_id" name="department_id" class="form-select">
                                    <option value="">Select Department</option>
                                    @foreach($_department as $key => $department)
                                        <option value="{{$key}}" @if($department_id == $key) selected @endif>{{ucfirst($department)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-success form-control">Search</button>
                            </div>
                            <div class="col-lg-1 col-md-3 col-sm-6 mb-2">
                                <a class="btn btn-block btn-primary"
                                   href="{{route('admin.user.index')}}">Reset</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <caption class="pb-0">Employee List</caption>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Department</th>
                                <th>Shift</th>
                                <th class="text-center">Face Ids Count</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($_users->total() > 0)
                                @foreach($_users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->mobile }}</td>
                                        <td>{{ ucfirst($user->getDepartment->title) }}</td>
                                        <td>{{ ucfirst($user->getShift->title) }}</td>
                                        <td class="text-center">{{ $user->countFaceIds() }}</td>
                                        <td class="text-center">
                                            <ul class="d-flex justify-content-center  list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{route('admin.user.edit',['user'=>$user->id])}}"
                                                       title="Edit Employee">
                                                        <em class="link-icon" data-feather="edit"></em>
                                                    </a>
                                                </li>
                                                @if($user->user_type == "employee")
                                                    <li class="me-2">
                                                        <a class="delete-modal " data-bs-toggle="modal"
                                                           data-bs-target="#deleteModel" title="Delete Employee"
                                                           link='{{route('admin.user.destroy',['user'=>$user->id])}}'>
                                                            <em class="link-icon" data-feather="trash"></em>
                                                        </a>
                                                    </li>
                                                    <li class="me-2">
                                                        <a class="delete-modal "
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#deleteModel" title="Delete Employee FaceIds"
                                                           link='{{route('admin.user.deleteFaceIds',['id'=>$user->id])}}'>
                                                            <em class="link-icon" data-feather="user-x"></em> Delete
                                                            FaceIds
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-left" colspan="4">No data found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $_users->links('include.pagination') }}
    @include('include.delete-model')
@stop
