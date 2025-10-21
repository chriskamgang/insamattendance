<nav class="navbar">
    <a href="#" class="sidebar-toggler">
        <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
        <ul class="navbar-nav">

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @if($_companyDetail ?? false)
                        <img class="wd-30 ht-30 rounded-circle" src="{{ $_companyDetail->image_path  }}" alt="{{ $_companyDetail->title }}" height="30px" width="30px">
                    @else
                        <img class="wd-30 ht-30 rounded-circle" src="https://via.placeholder.com/30x30" alt="profile">
                    @endif

                </a>
                <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                    <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                        <div class="mb-3">
                            @if($_companyDetail ?? false)
                                <img class="wd-80 ht-80 rounded-circle" src="{{ $_companyDetail->image_path  }}" alt="{{ $_companyDetail->title }}" height="80px" width="80px">
                            @else
                                <img class="wd-80 ht-80 rounded-circle" src="https://via.placeholder.com/80x80" alt="">
                            @endif

                        </div>
                        <div class="text-center">
                            <p class="tx-16 fw-bolder">{{Auth::user()->name}}</p>
                            <p class="tx-12 text-muted">{{Auth::user()->email}}</p>
                        </div>
                    </div>
                    <ul class="list-unstyled p-1">
                        <li class="dropdown-item py-2">
                            <a href="{{route('admin.admin.editAdmin',['user'=>Auth::user()->id])}}" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="user"> </i>Edit Profile
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="{{route('admin.admin.changePassword',['id'=>Auth::user()->id])}}" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="key"> </i>Change password
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="{{route('admin.admin.changeAppPassword',['id'=>Auth::user()->id])}}" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="key"> </i>App password
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="log-out"> </i>log out
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
