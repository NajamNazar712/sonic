<div class="main-menu menu-fixed menu-light menu-accordion menu-bordered menu-shadow"
     data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
{{--            <li class=" nav-item"><a href="{{route('admin.dashboard.index')}}"><i class="la la-area-chart"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span></a>--}}
{{--            </li>--}}
            <li><a class="menu-item" href="{{route('retail.shipment.book.index')}}"><i class="la la-cart-plus"></i>Booking</a></li>
            <li><a class="menu-item" href="{{route('retail.parcel_receiving.index')}}"><i class="la la-dropbox"></i>Parcel Receiving</a></li>
            <li><a class="menu-item" href="{{route('retail.cash_deposit.index')}}"><i class="la la-money"></i>Cash Deposit</a></li>
        </ul>
    </div>
</div>