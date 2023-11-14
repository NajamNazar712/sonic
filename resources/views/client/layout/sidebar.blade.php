<div class="main-menu menu-fixed menu-light menu-accordion menu-bordered menu-shadow"
     data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{--<li class=" nav-item"><a href="{{route('cod.mentor_health.index')}}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-plus-circle"></i>TRAX Health</span><span class="font-weight-bold">Powered by Mentor Health</span></a></li>--}}

            <li class=" nav-item"><a href="{{route('cod.orders.index')}}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-bar-chart-o"></i>Order Management</span></a></li>

            <li class=" nav-item"><a href="{{route('cod.quick_search.index')}}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-search"></i>Quick Search</span></a></li>

            @if (session('user_type') == 1 || count(array_intersect([1, 3], session('permissions'))) !== 0)
                <li class="nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cart-plus"></i>Bookings</span></a>
                    <ul class="menu-content">
                        <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Book</span></a>
                            <ul class="menu-content">
                                @if (session('user_type') == 1 || in_array(1, session('permissions')))
                                    @if (session('account_type') == 1 && session('rate_status') == 1)
                                        <li><a class="menu-item" href="{{ route('cod.shipment.book.index') }}">Order Form</a></li>
                                        <li><a class="menu-item" href="{{ route('cod.shipment.book.excel_index') }}">Excel Sheet</a></li>
                                    @elseif (session('account_type') == 2 && session('rate_status') == 1)
                                        <li><a class="menu-item" href="{{ route('cod.shipment.book.corporate.index') }}">Order Form</a></li>
                                        @if(session('user_id') == 10354)
                                            <li><a class="menu-item" href="{{ route('cod.shipment.book.corporate_excel_distribution') }}">Excel Sheet</a></li>
                                        @else
                                            <li><a class="menu-item" href="{{ route('cod.shipment.book.corporate_excel_index') }}">Excel Sheet</a></li>
                                        @endif
                                    @endif
                                    @if(session('international_rates') == 1)
                                        <li><a class="menu-item" href="{{ route('cod.shipment.book.international.index') }}">International Order Form</a></li>
                                        <li><a class="menu-item" href="{{ route('cod.shipment.book.international.excel_index') }}">International Excel Sheet</a></li>
                                    @endif
                                @endif
                            </ul>
                        </li>
                        @if (session('user_type') == 1 || in_array(3, session('permissions')))
                            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Receiving Sheet</span></a>
                                <ul class="menu-content">
                                    <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.index') }}">Create</a></li>
                                    <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.new') }}">Create By Scan</a></li>
                                    <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.excel.index') }}">Excel</a></li>
                                    <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet_history.index') }}">History</a></li>
                                    <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.shipments.index') }}">Shipments</a></li>
                                </ul>
                            </li>
                        @endif
                        @if (Session::has('air_waybill_type') && session('air_waybill_type') == 3)
                            <li class=" nav-item"><a href="{{ route('cod.shipment.list.index') }}"><span class="menu-title" data-i18n="nav.dash.main">List</span></a></li>
                            <li class=" nav-item"><a href="{{ route('cod.shipment.verify.index') }}"><span class="menu-title" data-i18n="nav.dash.main">Verify</span></a></li>
                        @endif

                        <li><a class="menu-item" href="{{ route('cod.cancelled_shipments.index') }}">Cancelled</a></li>

{{--                        @if(session('user_id') == 3324)--}}
{{--                            <li><a class="menu-item" href="{{ route('cod.shipment.telenor.other_courier.index') }}">Other Courier Shipments</a></li>--}}
{{--                        @endif--}}
                        @if(session('shipper_origin_change') == 1)
                            <li><a class="menu-item" href="{{ route('cod.shipment.origin.index') }}">Origin Change</a></li>
                        @endif
                        @if(session('shipper_return_address') == 1)
                            <li><a class="menu-item" href="{{ route('cod.shipment.book.return_address.excel.index') }}">Return Address</a></li>
                        @endif

                        {{--						<li><a class="menu-item" href="{{ route('cod.consolidation.history.index') }}">Consolidation History</a></li>--}}
                    </ul>
                </li>
            @endif

            @if (session('user_type') == 1)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-list-ul"></i>Multiple Piece</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.multiple_pieces.index') }}">Pending</a></li>
                        <li><a class="menu-item" href="{{ route('cod.multiple_pieces.resolved.index') }}">Resolved</a></li>
                    </ul>
                </li>
            @endif

            @if (session('user_type') == 1 && session('user_id') != 7762)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-list-ul"></i>Rates</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.rates.view.index') }}">View</a></li>
                    </ul>
                </li>
            @endif

            @if (session('user_type') == 1 || in_array(9, session('permissions')))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-rotate-left"></i>Return</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.return.pending.index') }}">Shipper Advise Requested</a></li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('cod.return.reattempt_history.index') }}">Re-Attempt Request</a></li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{route('cod.return.confirmed.index')}}">Confirmed</a></li>
                    </ul>
                    @if(session('shipment_return_address_change') == 1)
                        <ul class="menu-content">
                            <li><a class="menu-item" href="{{ route('cod.shipment.return_address_change.index') }}">Return Address Change</a></li>
                        </ul>
                    @endif
                    <ul class="menu-content">
                        <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Return Sheet</span></a>
                            <ul class="menu-content">
                                <li><a class="menu-item" href="{{ route('cod.return.sheet.pending.index') }}">Pending</a></li>
                                <li><a class="menu-item" href="{{ route('cod.return.sheet.receive.index') }}">Receive</a></li>
                                <li><a class="menu-item" href="{{ route('cod.return.sheet.history.index') }}">History</a></li>
                            </ul>
                        </li>
                    </ul>

                </li>
            @endif

            @if(session('packaging_charges_check') && (session('user_type') == 1 || in_array(4, session('permissions'))))
                <li class=" nav-item"><a href="{{ route('cod.packaging.requests.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-sticky-note-o"></i>Packaging Requests</span></a></li>
            @endif

            @if (session('user_type') == 1 || in_array(12, session('permissions')))
                <li class=" nav-item"><a href="{{ route('cod.pickup.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cubes"></i>Pickup</span></a></li>
            @endif

            @if (session('user_type') == 1 || in_array(5, session('permissions')))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-money"></i>Finance</span></a>
                    <ul class="menu-content">
                        <li><a href="{{ route('cod.finance.payments.index') }}">Payments</a></li>
                        <li><a href="{{ route('cod.finance.payments.reconcile_through_receiving_sheet.index') }}">Payments Reconcile through Receiving Sheet</a></li>
                        <li><a href="{{ route('cod.ledger') }}">General Ledger</a></li>
                        @if (session('user_type') == 1 || in_array(14, session('permissions')))
                            <li><a href="{{ route('cod.finance.invoice.index') }}">Invoice</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            {{--<li class=" nav-item"><a href="https://form.jotform.me/81993400128456" target="_blank"><span class="menu-title" data-i18n="nav.dash.main">Complain Form</span></a></li>--}}
            @if (session('user_type') == 1 || count(array_intersect([8, 15], session('permissions'))) !== 0)

                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-check-square-o"></i>Reports</span></a>
                    <ul class="menu-content">
                        @if (session('user_id') == 3324)
                            <li><a class="menu-item" href="{{ route('cod.reports.sales.telenor.index') }}">Overall Sales - Telenor</a></li>
                        @endif

                        <li><a class="menu-item" href="{{ route('cod.reports.sales.index') }}">Overall Sales</a></li>
                        <li><a class="menu-item" href="{{ route('cod.reports.summary.index') }}">Summary</a></li>
                        <li><a class="menu-item" href="{{ route('cod.reports.adjustments.index') }}">Adjustments</a></li>
                        <li><a class="menu-item" href="{{ route('cod.reports.weight_reconciliation.index') }}">Weight Reconciliation</a></li>
                        <li><a class="menu-item" href="{{ route('cod.reports.confirmation_pending_report.index') }}">Confirmation Pending Shipment</a></li>
                        <li><a class="menu-item" href="{{ route('cod.reports.rider_pickup.index') }}">Rider Wise Pickup</a></li>
                        @if(session('project_arrival_shipper'))
                            <li><a class="menu-item" href="{{ route('cod.reports.project_arrival.index') }}">Project Arrival</a></li>
                        @endif

                        @if (in_array(session('user_id'), [7762, 167, 1159, 2035]))
                            <li><a class="menu-item" href="{{ route('cod.reports.delivery_and_return.index') }}">Delivery & Return</a></li>
                        @endif
                        @if (session('permissions') != null)
                            @if (Auth::id() == 7306)
                                <li><a class="menu-item" href="{{ route('cod.reports.daraz_mis.index') }}">Daraz MIS</a></li>
                            @elseif (in_array(15, session('permissions')))
                                <li><a class="menu-item" href="{{ route('cod.reports.daraz_mis.index') }}">Daraz MIS</a></li>
                            @endif
                        @else
                            @if (Auth::id() == 7306)
                                <li><a class="menu-item" href="{{ route('cod.reports.daraz_mis.index') }}">Daraz MIS</a></li>
                            @endif
                        @endif
                        @if(session('mms_shippers') != null)
                            @if (in_array(session('user_id'), session('mms_shippers')))
                                <li><a class="menu-item" href="{{ route('cod.reports.mms.index') }}">MMS Report</a></li>
                            @endif
                        @endif

                    </ul>
                </li>
            @elseif(session('special_dashboard_user'))
                @if(session('mms_shippers') != null)
                    @if(in_array(session('user_id'), session('mms_shippers')))
                        <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-check-square-o"></i>Reports</span></a>
                            <ul class="menu-content">
                                    <li><a class="menu-item" href="{{ route('cod.reports.special_dashboard.index') }}">MMS Report</a></li>
                            </ul>
                        </li>
                    @endif
                @endif
            @endif
            @if (session('user_type') == 1 || session('special_dashboard_user') || in_array(10, session('permissions')))
                <li class=" nav-item"><a href="{{ route('cod.crm.request.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-commenting-o"></i>Requests</span></a></li>
            @endif

            @if (session('user_type') == 1 || in_array(19, session('permissions')))
                <li class=" nav-item"><a href="{{ route('cod.crm.bulk_claim.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-commenting-o"></i>Bulk Claim Logging</span></a></li>
            @endif

            @if (session('user_type') == 1)
                <li class=" nav-item"><a href="{{ route('cod.substitute_account_management.index') }}"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-users"></i>Substitute Accounts</span></a></li>
            @endif

            @if (session('user_type') == 1 || count(array_intersect([11], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cogs"></i>Settings</span></a>
                    <ul class="menu-content">
                        @if(session('user_type') == 1 || in_array(11, session('permissions')))
                            <li><a class="menu-item" href="{{route('cod.settings.air_waybill_printing.index')}}">Air Waybill information</a></li>
                            <li><a class="menu-item" href="{{route('cod.settings.logo.index')}}">Logo</a></li>
                        @endif
                        @if(session('pickup_wise_account'))
                            <li><a class="menu-item" href="{{route('cod.settings.shipping_information.index')}}">IBAN</a></li>
                        @endif
                        @if(session('user_type') == 1)
                            <li><a class="menu-item" href="{{route('cod.settings.subscription.index')}}">Shipment Status Subscription</a></li>
                        @endif
                        @if(session('user_type') == 1)
                            <li><a class="menu-item" href="{{route('cod.settings.payment_subscription.index')}}">Payment Status Subscription</a></li>
                        @endif
                        @if(session('user_type') == 1)
                            <li><a class="menu-item" href="{{route('cod.settings.initial_charges_subscription.index')}}">Initial Charges Subscription</a></li>
                        @endif
                        @if(session('user_type') == 1)
                            <li><a class="menu-item" href="{{route('cod.settings.final_charges_subscription.index')}}">Final Charges Subscription</a></li>
                        @endif

                        @if(session('user_type') == 1)
                            <li><a class="menu-item" href="{{route('cod.settings.receiving_sheet.index')}}">Receiving Sheet (Shipment Description)</a></li>
                        @endif
                        @if(session('user_type') == 1)
                            <li><a class="menu-item" href="{{ route('cod.cancelled_shipments_arrival.cancelled_shipments_arrival_index') }}">Restrict Cancelled Shipment Arrival</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            <li class=" nav-item"><a href="{{ route('cod.tracking.index') }}"><span class="menu-title"><i class="la la-crosshairs"></i>Tracking</span></a></li>

        </ul>
    </div>
</div>