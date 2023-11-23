@extends('admin.layout.master')

@section('title', 'Schedule Pickups')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Schedule Pickups
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

                           
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Pickup Request ID</th>
                                    <th class="border-primary border-darken-1">Date & Time</th>
                                    <th class="border-primary border-darken-1">Ask Time</th>
                                    <th class="border-primary border-darken-1">Shipments/Pieces</th>
                                    <th class="border-primary border-darken-1">Weight (KG)</th>
                                    <th class="border-primary border-darken-1">Additional Services</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Scheduled Days</th>
                                    <th class="border-primary border-darken-1">Station</th>
                                    <th class="border-primary border-darken-1">Route Code</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1">Assigned Courier</th>
                                    <th class="border-primary border-darken-1">Special Request</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Update by</th>
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

      <!---- start  reschedule days modal---->
    <div class="modal fade text-left" id="RescheduleDaysModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="RescheduleDaysModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Reschedule Days</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form method="POST" action="{{ route('admin.v3_pickups.pending.schedule.pickup_days_update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="regular_pickup_id" id="regular_pickup_id">
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-8">
                                    <div class="form-group">
                                        {{-- <label>Days</label> --}}
                                        <select name="days[]" id="days" class="form-control select2" multiple="multiple" data-msg-required="Atleast one shipper is required" data-rule-required="true" required="required">
                                            <option value="1">Mo</option>
                                            <option value="2">Tu</option>
                                            <option value="3">We</option>
                                            <option value="4">Th</option>
                                            <option value="5">Fr</option>
                                            <option value="6">Sa</option>
                                        </select>
        
                                    </div>
                                </div>       
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block">Update
                                    </button>
                                </div>
                            </div>
                        </form>
                        {{-- <div class="col-2"> --}}
                         
                                       
                    </div>
                </div>
            </div>
        </div>
    </div>
     <!---- end reschedule days modal---->

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
            $('#estimated_weight').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
            });
            $('#shipments_count').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
            });

            $('#total_pieces_count').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
            });
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
                dropdownParent:$('#add_pickup_request')
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
                                placeholder: 'Select Pickup Address',
                                width: '100%'
                            }).val(null).trigger('change');
                            $('#product_select').val(data.product_id).trigger('change');
                            $('#product_select').attr('disabled', true);
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
                        $('#poc').text(value.poc);
                        $('#poc_phone').text(value.phone);
                    }
                });
            });

            var services = @json($services);
            $('#product_select').prepend('<option value="" selected="selected">Select Product</option>').select2({
                placeholder: 'Select Product',
                width: '100%',
                dropdownParent:$('#add_pickup_request')
            }).bind('change', function () {
                var product_id = parseInt($(this).val());
                $('#service_select').empty();
                $.each(services, function(key,value) {
                    if(value.segment_id == product_id){
                        var name = value.name;
                        var service_option = new Option(name, value.id, false, false);
                        $('#service_select').append(service_option).trigger('change');
                    }
                });
                $('#service_select').select2({
                    placeholder: 'Select Service',
                    width: '100%',
                    dropdownParent:$('#add_pickup_request')
                }).val(null).trigger('change');
            });

            $('#edit_product_select').prepend('<option value="" selected="selected">Select Product</option>').select2({
                placeholder: 'Select Product',
                width: '100%',
                dropdownParent:$('#update_pickup_request')
            }).bind('change', function () {
                var product_id = parseInt($(this).val());
                $('#edit_service_select').empty();
                $.each(services, function(key,value) {
                    if(value.segment_id == product_id){
                        var name = value.name;
                        var service_option = new Option(name, value.id, false, false);
                        $('#edit_service_select').append(service_option).trigger('change');
                    }
                });
                $('#edit_service_select').select2({
                    placeholder: 'Select Service',
                    width: '100%',
                    dropdownParent:$('#update_pickup_request')
                }).val(null).trigger('change');
            });
            
            

            $('#service_select').prepend('<option value="" selected="selected">Select Service</option>').select2({
                placeholder: 'Select Service',
                width: '100%',
                dropdownParent:$('#add_pickup_request')
            });
            $("#edit_service_select").prepend('<option value="" selected="selected">Select Service</option>').select2({
                placeholder: 'Select Service',
                width: '100%',
                dropdownParent:$('#update_pickup_request')
            });

            $('#shipment_select').prepend('<option value="" selected="selected">Select Shipment Type</option>').select2({
                placeholder: 'Select Shipment Type',
                width: '100%',
                dropdownParent:$('#add_pickup_request')
            });
            $('#edit_shipment_select').prepend('<option value="" selected="selected">Select Shipment Type</option>').select2({
                placeholder: 'Select Shipment Type',
                width: '100%',
                dropdownParent:$('#update_pickup_request')
            });
            

            $('#preferred_time_range').prepend('<option value="" selected="selected">Pickup Time</option>').select2({
                placeholder: 'Select Pickup Time*',
                width: '100%',
                dropdownParent:$('#add_pickup_request') 
            });
            $('#edit_preferred_time_range').prepend('<option value="" selected="selected">Pickup Time</option>').select2({
                placeholder: 'Select Pickup Time*',
                width: '100%',
                dropdownParent:$('#update_pickup_request') 
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
            var edit_pickup_date = $('#edit_pickup_date').pickadate({
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

            $('#customer_type').on('click', 'a',function (){
                if($(this).hasClass('registered')){
                    $('#pickup_type_id').val(1);
                }
                else if($(this).hasClass('walk_in')){
                    $('#pickup_type_id').val(2);
                }
            });
            $('#regular_section').on('click', 'a',function (){
                if($(this).hasClass('onetime')){
                    $('#regular_pickup').val(1);
                }
                else if($(this).hasClass('schedule')){
                    $('#regular_pickup').val(2);
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
                        url: '{{ route('admin.v3_pickups.history.list') }}',
                        data: params,
                        success: function (result) {
                            dd(result);
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

            var days_map={
                1: 'Mo',
                2: 'Tu',
                3: 'We',
                4: 'Th',
                5: 'Fr',
                6: 'Sa',
            };
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
              
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Schedule Pickups',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
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
                    url: '{{ route('admin.v3_pickups.pending.schedule.list') }}',
                    data: function (d) {
                        d.before_cut_off_time = $('#search_filter').val();
                        d.requested_from_date = $('#requested_from_date').val();
                        d.requested_to_date = $('#requested_to_date').val();
                        d.pickup_status_id = $('#status_filter_input').val();
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
                    {data: 'product', name: 'product', class: 'align-middle product'},
                    {data: 'shipper', name: 'shipper', class: 'align-middle shipper',render:function(data,type,row){
                        return row.user_id +'-'+ row.shipper;
                    }},
                    {data: 'days', name: 'days', class: 'align-middle text-center days',orderable: false,render:function(data,type,row){
                        var days_string=row.days.split(',').map(dayvalue=>days_map[parseInt(dayvalue)] || dayvalue);
                        days_string=days_string.join(' ,');
                        return days_string;
                    }},
                    
                    {data: 'hub', name: 'hub', class: 'align-middle station'},
                    {data: 'route_code', name: 'route_code', class: 'align-middle route_code'},
                    {data: 'rider_id', name: 'rider_id', class: 'align-middle rider_id',
                    render: function (data, type, row) {
                            if (row.rider_id !== null) {
                                return row.rider_id + ' - ' + row.rider_name;
                            } else {
                                return ''; 
                            }
                        }
                    },
                    {data: 'current_rider', name: 'current_rider', class: 'align-middle current_rider'},
                    {data: 'special_request', name: 'special_request', class: 'align-middle special_request'},
                    {data: 'approval', name: 'approval', class: 'align-middle approval',render:function(data,type,row){
                        if(row.approval==1)
                        {
                            return'Approved';
                        }else if(row.approval==2)
                        {
                            return'Rejected';
                        }else{
                            return 'Pending'
                        }
                     
                    }},
                    {data: 'updated_by', name: 'updated_by', class: 'align-middle updated_by'},
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

                        if ($(header).is('.action') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.days') || $(header).is('.shipments') || $(header).is('.status') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
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

            $('#EditRequestModal .reason').select2({
                width: '100%',
                placeholder: 'Not Pick Reason*',
                dropdownParent: $('#EditRequestModal')
            }).bind('change', function () {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });
            $('body').on('change', '#EditRequestModal #trax_remarks', function () {
                $(this).val($(this).val().trim());
            });
            $('#EditRequestModal form').validate({
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

            $('#days').select2({
                placeholder:'Days',
                width:'100%',
                allowClear:true
            });

            $('body').on('click','.reschedule_days',function(){
                $("#RescheduleDaysModal form")[0].reset();
                $("#RescheduleDaysModal select").val(null).trigger('change.select2');
                var regular_pickup_id=$(this).closest('tr').attr('id');
                var days='';
                $.ajax({
                    url:'{{route('admin.v3_pickups.pending.schedule.regular_pickup_days',['id'=>':id']) }}'.replace(':id',regular_pickup_id ),
                    method: 'GET',
                }).done(function(data){
                    if(data.status==0){
                        days=data.regular_pickup.days;
                        days=days.split(',');
                        $("#days").val(days).trigger('change');
                    }else{
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                   
                });
                $("#regular_pickup_id").val(regular_pickup_id);
                $("#RescheduleDaysModal").modal('show');
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


                // console.log(pickup_req_id);

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

            //Approve button schedule
            $('body').on('click','.approve_schedule',function(){
                var currentrow=$(this).closest('tr');
                var regular_pickup_id=currentrow.attr('id');
                $.ajax({
                    url:'{!! route('admin.v3_pickups.pending.schedule.approved') !!}',
                    method:"POST",
                    data:{
                        'regular_pickup_id':regular_pickup_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function(data){
                    if(data.status==0){
                        // currentrow.find('.approve_schedule').remove();
                        // currentrow.find('.reject_schedule').remove();
                        toastr.success(data.success, 'Success!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        table.draw();
               
                    }else {
                        toastr.error('Something went wrong, please refresh and try again!', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                   
                });
            });
            //Schedule Reject
            $('body').on('click','.reject_schedule',function(){
                var currentrow=$(this).closest('tr');
                var regular_pickup_id=currentrow.attr('id');
                $.ajax({
                    url:'{!! route('admin.v3_pickups.pending.schedule.rejected') !!}',
                    method:"POST",
                    data:{
                        'regular_pickup_id':regular_pickup_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function(data){
                    if(data.status==0){
                        // currentrow.find('.approve_schedule').remove();
                        // currentrow.find('.reject_schedule').remove();
                        
                        toastr.info(data.success, 'Success!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        table.draw();
               
                    }else {
                        toastr.error('Something went wrong, please refresh and try again!', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                   
                });
            });  

          
            // increment and decrement buttons
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
                url:'{{ route('admin.v3_pickups.pending.pickup_request_services') }}',
                method:'POST',
                data:{
                    'pickup_request_id':pickup_request_id,
                    '_token':'{{ csrf_token() }}'
                }
            }).done(function(data){
                if (data.status == 0) {
                    // console.log(data);
                    // var pickup_request_services=data.additional_services[0].pickup_request_services;
                            $.each(data.pickup_request_services,function(key,value) {
                            //   $(".additionalservices").append('<div class="d-flex align-items-center service-item mt-md-1"><div class="col-md-6"><p class="mb-0 text-dark">'+value.service_name+' </p></div> <div class="col-md-6"><div class="form-group input-group mb-0"><input type="text" value='+value.count+' class="form-control text-center" readonly></div></div> </div>')
                              $("#AdditionalServiceModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle service_name">'+value.service_name+'</td><td class=" align-middle service_count">'+value.count+'</td></tr>')
                            });
                        
                        } else {
                            toastr.error('No Additional Service found!', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                        });
                }
            })
           $("#AdditionalServiceModal").modal("show");
        }
    </script>
@endsection