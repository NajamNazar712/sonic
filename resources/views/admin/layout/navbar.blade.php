<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item">
                    <a class="navbar-brand" href="{{route('admin.dashboard.index')}}">
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
                   {{-- <li class="nav-item d-none d-md-flex justify-content-center h4 m-auto"><p class="m-auto white"><span class="d-none d-lg-inline-block">For Assistance Call:</span> <a href="tel:+922138772222" target="_blank" class="text-bold-700 white">021-111-118-729</a></p></li>--}}
                    <a class="nav-link d-inline-flex align-middle p-1" href="{{ route('admin.attendance.mark') }}" target="_blank">
                        <div class="m-0 bg-white primary rounded custom-nav-buttons-padding">
                            <span class="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold">Attendance</span>
                            <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-calendar m-0"></i></h2>
                        </div>
                    </a>
                </ul>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-user nav-item">
                        <a class="nav-link d-inline-flex align-middle p-0" href="{{ route('admin.tracking.index') }}" target="_blank">
                            <div class="m-0 bg-white primary rounded custom-nav-buttons-padding">
                                <span class="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold">Tracking</span>
                                <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-crosshairs m-0"></i></h2>
                            </div>
                        </a>
                        @if(session('role_id') == 1 || in_array(204, session('permissions')))
                            <a class="nav-link d-inline-flex align-middle p-0" href="{{ route('admin.cx_quick_tracking.cx_index') }}" target="_blank">
                                <div class="m-0 bg-white primary rounded custom-nav-buttons-padding">
                                    <span class="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold">Quick Tracking</span>
                                    <h2 class="d-inline-block m-0 align-middle primary"><i class="la la-crosshairs m-0"></i></h2>
                                </div>
                            </a>
                        @endif
                        {{--<a class="nav-link d-inline-flex align-middle p-0" href="http://bit.ly/sonic_manuals" target="_blank">--}}
                            {{--<div class="m-0 bg-white primary rounded custom-nav-buttons-padding">--}}
                                {{--<span class ="d-inline-block d-md-none d-lg-none d-xl-inline-block align-middle font-weight-bold">HELP</span>--}}
                                {{--<h2 class="d-inline-block m-0 align-middle primary"><i class="ft-help-circle m-0"></i></h2>--}}
                            {{--</div>--}}
                        {{--</a>--}}

                        <a class="dropdown-toggle nav-link d-flex d-md-inline-flex align-middle dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="d-inline-block align-middle">
                                <div class="text-bold-700 border-bottom-white text-right">{{ucfirst(Auth::user()->name)}}</div>
                                <div class="border-top-white text-right">{{Auth::user()->role->department->name ?? "-"}}</div>
                            </span>

                            <i class="ft-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" id="adminprofileshow"><span class="menu-title adminprofile"><i class="ft-user"></i> Profileee</span></a>
                            <a class="dropdown-item" href="http://bit.ly/sonic_manuals" target="_blank"><i class="ft-help-circle"></i> HELP</a>
                            <a class="dropdown-item" href="{{ route('admin.resources.index') }}"><span class="menu-title"><i class="ft-file"></i>Resources</span></a>
                            <a class="dropdown-item" href="#" id="editprofileshow"><span class="menu-title editprofile"><i class="ft-edit-2"></i>Edit Profile</span></a>
                            <a class="dropdown-item" href="{{ route('admin.update.profile.password') }}"><span class="menu-title"><i class="ft-edit"></i>Change Pin</span></a>
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
@section('js')
    <script type="text/javascript">

        $('body').on('click','.adminprofile',function(){
            var full_name , department , employee_id , email , contact , designation, blood_group, emergency_contact_no, emergency_contact_person;
            $.ajax({
                    url:'{!! route('admin.dashboard.admin_profile') !!}',
                    type:'GET'
                }).done(function (data) {

                    if(data){
                        full_name = data.full_name;
                        department = data.department;
                        employee_id = data.employee_id;
                        email = data.email;
                        contact = data.contact;
                        designation = data.designation;
                        blood_group = data.blood_group;
                        emergency_contact_person = data.emergency_contact_person;
                        emergency_contact_no = data.emergency_contact_no;

                        $('#adminprofile #admin_profile_body #full_name').html((full_name)? full_name : '--');
                        $('#adminprofile #admin_profile_body #department').html((department)? department : '--');
                        $('#adminprofile #admin_profile_body #employee_id').html((employee_id) ? employee_id : '--');
                        $('#adminprofile #admin_profile_body #email').html((email)? email : '--');
                        $('#adminprofile #admin_profile_body #contact').html((contact)? contact : '--');
                        $('#adminprofile #admin_profile_body #designation').html((designation)? designation : '--');
                        $('#adminprofile #admin_profile_body #blood_group').html((blood_group)? blood_group : '--');
                        $('#adminprofile #admin_profile_body #emergency_contact_person').html((emergency_contact_person)? emergency_contact_person : '--');
                        $('#adminprofile #admin_profile_body #emergency_contact_no').html((emergency_contact_no)? emergency_contact_no : '--');
                        $('#adminprofile').modal('show');
                    }
                });
            });

        $('body').on('click','.editprofile',function(){
            $.ajax({
                    url:'{!! route('admin.dashboard.edit_profile') !!}',
                    type:'GET'
                }).done(function (data) {
                    if(data.status == 1){
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }else{
                        $('#editprofile #editProfileDiv').html(data);
                        $('#editprofile').modal('show');
                    }
                });
            });
    </script>