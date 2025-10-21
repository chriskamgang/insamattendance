@extends('master')

@section('title')
    Attendance
@stop
@section('page_title')
    Attendance Of The Day
@stop
@section('action_button')
    @include('include.addButton',[ 'route' => route('admin.attendance.attendanceList'), 'button_text' => "Back to Attendance list"])
@stop
@section('js')
    <script src="{{ asset('admin/assets/changeStatus.js') }}"></script>
    <script>
        $(document).ready(function(){
            function getAttendanceFilterParam()
            {
                let params = {
                    year: $('#year').val(),
                    month: $('#month').val()
                }
                return params;
            }
            $('#download-excel').on('click',function (e){
                e.preventDefault();
                let route = $(this).data('href');
                let filtered_params = getAttendanceFilterParam();
                filtered_params.download_excel = true;
                let queryString = $.param(filtered_params)
                let url = route +'?'+queryString;
                window.open(url,'_blank');
            });
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
                <div class="card-body">
                    <h5>Attendance Of {{ucfirst($userDetail->name)}}</h5>
                    <form class="forms-sample mb-4" action="{{route('admin.attendance.monthlyAttendanceDetail',$userDetail->id )}}" method="get">
                        <div class="row align-items-center mt-3">
                            <div class="col-xl col-lg-6 col-md-g mb-4">
                                <input type="number" min="2023"
                                       max="2026" step="1"
                                       placeholder="Attendance year e.g : 2023"
                                       id="year"
                                       name="year"
                                       value="{{$_GET['year'] ?? date('Y')}}"
                                       class="form-control">
                            </div>
                            <div class="col-xl col-lg-6 col-md-g mb-4">
                                <select class="form-select form-select-lg" name="month" id="month">
                                    <option>All Month</option>
                                    @foreach($monthList as $key => $value)
                                        <option value="{{$key}}" {{ (($_GET['month'] ?? date('m')) == $key  ) ?'selected':'' }} >
                                            {{$value}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl col-lg-6 col-md-g mb-4">
                                <button type="submit" class="btn btn-block btn-success form-control">Search</button>
                            </div>
                            <div class="col-xl col-lg-6 col-md-g mb-4">
                                <button type="button" id="download-excel" class="btn btn-block btn-primary form-control" data-href="{{route('admin.attendance.downloadExcelAttendanceDetail',$userDetail->id)}}"> CSV Export</button>
                            </div>
                            <div class="col-xl col-lg-6 col-md-g mb-4">
                                <a class="btn btn-block btn-primary" href="{{route('admin.attendance.monthlyAttendanceDetail',$userDetail->id  )}}">Reset</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class=" col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <h6 class="card-title w-100">Total Days In Month
                    </h6>
                    <h5 class="text-primary">{{$total_days_of_month}}</h5>
                </div>
            </div>
        </div>
        <div class=" col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <h6 class="card-title w-100">Present Days
                    </h6>
                    <h5 class="text-primary">{{$presentCount}}</h5>
                </div>
            </div>
        </div>
        <div class=" col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <h6 class="card-title w-100">Absent Days
                    </h6>
                    <h5 class="text-primary">{{$absentCount}}</h5>

                </div>

            </div>
        </div>
        <div class=" col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <h6 class="card-title w-100">Leave Days
                    </h6>
                    <h5 class="text-primary">{{$leaveCount}}</h5>
                </div>
            </div>
        </div>
        <div class=" col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <h6 class="card-title w-100">Working Hours
                    </h6>
                    <h6 class="text-primary">{{$totalWorkingHours}}</h6>
                </div>
            </div>
        </div>
        <div class=" col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-md-start text-center">
                    <h6 class="card-title w-100">Worked Hours
                    </h6>
                    <h6 class="text-primary">{{$totalWorkedHours}}</h6>
                </div>
            </div>
        </div>
    </div>
    @if(checkUserRole())
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
                                    <th>Date</th>
                                    <th>CHECK IN AT</th>
                                    @if($enableLunchInOut)
                                        <th class="text-center">LUNCH IN</th>
                                        <th class="text-center">LUNCH OUT</th>
                                    @endif
                                    <th>CHECK OUT AT</th>
                                    <th>Is on Leave</th>
                                    <th>Attendance Type</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                @forelse($attendanceDetail as $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{$value['date']}}</td>
                                        <td class="text-center">
                                            @if($value['check_in'])
                                                <button class="btn btn-outline-success btn-xs imageModel" data-bs-toggle="modal"
                                                        data-bs-target="#imageModel"
                                                        link='{{asset('admin/uploads/attendance/'.$value['check_in_image'])}}'>
                                                    {{ date('h:i:s A', strtotime($value['check_in']))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        @if($enableLunchInOut)
                                            <td class="text-center">
                                                @if($value['lunch_in'])
                                                    <button class="btn btn-outline-success btn-xs imageModel" data-bs-toggle="modal"
                                                            data-bs-target="#imageModel"
                                                            link='{{asset('admin/uploads/attendance/'.$value['lunch_in_image'])}}' >
                                                        {{ date('h:i:s A', strtotime($value['lunch_in']))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($value['lunch_out'])
                                                    <button class="btn btn-outline-success btn-xs imageModel" data-bs-toggle="modal"
                                                            data-bs-target="#imageModel"
                                                            link='{{asset('admin/uploads/attendance/'.$value['lunch_out_image'])}}' >
                                                        {{ date('h:i:s A', strtotime($value['lunch_out']))}}
                                                    </button>
                                                @else
                                                    <em class="link-icon" data-feather="x"></em>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            @if($value['check_out'])
                                                <button class="btn btn-outline-success btn-xs imageModel" data-bs-toggle="modal"
                                                        data-bs-target="#imageModel"
                                                        link='{{asset('admin/uploads/attendance/'.$value['check_out_image'])}}' >
                                                    {{ date('h:i:s A', strtotime($value['check_out']))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        <td>{{ ($value['is_on_leave'])? "Is on Leave" : "" }}</td>
                                        <td>{{ ucfirst($value['attendance_type']) }}</td>
                                        <td>{{ ucfirst($value['status']) }}</td>
                                        <td class="text-center">
                                            @if(!$value['is_on_leave'])
                                                <div class="dropdown text-center">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                        <i class="link-icon" data-feather="more-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        @if(!$value['check_in'])
                                                            <li>
                                                                <a href="{{route('admin.attendance.checkInEmployee',['user_id'=>$value['user_id'] , 'attendance_date'=>$value['simple_date']])}}"
                                                                   class="dropdown-item">Check In</a>
                                                            </li>
                                                        @else
                                                            @if($enableLunchInOut)
                                                                @if(!$value['lunch_in'])
                                                                    <li>
                                                                        <a href="{{route('admin.attendance.lunchCheckInEmployee',['user_id'=>$value['user_id'] , 'attendance_id'=>$value['attendance_id'] , 'attendance_date'=>$value['simple_date']])}}"
                                                                           class="dropdown-item btn btn-warning">Lunch In</a>
                                                                    </li>
                                                                @endif

                                                                @if($value['lunch_in'] && !$value['lunch_out'])
                                                                    <li>
                                                                        <a href="{{route('admin.attendance.lunchCheckOutEmployee',['user_id'=>$value['user_id'] , 'attendance_id'=>$value['attendance_id'] , 'attendance_date'=>$value['simple_date']])}}"
                                                                           class="dropdown-item">Lunch Out</a>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                            @if(!$value['check_out'])
                                                                <li>
                                                                    <a href="{{route('admin.attendance.checkOutEmployee',['user_id'=>$value['user_id'] , 'attendance_id'=>$value['attendance_id'] , 'attendance_date'=>$value['simple_date']])}}"
                                                                       class="dropdown-item">Check out</a>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <a href="{{route('admin.attendance.attendanceEdit',['attendance_id'=>$value['attendance_id']])}}"
                                                                   class="dropdown-item"> Edit</a>
                                                            </li>
                                                            <li>
                                                                <a data-bs-toggle="modal"
                                                                   data-bs-target="#deleteModel"
                                                                   link='{{route('admin.attendance.deleteAttendance',['attendance_id'=>$value['attendance_id']])}}'
                                                                   class="dropdown-item delete-modal"> Delete</a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="100%">
                                            <p class="text-center"><b>No records found!</b></p>
                                        </td>
                                    </tr>
                                @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(checkUserPermission())
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
                                <th>Date</th>
                                <th>CHECK IN AT</th>
                                @if($enableLunchInOut)
                                    <th class="text-center">LUNCH IN</th>
                                    <th class="text-center">LUNCH OUT</th>
                                @endif
                                <th>CHECK OUT AT</th>
                                <th>Is on Leave</th>
                                <th>Attendance Type</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            @forelse($attendanceDetail as $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{$value['date']}}</td>
                                    <td class="text-center">
                                        @if($value['check_in'])
                                            <button class="btn btn-outline-success btn-xs imageModel" data-bs-toggle="modal"
                                                    data-bs-target="#imageModel"
                                                    link='{{asset('admin/uploads/attendance/'.$value['check_in_image'])}}'>
                                                {{ date('h:i:s A', strtotime($value['check_in']))}}
                                            </button>
                                        @else
                                            <em class="link-icon" data-feather="x"></em>
                                        @endif
                                    </td>
                                    @if($enableLunchInOut)
                                        <td class="text-center">
                                            @if($value['lunch_in'])
                                                <button class="btn btn-outline-success btn-xs imageModel" data-bs-toggle="modal"
                                                        data-bs-target="#imageModel"
                                                        link='{{asset('admin/uploads/attendance/'.$value['lunch_in_image'])}}' >
                                                    {{ date('h:i:s A', strtotime($value['lunch_in']))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($value['lunch_out'])
                                                <button class="btn btn-outline-success btn-xs imageModel" data-bs-toggle="modal"
                                                        data-bs-target="#imageModel"
                                                        link='{{asset('admin/uploads/attendance/'.$value['lunch_out_image'])}}' >
                                                    {{ date('h:i:s A', strtotime($value['lunch_out']))}}
                                                </button>
                                            @else
                                                <em class="link-icon" data-feather="x"></em>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        @if($value['check_out'])
                                            <button class="btn btn-outline-success btn-xs imageModel" data-bs-toggle="modal"
                                                    data-bs-target="#imageModel"
                                                    link='{{asset('admin/uploads/attendance/'.$value['check_out_image'])}}' >
                                                {{ date('h:i:s A', strtotime($value['check_out']))}}
                                            </button>
                                        @else
                                            <em class="link-icon" data-feather="x"></em>
                                        @endif
                                    </td>
                                    <td>{{ ($value['is_on_leave'])? "Is on Leave" : "" }}</td>
                                    <td>{{ ucfirst($value['attendance_type']) }}</td>
                                    <td>{{ ucfirst($value['status']) }}</td>
                                    <td class="text-center">
                                        @if(!$value['is_on_leave'])
                                            <div class="dropdown text-center">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                    <i class="link-icon" data-feather="more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                    @if(!$value['check_in'])
                                                        <li>
                                                            <a href="{{route('admin.attendance.checkInEmployee',['user_id'=>$value['user_id'] , 'attendance_date'=>$value['simple_date']])}}"
                                                               class="dropdown-item">Check In</a>
                                                        </li>
                                                    @else
                                                        @if($enableLunchInOut)
                                                            @if(!$value['lunch_in'])
                                                                <li>
                                                                    <a href="{{route('admin.attendance.lunchCheckInEmployee',['user_id'=>$value['user_id'] , 'attendance_id'=>$value['attendance_id'] , 'attendance_date'=>$value['simple_date']])}}"
                                                                       class="dropdown-item btn btn-warning">Lunch In</a>
                                                                </li>
                                                            @endif

                                                            @if($value['lunch_in'] && !$value['lunch_out'])
                                                                <li>
                                                                    <a href="{{route('admin.attendance.lunchCheckOutEmployee',['user_id'=>$value['user_id'] , 'attendance_id'=>$value['attendance_id'] , 'attendance_date'=>$value['simple_date']])}}"
                                                                       class="dropdown-item">Lunch Out</a>
                                                                </li>
                                                            @endif
                                                        @endif
                                                        @if(!$value['check_out'])
                                                            <li>
                                                                <a href="{{route('admin.attendance.checkOutEmployee',['user_id'=>$value['user_id'] , 'attendance_id'=>$value['attendance_id'] , 'attendance_date'=>$value['simple_date']])}}"
                                                                   class="dropdown-item">Check out</a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <a href="{{route('admin.attendance.attendanceEdit',['attendance_id'=>$value['attendance_id']])}}"
                                                               class="dropdown-item"> Edit</a>
                                                        </li>
                                                        <li>
                                                            <a data-bs-toggle="modal"
                                                               data-bs-target="#deleteModel"
                                                               link='{{route('admin.attendance.deleteAttendance',['attendance_id'=>$value['attendance_id']])}}'
                                                               class="dropdown-item delete-modal"> Delete</a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-center"><b>No records found!</b></p>
                                    </td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="row">
            <div class="col-md-12 card">
                <div class="card-body text-center bg-danger text-white">
                    Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code
                </div>
            </div>
        </div>
    @endif
    @include('include.delete-model')
    <div class="modal fade" id="imageModel" tabindex="-1" aria-labelledby="imageModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="exampleModalLabel">Employee Image </h5>
                </div>
                <div class="modal-body text-center">
                    <img class="employeeImage" src="" alt="employeeImage" height="500" width="500">
                </div>
            </div>
        </div>
    </div>
@stop
