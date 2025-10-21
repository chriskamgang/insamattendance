@php
    $currentRoute = Request::route()->getName();
    $Route = explode('.',$currentRoute);
@endphp
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{route('admin.dashboard')}}" class="sidebar-brand"> Digital<span>HR</span> </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <nav class="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-brand" target="_blank"> Digital<span>HR</span> </a>
            <div class="sidebar-toggler not-active">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="sidebar-body">
            <ul class="nav">
                <li class="nav-item @if($Route[1] === 'dashboard') active @endif">
                    <a href="{{route('admin.dashboard')}}" class="nav-link">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'companyDetail') active @endif">
                    <a href="{{route('admin.companyDetail.companyDetail')}}" class="nav-link">
                        <i class="link-icon" data-feather="credit-card"></i>
                        <span class="link-title">Company Detail</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'shift') active @endif">
                    <a href="{{route('admin.shift.index')}}" class="nav-link">
                        <i class="link-icon" data-feather="file-text"></i>
                        <span class="link-title">Shift</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'department') active @endif">
                    <a href="{{route('admin.department.index')}}" class="nav-link">
                        <i class="link-icon" data-feather="archive"></i>
                        <span class="link-title">Department</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'user') active @endif">
                    <a href="{{route('admin.user.index')}}" class="nav-link">
                        <i class="link-icon" data-feather="user"></i>
                        <span class="link-title">Employee</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'admin') active @endif">
                    <a href="{{route('admin.admin.listAdmin')}}" class="nav-link">
                        <i class="link-icon" data-feather="user"></i>
                        <span class="link-title">User</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'attendance') active @endif">
                    <a href="{{route('admin.attendance.attendanceList')}}" class="nav-link">
                        <i class="link-icon" data-feather="list"></i>
                        <span class="link-title">Attendance List</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'leaveType') active @endif">
                    <a href="{{route('admin.leaveType.index')}}" class="nav-link">
                        <i class="link-icon" data-feather="activity"></i>
                        <span class="link-title">Leave Type</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'leave') active @endif">
                    <a href="{{route('admin.leave.index')}}" class="nav-link">
                        <i class="link-icon" data-feather="thermometer"></i>
                        <span class="link-title">Employee Leave </span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'holiday') active @endif">
                    <a href="{{route('admin.holiday.index')}}" class="nav-link">
                        <i class="link-icon" data-feather="coffee"></i>
                        <span class="link-title">Holiday</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'notice') active @endif">
                    <a href="{{route('admin.notice.index')}}" class="nav-link">
                        <i class="link-icon" data-feather="calendar"></i>
                        <span class="link-title">Notice</span>
                    </a>
                </li>
                <li class="nav-item @if($Route[1] === 'setting') active @endif">
                    <a href="{{route('admin.setting.appSetting')}}" class="nav-link">
                        <i class="link-icon" data-feather="settings"></i>
                        <span class="link-title">App Setting</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</nav>
