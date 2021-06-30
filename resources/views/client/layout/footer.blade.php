<footer class="footer footer-static footer-light navbar-border navbar-shadow">
  <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
      <span class="float-md-left d-block d-md-inline-block"><i class="ft-phone-call align-middle mr-1"></i><span class="align-middle">021-111-118-729</span></span>
    <span class="float-md-right d-block d-md-inline-blockd-none d-lg-block">Copyright &copy; {{ now()->year }} By <a class="text-bold-800 grey darken-2" href="#">Trax</a>, All Rights Reserved.</span>
  </p>
</footer>
<!-- BEGIN VENDOR JS-->
  <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
  <!-- BEGIN VENDOR JS-->
<script src="{{asset('js/main-2.2.js')}}" type="text/javascript"></script>
  <!-- BEGIN MODERN JS-->
  <script src="{{asset('app-assets/js/core/app-menu.js')}}" type="text/javascript"></script>
  <script src="{{asset('app-assets/js/core/app.js')}}" type="text/javascript"></script>
  <!-- END MODERN JS-->

@if (isset($ticker))
  <script src="{{asset('app-assets/vendors/js/marquee3000/marquee3k.js')}}" type="text/javascript"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      Marquee3k.init();
        $('body #app_content').on('click', function () {
            if($('#sidebar_menu').hasClass('is-active')){
                $.app.menu.hide();
            }
        });
    });
  </script>
@endif
<script type="text/javascript">

    $(document).ready(function() {
      
            $('#ShowAgreementModal').modal({
                backdrop: 'static',
                keyboard: false
             });
        $('body #app_content').on('click', function () {
            if($('#sidebar_menu').hasClass('is-active')){
                $.app.menu.hide();
            }
        });

        @if(Session::has('agreement_signed') && session('agreement_signed') != 1)
            $('#ShowAgreementModal').modal('show');

            // $('#peye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});
            // $('#cpeye').on('mousedown',function(){$('input[name="confirm_password"]').attr('type','text')}).on('mouseup',function(){$('input[name="confirm_password"]').attr('type','password')});

        $( "#agreement-form" ).validate({
            errorClass:"danger",
            normalizer: function(value) {
                return $.trim(value);
            },
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                // var new_password = $('#new_password').val();
                // var confirm_password = $('#confirm_password').val();
                // if(new_password === confirm_password){
                    swal({
                        title: 'Please Wait!',
                        text: 'Profile is being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                // }
                  // else{
                  //     var error = "The password and confirmation password do not match";
                  //     toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                  // }

            }
        });
        @endif
    });
</script>

  @yield('js')