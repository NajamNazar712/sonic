@extends('client.layout.master')
@section('title','Pickups History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                     Pickups History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            <div class="container">
                                <div class="row justify-content-center">

                                    <div class="col">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                   <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_from_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_from_date" placeholder="Requested Date From">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                     <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_to_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_to_date" placeholder="Requested Date To">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button type="button" id="search_filter_btn"
                                                class="mb-1 btn btn-outline-primary btn-min-width"><i
                                                    class="la la-search" style="margin-right: 10px"></i> Search
                                        </button>
                                    </div>
                                </div>


                            </div>

                            {{-- <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <input type="hidden" value="0" id="status_filter_input" name="pickup_status_id">
                                    @foreach($statuses as $id => $status)
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary btn-min-width mr-1 mb-1 pickup_status_btn" rel="{{ $id }}">{{ $status['name'] }}
                                                ({{ $status['count'] }})
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div> --}}
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">ID</th>
                                    <th class="border-primary border-darken-1">Date & Time</th>
                                    <th class="border-primary border-darken-1">Ask Time</th>
                                    <th class="border-primary border-darken-1">Shipments/Pieces</th>
                                    <th class="border-primary border-darken-1">Weight (KG)</th>
                                    <th class="border-primary border-darken-1">Additional Services</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Shipments Picked</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Station</th>
                                    <th class="border-primary border-darken-1">Assigned Courier</th>
                                    <th class="border-primary border-darken-1">Special Request</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>


                <!--Shipments popup -->
                <div class="modal fade" id="bookings_modal" data-backdrop="static" role="dialog"
                     aria-labelledby="bookings_modal" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="bookings_modal_title">Booking Shipment(s)</h4>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Shipments popup -->

                <!--Shipments popup pending booking-->
                <div class="modal fade" id="pending_bookings_modal" data-backdrop="static" role="dialog"
                     aria-labelledby="pending_bookings_modal" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="pending_bookings_modal_title">Received Shipment(s)</h4>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Shipments popup -->

            </div>
        </div>
    </div>

    <!-- start of add request popup modal -->
    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="AddRequestModal"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add New Pickup</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        {{-- <div class="d-flex justify-content-start vh-100 pl-1">
                            <b class="text-dark"> Customer Information </b>
                        </div> --}}
                        <h4 class="text-center"><b>{{ucfirst(Auth::user()->name)}}</b></h4>
                        <form id="add_pickup_request" method="post" action="{{ route('cod.pickup.add') }}" >
                            @csrf
                            @method('post')
                            <input type="hidden" id="pickup_type_id" name="pickup_type_id" value="1">
                            <input type="hidden" id="regular_pickup" name="regular_pickup" value="1">
                                    <div class="tab-content px-1">
                                        <div role="tabpanel" class="tab-pane active" id="activeIcon32" aria-labelledby="activeIcon32-tab1" aria-expanded="true">

                                                <div class="row align-items-center mt-md-2">
                                                  
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>Pickup Address</label>
                                                            <select name="pickup_address_id" id="pickup_address_id" class="form-control select2" data-rule-required="true" data-msg-required="Shippers is Required">
                                                                @foreach ($pickup_addresses as $pickup_address)
                                                                     <option value="{{ $pickup_address->id }}" data-address="{{ $pickup_address->pickup_address }}" data-phone="{{ $pickup_address->phone }}" data-poc="{{ $pickup_address->poc }}">{{$pickup_address->city->name .'-'.$pickup_address->pickup_address  }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>Address</label>
                                                            <input type="text" placeholder="Address" name="address" id="reg_address" class="form-control" data-rule-required="true" data-msg-required="Address is Required"/>

                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>Phone</label>
                                                            <input type="text" placeholder="Caller Contact No" name="phone" id="reg_phone" class="form-control" data-rule-required="true"  data-msg-required="Contact Number is Required"/>

                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <p class="text-gray font-weight-bold">Contact Person </p>
                                                        <p class="text-dark" id="poc"></p>
                                                    </div>

                                                    <div class="col-6 mt-1">
                                                        <p class="text-gray">Mobile No </p>
                                                        <p class="text-dark" id="poc_phone"></p>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-start vh-100 pl-0">
                                                    <b class="text-dark"> Pickup Details </b>
                                                </div>

                                                <div class="d-flex justify-content-start vh-100 pl-0 mt-md-2">
                                                     <p class="text-gray mb-0"> Pickup Type </p>
                                                </div>

                                                <ul class="nav nav-tabs nav-iconfall custom-nav nav-justified w-100 mb-md-1" id="regular_section">
                                                    <li class="nav-item ml-md-0">
                                                        <a class="nav-link active p-0 pl-1 text-left onetime" id="oneTimePickup-tab1" data-toggle="tab"
                                                        href="#oneTimePickup" aria-controls="oneTimePickup" aria-expanded="true">
                                                        <b> One-time Pickup </b>
                                                        <p> Request a pickup for a single use. </p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link p-0 pl-1 text-left schedule" id="scheduledPickup-tab1" data-toggle="tab"
                                                        href="#scheduledPickup" aria-controls="scheduledPickup" aria-expanded="false">
                                                        <b> Scheduled Pickup </b>
                                                        <p> Set regular pickups for specific days every week.</p>
                                                        </a>
                                                    </li>
                                                </ul>

                                                <div class="tab-content">

                                                    <div role="tabpanel" class="tab-pane active" id="oneTimePickup" aria-labelledby="oneTimePickup-tab1" aria-expanded="true">

                                                    </div>

                                                    <div role="tabpanel" class="tab-pane" id="scheduledPickup" aria-labelledby="scheduledPickup-tab1" aria-expanded="false">

                                                    <div class="d-flex justify-content-start vh-100 pl-0 mt-md-1">
                                                        <p class="text-gray mb-0"> Scheduled Days </p>
                                                    </div>


                                                        <div class="d-flex justify-content-start vh-100 pl-0 mt-md-1 mb-md-1 scheduled_days_area">

                                                    
                                                        <div class="col-1 mb-0 item-column p-0">
                                                            <input class="apply-checked" type="checkbox" name='days[1]' id="mo">
                                                            <label for="mo" class="w-100 text-center" id="mo">Mo</label>
                                                        </div>

                                                        <div class="col-1 mb-0 item-column p-0">
                                                            <input class="apply-checked" type="checkbox" name='days[2]' id="tu">
                                                            <label for="tu" class="w-100 text-center" id="tu">Tu</label>
                                                        </div>

                                                        <div class="col-1 mb-0 item-column p-0">
                                                            <input class="apply-checked" type="checkbox" name='days[3]' id="we">
                                                            <label for="we" class="w-100 text-center" id="we">We</label>
                                                        </div>

                                                        <div class="col-1 mb-0 item-column p-0">
                                                            <input class="apply-checked" type="checkbox" name='days[4]' id="th">
                                                            <label for="th" class="w-100 text-center" id="th">Th</label>
                                                        </div>

                                                        <div class="col-1 mb-0 item-column p-0">
                                                            <input class="apply-checked" type="checkbox" name='days[5]' id="fr">
                                                            <label for="fr" class="w-100 text-center" id="fr">Fr</label>
                                                        </div>

                                                        <div class="col-1 mb-0 item-column p-0">
                                                            <input class="apply-checked" type="checkbox" name='days[6]' id="sa">
                                                            <label for="sa" class="w-100 text-center" id="sa">Sa</label>
                                                        </div>

                                                        </div>

                                                    </div>

                                                </div>

                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center vh-100 mt-md-0 px-1">
                                            <div class="col-6">

                                                <div class="form-group">
                                                    <div class="form-group input-group">
                                                        <div class="input-group-prepend">
                                                                    <span
                                                                            class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o"></span>
                                                                    </span>
                                                        </div>

                                                        <input type="text" name="pickup_date" class="form-control pickadate bg-primary border-primary white rounded-right require_one pickup_date" id="pickup_date" placeholder="Pickup Date*" data-rule-required="true" data-msg-required="Pickup Date is Required">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="preferred_time_range" id="preferred_time_range" class="form-control select2" data-rule-required="true" data-msg-required="Pickup Time is required">
                                                        {{-- @foreach($time_ranges as $time_range)
                                                            <option value="{{ $time_range->id }}">{{ $time_range->name }}</option>
                                                        @endforeach --}}

                                                    </select>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center px-1">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="product_id" id="product_select" class="form-control select2"
                                                            data-rule-required="true" data-msg-required="Product is required" >
                                                        {{-- @foreach($products as $product) --}}
                                                            <option value="{{ $product->id }}" selected>{{ $product->name }}</option>
                                                        {{-- @endforeach --}}
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="service_id" id="service_select" class="form-control select2">
                                                        @foreach ($product->subCategorySegment as $service)
                                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                    </div>
                                    <div class="row justify-content-start align-items-center vh-100 px-1">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="shipment_type_id" id="shipment_select" class="form-control select2"
                                                            data-rule-required="true" data-msg-required="Shipment Type is required">
                                                        @foreach($pickup_shipment_types as $shipment_type)
                                                            <option value="{{ $shipment_type->id }}">{{ $shipment_type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row align-items-center vh-100 px-1">
                                            <div class="col-4">
                                                {{-- input-group --}}
                                                <div class="form-group">
                                                    <label>Shipments</label>
                                                    <input type="text" id="shipments_count" placeholder="Shipments" name="shipments_count" class="form-control text-left"
                                                        data-rule-required="true" data-msg-required="No. of Shipments is required">
                                                </div>
                                            </div>

                                            <div class="col-4 pl-0">
                                                <div class="form-group">
                                                    <label>Total Pieces</label>
                                                    <input type="text" id="total_pieces_count" placeholder="Total Pieces" name="pieces" class="form-control text-left"
                                                        data-rule-required="true" data-msg-required="Total Pieces is required">
                                                </div>
                                            </div>
                                            <div class="col-4 pl-0">
                                                <div class="form-group">
                                                    <label>Weight</label>
                                                    <input type="text" id="estimated_weight" name="estimated_weight" class="form-control text-left" placeholder="Weight" data-rule-required="true" data-msg-required="Weight is required">
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center px-1">
                                    </div>
                                    <div class="row justify-content-center align-items-center vh-100 px-1">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Additional Request</label>
                                                    <textarea type="text" name="remarks" class="form-control" placeholder="Additional Request"></textarea>
                                                </div>
                                            </div>
                                    </div>
                                <!-- start additional services -->
                                    <div class="d-flex justify-content-start vh-100 pl-1">
                                         <b class="text-dark"> Additional Services </b>
                                    </div>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <!-- start service list -->

                                            <div class="services-list col-12">
                                                @foreach($additional_services as $additional_service)

                                                <div class="d-flex align-items-center service-item mt-md-1">
                                                    <div class="col-md-6">
                                                        <p class="mb-0 text-dark"> {{ $additional_service->name }} </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group input-group mb-0">
                                                            <input type="text" name="additional_services[{{ $additional_service->id }}]" id="a_service_{{ $additional_service->id }}" value="0" class="form-control text-center quantity" placeholder="Item Qty.">
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                        <!-- end service list -->

                                    </div>
                                <!-- end additional services -->
                                    <div class="row justify-content-center mt-md-2">
                                        <div class="col-3">
                                            <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block">Submit
                                            </button>
                                        </div>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <!-- end of add request popup modal -->

    <div class="modal fade" id="AllRemarksModal" data-backdrop="static" role="dialog" aria-labelledby="AllRemarksModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">All Remarks</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-bordered" id="all_remarks_tabel">
                        <tbody>
                        <tr>
                            <td>Rider Remarks:</td>
                            <td id="rider_remarks_td"></td>
                        </tr>
                        <tr>
                            <td>Shipper Remarks:</td>
                            <td id="shipper_remarks_td"></td>
                        </tr>
                        <tr>
                            <td>Trax Reason:</td>
                            <td id="trax_reason_td"></td>
                        </tr>
                        <tr>
                            <td>Trax Remarks:</td>
                            <td id="trax_remarks_td"></td>
                        </tr>
                        <tr>
                            <td>Reverse Pickup Remarks:</td>
                            <td id="remarks_td"></td>
                        </tr>
                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>

    <!---- start of show additional services modal---->
    <div class="modal fade text-left" id="AdditionalServiceModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="AdditionalServiceModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Additional Services</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        
                        <table class="table table-bordered">
                                <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">Service</th>
                                            <th class="border-primary border-darken-1">Qty</th>
                                        </tr>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>
                        <!-- end additional services -->

                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!----end of show additional services modal --->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .btn-min-width {
            min-width: 5.5rem;
        }

        .legends {
            cursor: pointer;
        }

        .custom-nav {
           margin-left: 4px;
        }

        .custom-nav li {

           border: 1px solid #CCCCCC;
           border-radius: 4px;
        }

        .custom-nav li:first-child {

           margin-right: 4px !important;
        }

        .custom-nav li:last-child {

           margin-left: 4px !important;
        }

        .custom-nav .nav-item a.nav-link {

            color: #CCCCCC;
            border: 1px solid #CCCCCC !important;
        }

        .custom-nav .nav-item p {

            line-height: 1.4;
        }

        .custom-nav .nav-item a.active {

            /* color: #64A0D2 !important; */
            border: 1px solid #64A0D2 !important;
            /* background-color: #F7FAFC !important; */
            color: #fff!important;
            background: #5587b4!important;
        }

        .custom-nav .nav-item a:hover {
            color: #64A0D2 !important;
            border: 1px solid #64A0D2 !important;
            background-color: #F7FAFC !important;
        }

        /* start scheduled days area */

        /* Hide checkboxes */
        .scheduled_days_area input[type="checkbox"] {
        display: none;
        }

        /* Style labels for checkboxes */
        .scheduled_days_area input + label {
        display: inline-block;
        border: 1px solid #CCCCCC;
        background: #fff;
        padding: 5px 1px;
        color: #A3A3A3;
        border-radius: 5px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s;
        }

        /* Style the checkbox's unchecked state */
        .scheduled_days_area input:checked + label {
            background: #5587b4;
            border-color: #64A0D2;
            color: #fff;

        }

        /* Style the checkbox's unchecked state icon */
        .scheduled_days_area input:checked + label:before {
        font-size: 17px;
        position: absolute;
        left: 24px;
        top: 6px;
        opacity: 1;
        }

        /* .scheduled_days_area input + label:hover {
            background: #fff;
            border-color: #CCCCCC;
            color: #000;
        } */

        .scheduled_days_area input:not(:checked) + label:hover {
            background: #F7FAFC;
            border-color: #64A0D2;
            color: #64A0D2;
        }

        .scheduled_days_area .item-column {
            margin-right: 10px;
        }

        /* end scheduled days area */

        /* start addition services */

        .service-item {

            border: 1px solid #CCCCCC;
            border-radius: 4px;
            padding-top: 12px;
            padding-bottom: 12px;

        }

        .btn-service {
            border-radius: 50%;
            padding: 4px;
            width: 30px;
            height: 30px;
            transition: all 0.4s;
        }

        .btn-service:hover {

            background: #6496BE !important;
        }

        .btn-service:active,
        .btn-service:focus {

            background: #6496BE !important;
        }

        .service-item .custom-input-number[type="number"]::-webkit-inner-spin-button,
        .service-item .custom-input-number[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            appearance: none;
            margin: 0;
        }

        /* end addition services */
        .delay_time{
            background-color: #8fc5ea;
            /* background-color: #9fa1ae; */
            color: white;
            /* background-color: #FF0000; */
            /* background-color: #FFA500; */
        }

    </style>
        <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}?v=24052022" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>


        $(document).ready(function () {
           
            var booking_from_date = $('#requested_from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#requested_to_date').pickadate('picker').set('min', $('#requested_from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var pickup_date = $('#pickup_date').pickadate({
                firstDay: 1,
                clear: '',
                min: '{{ Carbon\Carbon::today() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
            });

            $('#reg_phone').inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $('#pickup_address_id').prepend('<option value="" selected="selected">Select Pickup Address</option>').select2({
                placeholder: 'Select Pickup Address',
                width: '100%'
            }).on('select2:select', function (e) {
                var address = e.params.data.element.getAttribute('data-address');
                var phone = e.params.data.element.getAttribute('data-phone');
                var poc = e.params.data.element.getAttribute('data-poc');
                $('#reg_address').val(address);
                $('#reg_phone').val(phone);
                $("#poc").text(poc);
                $("#poc_phone").text(phone);
                
            }).bind('change',function(){
                var pickup_address_id=parseInt($(this).val());
                $("#preferred_time_range").empty();
                $.ajax({
                        url: '{!! route('cod.pickup.time_ranges') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'pickup_address_id': pickup_address_id
                        }
                }).done(function(data){
                    if(data.status==0){
                        $('#preferred_time_range').prepend('<option value="" selected="selected">Select Pickup Time*</option>');
                        $.each(data.time_ranges, function(key,value){
                            var time_range = new Option(value.name, value.id, false, false);
                            $('#preferred_time_range').append(time_range).trigger('change');
                        });
                    }
                });
                
            });
            // $("#pickup_address_id").on('change',function(){
            //     var pickup_address_id=parseInt((this).val());
            //     $("#preferred_time_range").empty();
            //     $.ajax({
            //             url: '{!! route('cod.pickup.time_ranges') !!}',
            //             method: 'POST',
            //             data: {
            //                 '_token': '{{ csrf_token() }}',
            //                 'pickup_address_id': pickup_address_id
            //             }
            //         })
            //         .done(function (data) {
            //             if (data.status==0) {
            //                 var shipments = '';
            //                 if (data.booked) {
            //                     $.each(data.time_ranges, function (index, time_ranges) {
            //                         console.log(time_ranges.name);
            //                         $('#preferred_time_range').append('<option value='+time_ranges.id+'>'+time_ranges.name+'</option>');
            //                     });
            //                 }
            //             }
            //         });
            // });
            $('#regular_section').on('click', 'a',function (){
                if($(this).hasClass('onetime')){
                    $('#regular_pickup').val(1);
                }
                else if($(this).hasClass('schedule')){
                    $('#regular_pickup').val(2);
                }
            });
            $('#preferred_time_range').prepend('<option value="" selected="selected">Pickup Time</option>').select2({
                placeholder: 'Select Pickup Time*',
                width: '100%',
                dropdownParent:$('#add_pickup_request') 
            });

            $('#service_select').prepend('<option value="" selected="selected">Select Service</option>').select2({
                    placeholder: 'Select Service',
                    width: '100%',
                    dropdownParent:$('#add_pickup_request')
            });
            
            $('#shipment_select').prepend('<option value="" selected="selected">Select Shipment Type</option>').select2({
                placeholder: 'Select Shipment Type',
                width: '100%',
                dropdownParent:$('#add_pickup_request')
            });
            // $('#product_select').select2({
            //     placeholder: 'Select Product',
            //     width: '100%',
            //     dropdownParent:$('#add_pickup_request')
            // });
            $('#product_select').prop('disabled', true);

            var booking_to_date = $('#requested_to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
                    }
                }
            });
           

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.pickup.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Pickup Request ID');
                            head.push('Requested Date');
                            head.push('Current Rider');
                            head.push('Last Rider');
                            head.push('Pickup Note ID');
                            head.push('Shipment(s) Booked');
                            head.push('Shipment(s) Rider Picked');
                            // head.push('Shipment(s) Received');
                            head.push('Shipper');
                            head.push('Territory');
                            head.push('Contact Person');
                            head.push('Vendor');
                            head.push('Brand Name');
                            head.push('Contact No(s).');
                            head.push('Address');
                            head.push('City');
                            head.push('Status');
                            head.push('Trax Reason');
                            head.push('Trax Remark(s)');
                            head.push('Shipper Remark(s)');
                            head.push('Rider Remark(s)');
                            head.push('Assigned Date');
                            head.push('Attempt Date');
                            head.push('Aging');
                            head.push('Attempt(s)');


                            $.each(result.data, function (index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.pickup_request_id);
                                row.push(values.requested_date);
                                row.push(values.current_rider);
                                row.push(values.last_rider);
                                row.push(values.pickup_note_id);
                                row.push(values.booked);
                                row.push(values.shipments_rider_picked);
                                // row.push(values.received);
                                row.push(values.shipper);
                                row.push(values.territory);
                                row.push(values.contact_person);
                                row.push(values.vendor_name);
                                row.push(values.brand_name);
                                row.push(values.contact_number);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.pickup_status);
                                row.push(values.trax_reason);
                                row.push(values.trax_remarks);
                                row.push(values.shipper_remarks);
                                row.push(values.rider_remarks);
                                row.push(values.assigned_date);
                                row.push(values.attempted_date);
                                row.push(values.aging);
                                row.push(values.attempts);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });


            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [

                    {
                        text: '<i class="la la-plus"></i> Add New',
                        className: 'btn btn-primary request_add',
                        action: function (e, dt, node, config) {
                            $('#AddRequestModal').modal('show');
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Pending Pickups Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }
                ],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('cod.pickup.list') }}',
                    data: function (d) {
                        d.requested_from_date = $('#requested_from_date').val();
                        d.requested_to_date = $('#requested_to_date').val();
                        // d.pickup_status_id = $('#status_filter_input').val();
                    }
                },
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'pickup_requests.id',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'pickup_request_id', name: 'pickup_request_id', class: 'align-middle pickup_request_id'},
                    {data: 'pickup_date', name: 'pickup_date', class: 'align-middle pickup_date'},
                    {data: 'time_range', name: 'time_range', class: 'align-middle ask_time'},
                    {data: 'shipment_pieces', name: 'shipment_pieces', class: 'align-middle shipments', orderable: false},
                    {data: 'weight', name: 'weight', class: 'align-middle weight'},
                    {data: 'services_count_btn', name: 'services_count', class: 'align-middle text-center services_count'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'shipments_picked', name: 'shipments_picked', class: 'align-middle shipments_picked'},
                    {data: 'product', name: 'product', class: 'align-middle product'},
                    {data: 'shipper', name: 'shipper', class: 'align-middle shipper'},
                    {data: 'hub', name: 'hub', class: 'align-middle station'},
                    {data: 'current_rider', name: 'current_rider', class: 'align-middle current_rider'},
                    {data: 'special_request', name: 'special_request', class: 'align-middle special_request'},
                    {
                        data: 'action',
                        name: 'action',
                        class: 'align-middle text-center action',
                        orderable: false,
                        searchable: false
                    }

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.shipments') || $(header).is('.status') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    this.api().table().columns.adjust();
                }

            });

           
            var route = '{!! route('admin.tracking.index') !!}';
            $('body').on('click', '#datatable tbody tr td.bookings_link button', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#bookings_modal .modal-body').html('');
                $('#bookings_modal').modal('show');

                    $.ajax({
                        url: '{!! route('admin.v2_pickups.pending.bookings.all') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'pickup_request_id': id
                        }
                    })
                    .done(function (data) {
                        if (data) {
                            var shipments = '';
                            if (data.booked) {
                                $.each(data.booked, function (index, tracking_numbers) {
                                    shipments += '<u><a href=' + route + '?tracking_number=' + tracking_numbers + ' target="_blank">' + tracking_numbers + '</a></u><br>';
                                });
                            }
                            $('#bookings_modal .modal-body').html(shipments);
                        }
                    });
            });

            $('#search_filter_btn').on('click', function () {
                table.draw(true);
            });


            $('#datatable tbody').on('click', 'tr td.all_remarks button.all_remarks_btn', function () {
                var pickup_req_id = parseInt($(this).attr('rel'));


                console.log(pickup_req_id);

                $.ajax({
                    url: '{!! route('admin.v2_pickups.pending.all_remarks') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_req_id': pickup_req_id
                    }
                })
                    .done(function (data) {
                        if (data) {
                            console.log(data.remarks);

                            $('#rider_remarks_td').html(data.remarks.rider_remarks);
                            $('#shipper_remarks_td').html(data.remarks.shipper_remarks);
                            $('#trax_reason_td').html(data.remarks.trax_reason);
                            $('#trax_remarks_td').html(data.remarks.trax_remarks);
                            $('#remarks_td').html(data.remarks.remarks);
                            $('#AllRemarksModal').modal('show');
                            if (!data.remarks.reverse_pickup) {

                                $("#remarks_td").parent().css({"display": "none"});
                            }
                            /* var shipments = '';
                            if (data.booked) {
                                $.each(data.booked, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#bookings_modal .modal-body').html(shipments); */
                        }
                    });
                /* print(pickup_note_id); */
            });

            $('body').on('click', 'button.pickup_status_btn', function(){
                var status_id = $(this).attr('rel');
               $('#status_filter_input').val(status_id);
               table.draw();
            });

            $('.quantity').TouchSpin({
                min: 0,
                max: 1000,
                buttondown_class: 'btn btn-primary rounded-left',
                buttonup_class: 'btn btn-primary rounded-right',
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            }).bind('input change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });


            $('#add_pickup_request').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#add_pickup_request button#add').prop('disabled', true);
                    swal({
                        title: 'Please Wait!',
                        text: 'Pickup request is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            $('.apply-checked').change(function() {

                if ($(this).is(':checked')) {

                    $(this).attr('checked', 'checked');

                } else {

                    $(this).removeAttr('checked');
                }
            });

        });
        
        function showadditionalservices(event,pickup_request_id){
            $("#AdditionalServiceModal table tbody").empty();
            $.ajax({
                url:'{{ route('cod.pickup.pickup_request_services') }}',
                method:'POST',
                data:{
                    'pickup_request_id':pickup_request_id,
                    '_token':'{{ csrf_token() }}'
                }
            }).done(function(data){
                if (data.status == 0) {
                            $.each(data.pickup_request_services,function(key,value) {
                              $("#AdditionalServiceModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle service_name">'+value.service_name+'</td><td class=" align-middle service_count">'+value.count+'</td></tr>')
                            });
                        } else {
                            toastr.error('No pickup address found!', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                        });
                }
            })
           $("#AdditionalServiceModal").modal("show");
        }
    </script>
@endsection