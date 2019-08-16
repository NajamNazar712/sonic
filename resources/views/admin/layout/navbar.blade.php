<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item">
                    <a class="navbar-brand" href="{{route('admin.dashboard.index')}}">
                        <img class="brand-logo sonic" alt="Sonic" src="{{ asset('img/sonic_logo_white.png') }}">
                        <img class="brand-logo trax" alt="Trax" src="{{ asset('img/trax_logo_white.png') }}">
                    </a>
                </li>
                <li class="nav-item d-md-none">
                    <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs" id="sidebar_menu" href="#"><i class="ft-menu"></i></a></li>
                </ul>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-user nav-item">
                        <a class="nav-link d-inline-flex align-middle p-0" href="{{ route('admin.tracking.index') }}" target="_blank">
                            <div class="m-0 bg-white primary rounded custom-nav-buttons-padding">
                                <span class="align-middle font-weight-bold">Tracking</span>
                                <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-crosshairs m-0"></i></h2>
                            </div>
                        </a>
                        <a class="nav-link d-inline-flex align-middle p-0" href="http://bit.ly/sonic_manuals" target="_blank">
                            <div class="m-0 bg-white primary rounded custom-nav-buttons-padding">
                                <span class="align-middle font-weight-bold">HELP</span>
                                <h2 class="d-inline-block m-0 align-middle primary"><i class="ft-help-circle m-0"></i></h2>
                            </div>
                        </a>

                        <a class="dropdown-toggle nav-link d-inline-flex align-middle dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="d-inline-block align-middle">
                                <div class="text-bold-700 border-bottom-white text-right">{{ucfirst(Auth::user()->name)}}</div>
                                <div class="border-top-white text-right">{{Auth::user()->role->department->name}}</div>
                            </span>

                            <i class="ft-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{route('admin.logout')}}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="ft-power"></i> Logout</a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</nav>