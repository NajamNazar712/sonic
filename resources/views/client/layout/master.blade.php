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
  @yield('content')
  @include('client.layout.footer')
</body>
</html>