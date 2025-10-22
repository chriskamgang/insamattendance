@extends('master')

@section('title')
    Attendance
@stop
@section('page_title')
    Attendance Of The Day
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.attendance.attendanceCreate'), 'button_text' => "Create Attendance Of employee"])
@stop
@section('js')
    <script src="{{ asset('assets/changeStatus.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.imageModel').on('click', function () {
                var imageModel = $(this).attr('link');
                $('.employeeImage').attr('src', imageModel);
            });
        });
    </script>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <h4 class="mb-4">Attendance Of The Day : {{$_date}}</h4>
                    <form class="forms-sample mb-4" action="{{route('admin.attendance.attendanceList' )}}" method="get">
                        <div class="row align-items-center mt-3">
                            <div class="col-xl-4 col-lg-3 col-md-3 mb-2">
                                <input type="date" class="form-control" name="date" value="{{$_GET['date'] ?? $_date}}">
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-success form-control">Search</button>
                            </div>
                            <div class="col-lg-1 col-md-3 col-sm-6 mb-2">
                                <a class="btn btn-block btn-primary"
                                   href="{{route('admin.attendance.attendanceList')}}">Reset</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    @if( checkUserRole())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                Edits cannot be done in demo version. Please purchase the full code to unlock these features
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body pb-0">
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <caption class="pb-0">Attendance Of The Day</caption>
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>Date</th>
                                    <th class="text-center">CHECK IN</th>
                                    @if($enableLunchInOut)
                                        <th class="text-center">LUNCH IN</th>
                                        <th class="text-center">LUNCH OUT</th>
                                    @endif
                                    <th class="text-center">CHECK OUT</th>
                                    <th class="text-center">Leave</th>
                                    <th class="text-center">Attendance Type</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($_attendances as $attendance)
                                    <tr>
                                        <td>
                                            <a href="{{route('admin.attendance.monthlyAttendanceDetail',$attendance->user_id)}}"
                                               title="show detail">
                                                <i class="link-icon" data-feather="eye"></i>
                                            </a>
                                        </td>
                                        <td>{{ $attendance->name }}</td>
                                        <td>{{ $attendance->date ?? $_date }}</td>
                                        <td class="text-center">
                                            @if($attendance->check_in)
                                                <button class="btn btn-outline-success btn-xs imageModel"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#imageModel"
                                                        link='{{asset('uploads/attendance/'.$attendance->check_in_image)}}'>
                                                    {{ date('h:i:s A', strtotime($attendance->check_in))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        @if($enableLunchInOut)
                                            <td class="text-center">
                                                @if($attendance->lunch_in)
                                                    <button class="btn btn-outline-success btn-xs imageModel"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#imageModel"
                                                            link='{{asset('uploads/attendance/'.$attendance->lunch_in_image)}}'>
                                                        {{ date('h:i:s A', strtotime($attendance->lunch_in))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attendance->lunch_out)
                                                    <button class="btn btn-outline-success btn-xs imageModel"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#imageModel"
                                                            link='{{asset('uploads/attendance/'.$attendance->lunch_out_image)}}'>
                                                        {{ date('h:i:s A', strtotime($attendance->lunch_out))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            @if($attendance->check_out)
                                                <button class="btn btn-outline-success btn-xs imageModel"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#imageModel"
                                                        link='{{asset('uploads/attendance/'.$attendance->check_out_image)}}'>
                                                    {{ date('h:i:s A', strtotime($attendance->check_out))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ ($attendance->is_on_leave)? "Yes" : "" }}</td>
                                        <td class="text-center">{{ ucfirst($attendance->attendance_type) }}</td>
                                        <td class="text-center">
                                            @if(!$attendance->is_on_leave)
                                                <div class="dropdown text-center">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                        <i class="link-icon" data-feather="more-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        @if(!$attendance->check_in)
                                                            <li>
                                                                <a href="{{route('admin.attendance.checkInEmployee',['user_id'=>$attendance->user_id])}}"
                                                                   class="dropdown-item">Check In</a>
                                                            </li>
                                                        @else
                                                            @if($enableLunchInOut)
                                                                @if(!$attendance->lunch_in)
                                                                    <li>
                                                                        <a href="{{route('admin.attendance.lunchCheckInEmployee',['user_id'=>$attendance->user_id , 'attendance_id'=>$attendance->attendance_id])}}"
                                                                           class="dropdown-item btn btn-warning">Lunch
                                                                            In</a>
                                                                    </li>
                                                                @endif

                                                                @if($attendance->lunch_in && !$attendance->lunch_out)
                                                                    <li>
                                                                        <a href="{{route('admin.attendance.lunchCheckOutEmployee',['user_id'=>$attendance->user_id , 'attendance_id'=>$attendance->attendance_id])}}"
                                                                           class="dropdown-item">Lunch Out</a>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                            @if(!$attendance->check_out)
                                                                <li>
                                                                    <a href="{{route('admin.attendance.checkOutEmployee',['user_id'=>$attendance->user_id , 'attendance_id'=>$attendance->attendance_id])}}"
                                                                       class="dropdown-item">Check out</a>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <a href="{{route('admin.attendance.attendanceEdit',['attendance_id'=>$attendance->attendance_id])}}"
                                                                   class="dropdown-item"> Edit</a>
                                                            </li>
                                                            <li>
                                                                <a data-bs-toggle="modal"
                                                                   data-bs-target="#deleteModel"
                                                                   link='{{route('admin.attendance.deleteAttendance',['attendance_id'=>$attendance->attendance_id])}}'
                                                                   class="dropdown-item delete-modal"> Delete</a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            @endif
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
    @endif
    @if( checkUserPermission())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <caption class="pb-0">Attendance Of The Day</caption>
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>Date</th>
                                    <th class="text-center">CHECK IN</th>
                                    @if($enableLunchInOut)
                                        <th class="text-center">LUNCH IN</th>
                                        <th class="text-center">LUNCH OUT</th>
                                    @endif
                                    <th class="text-center">CHECK OUT</th>
                                    <th class="text-center">Leave</th>
                                    <th class="text-center">Attendance Type</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($_attendances as $attendance)
                                    <tr>
                                        <td>
                                            <a href="{{route('admin.attendance.monthlyAttendanceDetail',$attendance->user_id)}}"
                                               title="show detail">
                                                <i class="link-icon" data-feather="eye"></i>
                                            </a>
                                        </td>
                                        <td>{{ $attendance->name }}</td>
                                        <td>{{ $attendance->date ?? $_date }}</td>
                                        <td class="text-center">
                                            @if($attendance->check_in)
                                                <button class="btn btn-outline-success btn-xs imageModel"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#imageModel"
                                                        link='{{asset('uploads/attendance/'.$attendance->check_in_image)}}'>
                                                    {{ date('h:i:s A', strtotime($attendance->check_in))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        @if($enableLunchInOut)
                                            <td class="text-center">
                                                @if($attendance->lunch_in)
                                                    <button class="btn btn-outline-success btn-xs imageModel"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#imageModel"
                                                            link='{{asset('uploads/attendance/'.$attendance->lunch_in_image)}}'>
                                                        {{ date('h:i:s A', strtotime($attendance->lunch_in))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attendance->lunch_out)
                                                    <button class="btn btn-outline-success btn-xs imageModel"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#imageModel"
                                                            link='{{asset('uploads/attendance/'.$attendance->lunch_out_image)}}'>
                                                        {{ date('h:i:s A', strtotime($attendance->lunch_out))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            @if($attendance->check_out)
                                                <button class="btn btn-outline-success btn-xs imageModel"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#imageModel"
                                                        link='{{asset('uploads/attendance/'.$attendance->check_out_image)}}'>
                                                    {{ date('h:i:s A', strtotime($attendance->check_out))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ ($attendance->is_on_leave)? "Yes" : "" }}</td>
                                        <td class="text-center">{{ ucfirst($attendance->attendance_type) }}</td>
                                        <td class="text-center">
                                            @if(!$attendance->is_on_leave)
                                                <div class="dropdown text-center">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                        <i class="link-icon" data-feather="more-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        @if(!$attendance->check_in)
                                                            <li>
                                                                <a href="{{route('admin.attendance.checkInEmployee',['user_id'=>$attendance->user_id])}}"
                                                                   class="dropdown-item">Check In</a>
                                                            </li>
                                                        @else
                                                            @if($enableLunchInOut)
                                                                @if(!$attendance->lunch_in)
                                                                    <li>
                                                                        <a href="{{route('admin.attendance.lunchCheckInEmployee',['user_id'=>$attendance->user_id , 'attendance_id'=>$attendance->attendance_id])}}"
                                                                           class="dropdown-item btn btn-warning">Lunch
                                                                            In</a>
                                                                    </li>
                                                                @endif

                                                                @if($attendance->lunch_in && !$attendance->lunch_out)
                                                                    <li>
                                                                        <a href="{{route('admin.attendance.lunchCheckOutEmployee',['user_id'=>$attendance->user_id , 'attendance_id'=>$attendance->attendance_id])}}"
                                                                           class="dropdown-item">Lunch Out</a>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                            @if(!$attendance->check_out)
                                                                <li>
                                                                    <a href="{{route('admin.attendance.checkOutEmployee',['user_id'=>$attendance->user_id , 'attendance_id'=>$attendance->attendance_id])}}"
                                                                       class="dropdown-item">Check out</a>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <a href="{{route('admin.attendance.attendanceEdit',['attendance_id'=>$attendance->attendance_id])}}"
                                                                   class="dropdown-item">Edit</a>
                                                            </li>
                                                            <li>
                                                                <a data-bs-toggle="modal"
                                                                   data-bs-target="#deleteModel"
                                                                   link='{{route('admin.attendance.deleteAttendance',['attendance_id'=>$attendance->attendance_id])}}'
                                                                   class="dropdown-item delete-modal">Delete</a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            @endif
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
    @else
        <div class="row">
            <div class="col-md-12 card">
                <div class="card-body text-center bg-danger text-white">
                    Please use valid purchase key obtained from code canyon. Critical features are locked because the
                    system could not verify the purchase code
                </div>
            </div>
        </div>
    @endif


@stop

