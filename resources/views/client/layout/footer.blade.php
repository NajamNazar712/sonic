<footer class="footer footer-static footer-light navbar-border navbar-shadow">
  <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
      <span class="float-md-left d-block d-md-inline-block"><i class="ft-phone-call align-middle mr-1"></i><span class="align-middle">0213-877-22-22</span></span>
    <span class="float-md-right d-block d-md-inline-blockd-none d-lg-block">Copyright &copy; {{ now()->year }} By <a class="text-bold-800 grey darken-2" href="#">Trax Logistics </a>, All Rights Reserved.</span>
  </p>
</footer>
<!-- BEGIN VENDOR JS-->
  <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
  <!-- BEGIN VENDOR JS-->
<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>
  <!-- BEGIN MODERN JS-->
  <script src="{{asset('app-assets/js/core/app-menu.js')}}" type="text/javascript"></script>
  <script src="{{asset('app-assets/js/core/app.js')}}" type="text/javascript"></script>
  <!-- END MODERN JS-->

@if (isset($ticker))
  <script src="{{asset('app-assets/vendors/js/marquee3000/marquee3k.js')}}" type="text/javascript"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      Marquee3k.init();
    });
  </script>
@endif

  @yield('js')