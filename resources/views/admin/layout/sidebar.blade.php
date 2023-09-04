@php
    $settings = App\Http\Models\Admin\GlobalSettings::where('type', 'debriefing_role_setting')->first();
    $roles = [];
    if(isset($settings))
    {
        $roles = explode("," , $settings->text);
    }
@endphp
<div class="main-menu menu-fixed menu-light menu-accordion menu-bordered menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a href="{{ route('admin.dashboard.index') }}"><i class="la la-area-chart"></i><span
                        class="menu-title" data-i18n="nav.dash.main">Dashboard</span></a>
            </li>
            @if (session('role_id') == 1 || in_array(416, session('permissions')) || in_array(903, session('permissions')))
            <li><a class="menu-item" ><i class="la la-user-plus"></i>Leads</a>
                <ul> <!-- Nested menu for Team Lead -->

                    @if (session('role_id') == 1 || in_array(416, session('permissions')))
                    <li><a class="menu-item" href="{{ route('admin.leads.index') }}">Leads</a></li>
                @endif

                    @if (session('role_id') == 1 || in_array(903, session('permissions')))

                    <li><a class="menu-item" href="{{ route('admin.team_lead.index') }}">Team Lead</a></li>
                    @endif

                </ul>
            </li>
        @endif
        


            @if (session('role_id') == 1 || in_array(416, session('permissions')))
                <li><a class="menu-item" href="{{ route('admin.assigned_shipment.index') }}"><i
                            class="la la-user-plus"></i>Assigned Shipment</a></li>
            @endif
            @if (session('role_id') == 1 || in_array(669, session('permissions')))
                <li><a class="menu-item" href="{{ route('admin.pam_leads.index') }}"><i class="la la-truck"></i>Movit
                        Leads</a></li>
            @endif
            @if (session('role_id') == 1 ||
                    count(array_intersect([5, 11, 15, 242, 76, 79, 217, 315, 428, 470, 762, 767, 791], session('permissions'))) !==
                        0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.vertical_nav.main"><i
                                class="la la-users"></i>Shippers</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(364, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.shipment.receiving_sheet.index') }}">Receiving Sheets</a></li>
                        @endif
                        @if (session('role_id') == 1 ||
                                count(array_intersect([5, 11, 15, 242, 428, 470, 762, 767], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title"
                                        data-i18n="nav.dash.main">Accounts</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(5, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.accounts.pending') }}">Pending</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(11, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.accounts.active') }}">Active</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(15, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.accounts.block') }}">Blocked</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(242, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.accounts.merged_account.index') }}">Merged</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(428, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.retail.accounts.index') }}">Retail Accounts</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(470, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.accounts.active.today') }}">Active Today</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || count(array_intersect([762, 767], session('permissions'))) !== 0)
                                        <li><a class="menu-item" href="#">Disable Account Intimation Survey</a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(762, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.accounts.disable.account.intimation.survey.index') }}">Send
                                                            Survey</a>
                                                    </li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(767, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.accounts.disable.account.intimation.survey.report') }}">Survey
                                                            Report</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif

                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([79], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Packaging</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(79, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.packaging.requests.index') }}">Request</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(217, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.packaging.warehouse.index') }}">Warehouses</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || in_array(791, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.daily_visit.screen.index') }}">Daily
                                    Visit</a>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || in_array(265, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.daily_visit.index') }}">Daily Visit Form</a>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([315], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title"
                                        data-i18n="nav.dash.main">Projection</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(315, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.dashboard.sales.index') }}">Dashboard</a></li>
                                    @endif

                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || session('sales_coordinator'))
                            <li><a class="menu-item"
                                    href="{{ route('admin.shipment.poc_kam_tagged_accounts.index') }}">POC and KAM
                                    Tagged Accounts</a></li>
                        @endif
                    </ul>
                </li>
            @endif


            @if (session('role_id') == 1 || count(array_intersect([155, 117, 209, 254, 516, 511], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                                class="la la-cart-plus"></i>Bookings</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([155, 209], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title"
                                        data-i18n="nav.dash.main">Walk-In</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(155, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.shipment.book.walk_in') }}">Domestic Book</a>
                                        </li>
                                        <li><a class="menu-item"
                                                href="{{ route('admin.shipment.book.international_walk_in') }}">International
                                                Book</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(209, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.shipment.history.walk_in_history') }}">History</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([511, 516], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title"
                                        data-i18n="nav.dash.main">FTL</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(511, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.ftl.request.index') }}">FTL
                                                Requests</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(516, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.shipment.book.ftl.walk_in') }}">Walkin FTL
                                                Booking</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(117, session('permissions')))
                            <li class="menu-item"><a
                                    href="{{ route('admin.cancelled_shipments.index') }}">Cancelled</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(254, session('permissions')))
                            <li class="menu-item"><a
                                    href="{{ route('admin.shipment.consolidation.history.index') }}">Consolidation
                                    History</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif


            @if (session('role_id') == 1 ||
                    count(array_intersect([17, 20, 23, 123, 368, 369, 370, 446, 670, 830], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                                class="la la-cubes"></i>First Mile</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([368, 369, 370], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Multiple
                                        Pieces</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(368, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.multiple_pieces.add.index') }}">Add</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(369, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.multiple_pieces.hold.index') }}">List</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(370, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.multiple_pieces.resolved.index') }}">Resolved</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        {{--                             <li class=" nav-item"><a href="#"><span class="menu-title">Pickups</span></a> --}}
                        {{--                                <ul class="menu-content"> --}}
                        {{--                                    @if (session('role_id') == 1 || in_array(17, session('permissions'))) --}}
                        {{--                                        <li><a class="menu-item" href="{{ route('admin.pickups.pending.index') }}">Pending</a></li> --}}
                        {{--                                    @endif --}}

                        {{--                                    @if (session('role_id') == 1 || in_array(20, session('permissions'))) --}}
                        {{--                                        <li><a class="menu-item" href="{{ route('admin.pickups.assigned.index') }}">Assigned</a> --}}
                        {{--                                        </li> --}}
                        {{--                                    @endif --}}

                        {{--                                    @if (session('role_id') == 1 || in_array(23, session('permissions'))) --}}
                        {{--                                        <li><a class="menu-item" href="{{ route('admin.pickups.receive.index') }}">Receive</a></li> --}}
                        {{--                                    @endif --}}

                        {{--                                    @if (session('role_id') == 1 || in_array(123, session('permissions'))) --}}
                        {{--                                        <li><a class="menu-item" href="{{ route('admin.pickups.history.index') }}">History</a></li> --}}
                        {{--                                    @endif --}}

                        {{--                                    @if (session('role_id') == 1 || in_array(271, session('permissions'))) --}}
                        {{--                                        <li><a class="menu-item" href="{{ route('admin.pickups.rider.index') }}">Rider</a></li> --}}
                        {{--                                    @endif --}}

                        {{--                                    @if (session('role_id') == 1 || in_array(272, session('permissions'))) --}}
                        {{--                                        <li><a class="menu-item" href="{{ route('admin.pickups.rider.action_log.index') }}">Rider Action Log</a></li> --}}
                        {{--                                    @endif --}}

                        {{--                                    @if (session('role_id') == 1 || in_array(24, session('permissions'))) --}}
                        {{--                                        <li><a class="menu-item" href="{{ route('admin.pickups.quick_arrival_of_shipments.index') }}">Quick Arrival of Shipments</a></li> --}}
                        {{--                                    @endif --}}
                        {{--                                </ul> --}}
                        {{--                            </li> --}}


                        @if (session('role_id') == 1 || count(array_intersect([17, 24, 271, 272, 366, 670, 830], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Pickups</span></a>
                                <ul class="menu-content">
                                    {{--                                        @if (session('role_id') == 1 || in_array(17, session('permissions'))) --}}
                                    {{--                                            <li><a class="menu-item" href="{{ route('admin.v2_pickups.un_assigned.index') }}">Un Assigned</a></li> --}}
                                    {{--                                        @endif --}}
                                    @if (session('role_id') == 1 || in_array(17, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.pending.index') }}">Pending</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(17, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.arrival_service.index') }}">Arrival Service
                                                Center</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(24, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.arrival.individual.index') }}">Individual
                                                Arrival</a></li>
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.arrival.bulk.index') }}">Bulk
                                                Arrival</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(271, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.rider.index') }}">Rider Pickups</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(272, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.action_log.index') }}">Rider Action
                                                Log</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(366, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.rider_receiving.index') }}">Rider
                                                Receiving</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(670, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.rider_receiving.dws.index') }}">Rider
                                                Receiving DWS</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(406, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.pickup_route.index') }}">Pickup
                                                Route</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(830, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.arrival.project_shippers.index') }}">Project
                                                Shippers Arrival</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(446, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.v2_pickups.rider_tracking.index') }}">Rider Tracking</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 ||
                    count(array_intersect(
                            [25, 26, 27, 124, 386, 501, 545, 546, 547, 551, 554, 557, 556, 559, 564],
                            session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                                class="la la-truck"></i>Supply Chain</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(376, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.cargo.supply_chain.supply_chain_index') }}">Order
                                    Management</a></li>
                        @endif
                    </ul>
                    <ul>
                        @if (session('role_id') == 1 || in_array(682, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.cargo_manifest.draft.setting') }}">Cargo
                                    Manifest Draft Setting</a></li>
                        @endif
                    </ul>


                    <ul class="menu-content">
                        @if (session('role_id') == 1 ||
                                count(array_intersect([545, 546, 547, 551, 554, 557, 556, 559, 564], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Cargo Vehicle
                                        Manifest</span></a>
                                <ul class="menu-content">
                                    <li class=" nav-item"><a href="#"><span class="menu-title">Bag</span></a>
                                        <ul class="menu-content">
                                            @if (session('role_id') == 1 || in_array(545, session('permissions')))
                                                <li><a class="menu-item"
                                                        href="{{ route('admin.cargo_manifest.bags.pending.index') }}">Pending</a>
                                                </li>
                                            @endif

                                            @if (session('role_id') == 1 || (in_array(546, session('permissions')) && Auth::user()->default_hub_id != null))
                                                <li><a class="menu-item"
                                                        href="{{ route('admin.cargo_manifest.bags.create.index') }}">Create
                                                        Bag</a></li>
                                            @endif

                                            @if (session('role_id') == 1 || (in_array(559, session('permissions')) && Auth::user()->default_hub_id != null))
                                                <li><a class="menu-item"
                                                        href="{{ route('admin.cargo_manifest.bags.create.open_bag.index') }}">Create
                                                        Open Bag</a></li>
                                            @endif

                                            {{-- @if (session('role_id') == 1 || in_array(26, session('permissions')))
                                                     <li><a class="menu-item" href="{{ route('admin.master_cargo.bag.create.open_bag.index') }}">Create Open Bag</a></li>
                                                 @endif --}}

                                            @if (session('role_id') == 1 || in_array(547, session('permissions')))
                                                <li><a class="menu-item"
                                                        href="{{ route('admin.cargo_manifest.bags.history.index') }}">History</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                    @if ((session('role_id') == 1 || in_array(551, session('permissions'))) && Auth::user()->default_hub_id != null)
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo_manifest.create') }}">Create Manifest</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(564, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo_manifest.index') }}">Bags in Transit</a>
                                        </li>
                                    @endif
                                    @if ((session('role_id') == 1 || in_array(554, session('permissions'))) && Auth::user()->default_hub_id != null)
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo_manifest.receive.index') }}">Quick Receive
                                                Bags</a></li>
                                    @endif
                                    @if ((session('role_id') == 1 || in_array(557, session('permissions'))) && Auth::user()->default_hub_id != null)
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo_manifest.receive.bag.index') }}">Quick
                                                Receive Bag Shipments</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(556, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo_manifest.history') }}">Manifest
                                                History</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>

                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([386, 501], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Runner</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(386, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.runner.index') }}">On
                                                Route</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(501, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.runner.intransit') }}">In
                                                Transit</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || count(array_intersect([404, 405], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Shipment
                                        On-Hold</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(404, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo.supply_chain.shipment_on_hold.index') }}">Update</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(405, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo.supply_chain.shipment_on_hold.history.index') }}">History</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif


            @if (in_array(session('role_id'), [1]) || count(array_intersect([32,33,35,36,40,105,44,47,48,49,126,262,441,464,495,496,497,531,566,675,757,758,815,], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                                class="la la-motorcycle"></i>Last Mile</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 ||
                                count(array_intersect([32, 33, 35, 36, 40, 42, 105, 262, 441, 464, 531, 757, 758, 815], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title"
                                        data-i18n="nav.dash.main">Delivery</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(33, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.pending.index') }}">Pending</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(35, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.note.index') }}">Create Note</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(36, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.receive.index') }}">Receive</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(464, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.quick_receiving.index') }}">Quick
                                                Receiving</a>
                                        </li>
                                    @endif

                                    {{--                                    @if (session('role_id') == 1 || in_array(105, session('permissions'))) --}}
                                    {{--                                        <li><a class="menu-item" href="{{ route('admin.delivery.cash_collection.pending.index') }}">Pending --}}
                                    {{--                                                Cash Collection</a></li> --}}
                                    {{--                                    @endif --}}
                                    @if (session('role_id') == 1 || count(array_intersect([105, 423], session('permissions'))) !== 0)
                                        <li class=" nav-item"><a href="#"><span class="menu-title"
                                                    data-i18n="nav.dash.main">Pending
                                                    Cash Collection</span></a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(105, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.delivery.cash_collection.pending.index') }}">COD</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(423, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.delivery.cash_collection.retail.index') }}">Retail</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif

                                    {{-- @if (session('role_id') == 1 || in_array(40, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.delivery.completed.index') }}">Completed</a>
                                        </li>
                                    @endif --}}
                                    @if (session('role_id') == 1 || count(array_intersect([40, 424], session('permissions'))) !== 0)
                                        <li class=" nav-item"><a href="#"><span class="menu-title"
                                                    data-i18n="nav.dash.main">Completed</span></a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(40, session('permissions')))
                                                    <li>
                                                        <a class="menu-item"
                                                            href="{{ route('admin.delivery.completed.index') }}">COD</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(424, session('permissions')))
                                                    <li>
                                                        <a class="menu-item"
                                                            href="{{ route('admin.delivery.completed.retail.index') }}">Retail</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif



                                    @if (session('role_id') == 1 || in_array(203, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.fake_status.index') }}">Remove Fake
                                                Status</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(125, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.history.index') }}">History</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(859, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.delivery_shipments.index') }}">Delivery
                                                Shipments</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(441, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.signature.index') }}">Signature</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(32, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.sameday.index') }}">Same-Day</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(262, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.fake_status.log.index') }}">Log Fake
                                                Status</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(531, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.note.request_index') }}">DN ByPass
                                                Request</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(757, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.note.rider_category_bypass_request') }}">Rider
                                                Category ByPass Request</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(758, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.note.rider_category_bypass_weight') }}">Rider
                                                Category ByPass Weight</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(815, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.shipment_otp.index') }}">Non-COD Shipments
                                                OTP</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(35, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.rider_request.index') }}">Pending
                                                Delivery Note Requests</a></li>
                                    @endif

                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 ||
                                count(array_intersect([44, 47, 48, 49, 126, 566, 600, 643, 675, 781,849,885], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title"
                                        data-i18n="nav.dash.main">Return</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(44, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.index') }}">Shipment - Reason Validation Required</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(47, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.confirmed') }}">Confirmed</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(48, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.create.index') }}">Create Note</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(49, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.receive.index') }}">Receive</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(266, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.cx_sales.index') }}">Unable to Return</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(126, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.history.index') }}">History</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(566, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.return_deliveries.index') }}">Return
                                                Deliveries</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(860, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.return_shipments.index') }}">Return
                                                Shipments</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(600, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.rcp_agent.index') }}">RCP Agent
                                                Productivity</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(876, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.rcp_agent_cn.index') }}">RCP Agent
                                                Productivity Shipment Wise</a></li>
                                    @endif                                    
                                    @if (session('role_id') == 1 || in_array(643, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.revert.index') }}">Return Revert</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(675, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.confirmation_pending_sms') }}">RCP
                                                SMS</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(781, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.confirmation_pending_manual_sms') }}">RCP
                                                Manual SMS Log</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(48, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.rider_request.index') }}">Pending
                                                Return Note Requests</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(849, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.return.return_confirm_otp.index') }}">Return
                                                Confirm OTP</a></li>
                                                href="{{ route('admin.return.return_confirm_otp.index') }}">Return OTP History</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(885, session('permissions')))
                                        <li><a class="menu-item" href="{{route('admin.return.shipper_return_receiving.history.index')}}">Shipper Return Receiving History</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (in_array(session('role_id'), array_merge([1], $roles)) ||
                                count(array_intersect([495, 496, 497], session('permissions'))) !== 0)

                            <li class=" nav-item"><a href="#"><span class="menu-title"
                                        data-i18n="nav.dash.main">Debriefing</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(495, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.debriefing.supervisor.index') }}">Supervisor
                                                Dashboard</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(496, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.debriefing.agents_call_monitoring.index') }}">Agents
                                                Call Monitoring</a></li>
                                    @endif
                                    @if (in_array(session('role_id'), array_merge([1], $roles)))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.debriefing.caller_agent.index') }}">Caller Agent
                                                Screen</a></li>
                                    @endif

                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 ||
                    count(array_intersect(
                            [42, 52, 54, 59, 61, 136, 167, 232, 120, 145, 146, 147, 232, 238, 243, 454, 455, 509, 625, 807, 827],
                            session('permissions'))) !== 0)

                <li class="nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                                class="la la-money"></i>Financials</span></a>
                    <ul class="menu-content">

                        @if (session('role_id') == 1 || in_array(136, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.finance.add_shipment_adjustment.index') }}">Add
                                    Adjustment</a></li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([59, 61, 232, 625], session('permissions'))) !== 0)
                            <li class=" menu-item"><a href="#"><span class="menu-title">COD Payments</span></a>
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
                                        <li><a class="menu-item"
                                                href="{{ route('admin.finance.invoice_for_reimbursement.index') }}">Invoice
                                                for Reimbursement</a></li>
                                    @endif
                                    {{--   @if (session('role_id') == 1 || in_array(625, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.finance.invoices.reimbursement.index') }}">Reimbursement Invoices</a></li>
                                    @endif --}}
                                    @if (session('role_id') == 1 || in_array(396, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.finance.make_payments_pickup_wise.index') }}">Pickup
                                                Wise Make</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([454, 455, 232], session('permissions'))) !== 0)
                            <li class=" menu-item"><a href="#"><span class="menu-title">Retail
                                        Payments</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(454, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.finance.retail.make_payments.index') }}">Make</a>
                                        </li>
                                    @endif


                                    @if (session('role_id') == 1 || in_array(455, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.finance.retail.done_payments.index') }}">Done</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([120, 509, 807], session('permissions'))))
                            <li class=" nav-item"><a href="#"><span class="menu-title">Invoices</span></a>
                                <ul class="menu-content">
                                    <li><a class="menu-item"
                                            href="{{ route('admin.finance.invoices.index') }}">Invoice</a></li>
                                    {{--     <li><a class="menu-item" href="{{ route('admin.finance.invoices.received_index') }}">Received </a></li> --}}
                                    @if (session('role_id') == 1 || in_array(509, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.finance.ftl_invoice.index') }}">FTL
                                                Invoices</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(807, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.international.wholesale.invoices.index') }}">Wholesale
                                                Invoices</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([42, 52, 54, 167], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Recovery</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(52, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.finance.outstanding_sdn.index') }}">SDN</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(42, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.delivery.sdn.index') }}">Station Deposit
                                                Notes</a></li>
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
                                    @if (session('role_id') == 1 || in_array(645, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.petty_cash.approved.index') }}">Approved</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(243, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.petty_cash.rejected.index') }}">Rejected</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 ||count(array_intersect([881,882], session('permissions'))) !== 0)
                                        <li class=" nav-item"><a href="#"><span class="menu-title">Advance Petty Cash</span></a>

                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(881, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.petty_cash.advance.index') }}">Make</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(882, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.petty_cash.advance.statements.index') }}">Statement</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif


                                </ul>
                            </li>
                        @endif
                    </ul>

                </li>
            @endif
            @if (session('role_id') == 1 ||
                    session('department_id') == 3 ||
                    count(array_intersect([233, 234, 235, 236, 363, 795], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                                class="la la-commenting-o"></i>CRM</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(869, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.crm.dashboard.index') }}">CRM Dashboard</a></li>
                        @endif
                        @if (session('role_id') == 1 ||
                                session('department_id') == 3 ||
                                count(array_intersect([233, 234, 235, 236], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Requests</span></a>

                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || session('department_id') == 3 || in_array(233, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.crm.launched_re_open.index') }}">Launched/Re-Open</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || session('department_id') == 3 || in_array(234, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.crm.in_process.index') }}">In-Process</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || Auth::user()->role->department_id == 3 || in_array(235, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.crm.resolved.index') }}">Resolved</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || Auth::user()->role->department_id == 3 || in_array(236, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.crm.closed.index') }}">Closed</a></li>
                                    @endif
                                </ul>
                            </li>

                            @if (session('role_id') == 1 || Auth::user()->role->department_id == 3 || in_array(363, session('permissions')))
                                <li><a class="menu-item"
                                        href="{{ route('admin.crm.consignee_info.index') }}">Consignee Info</a></li>
                            @endif
                            @if (session('role_id') == 1 || in_array(795, session('permissions')))
                                <li><a class="menu-item" href="{{ route('admin.crm.bulk_claim.index') }}">Bulk Claim
                                        Logging</a></li>
                            @endif
                          
                        @endif
                    </ul>
                </li>
            @endif

            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                            class="la la-check-square-o"></i>Support</span></a>
                <ul class="menu-content">
                    @if (session('role_id') == 1 || in_array(621, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.orders.index') }}">Order Management</a></li>
                    @endif
                    @if (session('role_id') == 1 || in_array(622, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.orders.self_collection.index') }}">Self
                                Collection Shipments</a></li>
                    @endif
                    <li><a class="menu-item" href="{{ route('admin.quick_tracking.index') }}">Quick Tracking</a>
                    </li>
                    @if (session('role_id') == 1 || in_array(204, session('permissions')))
                        <li class=" nav-item"><a href="{{ route('admin.cx_quick_tracking.cx_index') }}"><span
                                    class="menu-title">CX Quick Tracking</span></a></li>
                    @endif
                    @if (session('role_id') == 1 || in_array(822, session('permissions')))
                        <li class=" nav-item"><a href="{{ route('admin.tracking.shipment_position.track') }}"><span
                                    class="menu-title">Track Actual Shipment Position</span></a></li>
                    @endif
                    @if (session('role_id') == 1 || in_array(1, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.dispute.index') }}">Dispute</a></li>
                    @endif

                    @if (session('role_id') == 1 ||
                            count(array_intersect([399, 400, 402, 421, 422, 525, 595], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Telenor</span></a>

                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(421, session('permissions')))
                                    <li><a class="menu-item" href="{{ route('admin.telenor.arrival.index') }}">Bulk
                                            Arrival</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(399, session('permissions')))
                                    <li><a class="menu-item" href="{{ route('admin.telenor.delivery.index') }}">Bulk
                                            Delivered</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(400, session('permissions')))
                                    <li><a class="menu-item" href="{{ route('admin.telenor.return.index') }}">Return
                                            Update</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(422, session('permissions')))
                                    <li><a class="menu-item" href="{{ route('admin.telenor.order_id.index') }}">Bulk
                                            Order ID</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(525, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.telenor.return.bulk_return') }}">Bulk Return</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(595, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.telenor.revert.bulk_revert') }}">Bulk Revert</a>
                                    </li>
                                @endif

                                {{--                                @if (session('role_id') == 1 || in_array(402, session('permissions'))) --}}
                                {{--                                    <li><a class="menu-item" --}}
                                {{--                                           href="{{ route('admin.telenor.call.index') }}">Call(s)</a> --}}
                                {{--                                    </li> --}}
                                {{--                                @endif --}}
                            </ul>
                        </li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([579, 606, 607], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Carrefour</span></a>

                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(579, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.carrefour.arrival.index') }}">Bulk Arrival</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(606, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.carrefour.delivery.index') }}">Bulk Delivery</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(607, session('permissions')))
                                    <li><a class="menu-item" href="{{ route('admin.carrefour.return.index') }}">Bulk
                                            Return</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([408, 409], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Month Closing</span></a>
                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(408, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.month_closing.pending.index') }}">Pending</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(409, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.month_closing.resolved.index') }}">Resolved</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([107, 108, 119], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Misroute</span></a>

                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(107, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.delivery.misroute.index') }}">Shipments</a>
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


                    @if (session('role_id') == 1 || count(array_intersect([127, 130], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Lost</span></a>

                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(127, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.delivery.lost.index') }}">Shipments</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(130, session('permissions')))
                                    <li><a class="menu-item" href="{{ route('admin.delivery.lost.add.index') }}">Add
                                            Shipments</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(708, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.settings.lost_shipment_shippers.index') }}">Lost
                                            Shipments Shippers</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(710, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.settings.lost_shipment_admins.index') }}">Lost
                                            Shipments Admins</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([193, 196], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Intercept</span></a>
                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(193, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.delivery.intercept.index') }}">Request</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(196, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.delivery.intercept.history.index') }}">History</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if (session('role_id') == 1 || count(array_intersect([206, 207, 208], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Replacement To
                                    Regular</span></a>

                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(206, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.delivery.replacement.not_collected.index') }}">Not
                                            Collected</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(207, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.delivery.replacement.collected.index') }}">Collected</a>
                                    </li>
                                @endif
                                @if (session('role_id') == 1 || in_array(208, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.delivery.replacement.logs.index') }}">Logs</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if (session('role_id') == 1 || in_array(57, session('permissions')))
                        <li><a class="menu-item"
                                href="{{ route('admin.finance.change_shipment_amount.index') }}">Change
                                Shipment Amount</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(134, session('permissions')))
                        <li><a class="menu-item"
                                href="{{ route('admin.finance.change_shipment_weight.index') }}">Change
                                Shipment Weight</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(324, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.coordinates.add.index') }}">Add
                                Cordinates</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(306, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.scanning_history.index') }}">Scanning
                                History</a></li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([307, 308, 397], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Sales</span></a>
                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(307, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.settings.sales.targets.index') }}">Targets</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(308, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.settings.sales.history.index') }}">History</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(397, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.settings.sales.key_accounts.dashboard') }}">Key
                                            Accounts Dashboard</a>
                                    </li>
                                @endif
                            </ul>

                        </li>
                    @endif

                    @if (session('role_id') == 1 || in_array(333, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.dashboard.userwise') }}">User wise
                                Commission</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(334, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.dashboard.overall.commission') }}">Overall
                                Commission</a></li>
                    @endif
                    @if (session('role_id') == 1 || in_array(392, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.open_parcel_history.index') }}">Open Parcel
                                History</a></li>
                    @endif

                    <li><a class="menu-item" href="{{ route('admin.trax_directory.index') }}">Trax Directory</a>
                    </li>

                    @if (session('role_id') == 1 || in_array(398, session('permissions')))
                        <li><a class="menu-item"
                                href="{{ route('admin.international.tracking_upload.index') }}">International
                                Tracking Upload</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(508, session('permissions')))
                        <li><a class="menu-item"
                                href="{{ route('admin.international.shipment_status.index') }}">International
                                Shipment Status</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(750, session('permissions')))
                        <li><a class="menu-item"
                                href="{{ route('admin.international.extra_service_charges.index') }}">International
                                Extra Service Charges</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(471, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.activity_trail.index') }}">Activity Trail</a>
                        </li>
                    @endif

                    @if (session('role_id') == 1 || in_array(527, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.admin_otp.index') }}">Admin OTP</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(563, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.rider_otp.index') }}">Rider OTP (Login &
                                Delivery Note)</a></li>
                    @endif
                    @if (session('role_id') == 1 || in_array(567, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.airway_journey.index') }}">Airway Bill Print
                                History</a></li>
                    @endif
                    @if (session('role_id') == 1 || in_array(699, session('permissions')))
                        <li><a class="menu-item"
                                href="{{ route('admin.settings.cn_print_right.cn_print_right') }}">CN Print Rights
                                Setting</a></li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([770, 771], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">Vigilance</span></a>
                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(770, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.vigilance.note.index') }}">Note</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(771, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.vigilance.verification.history.index') }}">History
                                            (Old)</a>
                                    </li>
                                    <li><a class="menu-item"
                                            href="{{ route('admin.vigilance.note.history.index') }}">History</a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    @endif

                    @if (session('role_id') == 1 || count(array_intersect([799, 804], session('permissions'))) !== 0)
                        <li class=" nav-item"><a href="#"><span class="menu-title">International</span></a>
                            <ul class="menu-content">
                                @if (session('role_id') == 1 || in_array(799, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.international.wholesale.accounts.index') }}">Wholesale
                                            Accounts</a></li>
                                @endif
                                @if (session('role_id') == 1 || in_array(804, session('permissions')))
                                    <li><a class="menu-item"
                                            href="{{ route('admin.international.wholesale.excel.index') }}">Wholesale
                                            Excel Booking</a></li>
                                @endif

                            </ul>
                        </li>
                    @endif
                    {{--  <li><a class="menu-item" href="{{route('admin.attendance.mark')}}">Attendance</a></li> --}}
                    @if (session('role_id') == 1 || in_array(827, session('permissions')))
                        <li class=" nav-item"><a href="{{ route('admin.otp_history.index') }}"><span
                                    class="menu-title">OTP History</span></a>
                        </li>
                    @endif


                    @if (session('role_id') == 1 || in_array(862, session('permissions')))
                        <li class=" nav-item"><a
                                href="{{ route('admin.management.riders.rider_remarks.index') }}"><span
                                    class="menu-title">Rider Remarks</span></a>
                        </li>
                    @endif     
                    
                    @if (session('role_id') == 1 || in_array(878, session('permissions')))
                        <li class=" nav-item"><a href="{{ route('admin.management.shipment_received.index') }}"><span class="menu-title">Shipment Receiver Details</span></a>
                        </li>
                    @endif
                
                </ul>
            </li>

            @if (session('role_id') == 1 || count(array_intersect([64,65,66,67,68,69,70,71,72,73,74,75,113,138,148,153,156,169,170,172,176,200,210,258,259,263,264,275,300,301,319,327,328,337,356,401,437,444,472,476,493,502,524,532,555,613,614,624,642,647,653,679,673,676,688,705,717,780,784,786,793,794,823,824,839,886],session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title"><i
                                class="la la-file-text-o"></i>Reports</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(113, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.pickups.bookedvsreceived.index') }}">Booked
                                    VS Received VS Delivered VS Returned</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(64, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.pickup_note.index') }}">Pickup
                                    Notes
                                    Completed</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(328, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.multiple_iban.index') }}">Multiple IBAN Number
                                    Change</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(65, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.cargo_received.index') }}">Cargo
                                    Received</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(66, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.completed_delivery_notes.index') }}">Completed
                                    Delivery Notes</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(67, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.return_note.index') }}">Return
                                    Notes
                                    Completed</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(68, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.outstanding_shipments.index') }}">Outstanding
                                    Shipments</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(69, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.lead_time.index') }}">Lead
                                    Time</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(70, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.qsr.index') }}">Quality of
                                    Service</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(71, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.qa.index') }}">Quality
                                    Assurance</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(444, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.weight_qc.index') }}">Weight
                                    QC</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(72, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.customer_retention.index') }}">Customer
                                    Retention Rate</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(73, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.daily_pickup_sales.index') }}">Daily
                                    Pickup and Sales</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(74, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.customer_sales.index') }}">Monthwise
                                    Customer Sales</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(75, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.overall_sales.index') }}">Overall
                                    Sales</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(138, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.sales_person_performance.index') }}">Sales
                                    Person Performance</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(148, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.negative_balance_customers.index') }}">Negative
                                    Balance
                                    Customers</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(156, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.petty_cash.index') }}">Petty Cash
                                    Statements</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(153, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.call_verification.index') }}">Call
                                    Verification</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(169, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.fake_status.index') }}">Fake
                                    Statuses</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(170, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.debriefing.index') }}">Debriefing</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(172, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.cargo_returns_shipment.index') }}">Cargo
                                    Returns Shipment</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(174, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.return_reattempt_ratio.index') }}">Return
                                    Confirm To Re-Attempt Ratio</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(176, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.multiple_payment_report.index') }}">Multiple
                                    Payment</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(177, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.revenue.index') }}">Revenue</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(199, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.gst.index') }}">GST</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(200, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.crm.index') }}">CRM</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(210, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.summary.index') }}">Summary</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(246, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.account_activation.index') }}">Account
                                    Activation</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(248, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.adjustments.index') }}">Adjustments</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(252, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.sdn.index') }}">Station Deposit
                                    Notes</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(255, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.account_edit.index') }}">Rates
                                    Edit</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(257, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.bank_history.index') }}">Shipper
                                    Bank History</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(258, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.consignee_details.index') }}">Consignee Details
                                    History</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(259, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.booked_and_cancelled.index') }}">Booked And
                                    Cancelled</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(263, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.fake_status_shipments.index') }}">Fake
                                    Statuses Shipments</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(264, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.daily_visit.index') }}">Daily
                                    Visit</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(275, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.delivered_shipment.index') }}">Delivered
                                    Shipment</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(300, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.route_distribution.index') }}">Route Distribution
                                    Summary</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(301, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.destination_delivery_received.index') }}">Arrived
                                    At Destination VS Out For Delivery VS Receive</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(312, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.account_reconciliation.index') }}">Account
                                    Reconciliation</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(319, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.cargo_short_received_shipments.index') }}">Cargo
                                    Short Received Shipments</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(327, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.last_mile_status.index') }}">Last
                                    Mile Status</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(337, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.pickup_report.index') }}">Pickup
                                    Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(344, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.completed_aging.index') }}">Completed Aging</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(345, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.pending_cash_collection.index') }}">Pending Cash
                                    Collection</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(356, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.station_recovery.index') }}">Station Recovery</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(345, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.not_attempted_aging.index') }}">Not Attempted Aging
                                    Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(345, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.daily_monthly_adjustment.index') }}">Daily/Month
                                    Adjustment Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(395, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.petty_cash_expense_summary.index') }}">Petty Cash
                                    Expense Summary Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(401, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.app_efficiency.index') }}">App
                                    Efficiency Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(414, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.month_closing.individual.index') }}">Month Closing
                                    - Individual</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(415, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.month_closing.pivot.index') }}">Month Closing -
                                    Pivot</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(437, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.last_mile_app.index') }}">Last
                                    Mile App</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(472, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.master_cargo.bag.in_transit.index') }}">In Transit
                                    Report Bag Wise</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(476, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.master_cargo.short_received_shipments.index') }}">Master
                                    Cargo Short Received Shipments</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(555, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.manifest.short_received_shipments.index') }}">Manifest
                                    Short Received Shipments</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(493, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.retail_sales.index') }}">Retail
                                    Sales</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(502, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.shipper_insurance.index') }}">Shipper Insurance</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(532, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.work_code_master.index') }}">Work
                                    Code Master</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(524, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.operation_service_level.index') }}">Operation
                                    Service Level</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(624, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.reverse_pickup.index') }}">Reverse Pickup</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(633, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.sales_incentive.index') }}">Sales
                                    Incentive Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(634, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.sales_incentive.consolidated') }}">Consolidated
                                    Sales Incentive Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(642, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.dws_report.index') }}">DWS
                                    Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(647, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.osa_charges.index') }}">OSA
                                    Charges Log Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(653, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.revert.index') }}">Return Revert
                                    Log Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(673, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.crm_count.index') }}">CRM Count
                                    Report</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(679, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.pickup_history_cn_wise.index') }}">Pickup History
                                    (CN Wise)</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(676, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.debriefing.agent_index') }}">Debriefing Agent
                                    Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(688, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.crm_special_approval.index') }}">CRM Special
                                    Approval</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(705, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.rider_unresponsive_report.index') }}">Rider
                                    Unresponsive Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(780, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.mms.index') }}">MMS Report</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(786, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.employee_confirmation.index') }}">Employee
                                    Confirmation Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(784, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.crm_agent_wise_report.index') }}">CRM Agent Wise
                                    Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(793, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.ssr.index') }}">Summaries
                                    Sale (SSR)</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(794, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.shipper_summary.index') }}">Shipper Summary</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(824, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.one_link_charges_summary.index') }}">1link Shipment
                                    Wise Summary</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(823, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.rider_pickup.index') }}">Rider
                                    Pickup Report</a></li>
                            <li><a class="menu-item" href="{{ route('admin.reports.rider_pickup.index') }}">Rider Wise Pickup</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(839, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.reports.revenue_report_by_invoice.index') }}">Revenue
                                    Report By Invoice</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(879, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.project_arrival.index') }}">Project Arrival</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(880, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.reports.rider_picked.index') }}">Rider-Picked Status (W/O Arrival)</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(877, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.reports.quick_scanned_report.index') }}">Quick Scanned Report</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(886, session('permissions')))
                        <li><a class="menu-item" href="{{ route('admin.reports.overland.index') }}">Overland Report</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (session('role_id') == 1 || count(array_intersect([81, 85, 88, 92, 96, 558, 100, 131, 205, 231, 104, 116, 149, 150, 151, 152, 154, 157, 158, 171, 175, 188, 189, 192, 197, 198, 214, 228, 229, 230, 231, 237, 253, 302, 311, 313, 314, 318, 320, 329, 333, 362, 367, 388, 375, 377, 378, 379, 380, 387, 384, 385, 394, 417, 418, 425, 438, 443, 447, 462, 477, 488, 491, 494, 498, 499, 526, 544, 558, 565, 580, 581, 582, 601, 616, 646, 644, 656, 659, 661, 664, 660, 667, 668, 680, 674, 682, 683, 689, 697, 701, 708, 710, 714, 716, 747, 761, 788, 820, 826, 836,846,861,887], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                                class="la la-cogs"></i>Settings</span></a>
                    <ul class="menu-content">

                        @if (session('role_id') == 1 ||
                                count(array_intersect(
                                        [
                                            149,
                                            214,
                                            228,
                                            302,
                                            313,
                                            314,
                                            318,
                                            367,
                                            388,
                                            498,
                                            580,
                                            558,
                                            646,
                                            644,
                                            660,
                                            667,
                                            668,
                                            701,
                                            716,
                                            820,
                                            826,
                                            846,
                                            861,
                                        ],
                                        session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Shippers</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(149, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.auto_account_disabled_days.auto_index') }}">Auto
                                                Account Disabled Days</a></li>
                                    @endif
                                    {{--                                    @if (session('role_id') == 1 || in_array(228, session('permissions'))) --}}
                                    {{--                                        <li><a class="menu-item" href="{{ route('admin.settings.stock_movement.index') }}">Packaging Material Stock Movement Account</a></li> --}}
                                    {{--                                    @endif --}}
                                    @if (session('role_id') == 1 || in_array(214, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.packaging.types.index') }}">Packaging Types</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(302, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.foc_account.index') }}">FOC
                                                Accounts</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(558, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.ccd_booking.index') }}">CCD
                                                Booking</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(303, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.minimum_chargeable_weight.index') }}">Minimum
                                                Chargeable Weight</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(313, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.sales.projection.percentage.index') }}">Projection
                                                Percentage</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(314, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.sales.projection.reasons.index') }}">Projection
                                                Reasons</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(318, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.sales.projection.shipments.index') }}">Projection
                                                Shipments</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(367, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.nsa_account.index') }}">NSA
                                                Accounts</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(580, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.carrefour_account.index') }}">Carrefour
                                                Accounts</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(388, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.sms_shipper_wise.index') }}">Arrived
                                                At Origin Sms For Consignee</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(646, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.shippers.status_webhook.index') }}">Shippers
                                                Status Webhook Subscription</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(644, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.omni.index') }}">Omni User Setting</a>
                                        </li>
                                    @endif


                                    @if (session('role_id') == 1 || in_array(667, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.shippers_origin_change.index') }}"><span
                                                    class="menu-title">Shipper Origin Change</span></a> </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(668, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.shippers_return_address.index') }}"><span
                                                    class="menu-title">Shipper Return Address</span></a> </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(689, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.return_shipments_address.index') }}"><span
                                                    class="menu-title">Return Shipments Address Change</span></a> </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(701, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.referral.index') }}"><span
                                                    class="menu-title">Referral Module</span></a> </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(716, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.invoice_against_return_delivered_shipper.index') }}"><span
                                                    class="menu-title">Invoice Against Return Delivered
                                                    Shipper</span></a> </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(788, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.complain_portal_shippers.index') }}">Complaint
                                                Portal Shippers</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(820, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.non_cod_otp_shippers.index') }}">Non-Cod
                                                OTP Shippers Setting</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(826, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.consignee_refused_otp_bypass.index') }}">Consignee
                                                Refusal OTP Bypass</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(846, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.star_shippers.index') }}">Star
                                                Shippers</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(861, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.sms_notification_return_delivered_to_shipper.index') }}">
                                                SMS Notification Return Delivered to shipper
                                            </a>
                                        </li>
                                    @endif


                                    @if (session('role_id') == 1 || in_array(889, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.rv_disable_shippers.index') }}">
                                                Rv Disable Shippers
                                            </a>
                                        </li>
                                    @endif
                                        @if (session('role_id') == 1 || in_array(861, session('permissions')))
                                            <li><a class="menu-item"
                                                   href="{{ route('admin.settings.sms_notification_return_delivered_to_shipper.index') }}">
                                                    SMS Notification Return Delivered to shipper
                                                </a>
                                            </li>
                                        @endif
										@if (session('role_id') == 1 || in_array(868, session('permissions')))
                                            <li><a class="menu-item"
                                                   href="{{ route('admin.settings.mms_report.index') }}">MMS Report
                                                    Setting</a> </li>
                                        @endif
                                </ul>

                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([116, 150, 154, 197, 256, 375, 887], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Bookings</span></a>
                                <ul class="menu-content">

                                    @if (session('role_id') == 1 || in_array(116, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.shipment_cancellation_cut_off_days.index') }}">Shipment
                                                Cancellation Cut-Off Days</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(150, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.non_service_area.index') }}">Non
                                                Service Area</a></li>
                                    @endif
                                    <li class=" nav-item"><a href="#"><span
                                                class="menu-title">Walk-In</span></a>
                                        <ul class="menu-content">

                                            @if (session('role_id') == 1 || in_array(154, session('permissions')))
                                                <li><a class="menu-item"
                                                        href="{{ route('admin.settings.walk_in.index') }}">Domestic</a>
                                                </li>
                                                <li><a class="menu-item"
                                                        href="{{ route('admin.settings.international_walk_in.index') }}">International</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                    @if (session('role_id') == 1 || in_array(197, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.cod_cap_zones.index') }}">COD CAP for
                                                Zone Classes</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(256, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.consolidation.max.index') }}">Maximum
                                                Consolidation Shipments</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(375, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.restrict_cities_intercept.index') }}">Restrict
                                                Cities For Saver Plus Shipments</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(887, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.airway_bill_address_visibility.index') }}">Airway Bill Address Visibility</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([104, 326, 761, 828], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">First Mile</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(104, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.pickup.index') }}">Pickup Weight
                                                Threshold</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(338, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.pickup.pickup_settings') }}">Pickup
                                                Settings</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(761, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.pickup.weight_bypass') }}">Weight
                                                ByPass</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(828, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.project_arrival_shippers.index') }}">Project
                                                Arrival Shippers</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 ||
                                count(array_intersect([198, 387, 385, 498, 499, 544, 682], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Supply Chain</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(544, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo.mapping.manifest.index') }}">Mapping
                                                (Cargo Manifest)</a></li>
                                    @endif
                                    {{--                                    @if (session('role_id') == 1 || in_array(682, session('permissions'))) --}}
                                    {{--                                            <li><a class="menu-item" href="{{ route('admin.cargo_manifest.draft.setting') }}">Cargo Manifest Draft Setting</a></li> --}}
                                    {{--                                        @endif --}}
                                    @if (session('role_id') == 1 || in_array(198, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.cargo.mapping.index') }}">Mapping</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(387, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.short_received_hub_wise_cron.index') }}">Short
                                                Received Hub Wise Report Time</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(385, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.runner.index') }}">Runner Report</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(498, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.fleet.index') }}">Fleet Management</a>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(499, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.route_management.index') }}">Route
                                                Management</a></li>
                                    @endif
                                </ul>

                            </li>
                        @endif
                        @if (session('role_id') == 1 ||
                                count(array_intersect(
                                        [
                                            88,
                                            92,
                                            96,
                                            131,
                                            192,
                                            205,
                                            231,
                                            253,
                                            335,
                                            377,
                                            378,
                                            379,
                                            380,
                                            425,
                                            443,
                                            488,
                                            526,
                                            562,
                                            659,
                                            680,
                                            674,
                                            683,
                                            684,
                                            707,
                                            714,
                                            747,
                                            836,
                                        ],
                                        session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Last Mile</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(192, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.return_note_restriction_bypass.index') }}">Return
                                                Note Restriction Bypass</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(231, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.delivery_call_verification_ratio.index') }}">Delivery
                                                Call Verification Ratio</a></li>
                                    @endif
                                    @if (session('role_id') == 1 ||
                                            count(array_intersect([88, 92, 96, 131, 205, 377, 378, 379, 380, 425, 443, 562, 707], session('permissions'))) !== 0)
                                        <li class=" nav-item"><a href="#"><span class="menu-title"
                                                    data-i18n="nav.dash.main">Network Management</span></a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(131, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.management.zonal.index') }}">Zonal</a>
                                                    </li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(88, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.management.city.index') }}">City</a>
                                                    </li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(92, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.management.route.index') }}">Route</a>
                                                    </li>
                                                @endif

                                                @if (session('role_id') == 1 ||
                                                        count(array_intersect([377, 378, 379, 380, 425, 562, 707], session('permissions'))) !== 0)
                                                    <li class=" nav-item"><a href="#"><span class="menu-title"
                                                                data-i18n="nav.dash.main">Riders</span></a>
                                                        <ul class="menu-content">
                                                            @if (session('role_id') == 1 || in_array(425, session('permissions')))
                                                                <li><a class="menu-item"
                                                                        href="{{ route('admin.management.riders.rider_request.index') }}">Riders
                                                                        Request</a></li>
                                                            @endif
                                                            @if (session('role_id') == 1 || in_array(377, session('permissions')))
                                                                <li><a class="menu-item"
                                                                        href="{{ route('admin.management.riders.permanent.index') }}">Permanent</a>
                                                                </li>
                                                            @endif
                                                            @if (session('role_id') == 1 || in_array(378, session('permissions')))
                                                                <li><a class="menu-item"
                                                                        href="{{ route('admin.management.riders.incentive.index') }}">Incentive</a>
                                                                </li>
                                                            @endif
                                                            @if (session('role_id') == 1 || in_array(379, session('permissions')))
                                                                <li><a class="menu-item"
                                                                        href="{{ route('admin.management.riders.blacklist.index') }}">Blacklisted</a>
                                                                </li>
                                                            @endif
                                                            @if (session('role_id') == 1 || in_array(380, session('permissions')))
                                                                <li><a class="menu-item"
                                                                        href="{{ route('admin.management.riders.sms_history.index') }}">SMS
                                                                        History</a></li>
                                                            @endif
                                                            @if (session('role_id') == 1 || in_array(562, session('permissions')))
                                                                <li><a class="menu-item"
                                                                        href="{{ route('admin.settings.rider_shipment_attempt.index') }}">Rider
                                                                        Shipments Attempt</a></li>
                                                            @endif
                                                            @if (session('role_id') == 1 || in_array(707, session('permissions')))
                                                                <li><a class="menu-item"
                                                                        href="{{ route('admin.settings.rider_deactivation_cron.index') }}">Rider
                                                                        Deactivation Cron</a></li>
                                                            @endif
                                                        </ul>
                                                    </li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(205, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.management.city_list') }}">Walk-In
                                                            Cities List</a></li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(443, session('permissions')))
                                                    <li class=" nav-item"><a href="#"><span class="menu-title"
                                                                data-i18n="nav.dash.main">Territory</span></a>
                                                        <ul class="menu-content">

                                                            <li><a class="menu-item"
                                                                    href="{{ route('admin.management.territory.index') }}">Add
                                                                    Territory</a></li>

                                                            <li><a class="menu-item"
                                                                    href="{{ route('admin.management.area.index') }}">Add
                                                                    Area</a></li>
                                                        </ul>
                                                @endif

                                            </ul>
                                        </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(253, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.return_confirmation_pending_shipment_selection_time.index') }}">Return
                                                Confirmation Pending Shipment Selection Time</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(335, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.return.reason.index') }}">Return
                                                Reasons</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(680, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.rcp_sms.index') }}">RCP SMS</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(355, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.station_recovery_cron.index') }}">Station
                                                Recovery Cron</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(384, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.restrict_parcels_attempt.index') }}">Restrict
                                                Shipper Parcels Attempts</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(488, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.rcp_tat.index') }}">Return
                                                Confirmation Pending TAT Setting</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(858, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.debriefing_time_setting.index') }}">Debriefing
                                                Time Setting</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(858, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.debriefing_role_setting.index') }}">Caller
                                                Agent Role Assigning</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(674, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.debriefing_break_time.index') }}">Debriefing
                                                Break Time Setting</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(659, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.reattempt_percentage.index') }}">Re-attempt
                                                Percentage Setting</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(683, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.consignee_sms_expire.index') }}">Consignee
                                                SMS Expiration Time</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(684, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.return_reason_mandatory.index') }}">Return
                                                Reason Mandatory</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(714, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.undelivered_sms_hub_wise.index') }}">Undelivered
                                                SMS City Wise</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(747, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.delivery_area_keyword.index') }}">Delivery
                                                Area Keyword</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(772, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.booking_destination_keyword.index') }}">Booking
                                                Destination Keyword</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(836, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.auto_delivery_note_verification.index') }}">Auto
                                                Delivery Note Verification</a></li>
                                    @endif

                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 ||
                                count(array_intersect([157, 158, 171, 189, 229, 230, 362, 462, 825,851,854], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Financials</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || count(array_intersect([157, 158, 462], session('permissions'))) !== 0)
                                        <li><a class="menu-item" href="#">Petty Cash</a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(157, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.settings.petty_cash.heads.index') }}">Heads</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(158, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.settings.petty_cash.titles.index') }}">Titles</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(462, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.settings.petty_cash.consignee.index') }}">Hub
                                                            Assigning</a></li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(171, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.auto_invoice_generation_and_due_date.index') }}">Auto-Invoice
                                                Generation & Due Date Length</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(189, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.fuel_factor.index') }}">Fuel
                                                Factor</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(229, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.weight_factor.index') }}">Weight
                                                Charges Factor</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(230, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.ibft_charges.index') }}">IBFT
                                                Charges</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(362, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.over_payment_limit.index') }}">Over
                                                Payment Limit</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(391, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.pickup_address_wise_payment_accounts.index') }}">Pickup
                                                Wise Payment Accounts</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(825, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.onelink_payment_charges.index') }}">1Link
                                                Payment Charges</a></li>
                                    @endif


                                    @if (session('role_id') == 1 || in_array(851, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.fintech_company_charges.index') }}">Fintech Company Charges</a></li>
                                    @endif  


                                    @if (session('role_id') == 1 || in_array(854, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.standard_fintech_charges.index') }}">Standard Fintech Charges</a></li>
                                    @endif  


                                </ul>
                            </li>
                        @endif


                        @if (session('role_id') == 1 ||
                                session('role_id') == 6 ||
                                count(array_intersect([188, 237, 260, 274, 320, 325, 351, 616, 639], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">CRM</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || session('role_id') == 6 || in_array(188, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.crm.permissions') }}">Permissions</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(237, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.crm_cut_off_time_and_holidays.index') }}">TAT
                                                Cut-Off Time and Holidays</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(260, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.crm_case_nature_types.index') }}">Case
                                                Nature Types</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(274, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.crm_reopen.index') }}">Crm Re-Open
                                                Count</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(320, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.delay_in_delivery_massage.index') }}">Delay
                                                In Delivery Message</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(325, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.crm_comment.index') }}">Crm
                                                Comment</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(351, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.default_agent.index') }}">Default
                                                Agent</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(616, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.auto_assigning.index') }}">Auto
                                                Assigning</a></li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(639, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.auto_tagging.index') }}">Auto
                                                Tagging</a></li>
                                    @endif


                                    @if (session('role_id') == 1 ||
                                            session('role_id') == 6 ||
                                            count(array_intersect([347, 348, 349, 350], session('permissions'))) !== 0)
                                        <li class=" nav-item"><a href="#"><span
                                                    class="menu-title">Escalations</span></a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || session('role_id') == 6 || in_array(347, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.settings.escalation.launched.index') }}">Launched</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || session('role_id') == 6 || in_array(348, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.settings.escalation.in_process.index') }}">In-Process</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || session('role_id') == 6 || in_array(349, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.settings.escalation.levels.index') }}">Levels</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || session('role_id') == 6 || in_array(350, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.settings.escalation.tagging.index') }}">Tagging</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 ||
                                count(array_intersect([81, 85, 100, 417, 418, 152, 279, 358, 394, 447, 466, 601], session('permissions'))) !==
                                    0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Support</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 ||
                                            count(array_intersect([81, 85, 100, 152, 279, 358, 394, 447, 466, 601], session('permissions'))) !== 0)
                                        @if (session('role_id') == 1 || in_array(152, session('permissions')))
                                            <li><a class="menu-item"
                                                    href="{{ route('admin.settings.ticker.index') }}">Ticker</a></li>
                                        @endif


                                        @if (session('role_id') == 1 || in_array(466, session('permissions')))
                                            <li><a class="menu-item"
                                                    href="{{ route('admin.settings.rider_ticker.index') }}">App
                                                    Slider</a></li>
                                        @endif

                                        @if (session('role_id') == 1 || count(array_intersect([81, 85, 279, 394, 447], session('permissions'))) !== 0)
                                            <li class=" nav-item"><a href="#"><span class="menu-title">User
                                                        Management</span></a>
                                                <ul class="menu-content">
                                                    @if (session('role_id') == 1 || in_array(81, session('permissions')))
                                                        <li><a class="menu-item"
                                                                href="{{ route('admin.user_management.users.index') }}">Users</a>
                                                        </li>
                                                    @endif

                                                    @if (session('role_id') == 1 || in_array(85, session('permissions')))
                                                        <li><a class="menu-item"
                                                                href="{{ route('admin.user_management.roles.index') }}">Roles</a>
                                                        </li>
                                                    @endif

                                                    @if (session('role_id') == 1 || in_array(279, session('permissions')))
                                                        <li><a class="menu-item"
                                                                href="{{ route('admin.settings.multiple_sale_tagging.index') }}">Multiple
                                                                Sale Tagging</a>
                                                        </li>
                                                    @endif

                                                    @if (session('role_id') == 1 || in_array(394, session('permissions')))
                                                        <li><a class="menu-item"
                                                                href="{{ route('admin.user_management.user_requests.index') }}">User
                                                                Requests</a>
                                                        </li>
                                                    @endif
                                                    @if (session('role_id') == 1 || in_array(447, session('permissions')))
                                                        <li><a class="menu-item"
                                                                href="{{ route('admin.user_management.fuel_management.index') }}">Fuel
                                                                Management</a></li>
                                                    @endif


                                                </ul>
                                            </li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(100, session('permissions')))
                                            <li class=" nav-item"><a
                                                    href="{{ route('admin.notifications.index') }}"><span
                                                        class="menu-title">Notifications</span></a>
                                            </li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(601, session('permissions')))
                                            <li class=" nav-item"><a
                                                    href="{{ route('admin.app_notifications.index') }}"><span
                                                        class="menu-title">App-Notifications</span></a>
                                            </li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(358, session('permissions')))
                                            <li class=" nav-item"><a
                                                    href="{{ route('admin.settings.holidays.index') }}"><span
                                                        class="menu-title">Holidays</span></a>
                                            </li>
                                        @endif
                                        @if (session('role_id') == 1 || count(array_intersect([417, 418], session('permissions'))) !== 0)
                                            <li class=" nav-item"><a href="#"><span class="menu-title">Month
                                                        Closing</span></a>
                                                <ul class="menu-content">
                                                    @if (session('role_id') == 1 || in_array(417, session('permissions')))
                                                        <li><a class="menu-item"
                                                                href="{{ route('admin.settings.month_closing.types.index') }}">Types</a>
                                                        </li>
                                                    @endif
                                                    @if (session('role_id') == 1 || in_array(418, session('permissions')))
                                                        <li><a class="menu-item"
                                                                href="{{ route('admin.settings.month_closing.status.index') }}">Status</a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 ||
                                count(array_intersect([151, 175, 269, 311, 343, 357, 359, 565], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Reports</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(151, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.daily_pickup_sales_cron.index') }}">Daily
                                                Pickup & Sales Cron Time</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(175, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.debriefing_report_cut_off_time.index') }}">Debriefing
                                                Report Cut-Off Time</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(269, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.return_delivered_to_shipper_email_cut_off_time.index') }}">Return
                                                Delivered To Shipper Email Cut-Off Time</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(311, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.overnight_overland_cargo_report.index') }}">Rush
                                                Saver Plus Cargo Report</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(343, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.aging_report.index') }}">Pending Cash
                                                Collection & Completed Aging Reports</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(357, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.zero_charges.index') }}">Zero Charges
                                                Report Settings</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(359, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.not_attempted_cron.index') }}">Not
                                                Attempted Report Cron Time</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(565, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.last_mile_cron.index') }}">Last Mile
                                                Status Cron Time</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([329, 330], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Blacklist</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(329, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.blacklist.index') }}">Category</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(330, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.blacklist.search.index') }}">Search
                                                Consignee</a></li>
                                    @endif
                                </ul>

                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([331, 332], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Commission</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(331, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.commission.index') }}">Sales Tier</a>
                                        </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(332, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.commission.percentage.index') }}">Set
                                                Commission</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || count(array_intersect([431, 432, 474], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Retail</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || count(array_intersect([431, 432, 474], session('permissions'))) !== 0)
                                        <li class=" nav-item"><a href="#"><span class="menu-title">Store
                                                    Management</span></a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 || in_array(474, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.retail.users.index') }}">Users</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(584, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.retail.add.rates') }}">Retail
                                                            Standard Rates</a></li>
                                                @endif

                                                @if (session('role_id') == 1 || in_array(431, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.retail.franchise.index') }}">Franchise</a>
                                                    </li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(432, session('permissions')))
                                                    <li><a class="menu-item"
                                                            href="{{ route('admin.retail.trax_center.index') }}">Trax
                                                            Center</a></li>
                                                @endif
                                                @if (session('role_id') == 1 || in_array(585, session('permissions')))
                                                    <li class=" nav-item"><a
                                                            href="{{ route('admin.retail.international.rates.index') }}"><span
                                                                class="menu-title">Retail Rate Upload</span></a> </li>
                                                @endif

                                            </ul>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([438, 477, 581], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span
                                        class="menu-title">International</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(438, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.international_rates.index') }}"><span
                                                    class="menu-title">Rate Setting</span></a> </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(477, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.international_rates.upload.index') }}"><span
                                                    class="menu-title">Rate Upload</span></a> </li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(581, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.dhl_sync_time.index') }}">DHL Sync
                                                Time</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(582, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.international_automation_user.index') }}">Automation
                                                User</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([491, 494], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">HR</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(491, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.hr.rider_incentive.index') }}"><span
                                                    class="menu-title">Rider Incentive</span></a> </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(494, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.hr.rider_incentive.cron.index') }}"><span
                                                    class="menu-title">Rider Incentive Cron</span></a> </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([630, 631, 632], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Sales</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(630, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.sales.territory.territoryindex') }}"><span
                                                    class="menu-title">Sales Territory</span></a> </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(631, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.sales.designation.designationindex') }}"><span
                                                    class="menu-title">Sales Designations</span></a> </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(632, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.sales.incentive.index') }}"><span
                                                    class="menu-title">Incentive Settings</span></a> </li>
                                    @endif

                                    @if (session('role_id') == 1 || in_array(681, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.sales.user_restriction.index') }}"><span
                                                    class="menu-title">User Restriction</span></a> </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([534], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Telenor</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(534, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.settings.shipment_status_eta.index') }}"><span
                                                    class="menu-title">Shipment Status ETA</span></a> </li>
                                    @endif

                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([656], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Quality
                                        Assurance</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 || in_array(656, session('permissions')))
                                        <li class=" nav-item"><a
                                                href="{{ route('admin.qa_evaluation.edit_activities') }}"><span
                                                    class="menu-title">CX Evaluation Activities</span></a> </li>
                                    @endif

                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([661, 664], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Leads
                                        Management</span></a>
                                <ul class="menu-content">

                                    @if (session('role_id') == 1 || in_array(661, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.lead_tagging.index') }}">Auto
                                                Tagging</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(664, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.lead_zones.index') }}">Zone
                                                Tagging</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(671, session('permissions')))
                                        <li><a class="menu-item"
                                                href="{{ route('admin.settings.lead_notification.index') }}">Notifications</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(697, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.settings.auto_tag_territories.index') }}">Auto Tag
                                    Territories</a></li>
                        @endif
                        <!--                         add side bar-->


                    </ul>
                </li>
            @endif

            <!-- Handover -->
            @if (session('role_id') == 1 || count(array_intersect([339, 340, 341, 342], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title"><i
                                class="la la-hand-o-right"></i>Shipment Handover</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(339, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.handover.create.index') }}">Create</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(341, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.handover.receive.index') }}">Receive</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(342, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.handover.list.index') }}">List</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(340, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.handover.responsibles.index') }}">Responsibles</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            <li class=" nav-item"><a href="#"><span class="menu-title"><i class="ft-users"></i>Human
                        Resource</span></a>
                <ul class="menu-content">
                    @if (session('role_id') == 1 || count(array_intersect([449, 465, 467, 478, 481, 484, 492, 506, 568, 596, 592, 613, 717, 783,829, 831,845], session('permissions'))) !== 0)

                        <li><a class="menu-item" href="{{ route('admin.human_resource.download_docs') }}">Download
                                Docs</a></li>

                        @if (session('role_id') == 1 || in_array(465, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.attendance.index') }}">Employee
                                    Attendance</a></li>
                            <li><a class="menu-item"
                                    href="{{ route('admin.attendance.horizontal.index') }}">Employee Attendance
                                    (Horizontal)</a></li>
                        @endif


                        @if (session('role_id') == 1 || in_array(467, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.employee_directory.index') }}">Employee
                                    Directory</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(478, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.reporting_location.index') }}">Reporting
                                    Location</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(592, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.employee_shifts.index') }}">Employee
                                    Shifts</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(481, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.designation.index') }}">Designation</a>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || in_array(484, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.department.index') }}">Department</a></li>
                        @endif

                        @if (session('role_id') == 1 || in_array(492, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.rider_incentive.index') }}">Rider
                                    Incentives</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(506, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.human_resource.erf.index') }}">ERF</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(568, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.human_resource.fnf.index') }}">FNF</a>
                            </li>
                        @endif

                        @if (session('role_id') == 1 || in_array(613, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.leave.index') }}">Employee Leaves</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(717, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.adjustment.index') }}">Employee Attendance
                                    Adjustment</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(829, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.employee_penalty.index') }}">Employee
                                    Penalty</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(831, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.human_resource.fuel_allocation.index') }}">Rider Fuel
                                    Allocation</a></li>
                        @endif

                    @endif
                    @if (session('role_id') == 1 || in_array(783, session('permissions')))
                        <li><a class="menu-item"
                                href="{{ route('admin.human_resource.employee_confirmation.index') }}">Employee
                                Confirmation</a></li>
                    @endif
                    @if (Auth::user()->trax_id != null)
                        <li><a class="menu-item"
                                href="{{ route('admin.human_resource.payslip.index') }}">Payslips</a></li>
                    @endif

                    @if (session('role_id') == 1 || in_array(845, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.human_resource.employee_areas.index')}}">Assign Employee Area</a></li>
                    @endif
                    


                </ul>
            </li>
            @if (session('role_id') == 1 || count(array_intersect([535, 648, 692, 702, 777], session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title"><i class="ft-users"></i>Quality
                            Assurance</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(535, session('permissions')))
                            <li><a class="menu-item"
                                    href="{{ route('admin.incidence_monitoring.index') }}">Incidence Monitoring</a>
                            </li>
                        @endif
                        @if (session('role_id') == 1 || in_array(648, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.qa_evaluation.index') }}">CX
                                    Evaluation</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(692, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.qa.high_alert.shippers.index') }}">High
                                    Alert Shippers</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(702, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.dispute.shipments.index') }}">Dispute
                                    Shipments</a></li>
                        @endif
                        <ul class="menu-content">
                            @if (session('role_id') == 1 || count(array_intersect([751, 752], session('permissions'))) !== 0)
                                <li class=" nav-item"><a href="#"><span class="menu-title">NPS</span></a>
                                    <ul class="menu-content">
                                        @if (session('role_id') == 1 || in_array(751, session('permissions')))
                                            <li><a class="menu-item"
                                                    href="{{ route('admin.nps.index') }}">Survey</a></li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(752, session('permissions')))
                                            <li><a class="menu-item"
                                                    href="{{ route('admin.nps.response.report') }}">Response
                                                    Report</a></li>
                                        @endif
                                        @if (session('role_id') == 1 || in_array(752, session('permissions')))
                                            <li><a class="menu-item"
                                                    href="{{ route('admin.nps.consolidate.report') }}">Consolidated
                                                    Report</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                        </ul>
                        @if (session('role_id') == 1 || in_array(777, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.qa.cx_training.index') }}">CX
                                    Training</a></li>
                        @endif

                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>
