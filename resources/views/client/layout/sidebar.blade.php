<div class="main-menu menu-fixed menu-light menu-accordion menu-bordered menu-shadow"
     data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a href="{{route('cod.orders.index')}}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-bar-chart-o"></i>Order Management</span></a></li>

            @if (session('user_type') == 1 || count(array_intersect([1, 3], session('permissions'))) !== 0)
                <li class="nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cart-plus"></i>Bookings</span></a>
                    <ul class="menu-content">
                        <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Book</span></a>
                        <ul class="menu-content">
                        @if (session('user_type') == 1 || in_array(1, session('permissions')))
                            @if (session('account_type') == 1)
                                <li><a class="menu-item" href="{{ route('cod.shipment.book.index') }}">Order Form</a></li>
                                <li><a class="menu-item" href="{{ route('cod.shipment.book.excel_index') }}">Excel Sheet</a></li>
                            @elseif (session('account_type') == 2)
                                <li><a class="menu-item" href="{{ route('cod.shipment.book.corporate.index') }}">Order Form</a></li>
                                <li><a class="menu-item" href="{{ route('cod.shipment.book.corporate_excel_index') }}">Excel Sheet</a></li>
                            @endif
                        @endif
                        </ul>
                        </li>
                        <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Receiving Sheet</span></a>
                            <ul class="menu-content">
                        @if (session('user_type') == 1 || in_array(3, session('permissions')))
                            <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.new') }}">Create</a></li>
                            <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.index') }}">Create by scan</a></li>
                            <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet_history.index') }}">History</a></li>
                        @endif
                            </ul>
                        </li>
                    </ul>
                </li>
            @endif

            @if (session('user_type') == 1 || in_array(9, session('permissions')))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-rotate-left"></i>Return</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.return.pending.index') }}">Confirmation Pending</a></li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.return.reattempt_history.index') }}">Re-Attempt Request</a></li>
                    </ul>
                </li>
            @endif
            @if (session('user_type') == 1)
                <li class=" nav-item"><a href="{{ route('cod.cancelled_shipments.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-times"></i>Cancelled Shipments</span></a></li>
            @endif
            @if(session('packaging_charges_check') && (session('user_type') == 1 || in_array(4, session('permissions'))))
                <li class=" nav-item"><a href="{{ route('cod.packaging.requests.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-sticky-note-o"></i>Packaging Requests</span></a></li>
            @endif

            @if (session('user_type') == 1 || in_array(5, session('permissions')))
                <li class=" nav-item"><a href="{{ route('cod.finance.payments.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-money"></i>Finance Payments</span></a></li>
            @endif

            {{--<li class=" nav-item"><a href="https://form.jotform.me/81993400128456" target="_blank"><span class="menu-title" data-i18n="nav.dash.main">Complain Form</span></a></li>--}}
            @if (session('user_type') == 1 || in_array(8, session('permissions')))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-check-square-o"></i>Reports</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.reports.sales.index') }}">Overall Sales</a></li>
                        <li><a class="menu-item" href="{{ route('cod.reports.summary.index') }}">Summary</a></li>
                    </ul>
                </li>
            @endif
            <li class=" nav-item"><a href="{{ route('cod.crm.request.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-commenting-o"></i>Requests</span></a></li>

            <li class=" nav-item"><a href="https://form.jotform.me/83261529672462" target="_blank"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-file-o"></i>Claims Form</span></a></li>

            @if (session('user_type') == 1)
                <li class=" nav-item"><a href="{{ route('cod.substitute_account_management.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-users"></i>Substitute Accounts</span></a></li>
            @endif


            <li class=" nav-item"><a href="{{ route('cod.tracking.index') }}"><span class="menu-title"><i class="la la-crosshairs"></i>Tracking</span></a></li>

            @if (session('user_type') == 1 || in_array(9, session('permissions')))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cogs"></i>Settings</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{route('cod.settings.air_waybill_printing.index')}}">Air Waybill Print Count</a></li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>