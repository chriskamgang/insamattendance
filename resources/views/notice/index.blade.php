@extends('master')

@section('title') Notice @stop
@section('page_title')  List @stop

@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.notice.create'), 'button_text' => "Create Notice"])
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
                            <caption class="pb-0">Notice List</caption>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($_notices->total() > 0)
                                @foreach($_notices as $notice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $notice->title }}</td>
                                        <td>{{ $notice->start_date }}</td>
                                        <td>{{ $notice->end_date }}</td>
                                        <td class="text-center">
                                            <span class="form-check form-switch">
                                                <input type="checkbox" href="{{route('admin.notice.changeStatus',$notice->id)}}" class="form-check-input change-status-toggle" @if( (bool)$notice->is_active) checked @endif>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <ul class="d-flex justify-content-center  list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{route('admin.notice.edit',['notice'=>$notice->id])}}">
                                                        <em class="link-icon" data-feather="edit"></em>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="delete-modal" data-bs-toggle="modal"
                                                       data-bs-target="#deleteModel"
                                                       link='{{route('admin.notice.destroy',['notice'=>$notice->id])}}'>
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
    {{ $_notices->links('include.pagination') }}
    @include('include.delete-model')
@stop
