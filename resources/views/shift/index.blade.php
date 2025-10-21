@extends('master')

@section('title')
    Shift
@stop
@section('page_title')
    List
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.shift.create'), 'button_text' => "Create Shift"])
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
                            <caption class="pb-0">Shift List</caption>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Shift Type</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($_shifts->total() > 0)
                                @foreach($_shifts as $shift)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $shift->title }}</td>
                                        <td>{{ $shift->start }}</td>
                                        <td>{{ $shift->end }}</td>
                                        <td>{{ ucfirst($shift->type) }}</td>
                                        <td class="text-center">
                                            <span class="form-check form-switch">
                                                <input type="checkbox"
                                                       href="{{route('admin.shift.changeStatus',$shift->id)}}"
                                                       class="form-check-input change-status-toggle"
                                                       @if( (bool)$shift->is_active) checked @endif>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <ul class="d-flex justify-content-center  list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{route('admin.shift.edit',['shift'=>$shift->id])}}">
                                                        <em class="link-icon" data-feather="edit"></em>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="delete-modal" data-bs-toggle="modal"
                                                       data-bs-target="#deleteModel"
                                                       link='{{route('admin.shift.destroy',['shift'=>$shift->id])}}'>
                                                        <em class="link-icon" data-feather="trash"></em>
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
    {{ $_shifts->links('include.pagination') }}
    @include('include.delete-model')
@stop
