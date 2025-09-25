<div class="main-menu menu-fixed menu-light menu-accordion menu-bordered menu-shadow"
     data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
{{--            <li class=" nav-item"><a href="{{route('admin.dashboard.index')}}"><i class="la la-area-chart"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span></a>--}}
{{--            </li>--}}

            <li class="nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cart-plus"></i>Bookings</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('retail.shipment.book.index') }}">Order Form</a></li>
                    <li><a class="menu-item" href="{{ route('retail.shipment.book.excel') }}">Excel Sheet</a></li>
                </ul>
            </li>
            <li><a class="menu-item" href="{{route('retail.parcel_receiving.other_parcel')}}"><i class="la la-dropbox"></i>Other Parcels</a></li>
            <li><a class="menu-item" href="{{route('retail.arrival_service.service.index')}}"><i class="la la-dropbox"></i>Arrival Service Center</a></li>
            <li><a class="menu-item" href="{{route('retail.parcel_receiving.index')}}"><i class="la la-dropbox"></i>Parcel Receiving</a></li>
            <li class="menu-item"><a href="{{ route('retail.cancel_shipments.index') }}"><i class="la la-trash"></i>Cancelled</a>
            </li>
 			<li><a class="menu-item" href="{{route('retail.parcel_receiving.other_index')}}"><i class="la la-dropbox"></i>Other Parcel Receiving</a></li>
            <li><a class="menu-item" href="{{route('retail.cash_deposit.index')}}"><i class="la la-money"></i>Cash Deposit</a></li>
            <li><a class="menu-item" href="{{route('retail.shipment.tracking_slip.index')}}"><i class="la la-image"></i>Tracking Slip</a></li>

            <li>
                <a class="menu-item" href="{{route('retail.retail_commission.index')}}">
                    <i class="la la-file-text-o"></i>
                    Retail Commission View
                </a>
            </li>

            @if (session('category') == 2)
                <li><a class="menu-item" href="{{route('retail.shipment.other_booking.index')}}"><i class="la la-cubes"></i>Other Bookings</a></li>
            @endif
            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-rotate-left"></i>Return</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('retail.return.confirmation_pending.index') }}">Confirmation Pending</a></li>
                </ul>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('retail.return.reattempt_history.index') }}">Re-Attempt Request</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>