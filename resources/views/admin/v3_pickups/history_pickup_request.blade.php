@extends('admin.layout.master')

@section('title', 'Pickups Request History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pickups Request History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
{{--                            --}}
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <input type="text" name="cut_off_time" class="form-control cut_off_time"
                                               value="{{$cut_off_time}}:00" disabled>
                                    </div>
                                    <div class="col">
                                        <select name="search_filter" id="search_filter" class="form-control select2">

                                            <option value="10">Pickup Request Before Cut Off Time</option>
                                            <option value="0">All</option>
                                        </select>
                                    </div>
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
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" id="search_filter_btn"
                                                class="float-right mb-1 mt-2 btn btn-outline-primary btn-min-width"><i
                                                    class="la la-search" style="margin-right: 10px"></i> Search
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-header">
                                            <div class="heading-elements">
                                                <ul class="list-inline" style="margin-top: -10px">
                                                    <li class="primary border-primary round" value="0" id="star_shippers_filter"><a>
                                                            Star Shippers</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="heading-elements">
                                                    <ul class="list-inline mb-0">
                                                        <li class="primary border-primary round"><a
                                                                    data-action="collapse">Legend
                                                                <i class="ft-minus"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-content collapse">
                                                <div class="card-body p-1">
                                                    <h4 class=" info">Legend</h4>
                                                    <input type="hidden" id="legend_filter">

                                                    <table class="table mb-0" id="legends_table">
                                                        <tbody>
                                                        @foreach($legends as $legend)
                                                            @if($legend->id == 7)
                                                                <tr style="background-color: {{$legend->color}}; color:#010a10;"
                                                                    id="{{$legend->id}}" class="legends">
                                                                    <td>
                                                                        <button type="button"
                                                                                class="btn btn-sm round btn-min-width text-white"
                                                                                style="background-color: {{$legend->color}}"
                                                                                disabled>{{$cut_off_time}}:00
                                                                        </button>
                                                                    </td>
                                                                    <td class="align-middle">{{ $legend->name }}
                                                                        <b>({{$cut_off_time}}:00)</b></td>
                                                                </tr>
                                                            @else
                                                                <tr style="background-color: {{$legend->color}}; color:#010a10;"
                                                                    id="{{$legend->id}}" class="legends">
                                                                    <td>
                                                                        <button type="button"
                                                                                class="btn btn-sm round btn-min-width p-1"
                                                                                style="background-color: {{$legend->color}}"
                                                                                disabled></button>
                                                                    </td>
                                                                    <td class="align-middle">{{ $legend->name }}</td>
                                                                </tr>
                                                            @endif

                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            {{--                            --}}

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Pickup Request ID</th>
                                    <th class="border-primary border-darken-1">Requested Date</th>
                                    <th class="border-primary border-darken-1">Current Rider</th>
                                    <th class="border-primary border-darken-1">Last Rider</th>
                                    <th class="border-primary border-darken-1">Pickup Note ID</th>
                                    <th class="border-primary border-darken-1">Shipment(s) Booked</th>
                                    <th class="border-primary border-darken-1">Shipment(s) Rider Picked</th>
{{--                                    <th class="border-primary border-darken-1">Shipment(s) Received</th>--}}
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Territory</th>
                                    <th class="border-primary border-darken-1">Contact Person</th>
{{--                                    <th class="border-primary border-darken-1">Booking Type</th>--}}
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Brand Name</th>
                                    <th class="border-primary border-darken-1">Contact No(s).</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    {{-- <th class="border-primary border-darken-1">Trax Reason</th>
                                    <th class="border-primary border-darken-1">Trax Remark(s)</th>
                                    <th class="border-primary border-darken-1">Shipper Remark(s)</th>
                                    <th class="border-primary border-darken-1">Rider Remark(s)</th> --}}
                                    <th class="border-primary border-darken-1">All Remarks</th>
                                    <th class="border-primary border-darken-1">Rider Status</th>
                                    <th class="border-primary border-darken-1">Assigned Date</th>
                                    <th class="border-primary border-darken-1">Attempt Date/Time</th>
                                    <th class="border-primary border-darken-1">Attempt(s)</th>
                                    <th class="border-primary border-darken-1">Aging</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="assign_to_rider" data-backdrop="static" role="dialog" aria-labelledby="assign_to_rider_title" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <form class="form-horizontal" action="{{route('admin.v2_pickups.pending.assign')}}" method="post">
                                {{ csrf_field() }}
                                @method('put')
                                <div class="modal-header">
                                    <h4 class="modal-title" id="assign_to_rider_title">Assign to Rider</h4>
                                </div>
                                <input type="hidden" name="pickup_request_ids" id="assign_pickup_request_ids">
                                <div class="modal-body">
                                    <div class="form-group m-0">
                                        <select name="rider" class="select2 rider" data-rule-required="true" data-msg-required="Rider is required">
                                            @foreach($riders as $rider)
                                            @if($rider->trax_id)
                                                <option value="{{ $rider->id }}">{{ $rider->name }} - {{ $rider->trax_id }}</option>
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

                <div class="modal fade" id="update_pickup_modal" data-backdrop="static" role="dialog" aria-labelledby="update_pickup_modal_title" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <form class="form-horizontal" action="{{ route('admin.v2_pickups.pending.update') }}" method="post">
                                {{ csrf_field() }}
                                @method('put')
                                <div class="modal-header">
                                    <h4 class="modal-title" id="update_pickup_modal_title">Update Pickup Request</h4>
                                </div>
                                <input type="hidden" name="pickup_request_ids" id="update_pickup_request_ids">
                                <div class="modal-body">
                                    <div class="form-group m-0 mb-1">
                                        <select name="reason" class="select2 reason" data-rule-required="true" data-msg-required="Reason is required">
                                            @foreach($not_pick_reasons as $reason)
                                                <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group m-0">
                                        <textarea name="trax_remarks" id="trax_remarks" class="form-control" cols="30" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary ml-auto" id="update_pickup_request_btn_submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!--Shipments popup -->
                <div class="modal fade" id="bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="bookings_modal" aria-hidden="true">
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
                <div class="modal fade" id="pending_bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="pending_bookings_modal" aria-hidden="true">
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
    <div class="modal fade" id="ReturnConfirmReasonSingleModal" data-backdrop="static" role="dialog" aria-labelledby="ReturnConfirmReasonSingleModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Return Confirm Reason</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="single_update_return_reason_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                        <input type="hidden" id="return_reason_shipment_id">
                        <input type="hidden" id="return_reason_shipment_remarks">


                        <div class="form-group ml-1">
                            <button type="button" name="add" class="btn btn-primary single_update_return_confirm" id="single_reason_update_btn">Update To Return Confirm</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="AddRemarksModal" data-backdrop="static" role="dialog" aria-labelledby="AddRemarksModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Remarks</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form method="post" id="add_remarks_form" action="{{ route('admin.v2_pickups.pending.add_remarks') }}" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                        @csrf
                        <input type="hidden" id="add_remarks_pickup_note_id" name="v2_pickup_req_id">
                        <div class="form-group ml-1">
                            <input type="text"  name="add_remark"  id="add_remark" class="form-control" data-rule-required="true"  data-msg-required="Remarks is required" placeholder="Add Remarks*">

                        </div>


                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary" >Add</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="AllRemarksModal" data-backdrop="static" role="dialog" aria-labelledby="AllRemarksModal" aria-hidden="true">
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
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .btn-min-width {
            min-width: 5.5rem;
        }

        .legends{
            cursor:pointer;
        }

@foreach($legends as $legend)
    @if($legend->id == 1)
        .new_pickup{
            background-color: {{$legend->color}};
        }
    @elseif($legend->id == 2)
        .vendor_row{
            background-color: {{$legend->color}};
        }
    @elseif($legend->id == 3)
        .try_and_buy{
            background-color: {{$legend->color}};
        }
    @elseif($legend->id == 4)
        .first_attempt{
            background-color: {{$legend->color}};
        }
    @elseif($legend->id == 5)
        .second_attempt{
            background-color: {{$legend->color}};
        }
    @elseif($legend->id == 6)
        .multiple_attempt{
            background-color: {{$legend->color}};
        }
    @elseif($legend->id == 7)
        .after_cut_off_time{
            background-color: {{$legend->color}};
        }
    @elseif($legend->id == 8)
        .reverse_pickup_row{
            background-color: {{$legend->color}};
        }
        @elseif($legend->id == 8)
        .reverse_pickup_row{
            background-color: {{$legend->color}};
        }
        @elseif($legend->id == 9)
        .reminder_pending_row{
             background-color: {{$legend->color}};
         }
    @endif
@endforeach
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    todo for datepicker--}}
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    {{--    todo for datepicker end--}}

    {{--    todo date filter field--}}
    <script>
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

    </script>
    {{--    todo date filter field end--}}

    <script>
        $(document).ready(function () {
            $('#search_filter').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search',
                width:'100%',
                allowClear:false
            });

        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
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
                        head.push('Rider Status');
                        head.push('Assigned Date');
                        head.push('Attempt Date');
                        head.push('Aging');
                        head.push('Attempt(s)');


                        $.each(result.data, function(index, values) {
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
                            row.push(values.rider_status);
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
        } );


        var selected_rows = [];
        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            @if (session('role_id') == 1 || count(array_intersect([18, 19], session('permissions'))) !== 0)

            buttons: [
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
                // {
                //     text: 'Update',
                //     className: 'btn btn-primary update',
                //     enabled: false,
                //     action: function (e, dt, node, config) {
                //         $('#update_pickup_modal .reason').val(null).trigger('change');
                //         $('#update_pickup_modal #trax_remarks').val('');
                //
                //         $('#update_pickup_modal').modal('show');
                //     }
                // },
                {
                    extend: 'excel',
                    title: 'Pending Pickups',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                {
                    extend: 'selectAll',
                    text: 'Select All',
                    className: 'select_all',
                    action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
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
                    action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
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
                    title: 'Pending Pickups',
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
            ajax:{
                    url: '{{ route('admin.v3_pickups.history.list') }}',
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
                {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_requests.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                {data: 'pickup_request_id', name: 'v3_pickup_requests.id', class: 'align-middle pickup_request_id'},
                {data: 'requested_date', name: 'v3_pickup_requests.created_at', class: 'align-middle requested_date'},
                {data: 'current_rider', name: 'cr.name', class: 'align-middle current_rider'},
                {data: 'last_rider', name: 'lr.name', class: 'align-middle last_rider'},
                {data: 'pickup_note_no', name: 'vpn.pickup_note_id', class: 'align-middle pickup_note_no'},
                {data: 'bookings_link', name: 'v3_pickup_requests.booked', class: 'align-middle text-center bookings_link'},
                {data: 'shipments_rider_picked', name: 'vpr.shipments', class: 'align-middle shipments_rider_picked'},
                // {data: 'received_link', name: 'v3_pickup_requests.received', class: 'align-middle received_link text-center'},
                {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                {data: 'territory', name: 't.name', class: 'align-middle territory'},
                {data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
                // {data: 'type', name: 'booking_types.booking_type', class: 'align-middle type'},
                {data: 'vendor_name', name: 'usi.vendor', class: 'align-middle vendor_name'},
                {data: 'brand_name', name: 'usi.pickup_brand_name', class: 'align-middle brand_name', orderable: false, searchable: false},
                {data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
                {data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
                {data: 'city', name: 'ci.name', class: 'align-middle city'},
                {data: 'pickup_status', name: 'prs.id', class: 'align-middle pickup_status'},

                /* {data: 'trax_reason', name: 'trax_reason', class: 'align-middle trax_reason', orderable: false, searchable: false},
                {data: 'trax_remarks', name: 'trax_remarks', class: 'align-middle trax_remarks', orderable: false, searchable: false},
                {data: 'shipper_remarks', name: 'shipper_remarks', class: 'align-middle shipper_remarks', orderable: false, searchable: false},
                {data: 'rider_remarks', name: 'vpr.rider_remarks', class: 'align-middle rider_remarks', orderable: false, searchable: false},
                 */

                {data: 'all_remarks', name: 'vpn.pickup_note_id', class: 'align-middle all_remarks'},


                {data: 'rider_status', name: 'rs.id', class: 'align-middle rider_status'},
                {data: 'assigned_date', name: 'vpa.created_at', class: 'align-middle attempted_date', orderable: false, searchable: false},
                {data: 'attempted_date', name: 'attempted_date', class: 'align-middle attempted_date', orderable: false, searchable: false},
                {data: 'attempts', name: 'v2_pickup_requests.attempts', class: 'align-middle attempts'},
                {data: 'aging', name: 'aging', class: 'align-middle aging', orderable: false, searchable: false},
                {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}

            ],
            rowCallback: function(row, data, index) {
                var info = table.page.info();

                $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                if ($.inArray(data.id, selected_rows) !== -1) {
                    table.row(row).select();
                }
            },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                var rider_status_select = '<select name="rider_status_select" id="rider_status_select" class="select2 form-control"></select>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.action') || $(header).is('.aging') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
                        $(td).appendTo($(search));
                    }else if($(header).is('.pickup_status')){
                        $(drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }
                    else if($(header).is('.rider_status')){
                        $(rider_status_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }
                    else {
                        var current = $(input).appendTo($(search)).on('change', function() {
                            column.search($(this).val(), false, false, true).draw();
                        }).wrap(td).after(icon);

                        if (column.search()) {
                            current.val(column.search());
                        }
                    }
                });
                var data = $.map({!! $pickup_statuses !!}, function (obj) {
                    obj.id = obj.id; // replace pk with your identifier
                    obj.text = obj.name;
                    return obj;
                });

                $("#status_select").prepend('<option value="" selected></option>').select2({
                    data:data,
                    placeholder: "Select Pickup Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                var rider_data = $.map({!! $rider_statuses !!}, function (obj) {
                    obj.id = obj.id; // replace pk with your identifier
                    obj.text = obj.name;
                    return obj;
                });
                $("#rider_status_select").prepend('<option value="" selected></option>').select2({
                    data:rider_data,
                    placeholder: "Select Rider Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }

        });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.assign').enable();
                    table.button('.update').enable();
                }
                else {
                    table.button('.assign').disable();
                    table.button('.update').disable();
                }
            });

            $('#assign_to_rider .rider').select2({
                width: '100%',
                placeholder: 'Rider*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#assign_to_rider form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
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
                    }).then(function(confirm) {
                        if(confirm){
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
                dropdownParent:$('#update_pickup_modal')
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });
            $('body').on('change','#update_pickup_modal #trax_remarks',function() {
                $(this).val($(this).val().trim());
            });
            $('#update_pickup_modal form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

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
                    }).then(function(confirm) {
                        if(confirm){
                            $('#update_pickup_request_btn_submit').prop('disabled', true);

                            $('#update_pickup_request_ids').val(selected_rows);
                            form.submit();

                        }

                    });
                    $('#update_pickup_request_btn_submit').prop('disabled', false);
                }
            });
            var route = '{!! route('admin.tracking.index') !!}';
            $('body').on('click','#datatable tbody tr td.bookings_link button',function () {
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
                .done(function(data) {
                    if (data) {
                        var shipments = '';
                        if (data.booked) {
                            $.each(data.booked, function(index, tracking_numbers) {
                                shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                            });
                        }
                        $('#bookings_modal .modal-body').html(shipments);
                    }
                });
            });
            {{--$('#datatable tbody').on('click','tr td.received_link button',function () {--}}
            {{--    var id = parseInt($(this).parents('tr').attr('id'));--}}
            {{--    $('#pending_bookings_modal .modal-body').html('');--}}
            {{--    $('#pending_bookings_modal').modal('show');--}}

            {{--    $.ajax({--}}
            {{--        url: '{!! route('admin.v2_pickups.pending.bookings.received') !!}',--}}
            {{--        method: 'POST',--}}
            {{--        data: {--}}
            {{--            '_token': '{{ csrf_token() }}',--}}
            {{--            'pickup_request_id': id--}}
            {{--        }--}}
            {{--    })--}}
            {{--    .done(function(data) {--}}
            {{--        if (data) {--}}
            {{--            var shipments = '';--}}
            {{--            if (data.booked) {--}}
            {{--                $.each(data.booked, function(index, tracking_numbers) {--}}
            {{--                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';--}}
            {{--                });--}}
            {{--            }--}}
            {{--            $('#pending_bookings_modal .modal-body').html(shipments);--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}

            $('#datatable tbody').on('click', 'tr td.pickup_note_no button.print', function() {
                var pickup_note_id = parseInt($(this).attr('rel'));

                print(pickup_note_id);
            });



            function print(id) {
                $.ajax({
                    url: '{!! route('admin.v2_pickups.pending.print') !!}',
                    method: 'POST',
                    data: {
                        'ids': [id],
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }


            $('table#legends_table').on('click', 'tr', function(){
                var id = parseInt($(this).attr('id'));
                if(id){
                    $('#legend_filter').val(id);
                    table.draw()
                }
            })
            $('body').on('click','.reminderMarkStatus',function () {
                var action = $(this).data('action');
                var row_id = $(this).parents('tr').attr('id');
                console.log(row_id);
               if(action === 'reattempt'){
                   var atext = 'Select Yes to put Reminder!';
                }

                if(row_id != '' && action === 'reminder'){
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
                }
            });

            $('body').on('click','.addRemarks',function () {
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
            $( "#add_remarks_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();
                }

                });


            $('#search_filter_btn').on('click',function () {
               table.draw(true);
            });



            $('#datatable tbody').on('click', 'tr td.all_remarks button.all_remarks_btn', function() {
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
                .done(function(data) {
                    if (data) {
                        console.log(data.remarks);

                        $('#rider_remarks_td').html(data.remarks.rider_remarks);
                        $('#shipper_remarks_td').html(data.remarks.shipper_remarks);
                        $('#trax_reason_td').html(data.remarks.trax_reason);
                        $('#trax_remarks_td').html(data.remarks.trax_remarks);
                        $('#remarks_td').html(data.remarks.remarks);
                        $('#AllRemarksModal').modal('show');
                        if(!data.remarks.reverse_pickup){
                            
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

            $('#star_shippers_filter').on('click',function () {
                $('#star_shippers_filter').val(1);
                table.draw(true);
                $('#star_shippers_filter').val(0);
            });
        });
    </script>
@endsection