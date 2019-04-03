<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-border"
     data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a href="{{route('cod.orders.index')}}"><span class="menu-title" data-i18n="nav.dash.main">Order Management</span></a></li>

            @if (session('user_type') == 1 || count(array_intersect([1, 3], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Book Shipments</span></a>
                    <ul class="menu-content">
                        @if (session('user_type') == 1 || in_array(1, session('permissions')))
                            @if (session('account_type') == 1)
                                <li><a class="menu-item" href="{{ route('cod.shipment.book.index') }}">Book by Order Form</a></li>
                                <li><a class="menu-item" href="{{ route('cod.shipment.book.excel_index') }}">Book by Excel Sheet</a></li>
                            @elseif (session('account_type') == 2)
                                <li><a class="menu-item" href="{{ route('cod.shipment.book.corporate.index') }}">Book by Order Form</a></li>
                                <li><a class="menu-item" href="{{ route('cod.shipment.book.corporate_excel_index') }}">Book by Excel Sheet</a></li>
                            @endif
                        @endif

                        @if (session('user_type') == 1 || in_array(3, session('permissions')))
                            <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.new') }}">Create Receiving Sheet</a></li>
                            <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.index') }}">Receiving Sheet</a></li>
                            <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet_history.index') }}">Receiving Sheet History</a></li>
                        @endif
                    </ul>
                </li>
            @endif
            @if (session('user_type') == 1 || in_array(9, session('permissions')))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Return</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.return.pending.index') }}">Confirmation Pending</a></li>
                    </ul>
                </li>
            @endif
            @if(session('packaging_charges_check') && (session('user_type') == 1 || in_array(4, session('permissions'))))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Packaging Material</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.packaging.requests.index') }}">Request</a></li>
                    </ul>
                </li>
            @endif

            @if (session('user_type') == 1 || in_array(5, session('permissions')))
                <li class=" nav-item"><a href="{{ route('cod.finance.payments.index') }}"><span class="menu-title" data-i18n="nav.dash.main">Finance Payments</span></a></li>
            @endif

            <li class=" nav-item"><a href="https://form.jotform.me/81993400128456" target="_blank"><span class="menu-title" data-i18n="nav.dash.main">Complain Form</span></a></li>

            <li class=" nav-item"><a href="https://form.jotform.me/83261529672462" target="_blank"><span class="menu-title" data-i18n="nav.dash.main">Claims Form</span></a></li>

            @if (session('user_type') == 1)
                <li class=" nav-item"><a href="{{ route('cod.substitute_account_management.index') }}"><span class="menu-title" data-i18n="nav.dash.main">Substitute Accounts</span></a></li>
            @endif

            @if (session('user_type') == 1 || in_array(8, session('permissions')))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Reports</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.reports.sales.index') }}">Overall Sales</a></li>
                        <li><a class="menu-item" href="{{ route('cod.reports.summary.index') }}">Summary</a></li>
                    </ul>
                </li>
            @endif
            <li class=" nav-item"><a href="{{ route('cod.crm.request.index') }}"><span class="menu-title" data-i18n="nav.dash.main">Requests</span></a></li>
            @if (session('user_type') == 1)
                <li class=" nav-item"><a href="{{ route('cod.cancelled_shipments.index') }}"><span class="menu-title" data-i18n="nav.dash.main">Cancelled Shipments</span></a></li>
            @endif

            <li class=" nav-item"><a href="{{ route('cod.tracking.index') }}"><span class="menu-title">Tracking</span></a></li>
        </ul>
    </div>
</div>