<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-border"
     data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a href="{{route('admin.orders.index')}}"><span class="menu-title" data-i18n="nav.dash.main">Order Management</span></a></li>

            @if (session('role_id') == 1 || in_array(1, session('permissions')))
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Dispute</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="{{ route('admin.dispute.index') }}">Log</a></li>
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([5, 11, 15], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Shipper Accounts</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(5, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.accounts.pending')}}">Pending</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(11, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.accounts.active')}}">Active</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(15, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.accounts.block')}}">Block</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([17, 20, 23], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title">Pickups</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(17, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.pickups.pending.index') }}">Pending</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(20, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.pickups.assigned.index') }}">Assigned</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(23, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.pickups.receive.index') }}">Receive</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(113, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.pickups.bookedvsreceived.index') }}">Booked VS Received</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(123, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.pickups.history.index') }}">History</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([25, 26, 27], session('permissions'))) !== 0)
            <li class=" nav-item"><a href="#"><span class="menu-title">Cargo</span></a>
                <ul class="menu-content">
                    @if (session('role_id') == 1 || in_array(25, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.cargo.pending.index') }}">Pending</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(26, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.cargo.create.index') }}">Create</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(26, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.cargo.draft.index') }}">Draft</a>
                    @endif

                    @if (session('role_id') == 1 || in_array(27, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.cargo.in_transit.index') }}">In Transit</a>
                    @endif
                    @if (session('role_id') == 1 || in_array(124, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.cargo.history.index') }}">History</a>
                    @endif
                    </li>
                </ul>
            </li>
            @endif

            @if (session('role_id') == 1 || in_array(32, session('permissions')))
                <li class=" nav-item"><a href="{{route('admin.sameday.index')}}"><span class="menu-title">Same-Day Delivery</span></a></li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([33, 35, 36, 40, 105, 107], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Delivery</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(33, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.delivery.pending.index') }}">Pending</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(35, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.delivery.note.index') }}">Create Note</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(36, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.delivery.receive.index') }}">Receive</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(105, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.delivery.cash_collection.pending.index') }}">Pending Cash Collection</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(40, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.delivery.completed.index') }}">Completed</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(42, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.delivery.sdn.index') }}">Station Deposit Notes</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(125, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.delivery.history.index') }}">History</a></li>
                        @endif

                            @if (session('role_id') == 1 || count(array_intersect([107,108,119], session('permissions'))) !== 0)
                                <li class=" nav-item"><a href="#"><span class="menu-title">Misroute</span></a>

                                    <ul class="menu-content">
                                        @if (session('role_id') == 1 || in_array(107, session('permissions')))
                                            <li><a class="menu-item" href="{{route('admin.delivery.misroute.index')}}">Shipments</a></li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(108, session('permissions')))
                                            <li><a class="menu-item" href="{{ route('admin.delivery.misroute.update.index') }}">Update</a></li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(119, session('permissions')))
                                            <li><a class="menu-item" href="{{ route('admin.delivery.misroute.history.index') }}">History</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif


                        @if (session('role_id') == 1 || count(array_intersect([127,130], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Lost</span></a>

                                    <ul class="menu-content">
                                        @if (session('role_id') == 1 || in_array(127, session('permissions')))
                                            <li><a class="menu-item" href="{{route('admin.delivery.lost.index')}}">Shipments</a></li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(130, session('permissions')))
                                            <li><a class="menu-item" href="{{route('admin.delivery.lost.add.index')}}">Add Shipments</a></li>
                                        @endif
                                    </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([44, 47, 48, 49], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Return</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(44, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.return.index') }}">Confirmation Pending</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(47, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.return.confirmed') }}">Confirmed</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(48, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.return.create.index') }}">Create Note</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(49, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.return.receive.index') }}">Receive</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(126, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.return.history.index') }}">History</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([52, 54, 57, 59, 61, 120, 121, 122, 134, 136, 145, 146, 147], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title">Finance</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(57, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.finance.change_shipment_amount.index') }}">Change Shipment Amount</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(134, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.finance.change_shipment_weight.index') }}">Change Shipment Weight</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(136, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.finance.add_shipment_adjustment.index') }}">Add Shipment Adjustment</a></li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([52, 54], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Outstanding</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(52, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.finance.outstanding_sdn.index') }}">SDN</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(54, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.finance.outstanding_shipments.index') }}">Shipments</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([59, 61], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Payments</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(59, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.finance.make_payments.index') }}">Make</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(61, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.finance.done_payments.index') }}">Done</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([120, 121, 122], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Invoices</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(120, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.finance.generate_invoices.index') }}">Generate</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(122, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.finance.invoices_history.index') }}">History</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([145, 146, 147], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Petty Cash</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(145, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.petty_cash.make.index') }}">Make</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(146, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.petty_cash.statements.index') }}">Statements</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(147, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.petty_cash.approved.index') }}">Approved</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif



            @if (session('role_id') == 1 || in_array(141, session('permissions')))
                <li class=" nav-item"><a href={{ route('admin.month_closing.index') }}><span class="menu-title">Month Closing</span></a>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title">Reports</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(64, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.pickup_note.index') }}">Pickup Notes Completed</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(65, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.cargo_received.index') }}">Cargo Received</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(66, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.reports.completed_delivery_notes.index')}}">Completed Delivery Notes</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(67, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.return_note.index') }}">Return Notes Completed</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(68, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.outstanding_shipments.index') }}">Outstanding Shipments</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(69, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.lead_time.index') }}">Lead Time</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(70, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.qsr.index') }}">Quality of Service</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(71, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.qa.index') }}">Quality Assurance</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(72, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.reports.customer_retention.index')}}">Customer Retention Rate</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(73, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.daily_pickup_sales.index') }}">Daily Pickup and Sales</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(74, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.customer_sales.index') }}">Monthwise Customer Sales</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(75, session('permissions')))
                                <li><a class="menu-item" href="{{ route('admin.reports.overall_sales.index') }}">Overall Sales</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(138, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.sales_person_performance.index') }}">Sales Person Performance</a></li>
                        @endif
                            @if (session('role_id') == 1 || in_array(148, session('permissions')))
                                <li><a class="menu-item" href="{{ route('admin.reports.negative_balance_customers.index') }}">Negative Balance Customers</a></li>
                            @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([76, 79], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title">Packaging</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(76, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.packaging.index') }}">Stock</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(79, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.packaging.requests.index') }}">Requests</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([81, 85], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title">User Management</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(81, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.user_management.users.index') }}">Users</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(85, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.user_management.roles.index') }}">Roles</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([88, 92, 96, 131], session('permissions'))) !== 0)
            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Network Management</span></a>
                <ul class="menu-content">
                    @if (session('role_id') == 1 || in_array(131, session('permissions')))
                        <li><a class="menu-item" href="{{route('admin.management.zonal.index')}}">Zonal Management</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(88, session('permissions')))
                        <li><a class="menu-item" href="{{route('admin.management.city.index')}}">City Management</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(92, session('permissions')))
                        <li><a class="menu-item" href="{{route('admin.management.route.index')}}">Route Management</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(96, session('permissions')))
                        <li><a class="menu-item" href="{{route('admin.management.rider.index')}}">Rider Management</a></li>
                    @endif

                </ul>
            </li>
            @endif

            <li class=" nav-item"><a href="{{ route('admin.tracking.index') }}"><span class="menu-title">Tracking</span></a></li>

            @if (session('role_id') == 1 || in_array(117, session('permissions')))
                <li class=" nav-item"><a href="{{ route('admin.cancelled_shipments.index') }}"><span class="menu-title">Cancelled Shipments</span></a></li>
            @endif

            @if (session('role_id') == 1 || in_array(100, session('permissions')))
                <li class=" nav-item"><a href="{{ route('admin.notifications.index') }}"><span class="menu-title">Notifications</span></a></li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([104, 116, 149, 150, 151, 152], session('permissions'))) !== 0)
            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Settings</span></a>
                <ul class="menu-content">
                    @if (session('role_id') == 1 || in_array(104, session('permissions')))
                        <li><a class="menu-item" href="{{route('admin.settings.pickup.index')}}">Pickup Weight Threshold</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(116, session('permissions')))
                        <li><a class="menu-item" href="{{route('admin.settings.shipment_cancellation_cut_off_days.index')}}">Shipment Cancellation Cut-Off Days</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(149, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.settings.auto_account_disabled_days.auto_index')}}">Auto Account Disabled Days</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(150, session('permissions')))
                        <li><a class="menu-item" href="{{route('admin.settings.non_service_area.index')}}">Non Service Area</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(151, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.settings.daily_pickup_sales_cron.index')}}">Daily Pickup & Sales Cron Time</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(152, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.settings.ticker.index')}}">Ticker</a></li>
                    @endif
                </ul>
            </li>
            @endif
        </ul>
    </div>
</div>