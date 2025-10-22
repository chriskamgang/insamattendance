@extends('master')

@section('title')
    User
@stop
@section('page_title')
    List
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.admin.createAdmin'), 'button_text' => "Create User"])
@stop
@section('js')
    <script src="{{ asset('assets/changeStatus.js') }}"></script>
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
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <caption class="pb-0">User List</caption>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
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
                                        <td class="text-center">
                                            <ul class="d-flex justify-content-center  list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{route('admin.admin.editAdmin',['user'=>$user->id])}}"
                                                       title="Edit User" >
                                                        <em class="link-icon" data-feather="edit"></em>
                                                    </a>
                                                </li>
                                                <li class="me-2">
                                                    <a class="delete-modal " data-bs-toggle="modal"
                                                       data-bs-target="#deleteModel" title="Delete User"
                                                       link='{{route('admin.user.destroy',['user'=>$user->id])}}'>
                                                        <em class="link-icon" data-feather="trash"></em>
                                                    </a>
                                                </li>
                                                <li class="me-2">
                                                    <a href="{{route('admin.admin.changePassword',['id'=>$user->id])}}" title="Change password" >
                                                        <em class="link-icon" data-feather="key"></em> Change password
                                                    </a>
                                                </li>
                                                <li class="me-2">
                                                    <a href="{{route('admin.admin.changeAppPassword',['id'=>$user->id])}}" title="App password" >
                                                        <em class="link-icon" data-feather="smartphone"></em> App password
                                                    </a>
                                                </li>
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
