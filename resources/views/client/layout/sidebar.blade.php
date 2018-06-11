<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-border"
  data-scroll-to-active="true">
    <div class="main-menu-content">
      <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
        
      
        <!-- official -->
        <li class=" nav-item"><a href="/shipper/dashboard"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span></a></li>
        <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Order Management</span></a>
          <ul class="menu-content">
            <li><a class="menu-item" href="/shipper/order/management" data-i18n="nav.dash.ecommerce">Order Management</a></li>
          </ul>
        </li>

        <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Book Shipments</span></a>
          <ul class="menu-content">
            <li><a class="menu-item" href="{{ route('cod.shipment.book.index') }}">Book by Order Form</a></li>
            <li><a class="menu-item" href="{{ route('cod.shipment.book.excel_index') }}">Book by Excel Sheet</a></li>
            <li><a class="menu-item" href="{{ route('cod.shipment.receiving_sheet.index') }}">Receiving Sheet</a></li>
          </ul>
        </li>

         <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Profile Update</span></a></li>
         <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Excel Upload</span></a></li>
         <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Finance</span></a></li>
         <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Reports</span></a></li>
         <li class=" nav-item"><a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Tracking</span></a></li>
      </ul>
    </div>
  </div>