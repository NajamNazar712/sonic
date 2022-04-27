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





@if(Session::has('agreement_signed') && session('agreement_signed') != 1)
    <script src="{{asset('szimek-signature_pad/signature.min.js')}}" type="text/javascript"></script>
@endif
<script type="text/javascript">

    $(document).ready(function() {
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/5d8323ab9f6b7a4457e2756b/default';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();

        $('body #app_content').on('click', function () {
            if($('#sidebar_menu').hasClass('is-active')){
                $.app.menu.hide();
            }
        });

        @if(Session::has('agreement_signed') && session('agreement_signed') != 1)

        canvas = document.getElementById('e-sign-canvas');

        var signaturePad = new SignaturePad(canvas,{
            backgroundColor: 'rgb(248,248,248)',
        });
        $.ajax({
            url: '{!! route('cod.get_agreement') !!}',
            method: 'POST',
            data: {
                'id': '{{session('user_id')}}',
                '_token': '{{ csrf_token() }}'
            }
        })
        .done(function (data) {
            $('#ShowAgreementModal #crf_agreement').html(data);
            $('#ShowAgreementModal').modal('show');
        });


        $("#agreement-form #agreement_signed").on('click',function(){
            if($(this).prop('checked'))
            {
                $('#SignatureModal input[type=file]').val('');
                $('#ShowAgreementModal').modal('hide');
                $('#SignatureModal').modal('show');
            }
        });

        $("#SignatureModal #save_signature_btn").on('click',function(){
            if(signaturePad.isEmpty())
            {
                toastr.error("Signature is Required", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }
            else{
                var preview = document.querySelector('#agreement-form #esign_image');
                img = signaturePad.toDataURL();
                preview.src = img;
                $("#agreement-form #esign").val(img);
                $('#SignatureModal').modal('hide');
                $('#ShowAgreementModal').modal('show');
            }
        });

        $("#SignatureModal #clear_signature_btn").on('click',function(){
            signaturePad.clear();
        });

        $("#SignatureModal #upload_img_btn").on('click',function(){
            $("#SignatureModal #upload_e_sign").trigger('click');
        });


        $("#SignatureModal #upload_e_sign").on('change',function(){
            var allowedExtension = ['jpeg', 'jpg','png'];
            var fileExtension = document.querySelector('#SignatureModal input[type=file]').value.split('.').pop().toLowerCase();
            var isValidFile = false;
            for(var index in allowedExtension) {

                if(fileExtension === allowedExtension[index]) {
                    isValidFile = true;
                    break;
                }
            }
            if(isValidFile) {
                var preview = document.querySelector('#agreement-form #esign_image');
                var file = document.querySelector('#SignatureModal input[type=file]').files[0];
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    preview.src = reader.result;
                    $("#agreement-form #esign").val(reader.result);
                    $('#SignatureModal').modal('hide');
                    $('#ShowAgreementModal').modal('show');
                }, false);

                if (file) {
                    reader.readAsDataURL(file);
                }
            }
            else{
                toastr.error("Please select valid image", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }
        });

        $( "#agreement-form" ).validate({
            ignore: [],
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



        @if(isset($visit) && $visit)
            $("#DailyVisitRateModal").modal('show');
            $('.item label').tooltip({
                placement : 'top'
            });
            $("#skip_daily_visit_btn").on('click',function (e){
                $("#DailyVisitRateModal #DailyVisitRateForm #action_id").val(1);
                $("#DailyVisitRateModal #DailyVisitRateForm").submit();
            });

            $("#rate_daily_visit_btn").on('click',function (e){
                if(!$("#DailyVisitRateForm .feedback .radio").is(':checked'))
                {
                    toastr.error("Please Select Rating", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    return;
                }
                $("#DailyVisitRateModal #DailyVisitRateForm #action_id").val(2);
                $("#DailyVisitRateModal #DailyVisitRateForm").submit();
            });
        @endif
    });
</script>

  @yield('js')