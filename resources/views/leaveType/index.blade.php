@extends('master')

@section('title')
    Leave Type
@stop
@section('page_title')
    List
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.leaveType.create'), 'button_text' => "Create Leave Type"])
@stop
@section('js')
    <script src="{{ asset('assets/changeStatus.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <caption class="pb-0">Leave Type List</caption>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($_leaveTypes->total() > 0)
                                @foreach($_leaveTypes as $leaveType)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $leaveType->title }}</td>
                                        <td class="text-center">
                                            <span class="form-check form-switch">
                                                <input type="checkbox"
                                                       href="{{route('admin.leaveType.changeStatus',$leaveType->id)}}"
                                                       class="form-check-input change-status-toggle"
                                                       @if( (bool)$leaveType->is_active) checked @endif>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <ul class="d-flex justify-content-center  list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{route('admin.leaveType.edit',['leaveType'=>$leaveType->id])}}">
                                                        <em class="link-icon" data-feather="edit"></em>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="delete-modal" data-bs-toggle="modal"
                                                       data-bs-target="#deleteModel"
                                                       link='{{route('admin.leaveType.destroy',['leaveType'=>$leaveType->id])}}'>
                                                        <em class="link-icon" data-feather="trash"></em>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-left" colspan="5">No data found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $_leaveTypes->links('include.pagination') }}
    @include('include.delete-model')
@stop
