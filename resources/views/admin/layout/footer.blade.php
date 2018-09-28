<footer class="footer footer-static footer-light navbar-border navbar-shadow">
    <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
        <span class="float-md-left d-block d-md-inline-block">Copyright &copy; 2018 By <a class="text-bold-800 grey darken-2" href="#">Trax Logistics </a>, All Rights Reserved. </span>
        <span class="float-md-right d-block d-md-inline-blockd-none d-lg-block">Made with <i class="ft-heart pink"></i></span>
    </p>
</footer>
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
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
<script src="{{asset('js/app.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL JS-->

  @yield('js')