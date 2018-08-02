<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-border"
     data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">


            <!-- official -->
            <li class=" nav-item"><a href="{{route('admin.dashboard')}}"><i class="la la-home"></i><span class="menu-title">Dashboard</span></a></li>
            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title">Order Management</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{route('admin.orders.index')}}">Order Management</a>
                    </li>
                    <li><a class="menu-item" href="/admin/sheet/receiving">Booked Receiving Sheets</a>
                    </li>

                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dispute</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('admin.dispute.index') }}">Log</a></li>
                </ul>
            </li>

            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Shipper Accounts</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="/admin/accounts/pending" data-i18n="nav.dash.ecommerce">Pending</a></li>
                    <li><a class="menu-item" href="/admin/accounts/active" data-i18n="nav.dash.ecommerce">Active</a></li>
                    <li><a class="menu-item" href="/admin/accounts/block" data-i18n="nav.dash.ecommerce">Block</a></li>
                </ul>
            </li>

            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title">Pickups</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('admin.pickups.pending.index') }}">Pending</a></li>
                    <li><a class="menu-item" href="{{ route('admin.pickups.assigned.index') }}">Assigned</a></li>
                    <li><a class="menu-item" href="{{ route('admin.pickups.receive.index') }}">Receive</a></li>
                </ul>
            </li>

            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title">Cargo</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('admin.cargo.pending.index') }}">Pending</a></li>
                    <li><a class="menu-item" href="{{ route('admin.cargo.create.index') }}">Create</a></li>
                    <li><a class="menu-item" href="{{ route('admin.cargo.in_transit.index') }}">In Transit</a></li>
                </ul>
            </li>

            <li class=" nav-item"><a href="{{route('admin.sameday.index')}}"><i class="la la-home"></i><span class="menu-title">Same-day Delivery</span></a></li>

            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Delivery</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('admin.delivery.pending.index') }}">Pending</a></li>
                    <li><a class="menu-item" href="{{ route('admin.delivery.note.index') }}">Create Note</a></li>
                    <li><a class="menu-item" href="{{ route('admin.delivery.receive.index') }}">Receive</a></li>
                    <li><a class="menu-item" href="{{ route('admin.delivery.completed.index') }}">Completed</a></li>
                    <li><a class="menu-item" href="{{ route('admin.delivery.sdn.index') }}">Station Deposit Notes</a></li>
                </ul>
            </li>

            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Return</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('admin.return.index') }}">Mark</a></li>
                    <li><a class="menu-item" href="{{ route('admin.return.confirmed') }}">Confirmed</a></li>
                    <li><a class="menu-item" href="{{ route('admin.return.create.index') }}">Create Note</a></li>
                    <li><a class="menu-item" href="{{ route('admin.return.receive.index') }}">Receive</a></li>
                </ul>
            </li>

            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title">Finance</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('admin.finance.outstanding_sdn.index') }}">Outstanding SDN</a></li>
                    <li><a class="menu-item" href="{{ route('admin.finance.outstanding_shipments.index') }}">Outstanding Shipments</a></li>
                    <li><a class="menu-item" href="{{ route('admin.finance.make_payments.index') }}">Make Payments</a></li>
                    <li><a class="menu-item" href="{{ route('admin.finance.done_payments.index') }}">Done Payments</a></li>
                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title">Reports</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('admin.reports.qsr.index') }}">QSR Report</a></li>
                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title">Packaging</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('admin.packaging.index') }}">Stock</a></li>
                    <li><a class="menu-item" href="{{ route('admin.packaging.requests.index') }}">Requests</a></li>
                </ul>
            </li>

            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Network Management</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{route('admin.management.city.index')}}" data-i18n="nav.dash.ecommerce">City Management</a></li>
                    <li><a class="menu-item" href="{{route('admin.management.route.index')}}" data-i18n="nav.dash.ecommerce">Route Management</a></li>
                    <li><a class="menu-item" href="{{route('admin.management.rider.index')}}" data-i18n="nav.dash.ecommerce">Rider Management</a></li>

                </ul>
            </li>

            <li class=" nav-item"><a href="{{ route('admin.tracking.index') }}"><i class="la la-home"></i><span class="menu-title">Tracking</span></a></li>

            <li class=" nav-item"><a href="{{ route('admin.notifications.index') }}"><i class="la la-home"></i><span class="menu-title">Notifications</span></a></li>

            <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Settings</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{route('admin.settings.pickup.index')}}" data-i18n="nav.dash.ecommerce">Pickup Weight Settings</a></li>

                </ul>
            </li>
        </ul>
    </div>
</div>