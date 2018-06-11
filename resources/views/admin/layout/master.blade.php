<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    @include('admin.layout.header')
</head>
<body class="vertical-layout vertical-overlay-menu 2-columns   menu-expanded fixed-navbar"
      data-open="click" data-menu="vertical-overlay-menu" data-col="2-columns">
<!-- fixed-top-->
@include('admin.layout.navbar')
<!-- ////////////////////////////////////////////////////////////////////////////-->
@include('admin.layout.sidebar')
<div class="app-content content">
    <div class="content-wrapper">

        <div class="content-body">
            @yield('content')

        </div>
    </div>
</div>
@include('admin.components.modals')
@include('admin.layout.footer')

</body>
</html>