<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-border"
  data-scroll-to-active="true">
    <div class="main-menu-content">
      <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

        <!-- official -->
        <li class=" nav-item"><a href="{{route('cod.dashboard')}}"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span></a></li>
        <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Order Management</span></a>
          <ul class="menu-content">
            <li><a class="menu-item" href="{{route('cod.orders.index')}}" data-i18n="nav.dash.ecommerce">Order Management</a></li>
          </ul>
        </li>

        @if (session('user_type') == 1 || count(array_intersect([1, 3], session('permissions'))) !== 0)
          <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Book Shipments</span></a>
            <ul class="menu-content">
              @if (session('user_type') == 1 || in_array(1, session('permissions')))
                <li><a class="menu-item" href="{{ route('cod.shipment.book.index') }}">Book by Order Form</a></li>
                <li><a class="menu-item" href="{{ route('cod.shipment.book.excel_index') }}">Book by Excel Sheet</a></li>
              @endif

              @if (session('user_type') == 1 || in_array(3, session('permissions')))
                <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.index') }}">Receiving Sheet</a></li>
                <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet_history.index') }}">Receiving Sheet History</a></li>
              @endif
            </ul>
          </li>
        @endif

        @if(session('user_type') == 1 || session('packaging_charges_check') && in_array(4, session('permissions')))
          <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Packaging</span></a>
              <ul class="menu-content">
                  <li><a class="menu-item" href="{{ route('cod.packaging.requests.index') }}">Request</a></li>
              </ul>
          </li>
        @endif

        @if (session('user_type') == 1 || in_array(5, session('permissions')))
          <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Finance Payments</span></a></li>
        @endif

        @if (session('user_type') == 1 || count(array_intersect([6, 7], session('permissions'))) !== 0)
          <li class=" nav-item"><a href="#"><i class="la la-exclamation-circle"></i><span class="menu-title" data-i18n="nav.dash.main">Dispute</span></a>
              <ul class="menu-content">
                  @if (session('user_type') == 1 || in_array(6, session('permissions')))
                    <li><a class="menu-item" href="{{ route('cod.dispute.index') }}">Log</a></li>
                  @endif

                  @if (session('user_type') == 1 || in_array(7, session('permissions')))
                    <li><a class="menu-item" href="{{ route('cod.dispute.rebook.index') }}">Rebook</a></li>
                  @endif
              </ul>
          </li>
        @endif

        @if (session('user_type') == 1)
          <li class=" nav-item"><a href="{{ route('cod.substitute_account_management.index') }}"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Substitute Accounts</span></a></li>
        @endif

        @if (session('user_type') == 1 || in_array(8, session('permissions')))
          <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Reports</span></a></li>
        @endif

        <li class=" nav-item"><a href="{{ route('cod.tracking.index') }}"><i class="la la-home"></i><span class="menu-title">Tracking</span></a></li>
      </ul>
    </div>
  </div>