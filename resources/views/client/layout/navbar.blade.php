<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item">
                    <a class="navbar-brand" href="{{route('cod.welcome')}}" style="margin-left:-50px">
                        <img class="brand-logo sonic" alt="Sonic" src="{{ asset('img/sonic_logo_white_new.png') }}">
                        <img class="brand-logo trax" alt="Trax" src="{{ asset('img/trax_logo_white_new.png') }}">
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
                    <li class="nav-item d-none d-md-flex justify-content-center h4 m-auto"><p class="m-auto white"><span class="d-none d-lg-inline-block">For Assistance Call:</span> <a href="tel:+922138772222" target="_blank" class="text-bold-700 white">021-111-118-729</a></p></li>
                </ul>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-user nav-item">
                        @if(session('status') === 3)

                            @if(session('user_type') == 1)
                                @if(Auth::user()->wallet)
                                    <a class="nav-link d-inline-flex align-middle p-0" href="{{ route('cod.wallet.login') }}" target="_blank">
                                        <div class="m-0 bg-white primary rounded custom-nav-buttons-padding" style="background: rgb(67 118 98)!important;padding: 5px 8px;!important;">
                                            <span class="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold" style="color: white!important;font-size: 13px!important;">Payments</span>
                                            <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-money m-0" style="color: white!important;"></i></h2>
                                        </div>
                                    </a>
                                  @elseif(session('wallet_sign_up_allow'))
                                    <a class="nav-link d-inline-flex align-middle p-0" href="{{ route('cod.wallet.on_boarding') }}" target="_blank">
                                        <div class="m-0 bg-white primary rounded custom-nav-buttons-padding" style="background: rgb(67 118 98)!important;padding: 5px 8px;!important;">
                                            <span class="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold" style="color: white!important;font-size: 13px!important;">Payments</span>
                                            <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-money m-0" style="color: white!important;"></i></h2>
                                        </div>
                                    </a>
                                @endif
                            @else
                                @if(isset(auth()->user()->load('sub_wallet')->sub_wallet))
                                    <a class="nav-link d-inline-flex align-middle p-0" href="{{ route('cod.wallet.login') }}" target="_blank">
                                        <div class="m-0 bg-white primary rounded custom-nav-buttons-padding" style="background: rgb(67 118 98)!important;padding: 5px 8px;!important;">
                                            <span class="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold">Payments</span>
                                            <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-money m-0" style="color: white!important;font-size: 13px!important;"></i></h2>
                                        </div>
                                    </a>
                                @endif
                            @endif

                            <a class="nav-link d-inline-flex align-middle p-0" href="{{ route('cod.order.index') }}" target="_blank">
                                <div class="m-0 bg-white primary rounded custom-nav-buttons-padding" style="padding: 5px 8px;!important;">
                                    <span class="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold" style="font-size: 13px!important;">Order ID</span>
                                    <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-circle-o-notch m-0"></i></h2>
                                </div>
                            </a>
                            <a class="nav-link d-inline-flex align-middle p-0" href="{{ route('cod.tracking.index') }}" target="_blank">
                                <div class="m-0 bg-white primary rounded custom-nav-buttons-padding" style="padding: 5px 8px;!important;">
                                    <span class="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold" style="font-size: 13px!important;">Tracking</span>
                                    <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-crosshairs m-0"></i></h2>
                                </div>
                            </a>
                        @endif
                        <a class="nav-link d-inline-flex align-middle p-0" href="http://bit.ly/sonic_video_tutorial" target="_blank">
                            <div class="m-0 bg-white primary rounded custom-nav-buttons-padding" style="padding: 5px 8px;!important;">
                                <span class ="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold" style="font-size: 13px!important;">HELP</span>
                                <h2 class="d-inline-block m-0 align-middle primary"><i class="ft-help-circle m-0"></i></h2>
                            </div>
                        </a>

                        <a class="dropdown-toggle nav-link d-flex d-md-inline-flex align-middle dropdown-user-link" href="#" data-toggle="dropdown">
                <span class="d-inline-block align-middle">
                    <div class="text-bold-700 border-bottom-white text-right" data-toggle="tooltip"
                            data-trigger="hover"
                            data-placement="top"
                            data-title="{{ ucfirst(Auth::user()->name ) }}"> 
                            {{ strlen(Auth::user()->name) > 20 ? substr(ucfirst(Auth::user()->name), 0, 20) . '...' : ucfirst(Auth::user()->name)}}
                            </div>

                    @if (session('user_type') == 1)
                        <div class="border-top-white text-right">{{ str_pad(Auth::id(), 6, '0', STR_PAD_LEFT) }}</div>
                    @else
                        <div class="border-top-white text-right">{{ Auth::user()->shipper->name }} ({{ str_pad(Auth::user()->shipper->id, 6, '0', STR_PAD_LEFT) }})</div>
                    @endif
                </span>

                            <i class="ft-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @if(session('status') == 3)
                                @if(session('user_type') == 1)
                                    <a class="dropdown-item" href="{{ route('cod.edit.profile') }}"><span class="menu-title"><i class="ft-user"></i>Profile</span></a>

                                    @if(Auth::user()->wallet)
                                        {{--                              <a class="dropdown-item" href="{{ route('cod.wallet.login') }}"><span class="menu-title"><i class="ft-briefcase"></i>Wallet</span></a>--}}
                                        {{--<a class="dropdown-item" href="{{ route('cod.wallet.users') }}"><span class="menu-title"><i class="ft-user-plus"></i>Wallet Substitute User</span></a>--}}
                                    @else
                                        {{--                              <a class="dropdown-item" href="{{ route('cod.wallet.on_boarding') }}"><span class="menu-title"><i class="ft-file"></i>Sign-Up for Wallet</span></a>--}}
                                    @endif
                                @else
                                    @if(isset(auth()->user()->load('sub_wallet')->sub_wallet))
                                        {{--                                <a class="dropdown-item" href="{{ route('cod.wallet.login') }}"><span class="menu-title"><i class="ft-briefcase"></i>Wallet</span></a>--}}
                                    @endif
                                @endif
                                <a class="dropdown-item" href="{{ route('cod.resources.index') }}"><span class="menu-title"><i class="ft-file"></i>Resources</span></a>
                                @if(session('sale_person_status') == 1)
                                    <a class="dropdown-item" href="{{ route('cod.contacts') }}"><span class="menu-title"><i class="ft-phone"></i>Contacts</span></a>
                                @endif
                            @endif
                            <a class="dropdown-item" href="{{route('cod.logout')}}" onclick="event.preventDefault();


                document.getElementById('logout-form').submit();"><i class="ft-power"></i> Logout</a>
                            <form id="logout-form" action="{{ route('cod.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>

                            @if(Session::has('agreement_signed') && session('agreement_signed') == 1 && session('token') != null)
                                {{-- CRF form download --}}
                                <a class="dropdown-item" href="{{route('cod.terms.download', ['id' => session('user_id'), 'token' => session('token')])}}"><i class="ft-download"></i> Download CRF </a>

                                {{-- onclick="event.preventDefault();
                                document.getElementById('crf-form').submit();" --}}
                                {{-- <form id="crf-form" action="{{ route('cod.terms.download', ['id' => session('user_id'), 'token' => session('token')]) }}" method="POST" style="display: none;">
                                  @csrf
                                </form> --}}
                                {{-- end --}}

                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>