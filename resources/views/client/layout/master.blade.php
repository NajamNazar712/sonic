<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
  @include('client.layout.header')
</head>
<body class="vertical-layout vertical-overlay-menu 2-columns   menu-expanded fixed-navbar"
data-open="click" data-menu="vertical-overlay-menu" data-col="2-columns">
  <!-- fixed-top-->
  @include('client.layout.navbar')
  <!-- ////////////////////////////////////////////////////////////////////////////-->
  @include('client.layout.sidebar')
  <div class="app-content content" id="app_content">
    <div class="content-wrapper">
      @if (isset($ticker))
          <div class="marquee3k" data-speed="0.25" data-pausable="bool">
          <span>{{ $ticker }}</span>
        </div>
      @endif

      <div class="content-body">
        @yield('content')
      </div>
    </div>
  </div>
  @include('client.layout.footer')
</body>
</html>