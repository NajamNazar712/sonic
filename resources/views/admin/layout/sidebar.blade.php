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
                                         <li><a class="menu-item"
                                               href="{{ route('admin.shipment.book.logistic.book_index') }}">Logistic Book</a></li>
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
                    count(array_intersect([17, 20, 23, 123, 368, 369, 370, 446, 670, 830,919,920,921], session('permissions'))) !== 0)
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

                        @if (session('role_id') == 1 || count(array_intersect([17, 24, 271, 272, 366, 670, 830,919,920,921], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title">Pickups</span></a>
                                <ul class="menu-content">
                                    {{-- @if (session('role_id') == 1 || in_array(17, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.v3_pickups.add') }}">Add Pickup Request</a></li>
                                    @endif --}}
                                    @if (session('role_id') == 1 || in_array(920, session('permissions')))
                                        <li><a class="menu-item" href="{{ route('admin.v3_pickups.pending.index') }}">Pending Requests</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(921, session('permissions')))
                                        <li><a class="menu-item"
                                               href="{{ route('admin.v3_pickups.history.index') }}">Pickup Request
                                                History</a></li>
                                    @endif
                                    @if (session('role_id') == 1 || in_array(919, session('permissions')))
                                    <li><a class="menu-item"
                                           href="{{ route('admin.v3_pickups.pending.schedule.index') }}">Schedule Pickups</a></li>
                                    @endif
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
                                                href="{{ route('admin.v3_pickups.arrival.individual.index') }}">V3 Individual
                                                Arrival</a></li>
                                        <li><a class="menu-item"
                                                href="{{ route('admin.v2_pickups.arrival.individual.index_weight_scale') }}">Weight Scale Individual
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


            @if (session('role_id') == 1 || count(array_intersect([64,65,66,67,68,69,70,71,72,73,74,75,113,138,148,153,156,169,170,172,176,200,210,258,259,263,264,275,300,301,319,327,328,337,356,401,437,444,472,476,493,502,524,532,555,613,614,624,642,647,653,679,673,676,688,705,717,780,784,786,793,794,823,824,839,886,892,901,915,922,923],session('permissions'))) !== 0)
                <li class=" nav-item"><a href="#"><span class="menu-title"><i
                                    class="la la-file-text-o"></i>Reports</span></a>
                    <ul class="menu-content">
                        @if (session('role_id') == 1 || in_array(922, session('permissions')))
                             <li><a class="menu-item" href="{{ route('admin.reports.created_shipment.index') }}">Created Shipments vs Unpicked Shipments</a></li>
                        @endif
                        @if (session('role_id') == 1 || in_array(923, session('permissions')))
                            <li><a class="menu-item" href="{{ route('admin.reports.pickup_arival.index') }}">Rider Picked Vs Arrival Shipments</a></li>
                        @endif
                    </ul>
                </li>
            @endif

                @if (session('role_id') == 1 || count(array_intersect([81, 85, 88, 92, 96, 558, 100, 131, 205, 231, 104, 116, 149, 150, 151, 152, 154, 157, 158, 171, 175, 188, 189, 192, 197, 198, 214, 228, 229, 230, 231, 237, 253, 302, 311, 313, 314, 318, 320, 329, 333, 362, 367, 388, 375, 377, 378, 379, 380, 387, 384, 385, 394, 417, 418, 425, 438, 443, 447, 462, 477, 488, 491, 494, 498, 499, 526, 544, 558, 565, 580, 581, 582, 601, 616, 646, 644, 656, 659, 661, 664, 660, 667, 668, 680, 674, 682, 683, 689, 697, 701, 708, 710, 714, 716, 747, 761, 788, 820, 826, 836,846,861,887, 910], session('permissions'))) !== 0)
                            <li class=" nav-item"><a href="#"><span class="menu-title" data-i18n="nav.dash.main"><i
                                                class="la la-cogs"></i>Settings</span></a>
                                <ul class="menu-content">
                                    @if (session('role_id') == 1 ||
                                            count(array_intersect([81, 85, 100, 417, 418, 152, 279, 358, 394, 447, 466, 601, 912], session('permissions'))) !==
                                                0)
                                        <li class=" nav-item"><a href="#"><span class="menu-title">Support</span></a>
                                            <ul class="menu-content">
                                                @if (session('role_id') == 1 ||
                                                        count(array_intersect([81, 85, 100, 152, 279, 358, 394, 447, 466, 601], session('permissions'))) !== 0)
                                                  
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

                                                            </ul>
                                                        </li>
                                                    @endif
                                                
                                                  
                                                @endif
                                                @if (session('role_id') == 1 || in_array(516, session('permissions')))
                                                    <li><a class="menu-item" href="{{ route('admin.settings.admin_product.index') }}">Admin Products</a></li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

        </ul>
    </div>
</div>
