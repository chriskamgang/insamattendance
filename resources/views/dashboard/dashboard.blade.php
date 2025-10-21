@extends('master')

@section('title')
    Dashboard
@stop
@section('page_title')   @stop

@section('content')
    @if( checkUserRole())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                Edits cannot be done in demo version. Please purchase the full code to unlock these features
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-xxl-4  col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                        <h6 class="card-title">Total Employees</h6>
                    </div>

                    <div class="row align-items-center d-md-flex">
                        <div class="col-lg-6 col-md-6">
                            <h3>{{number_format($total_employee)}}</h3>
                        </div>
                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                            <i class="link-icon" data-feather="users"> </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4  col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                        <h6 class="card-title">Total Holidays</h6>
                    </div>
                    <div class="row align-items-center d-md-flex">
                        <div class="col-lg-6 col-md-6">
                            <h3>{{number_format($total_holidays)}}</h3>
                        </div>
                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                            <i class="link-icon" data-feather="umbrella"> </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4  col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                        <h6 class="card-title">On Leave Today</h6>
                    </div>
                    <div class="row align-items-center d-md-flex">
                        <div class="col-lg-6 col-md-6">
                            <h3>{{number_format($total_on_leave)}}</h3>
                        </div>
                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                            <i class="link-icon" data-feather="file-minus"> </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-3  col-md-6 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                        <h6 class="card-title">Total Check In Today</h6>
                    </div>
                    <div class="row align-items-center d-md-flex">
                        <div class="col-lg-6 col-md-6">
                            <h3>{{number_format($total_checked_in)}}</h3>
                        </div>
                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                            <i class="link-icon" data-feather="log-in"> </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3  col-md-6 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                        <h6 class="card-title">Total Lunch In Today</h6>
                    </div>
                    <div class="row align-items-center d-md-flex">
                        <div class="col-lg-6 col-md-6">
                            <h3>{{number_format($total_lunch_in)}}</h3>
                        </div>
                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                            <i class="link-icon" data-feather="log-in"> </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3  col-md-6 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                        <h6 class="card-title">Total Lunch Out Today</h6>
                    </div>
                    <div class="row align-items-center d-md-fle">
                        <div class="col-lg-6 col-md-6">
                            <h3>{{number_format($total_lunch_out)}}</h3>
                        </div>
                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                            <i class="link-icon" data-feather="external-link"> </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3  col-md-6 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                        <h6 class="card-title">Total Check Out Today</h6>
                    </div>
                    <div class="row align-items-center d-md-fle">
                        <div class="col-lg-6 col-md-6">
                            <h3>{{number_format($total_checked_out)}}</h3>
                        </div>
                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                            <i class="link-icon" data-feather="log-out"> </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @if($birthdayMessage)
        <div class="card mb-4">
            <div class="card-body d-lg-flex align-items-center text-lg-left text-center pb-3">
                <h4 class="me-4 mb-2">Birthday</h4>
                <marquee direction="left" class="mb-2">
                    {!! $birthdayMessage !!}
                </marquee>
            </div>
        </div>
    @endif
    @if($noticeMessage)
        <div class="card mb-4">
            <div class="card-body d-lg-flex align-items-center text-lg-left text-center pb-3">
                <h4 class="me-4 mb-2">Notice</h4>
                <marquee direction="left" class="mb-2">
                    {!! $noticeMessage !!}
                </marquee>
            </div>
        </div>
    @endif
    @if( checkUserRole())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                Edits cannot be done in demo version. Please purchase the full code to unlock these features
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-4">Attendance Of The Day : {{$_date}}</h4>
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <caption class="pb-0">Attendance Of The Day</caption>
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>Date</th>
                                    <th class="text-center">CHECK IN AT</th>
                                    @if($enableLunchInOut)
                                        <th  class="text-center">LUNCH IN AT</th>
                                        <th  class="text-center">LUNCH OUT AT</th>
                                    @endif
                                    <th  class="text-center">CHECK OUT AT</th>
                                    <th  class="text-center">Is on Leave</th>
                                    <th  class="text-center">Attendance Type</th>
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
                                        <td  class="text-center">
                                            @if($attendance->check_in)
                                                <button class="btn btn-outline-success btn-xs" disabled>
                                                    {{ date('h:i:s A', strtotime($attendance->check_in))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        @if($enableLunchInOut)
                                            <td  class="text-center">
                                                @if($attendance->lunch_in)
                                                    <button class="btn btn-outline-success btn-xs" disabled>
                                                        {{ date('h:i:s A', strtotime($attendance->lunch_in))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                            <td  class="text-center">
                                                @if($attendance->lunch_out)
                                                    <button class="btn btn-outline-success btn-xs" disabled>
                                                        {{ date('h:i:s A', strtotime($attendance->lunch_out))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                        @endif
                                        <td  class="text-center">
                                            @if($attendance->check_out)
                                                <button class="btn btn-outline-success btn-xs" disabled>
                                                    {{ date('h:i:s A', strtotime($attendance->check_out))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        <td  class="text-center">{{ ($attendance->is_on_leave)? "Is on Leave" : "" }}</td>
                                        <td  class="text-center">{{ ucfirst($attendance->attendance_type) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if( checkUserPermission())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-4">Attendance Of The Day : {{$_date}}</h4>
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <caption class="pb-0">Attendance Of The Day</caption>
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>Date</th>
                                    <th class="text-center">CHECK IN AT</th>
                                    @if($enableLunchInOut)
                                        <th  class="text-center">LUNCH IN AT</th>
                                        <th  class="text-center">LUNCH OUT AT</th>
                                    @endif
                                    <th  class="text-center">CHECK OUT AT</th>
                                    <th  class="text-center">Is on Leave</th>
                                    <th  class="text-center">Attendance Type</th>
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
                                        <td  class="text-center">
                                            @if($attendance->check_in)
                                                <button class="btn btn-outline-success btn-xs" disabled>
                                                    {{ date('h:i:s A', strtotime($attendance->check_in))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        @if($enableLunchInOut)
                                            <td  class="text-center">
                                                @if($attendance->lunch_in)
                                                    <button class="btn btn-outline-success btn-xs" disabled>
                                                        {{ date('h:i:s A', strtotime($attendance->lunch_in))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                            <td  class="text-center">
                                                @if($attendance->lunch_out)
                                                    <button class="btn btn-outline-success btn-xs" disabled>
                                                        {{ date('h:i:s A', strtotime($attendance->lunch_out))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                        @endif
                                        <td  class="text-center">
                                            @if($attendance->check_out)
                                                <button class="btn btn-outline-success btn-xs" disabled>
                                                    {{ date('h:i:s A', strtotime($attendance->check_out))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        <td  class="text-center">{{ ($attendance->is_on_leave)? "Is on Leave" : "" }}</td>
                                        <td  class="text-center">{{ ucfirst($attendance->attendance_type) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row ">
            <div class="col-md-12 card">
                <div class="card-body text-center bg-danger text-white">
                    Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code
                </div>
            </div>
        </div>
    @endif

@stop
