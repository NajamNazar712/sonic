<footer class="footer footer-static footer-light navbar-border navbar-shadow">
    <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
        <span class="float-md-left d-block d-md-inline-block font-weight-bold"><i class="ft-phone-call align-middle mr-1"></i><span class="align-middle">021-111-118-729</span></span>
        <span class="float-md-right d-block d-md-inline-blockd-none d-lg-block">Copyright &copy; {{ now()->year }} By <a class="text-bold-800 grey darken-2" href="#">Trax</a>, All Rights Reserved.</span>
    </p>
</footer>
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
<script src="{{asset('js/main-2.2.js')}}" type="text/javascript"></script>
{{--This needs to be moved--}}
<script src="{{asset('app-assets/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
{{--This needs to be moved--}}
<!-- BEGIN PAGE VENDOR JS-->
{{--<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}" type="text/javascript"></script>--}}
{{--<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>--}}
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/core/app.js')}}" type="text/javascript"></script>
<!-- END MODERN JS-->

<!-- BEGIN PAGE LEVEL JS-->
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
{{--<script src="{{asset('app-assets/js/scripts/forms/form-login-register.js')}}" type="text/javascript"></script>--}}
{{--Datatables javascript--}}
{{--This needs to be moved--}}
<script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/toggle/switchery.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/forms/switch.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/forms/input-groups.js')}}" type="text/javascript"></script>
{{--<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>--}}
<script src="{{asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
{{--This needs to be moved--}}
{{--<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}" type="text/javascript"></script>--}}
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>

@if (isset($ticker))
	<script src="{{asset('app-assets/vendors/js/marquee3000/marquee3k.js')}}" type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			Marquee3k.init();
		});
	</script>
@endif

<script src="{{asset('js/app.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL JS-->
<script type="text/javascript">
	$(document).ready(function () {
        $('body #app_content').on('click', function () {
            if($('#sidebar_menu').hasClass('is-active')){
                $.app.menu.hide();
            }
        });
{{--        @if(Session::has('first_login') && session('first_login') != 1)--}}
{{--            $('#FirstLoginPasswordChangeModal').modal('show');--}}

{{--            $('#first_peye').on('mousedown',function(){$('input[name="current_password"]').attr('type','text')}).on('mouseup',function(){$('input[name="current_password"]').attr('type','password')});--}}
{{--            $('#first_npeye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});--}}
{{--            $('#first_cpeye').on('mousedown',function(){$('input[name="confirm_password"]').attr('type','text')}).on('mouseup',function(){$('input[name="confirm_password"]').attr('type','password')});--}}

{{--        $( "#password-form" ).validate({--}}
{{--            errorClass:"danger",--}}
{{--            normalizer: function(value) {--}}
{{--                return $.trim(value);--}}
{{--            },--}}
{{--            errorPlacement: function(error, element) {--}}
{{--                error.addClass('w-100').appendTo(element.parent('.form-group'));--}}
{{--            },--}}
{{--            submitHandler: function(form) {--}}
{{--                var current_password = $('#current_password').val();--}}
{{--                var new_password = $('#new_password').val();--}}
{{--                var confirm_password = $('#confirm_password').val();--}}
{{--                if (current_password !== new_password) {--}}
{{--                    if(new_password === confirm_password){--}}
{{--                        swal({--}}
{{--                            title: 'Please Wait!',--}}
{{--                            text: 'Password is being updated!',--}}
{{--                            icon: 'info',--}}
{{--                            buttons: false,--}}
{{--                            closeOnClickOutside: false,--}}
{{--                            closeOnEsc: false--}}
{{--                        });--}}
{{--                        form.submit();--}}
{{--                    }--}}
{{--                    else{--}}
{{--                        var error = "The password and confirmation password do not match";--}}
{{--                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
{{--                    }--}}
{{--                }--}}
{{--                else{--}}
{{--                    var error = "The current and new password cannot be same";--}}
{{--                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}
{{--        @endif--}}
        @php($environment = env('APP_ENV'))
        @if ($environment != 'local')
            function getLocation() {
              swal({
                  text: 'Please Wait!',
                  icon: 'info',
                  buttons: false,
                  closeOnClickOutside: false,
                  closeOnEsc: false
              });

              setTimeout(function() {
                if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(locationSuccess, locationFail);
                }
                else {
                  locationFail();
                }
              }, 250);
            }

            function check_profile() {
                $.ajax({
                    url: '{{ route('admin.update_one_time_profile.check') }}',
                    method: 'GET'
                }).done(function (data) {
                        if(data.status == 0) {
                            $('#EditOneTimeProfileModal').modal('show');
                        }
                    });
            }

            function locationSuccess(position) {
              $.ajax({
                  url: '{{ route('admin.save_coordinates') }}',
                  method: 'POST',
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  data: {
                    'longitude': position.coords.longitude,
                    'latitude': position.coords.latitude
                  }
              });

              $('#LocationDeniedModal').modal('hide');
              swal.close();
              @if(!Illuminate\Support\Facades\Route::is('admin.update_one_time_profile.index'))
              check_profile();
              @endif
            }

            function locationFail() {
              swal.close();
              $('#LocationDeniedModal').modal('show');
            }

            $('#EditOneTimeProfileModal form button').bind('click', function() {
                $('#EditOneTimeProfileModal').modal('hide');
                window.location.href = "{{route("admin.update_one_time_profile.index")}}";
            });

            $('#LocationDeniedModal form button').bind('click', function() {
                getLocation();
            });

            getLocation();

        @endif
    });
</script>
  @yield('js')