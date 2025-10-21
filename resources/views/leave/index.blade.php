@extends('master')

@section('title')
    Leave
@stop
@section('page_title')
    List
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.leave.create'), 'button_text' => "Create Leave"])
@stop
@section('js')
    <script src="{{ asset('admin/assets/changeStatus.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <caption class="pb-0">Leave List</caption>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Leave Type</th>
                                <th>from</th>
                                <th>to</th>
                                <th>Employee</th>
                                <th class="text-center">No of Days</th>
                                <th>Applied By</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($_leaves as $leave)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $leave->leave_type_title }}</td>
                                    <td>{{ $leave->from_date }}</td>
                                    <td>{{ $leave->to_date }}</td>
                                    <td>{{ $leave->user_name }}</td>
                                    <td class="text-center">{{ $leave->no_of_days }}</td>
                                    <td>{{ $leave->applied_by }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-xs">Approved</button>
                                    </td>
                                    <td class="text-center">
                                        <ul class="d-flex justify-content-center  list-unstyled mb-0">
                                            <li class="me-2">
                                                <a href="{{route('admin.leave.edit',['leave_group_code'=>$leave->leave_group_code])}}">
                                                    <em class="link-icon" data-feather="edit"></em>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="delete-modal" data-bs-toggle="modal"
                                                   data-bs-target="#deleteModel"
                                                   link='{{route('admin.leave.delete',['leave_group_code'=>$leave->leave_group_code])}}'>
                                                    <em class="link-icon" data-feather="trash"></em>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('include.delete-model')
@stop
