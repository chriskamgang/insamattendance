@extends('master')

@section('title')
    Department
@stop
@section('page_title')
    List
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.department.create'), 'button_text' => "Create Department"])
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
                            <caption class="pb-0">Department List</caption>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($_departments->total() > 0)
                                @foreach($_departments as $department)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $department->title }}</td>
                                        <td class="text-center">
                                            <span class="form-check form-switch">
                                                <input type="checkbox"
                                                       href="{{route('admin.department.changeStatus',$department->id)}}"
                                                       class="form-check-input change-status-toggle"
                                                       @if( (bool)$department->is_active) checked @endif>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <ul class="d-flex justify-content-center  list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{route('admin.department.edit',['department'=>$department->id])}}">
                                                        <em class="link-icon" data-feather="edit"></em>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="delete-modal" data-bs-toggle="modal"
                                                       data-bs-target="#deleteModel"
                                                       link='{{route('admin.department.destroy',['department'=>$department->id])}}'>
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
    {{ $_departments->links('include.pagination') }}
    @include('include.delete-model')
@stop
