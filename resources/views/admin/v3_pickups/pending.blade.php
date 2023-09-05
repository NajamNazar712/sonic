@extends('admin.layout.master')

@section('title', 'Pending Pickups')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pending Pickups
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

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

                            <div class="container-fluid">
                                <div class="row justify-content-start">
                                    @foreach($pickup_statuses as $status)
                                        <div>
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-min-width mr-1 mb-1">{{ $status->name }}
                                                (10)
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">ID</th>
                                    <!-- <th class="border-primary border-darken-1">S. No.</th> -->
                                    <th class="border-primary border-darken-1">Date & Time</th>
                                    <th class="border-primary border-darken-1">Ask Time</th>
                                    <th class="border-primary border-darken-1">Shipments/Pieces</th>
                                    <th class="border-primary border-darken-1">Weight (KG)</th>
                                    <th class="border-primary border-darken-1">Courier Type</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Pickup Time</th>
                                    <th class="border-primary border-darken-1">Shipments Picked</th>
                                    <th class="border-primary border-darken-1">Picked Weight (KG)</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th clas="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Station</th>
                                    <th class="border-primary border-darken-1">Route No</th>
                                    <th clas="border-primary border-darken-1">Courier No</th>
                                    <th class="border-primary border-darken-1">Assigned Courier</th>
                                    <th class="border-primary border-darken-1">Communication Mode</th>
                                    <th class="border-primary border-darken-1">Requested By</th>
                                    <th class="border-primary border-darken-1">Special Request</th>
                                    <th class="border-primary border-darken-1">Additional Services</th>
                                    <th class="borde-primary border-darken-1">Remarks</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="assign_to_rider" data-backdrop="static" role="dialog"
                     aria-labelledby="assign_to_rider_title" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <form class="form-horizontal" action="{{route('admin.v3_pickups.pending.assign')}}"
                                  method="post">
                                @csrf

                                <div class="modal-header">
                                    <h4 class="modal-title" id="assign_to_rider_title">Assign to Rider</h4>
                                </div>
                                <input type="hidden" name="pickup_request_ids" id="assign_pickup_request_ids">
                                <div class="modal-body">
                                    <div class="form-group m-0">
                                        <select name="rider" class="select2 rider" data-rule-required="true"
                                                data-msg-required="Rider is required">
                                            @foreach($riders as $rider)
                                                @if($rider->trax_id)
                                                    <option value="{{ $rider->id }}">{{ $rider->name }}
                                                        - {{ $rider->trax_id }}</option>
                                                @else
                                                    <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary ml-auto">Assign</button>
                                </div>

                            </form>
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

    {{--   Modal Popup --}}
    <div class="modal fade" id="ReturnConfirmReasonSingleModal" data-backdrop="static" role="dialog"
         aria-labelledby="ReturnConfirmReasonSingleModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Return Confirm Reason</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="single_update_return_reason_form" class="form-horizontal mb-1 justify-content-center"
                          novalidate="novalidate">
                        <input type="hidden" id="return_reason_shipment_id">
                        <input type="hidden" id="return_reason_shipment_remarks">


                        <div class="form-group ml-1">
                            <button type="button" name="add" class="btn btn-primary single_update_return_confirm"
                                    id="single_reason_update_btn">Update To Return Confirm
                            </button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="AddRemarksModal" data-backdrop="static" role="dialog" aria-labelledby="AddRemarksModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Remarks</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form method="post" id="add_remarks_form"
                          action="{{ route('admin.v2_pickups.pending.add_remarks') }}"
                          class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                        @csrf
                        <input type="hidden" id="add_remarks_pickup_note_id" name="v2_pickup_req_id">
                        <div class="form-group ml-1">
                            <input type="text" name="add_remark" id="add_remark" class="form-control"
                                   data-rule-required="true" data-msg-required="Remarks is required"
                                   placeholder="Add Remarks*">

                        </div>


                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary">Add</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


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

                    <div class="d-flex justify-content-start vh-100 pl-1">

                       <b class="text-dark"> Customer Information </b>

                    </div>

                        <div>
                            <div class="d-flex justify-content-start vh-100 pl-1 mt-md-2">
                                <p class="text-gray mb-0"> Customer Type </p>
                            </div>
                            <ul class="nav nav-tabs nav-iconfall custom-nav nav-justified w-50">
                                <li class="nav-item">
                                    <a class="nav-link active p-0" id="activeIcon32-tab1" data-toggle="tab"
                                       href="#activeIcon32" aria-controls="activeIcon32" aria-expanded="true">
                                        Registered</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-0" id="linkIcon32-tab1" data-toggle="tab" href="#linkIcon32"
                                       aria-controls="linkIcon32" aria-expanded="false"> Walk-in</a>
                                </li>
                            </ul>
                        </div>
                        <form id="add_request_form" method="post" enctype="multipart/form-data">
                        <div class="tab-content px-1">
                            <div role="tabpanel" class="tab-pane active" id="activeIcon32"
                                 aria-labelledby="activeIcon32-tab1" aria-expanded="true">

                                    <div class="row align-items-center mt-md-2">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="shipper_id" id="shippers_select" class="form-control select2" data-rule-required="true" data-msg-required="Shippers is Required">
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">

                                            <p class="text-gray">Contact Person </p>
                                            <p class="text-dark">Shahid Aslam </p>

                                        </div>

                                        <div class="col-6">

                                            <p class="text-gray">Designation </p>
                                            <p class="text-dark">Operational Manager </p>

                                        </div>

                                        <div class="col-6 mt-1">

                                            <p class="text-gray">Mobile No </p>
                                            <p class="text-dark">03414285511 </p>

                                        </div>

                                        <div class="col-6 mt-1">

                                            <p class="text-gray">Customer </p>
                                            <p class="text-dark">Regular </p>

                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="pickup_address_id" id="pickup_address_id" class="form-control select2" data-rule-required="true" data-msg-required="Shippers is Required">
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <input type="text" placeholder="Address" name="address" id="reg_address" class="form-control" data-rule-required="true" data-msg-required="Address is Required"/>

                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <input type="text" placeholder="Caller Contact No" name="phone" id="reg_phone" class="form-control" data-rule-required="true"  data-msg-required="Contact Number is Required"/>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-start vh-100 pl-0">

                                         <b class="text-dark"> Pickup Details </b>

                                    </div>

                                    <div class="d-flex justify-content-start vh-100 pl-0 mt-md-2">

                                         <p class="text-gray mb-0"> Pickup Type </p>

                                    </div>

                                    <ul class="nav nav-tabs nav-iconfall custom-nav nav-justified w-100 mb-md-1">
                                        <li class="nav-item ml-md-0">
                                            <a class="nav-link active p-0 pl-1 text-left" id="oneTimePickup-tab1" data-toggle="tab"
                                            href="#oneTimePickup" aria-controls="oneTimePickup" aria-expanded="true">
                                            <b> One-time Pickup </b>
                                            <p> Request a pickup for a single use. </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link p-0 pl-1 text-left" id="scheduledPickup-tab1" data-toggle="tab"
                                            href="#scheduledPickup" aria-controls="scheduledPickup" aria-expanded="false">
                                            <b> Scheduled Pickup </b>
                                            <p> Set regular pickups for specific days every week.</p>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">

                                        <div role="tabpanel" class="tab-pane active" id="oneTimePickup" aria-labelledby="oneTimePickup-tab1" aria-expanded="true">

                                        <div class="row justify-content-center align-items-center vh-100 mt-md-0">
                                            <div class="col-6">

                                                <div class="form-group">
                                                    <div class="form-group input-group">
                                                        <div class="input-group-prepend">
                                                                    <span
                                                                            class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o"></span>
                                                                    </span>
                                                        </div>

                                                        <input type="text" name="pickup_date"
                                                            class="form-control pickadate bg-primary border-primary white rounded-right require_one pickup_date"
                                                            id="pickup_date" placeholder="Select Pickup Date"
                                                            data-rule-required="true" data-msg-required="Pickup Date is Required">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="preferred_time_range" id="preferred_time_range"
                                                            class="form-control select2" data-rule-required="true"
                                                            data-msg-required="Pickup Time Range is required">
                                                        <option value=""> Pickup Time Range</option>

                                                        @foreach($time_ranges as $time_range)
                                                            <option value="{{ $time_range->id }}">{{ $time_range->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="row justify-content-center align-items-center">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="product_id" id="product_select" class="form-control select2"
                                                            data-rule-required="true" data-msg-required="Product is required">
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="service_id" id="service_select" class="form-control select2" data-rule-required="true" data-msg-required="Service is Required">
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row justify-content-center align-items-center vh-100">
                                            <div class="col-12">
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

                                        <div class="row justify-content-center align-items-center">
                                            <div class="col-12">
                                                <div class="form-group input-group">

                                                    <div class="input-group-prepend">

                                                                <span class="input-group-text text-dark border-primary  rounded-left">
                                                                    <span> Estimated Weight </span>
                                                                </span>
                                                    </div>

                                                    <input type="text" id="estimated_weight" name="estimated_weight"
                                                        class="form-control text-center" placeholder="Estimated Weight">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center align-items-center vh-100">
                                            <div class="col-12">
                                                <div class="form-group input-group">
                                                    <div class="input-group-prepend">

                                                                <span class="input-group-text text-dark border-primary rounded-left">
                                                                    <span> No of Shipments </span>
                                                                </span>
                                                    </div>
                                                    <input type="text" id="shipments_count" name="shipments_count" class="form-control"
                                                        data-rule-required="true" data-msg-required="No. of Shipments is required">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center align-items-center vh-100">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <textarea type="text" name="remarks" class="form-control"
                                                            placeholder="Remarks"></textarea>
                                                </div>
                                            </div>
                                        </div>


                                        </div>

                                        <div role="tabpanel" class="tab-pane" id="scheduledPickup" aria-labelledby="scheduledPickup-tab1" aria-expanded="false">

                                        <div class="d-flex justify-content-start vh-100 pl-0 mt-md-2">
                                            <p class="text-gray mb-0"> Scheduled Days </p>
                                        </div>


                                          <div class="d-flex justify-content-start vh-100 pl-0 mt-md-2 scheduled_days_area">

                                          <div class="col-2 mb-0 p-0">
                                            <input checked type="checkbox" name='sa' id="sa">
                                            <label for="sa" class="w-75 text-center" id="sa">Sa</label>
                                            </div>
                                            <div class="col-2 mb-0 p-0">
                                            <input type="checkbox" name='su' id="su">
                                            <label for="su" class="w-75 text-center" id="su">Su</label>
                                           </div>

                                          </div>


                                        <div class="row justify-content-center align-items-center vh-100 mt-md-0">
                                            <div class="col-6">

                                                <div class="form-group">
                                                    <div class="form-group input-group">
                                                        <div class="input-group-prepend">
                                                                    <span
                                                                            class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o"></span>
                                                                    </span>
                                                        </div>

                                                        <input type="text" name="pickup_date"
                                                            class="form-control pickadate bg-primary border-primary white rounded-right require_one pickup_date"
                                                            id="pickup_date" placeholder="Select Pickup Date"
                                                            data-rule-required="true" data-msg-required="Pickup Date is Required">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="preferred_time_range" id="preferred_time_range"
                                                            class="form-control select2" data-rule-required="true"
                                                            data-msg-required="Pickup Time Range is required">
                                                        <option value=""> Pickup Time Range</option>

                                                        @foreach($time_ranges as $time_range)
                                                            <option value="{{ $time_range->id }}">{{ $time_range->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="row justify-content-center align-items-center">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="product_id" id="product_select" class="form-control select2"
                                                            data-rule-required="true" data-msg-required="Product is required">
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select name="service_id" id="service_select" class="form-control select2" data-rule-required="true" data-msg-required="Service is Required">
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row justify-content-center align-items-center vh-100">
                                            <div class="col-12">
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

                                        <div class="row justify-content-center align-items-center">
                                            <div class="col-12">
                                                <div class="form-group input-group">

                                                    <div class="input-group-prepend">

                                                                <span class="input-group-text text-dark border-primary  rounded-left">
                                                                    <span> Estimated Weight </span>
                                                                </span>
                                                    </div>

                                                    <input type="text" id="estimated_weight" name="estimated_weight"
                                                        class="form-control text-center" placeholder="Estimated Weight">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center align-items-center vh-100">
                                            <div class="col-12">
                                                <div class="form-group input-group">
                                                    <div class="input-group-prepend">

                                                                <span class="input-group-text text-dark border-primary rounded-left">
                                                                    <span> No of Shipments </span>
                                                                </span>
                                                    </div>
                                                    <input type="text" id="shipments_count" name="shipments_count" class="form-control"
                                                        data-rule-required="true" data-msg-required="No. of Shipments is required">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center align-items-center vh-100">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <textarea type="text" name="remarks" class="form-control"
                                                            placeholder="Remarks"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        </div>

                                    </div>

                            </div>

                            <div class="tab-pane" id="linkIcon32" role="tabpanel" aria-labelledby="linkIcon32-tab1"
                                 aria-expanded="false">

                                <div class="row mt-md-2">

                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" placeholder="Customer Name" name="customer_name"
                                                   id="customer_name" class="form-control" data-rule-required="true"
                                                   data-msg-required="Customer Name is Required"/>

                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" placeholder="Designation" name="designation"
                                                   id="designation" class="form-control" data-rule-required="true"
                                                   data-msg-required="Designation is Required"/>

                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" placeholder="Mobile Number" name="mobile_no"
                                                   id="mobile_no" class="form-control" data-rule-required="true"
                                                   data-msg-required="Mobile is Required"/>

                                        </div>
                                    </div>


                                </div>

                            </div>

                        </div>

                        <!-- <div class="d-flex justify-content-start align-items-center">
                            <div class="col-12">
                                <div id="pickup_div" class="form-group text-left p-0 pt-md-0 pb-md-0">
                                    <label class="d-block"><strong>Regular Pickup</strong></label>
                                    <input type="checkbox" name="pickup" class="switch hidden" id="pickup">
                                </div>
                            </div>

                        </div> -->

                        <!-- start additional services -->

                        <div class="d-flex justify-content-start vh-100 pl-1">
                        <b class="text-dark"> Additional Services </b>
                        </div>
                        <div class="d-flex justify-content-start align-items-center">

                        <!-- start service list -->

                        <div class="services-list col-12">

                            <!-- start service item 1 -->
                            <div class="d-flex align-items-center service-item mt-md-1">
                                <div class="col-md-6">
                                    <p class="mb-0 text-dark"> Labour </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-service btn-add-item"> <i class="la la-minus"></i> </button>
                                    </div>
                                    <div class="col-md-4 p-0">
                                        <input class="form-control text-center text-dark" type="text" value="0" />
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-service btn-remove-item"> <i class="la la-plus"></i> </button>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <!-- end service item 1 -->

                            <!-- start service item 2 -->
                            <div class="d-flex align-items-center service-item mt-md-1">
                                <div class="col-md-6">
                                    <p class="mb-0 text-dark"> Lifter </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-service btn-add-item"> <i class="la la-minus"></i> </button>
                                    </div>
                                    <div class="col-md-4 p-0">
                                        <input class="form-control text-center text-dark" type="text" value="0" />
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-service btn-remove-item"> <i class="la la-plus"></i> </button>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <!-- end service item 2 -->

                            <!-- start service item 3 -->
                            <div class="d-flex align-items-center service-item mt-md-1">
                                <div class="col-md-6">
                                    <p class="mb-0 text-dark"> Packaging </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-service btn-add-item"> <i class="la la-minus"></i> </button>
                                    </div>
                                    <div class="col-md-4 p-0">
                                        <input class="form-control text-center text-dark" type="text" value="0" />
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-service btn-remove-item"> <i class="la la-plus"></i> </button>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <!-- end service item 3 -->



                            <!-- start view more -->

                            <div class="d-flex justify-content-start label-view-more vh-100 mt-md-1 pl-0">
                               <b class="text-dark veiw_more"> View More <i class="la la-angle-down"></i> </b>
                            </div>

                            <!-- end view more -->

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

            color: #64A0D2 !important;
            border: 1px solid #64A0D2 !important;
            background-color: #F7FAFC !important;
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
        border: 1px solid #64A0D2;
        padding: 10px;
        border-radius: 5px;
        position: relative;
        cursor: pointer;
        }

        /* Style the checkbox's unchecked state */
        .scheduled_days_area input:checked + label {
        border-color: #CCCCCC;
        background: #fff;
        color: #333;
        }

        /* Style the checkbox's unchecked state icon */
        .scheduled_days_area input:checked + label:before {
        font-size: 17px;
        position: absolute;
        left: 24px;
        top: 6px;
        opacity: 1;
        }

        /* Style the checkbox's unchecked state circle */
        /* .scheduled_days_area input:checked + label:after {
        position: absolute;
        content: '';
        opacity: 1;
        left: 20px;
        top: 11px;
        width: 18px;
        height: 18px;
        line-height: 1;
        /* border: 2px solid #333; */
        /* border-radius: 50%; */
        } */

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

        .veiw_more {
            color: #64A0D2 !important;
        }

        .veiw_more i {
            font-size: 16px;
        }

        /* end addition services */

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

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
            var shipper_pickup_addresses = [];
            $('#shippers_select').prepend('<option value="" selected="selected">Select Shippers</option>').select2({
                placeholder: 'Select Customer',
                width: '100%',
            }).bind('select2:select', function () {
                var shipper_id = parseInt($(this).val());
                if (shipper_id) {
                    $('#pickup_address_id').empty();


                    $.ajax({
                        url: '{{ route('admin.v3_pickups.pending.shipper_info') }}',
                        method: 'POST',
                        data: {
                            'shipper_id': shipper_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 0) {
                            shipper_pickup_addresses = data.pickup_addresses;
                            $.each(data.pickup_addresses,function(key,value) {
                                var name = value.city.name + ' - ' + value.pickup_address;
                                var pickup = new Option(name, value.id, false, false);
                                $('#pickup_address_id').append(pickup).trigger('change');
                            });
                            $('#pickup_address_id').select2({
                                placeholder: 'Select pickup address',
                                width: '100%'
                            }).val(null).trigger('change');
                        } else {
                            toastr.error('No pickup address found!', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });

            $('#reg_phone').inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $('#pickup_address_id').prepend('<option value="" selected="selected">Select Pickup Address</option>').select2({
               placeholder: 'Select Pickup Address',
               width: '100%'
            }).bind('select2:select', function () {
                var address_id = parseInt($(this).val());

                $.each(shipper_pickup_addresses, function(key,value) {
                    if(value.id == address_id){
                        $('#reg_address').val(value.pickup_address);
                        $('#reg_phone').val(value.phone);
                    }
                });
            });

            $('#product_select').prepend('<option value="" selected="selected">Select Product</option>').select2({
                placeholder: 'Select Product',
                width: '100%'
            });

            $('#service_select').prepend('<option value="" selected="selected">Select Service</option>').select2({
                placeholder: 'Select Service',
                width: '100%'
            });

            $('#shipment_select').prepend('<option value="" selected="selected">Select Shipment Type</option>').select2({
                placeholder: 'Select Shipment Type',
                width: '100%'
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

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.v3_pickups.history.list') }}',
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
                @if (session('role_id') == 1 || count(array_intersect([18, 19], session('permissions'))) !== 0)

                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add New',
                        className: 'btn btn-primary request_add',
                        action: function (e, dt, node, config) {
                            $('#AddRequestModal').modal('show');
                        }
                    },
                        @if (session('role_id') == 1 || in_array(19, session('permissions')))

                    {
                        text: 'Assign',
                        className: 'btn btn-primary assign',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $('#assign_to_rider .rider').val(null).trigger('change');

                            $('#assign_to_rider').modal('show');
                        }
                    },

                        @endif
                    {
                        extend: 'excel',
                        title: 'Pending Pickups Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.assign').enable();
                                    table.button('.update').enable();

                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.assign').disable();
                                        table.button('.update').disable();
                                    }
                                }
                            });
                        }
                    },
                    'reset'
                ],
                @else
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pending Pickups Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                @endif
                scrollX: true, scrollY: '500px',
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.v3_pickups.pending.list') }}',
                    data: function (d) {
                        d.legend_filter = $('#legend_filter').val();
                        d.before_cut_off_time = $('#search_filter').val();
                        d.requested_from_date = $('#requested_from_date').val();
                        d.requested_to_date = $('#requested_to_date').val();
                        d.star_shipper_filter = $('#star_shippers_filter').val();
                    }
                },
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        class: 'text-center align-middle select select-checkbox p-1',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
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
                    {data: 'date', name: 'date', class: 'align-middle date_time'},
                    {data: 'ask_time', name: 'ask_time', class: 'align-middle ask_time'},
                    {data: 'shipments', name: 'shipments', class: 'align-middle shipments'},
                    {data: 'weight', name: 'weight', class: 'align-middle weight'},
                    {data: 'courier_type', name: 'courier_type', class: 'align-middle courier_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'pickup_time', name: 'pickup_time', class: 'align-middle pickup_time'},
                    {data: 'shipments_picked', name: 'shipments_picked', class: 'align-middle shipments_picked'},
                    {data: 'picked_weight', name: 'picked_weight', class: 'align-middle picked_weight'},
                    {data: 'product', name: 'product', class: 'align-middle product'},
                    {data: 'shipper', name: 'shipper', class: 'align-middle shipper'},
                    {data: 'station', name: 'station', class: 'align-middle station'},
                    {data: 'route_no', name: 'route_no', class: 'align-middle route_no'},
                    {data: 'courier_no', name: 'courier_no', class: 'align-middle courier_no'},
                    {data: 'assigned_courier', name: 'assigned_courier', class: 'align-middle assigned_courier'},
                    {data: 'communication_mode', name: 'communication_mode', class: 'align-middle communication_mode'},
                    {data: 'requested_by', name: 'requested_by', class: 'align-middle requested_by'},
                    {data: 'special_request', name: 'special_request', class: 'align-middle special_request'},
                    {
                        data: 'additional_services',
                        name: 'additional_services',
                        class: 'align-middle additional_services'
                    },
                    {data: 'remarks', name: 'remarks', class: 'align-middle remarks'},
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

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
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

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                } else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.assign').enable();
                    table.button('.update').enable();
                } else {
                    table.button('.assign').disable();
                    table.button('.update').disable();
                }
            });

            $('#assign_to_rider .rider').select2({
                width: '100%',
                placeholder: 'Rider*'
            }).bind('change', function () {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#assign_to_rider form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        text: 'Are you sure, you want to assign rider to the following pickup(s)?',
                        icon: 'info',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if (confirm) {
                            $(form).find('button[type=submit]').attr('disabled', 'disabled');
                            $('#assign_pickup_request_ids').val(selected_rows);
                            form.submit();
                        }
                        $(form).find('button[type=submit]').attr('disabled', false);

                    });

                }
            });

            $('#update_pickup_modal .reason').select2({
                width: '100%',
                placeholder: 'Not Pick Reason*',
                dropdownParent: $('#update_pickup_modal')
            }).bind('change', function () {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });
            $('body').on('change', '#update_pickup_modal #trax_remarks', function () {
                $(this).val($(this).val().trim());
            });
            $('#update_pickup_modal form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {

                    swal({
                        text: 'Are you sure, you want to update the following pickup(s)?',
                        icon: 'info',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if (confirm) {
                            $('#update_pickup_request_btn_submit').prop('disabled', true);

                            $('#update_pickup_request_ids').val(selected_rows);
                            form.submit();

                        }

                    });
                    $('#update_pickup_request_btn_submit').prop('disabled', false);
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


            $('body').on('click', '.addRemarks', function () {
                var action = $(this).data('action');
                var row_id = $(this).parents('tr').attr('id');
                $('#AddRemarksModal').modal('show');
                $('#add_remarks_pickup_note_id').val(row_id);

                /* if(action === 'reattempt'){
                    var atext = 'Select Yes to put Reminder!';
                 } */

                /* if(row_id != '' && action === 'reminder'){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Do you want to set reminder for this pickup request?',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if (confirm) {
                            blockPagePermanently();
                            $.ajax({
                                url:"{{route('admin.v2_pickups.pending.status.reminder.update')}}",
                                method:'POST',
                                data:{
                                    'shipment_id':row_id,
                                    '_token':'{{ csrf_token() }}',
                                }
                            }).done(function (data) {
                                if(data.status == 1){
                                    UnblockPagePermanently();
                                    table.draw('false');
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                }else{
                                    UnblockPagePermanently();
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                }

                            });
                        }
                    });
                } */
            });
            $('#AddRemarksModal').on('hidden.bs.modal', function () {
                $('#add_remark').val('');
            });
            $("#add_remarks_form").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    form.submit();
                }

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

            $('#star_shippers_filter').on('click', function () {
                $('#star_shippers_filter').val(1);
                table.draw(true);
                $('#star_shippers_filter').val(0);
            });
        });
    </script>
@endsection