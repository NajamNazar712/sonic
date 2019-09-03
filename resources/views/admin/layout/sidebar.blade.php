<div class="main-menu menu-fixed menu-light menu-accordion menu-bordered menu-shadow"
     data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a href="{{route('admin.dashboard.index')}}"><i class="la la-area-chart"></i><span class="menu-title"
                                                                                                                     data-i18n="nav.dash.main">Dashboard</span></a>
            </li>
            @if (session('role_id') == 1 || count(array_intersect([5, 11, 15, 242, 76, 79, 217], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.vertical_nav.main"><i class="la la-users"></i>Shippers</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([5, 11, 15, 242], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title"
                                                                    data-i18n="nav.dash.main">Accounts</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(5, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.accounts.pending')}}">Pending</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(11, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.accounts.active')}}">Active</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(15, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.accounts.block')}}">Blocked</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || session('role_id') == 4 || in_array(242, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.accounts.merged_account.index')}}">Merged</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([76, 79, 217], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Packaging</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || count(array_intersect([76, 79], session('permissions'))) !== 0)
                                        <li class=" nav-item"><a href="#"><span class="menu-title">Requests</span></a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(76, session('permissions')))
                                                    <li><a class="menu-item" href="{{ route('admin.packaging.index') }}">Warehouse Stock</a></li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(79, session('permissions')))
                                                    <li><a class="menu-item" href="{{ route('admin.packaging.requests.index') }}">Shipper</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(217, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.packaging.warehouse.index') }}">Warehouses</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([155, 117, 209], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cart-plus"></i>Bookings</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([155, 209], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Walk-In</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(155, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.shipment.book.walk_in')}}">Book</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(209, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.shipment.history.walk_in_history')}}">History</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(117, session('permissions')))
                            <li class="menu-item"><a href="{{ route('admin.cancelled_shipments.index') }}">Cancelled</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif


            @if (session('role_id') == 1 || count(array_intersect([17, 20, 23, 123], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cubes"></i>First Mile</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([17, 20, 23, 123], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Pickups</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(17, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.pickups.pending.index') }}">Pending</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(20, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.pickups.assigned.index') }}">Assigned</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(23, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.pickups.receive.index') }}">Receive</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(123, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.pickups.history.index') }}">History</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([25, 26, 27, 124], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-truck"></i>Supply Chain</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([25, 26, 27, 124], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Cargo</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(25, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.cargo.pending.index') }}">Pending</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(26, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.cargo.create.index') }}">Create</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(26, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.cargo.draft.index') }}">Draft</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(27, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.cargo.in_transit.index') }}">In Transit</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(124, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.cargo.history.index') }}">History</a></li>
                                    @endif

                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif


            @if (session('role_id') == 1 || count(array_intersect([32, 33, 35, 36, 40, 42, 105, 44, 47, 48, 49, 126], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-motorcycle"></i>Last Mile</span></a>
                    <ul class="menu-content">

                        @if (session('role_id') == 1 || count(array_intersect([32, 33, 35, 36, 40, 42, 105], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Delivery</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(33, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.pending.index') }}">Pending</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(35, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.note.index') }}">Create Note</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(36, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.receive.index') }}">Receive</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(105, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.cash_collection.pending.index') }}">Pending
                                                Cash Collection</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(40, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.completed.index') }}">Completed</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(42, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.sdn.index') }}">Station Deposit
                                                Notes</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(203, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.fake_status.index') }}">Remove Fake Status</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(125, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.history.index') }}">History</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(32, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.sameday.index')}}">Same-Day</a></li>
                                    @endif

                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([44, 47, 48, 49, 126], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Return</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(44, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.return.index') }}">Confirmation Pending</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(47, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.return.confirmed') }}">Confirmed</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(48, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.return.create.index') }}">Create Note</a>
                                        </li>
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
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([52, 54, 59, 61, 136, 167, 232, 120, 145, 146, 147,232, 238, 243], session('permissions'))) !== 0)

                <li class="nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-money"></i>Financials</span></a>
                    <ul class="menu-content">

                        @if (session('role_id') == 1 || in_array(136, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.finance.add_shipment_adjustment.index') }}">Add
                                    Adjustment</a></li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([59, 61, 232], session('permissions'))) !== 0)
                            <li class=" menu-item"><a href="#"><span class="menu-title">Payments</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(59, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.finance.make_payments.index') }}">Make</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(61, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.finance.done_payments.index') }}">Done</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(232, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.finance.invoice_for_reimbursement.index') }}">Invoice for Reimbursement</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || in_array(120, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.finance.invoices.index') }}">Invoices</a>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([52, 54, 167], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Recovery</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(52, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.finance.outstanding_sdn.index') }}">SDN</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(54, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.finance.outstanding_shipments.index') }}">Shipments</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(167, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.finance.outstanding_shipments.walk_in_index') }}">Walk-In
                                                Shipments</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([145, 146, 147, 238, 243], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Petty Cash</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(145, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.petty_cash.make.index') }}">Make</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(238, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.petty_cash.draft.index') }}">Draft</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(146, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.petty_cash.statements.index') }}">Statements</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(147, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.petty_cash.approved.index') }}">Approved</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(243, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.petty_cash.rejected.index') }}">Rejected</a>
                                        </li>
                                    @endif

                                </ul>
                            </li>
                        @endif
                    </ul>

                </li>
            @endif
            @if (session('role_id') == 1 || session('department_id') == 3 || count(array_intersect([233, 234, 235, 236], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-commenting-o"></i>CRM</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || session('department_id') == 3 || count(array_intersect([233, 234, 235, 236], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Requests</span></a>

                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || session('department_id') == 3 || in_array(233, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.crm.launched_re_open.index')}}">Launched/Re-Open</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || session('department_id') == 3 || in_array(234, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.crm.in_process.index')}}">In-Process</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || Auth::user()->role->department_id == 3 || in_array(235, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.crm.resolved.index')}}">Resolved</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || Auth::user()->role->department_id == 3 || in_array(236, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.crm.closed.index')}}">Closed</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-check-square-o"></i>Support</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{route('admin.orders.index')}}">Order Management</a></li>
                    <li><a class="menu-item" href="{{ route('admin.quick_tracking.index') }}">Quick Tracking</a></li>

                    @if(session('role_id') == 1 || in_array(204, session('permissions')))
                        <li class=" nav-item"><a href="{{ route('admin.cx_quick_tracking.cx_index') }}"><span class="menu-title">CX Quick Tracking</span></a>
                        </li>
                    @endif
                    @if (session('role_id') == 1 || in_array(1, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.dispute.index') }}">Dispute</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(141, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.month_closing.index') }}">Month Closing</a></li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([107,108,119], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Misroute</span></a>

                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(107, session('permissions')))
                                    <li><a class="menu-item" href="{{route('admin.delivery.misroute.index')}}">Shipments</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(108, session('permissions')))
                                    <li><a class="menu-item"
                                           href="{{ route('admin.delivery.misroute.update.index') }}">Update</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(119, session('permissions')))
                                    <li><a class="menu-item"
                                           href="{{ route('admin.delivery.misroute.history.index') }}">History</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif


                    @if (session('role_id') == 1 || count(array_intersect([127,130], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Lost</span></a>

                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(127, session('permissions')))
                                    <li><a class="menu-item"
                                           href="{{route('admin.delivery.lost.index')}}">Shipments</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(130, session('permissions')))
                                    <li><a class="menu-item" href="{{route('admin.delivery.lost.add.index')}}">Add
                                            Shipments</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([193,196], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Intercept</span></a>
                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(193, session('permissions')))
                                    <li><a class="menu-item" href="{{route('admin.delivery.intercept.index')}}">Request</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(196, session('permissions')))
                                    <li><a class="menu-item" href="{{route('admin.delivery.intercept.history.index')}}">History</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if (session('role_id') == 1 || count(array_intersect([206,207,208], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Replacement To Regular</span></a>

                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(206, session('permissions')))
                                    <li><a class="menu-item" href="{{route('admin.delivery.replacement.not_collected.index')}}">Not Collected</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(207, session('permissions')))
                                    <li><a class="menu-item" href="{{route('admin.delivery.replacement.collected.index')}}">Collected</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(208, session('permissions')))
                                    <li><a class="menu-item" href="{{route('admin.delivery.replacement.logs.index')}}">Logs</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if (session('role_id') == 1 || in_array(57, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.finance.change_shipment_amount.index') }}">Change
                                Shipment Amount</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(134, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.finance.change_shipment_weight.index') }}">Change
                                Shipment Weight</a></li>
                    @endif

                </ul>
            </li>

            @if (session('role_id') == 1 || count(array_intersect([64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75,113, 138, 148, 153, 156, 169, 170, 172, 176, 200], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title"><i class="la la-file-text-o"></i>Reports</span></a>

                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(113, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.pickups.bookedvsreceived.index') }}">Booked
                                    VS Received</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(64, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.pickup_note.index') }}">Pickup Notes
                                    Completed</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(65, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.cargo_received.index') }}">Cargo
                                    Received</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(66, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.reports.completed_delivery_notes.index')}}">Completed
                                    Delivery Notes</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(67, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.return_note.index') }}">Return Notes
                                    Completed</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(68, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.outstanding_shipments.index') }}">Outstanding
                                    Shipments</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(69, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.lead_time.index') }}">Lead Time</a>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || in_array(70, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.qsr.index') }}">Quality of
                                    Service</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(71, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.qa.index') }}">Quality Assurance</a>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || in_array(72, session('permissions')))
                            <li><a class="menu-item" href="{{route('admin.reports.customer_retention.index')}}">Customer
                                    Retention Rate</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(73, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.daily_pickup_sales.index') }}">Daily
                                    Pickup and Sales</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(74, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.customer_sales.index') }}">Monthwise
                                    Customer Sales</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(75, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.overall_sales.index') }}">Overall
                                    Sales</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(138, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.sales_person_performance.index') }}">Sales
                                    Person Performance</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(148, session('permissions')))
                            <li><a class="menu-item"
                                   href="{{ route('admin.reports.negative_balance_customers.index') }}">Negative Balance
                                    Customers</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(156, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.petty_cash.index') }}">Petty Cash
                                    Statements</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(153, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.call_verification.index') }}">Call
                                    Verification</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(169, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.fake_status.index') }}">Fake
                                    Statuses</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(170, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.debriefing.index') }}">Debriefing</a>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || in_array(172, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.cargo_returns_shipment.index') }}">Cargo
                                    Returns Shipment</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(174, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.return_reattempt_ratio.index') }}">Return
                                    Confirm To Re-Attempt Ratio</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(176, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.multiple_payment_report.index') }}">Multiple
                                    Payment</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(177, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.revenue.index') }}">Revenue</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(199, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.gst.index') }}">GST</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(200, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.crm.index') }}">CRM</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(206, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.summary.index') }}">Summary</a></li>
                        @endif
                    </ul>
                </li>
            @endif


            @if (session('role_id') == 1 || count(array_intersect([81, 85, 88, 92, 96, 100, 131, 205, 231, 104, 116, 149, 150, 151, 152, 154, 157, 158, 171, 175, 189, 192, 197, 198, 214, 228, 229, 230, 231, 237], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i class="la la-cogs"></i>Settings</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([149, 214, 228], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Shippers</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(149, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.auto_account_disabled_days.auto_index')}}">Auto Account Disabled Days</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(228, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.settings.stock_movement.index') }}">Packaging Material Stock Movement Account</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(214, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.packaging.types.index') }}">Packaging Types</a>
                                        </li>
                                    @endif
                                </ul>

                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([116, 150, 154, 197], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Bookings</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(116, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.shipment_cancellation_cut_off_days.index')}}">Shipment Cancellation Cut-Off Days</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(150, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.non_service_area.index')}}">Non Service Area</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(154, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.walk_in.index')}}">Walk-In</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(197, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.settings.cod_cap_zones.index') }}">COD CAP for Zone Classes</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([104], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">First Mile</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(104, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.pickup.index')}}">Pickup Weight Threshold</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([198], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Supply Chain</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(198, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.cargo.mapping.index') }}">Mapping</a></li>
                                    @endif
                                </ul>

                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([88, 92, 96, 131, 192, 205, 231], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Last Mile</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(192, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.return_note_restriction_bypass.index')}}">Return Note Restriction Bypass</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(231, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.settings.delivery_call_verification_ratio.index') }}">Delivery Call Verification Ratio</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || count(array_intersect([88, 92, 96, 131, 205], session('permissions'))) !== 0)
                                        <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main">Network Management</span></a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(131, session('permissions')))
                                                    <li><a class="menu-item" href="{{route('admin.management.zonal.index')}}">Zonal</a></li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(88, session('permissions')))
                                                    <li><a class="menu-item" href="{{route('admin.management.city.index')}}">City</a>
                                                    </li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(92, session('permissions')))
                                                    <li><a class="menu-item" href="{{route('admin.management.route.index')}}">Route</a></li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(96, session('permissions')))
                                                    <li><a class="menu-item" href="{{route('admin.management.rider.index')}}">Rider</a></li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(205, session('permissions')))
                                                    <li><a class="menu-item" href="{{route('admin.management.city_list')}}">Walk-In Cities List</a></li>
                                                @endif

                                            </ul>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([157,158, 171, 189, 229, 230], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Financials</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || count(array_intersect([157,158], session('permissions'))) !== 0)
                                        <li><a class="menu-item" href="#">Petty Cash</a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(157, session('permissions')))
                                                    <li><a class="menu-item" href="{{route('admin.settings.petty_cash.heads.index')}}">Heads</a></li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(158, session('permissions')))
                                                    <li><a class="menu-item" href="{{route('admin.settings.petty_cash.titles.index')}}">Titles</a></li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(171, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.auto_invoice_generation_and_due_date.index')}}">Auto-Invoice Generation & Due Date Length</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(189, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.fuel_factor.index')}}">Fuel Factor</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(229, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.settings.weight_factor.index') }}">Weight Charges Factor</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(230, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.ibft_charges.index')}}">IBFT Charges</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif


                        @if (session('role_id') == 1 || session('role_id') == 6 || count(array_intersect([237], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">CRM</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || session('role_id') == 6 || in_array(188, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.crm.permissions') }}">Permissions</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(237, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.settings.crm_cut_off_time_and_holidays.index') }}">TAT Cut-Off Time and Holidays</a></li>
                                    @endif
                                </ul>

                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([81, 85, 100, 152], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Support</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || count(array_intersect([81, 85, 100, 152], session('permissions'))) !== 0)
                                        @if (session('role_id') == 1 || in_array(152, session('permissions')))
                                            <li><a class="menu-item" href="{{route('admin.settings.ticker.index')}}">Ticker</a></li>
                                        @endif

                                        @if (session('role_id') == 1 || count(array_intersect([81, 85], session('permissions'))) !== 0)
                                            <li class=" nav-item"><a href="#"><span class="menu-title">User Management</span></a>
                                                <ul class="menu-content">
                                                    @if (session('role_id') == 1 || in_array(81, session('permissions')))
                                                        <li><a class="menu-item" href="{{ route('admin.user_management.users.index') }}">Users</a>
                                                        </li>
                                                    @endif

                                                    @if (session('role_id') == 1 || in_array(85, session('permissions')))
                                                        <li><a class="menu-item" href="{{ route('admin.user_management.roles.index') }}">Roles</a>
                                                        </li>
                                                    @endif


                                                </ul>
                                            </li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(100, session('permissions')))
                                            <li class=" nav-item"><a href="{{ route('admin.notifications.index') }}"><span class="menu-title">Notifications</span></a>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([151, 175], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Reports</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(151, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.daily_pickup_sales_cron.index')}}">Daily Pickup & Sales Cron Time</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(175, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.settings.debriefing_report_cut_off_time.index')}}">Debriefing Report Cut-Off Time</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
                <li class=" nav-item"><a href="{{route('admin.tracking.index')}}"><i class="la la-crosshairs"></i><span class="menu-title"
                                                                                                                        data-i18n="nav.dash.main">Tracking</span></a>
        </ul>
    </div>
</div>