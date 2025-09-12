
@extends('admin.layout.master')
@section('title','Lost Shipments')

@section('content')
    <h1 class="mb-1">
        Lost Shipments
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                @if (session('role_id') == 1 || in_array(944, session('permissions')))
                    <input type="hidden" name="search_total_lost_shipments" id="search_total_lost_shipments">
                    <input type="hidden" name="search_total_lost_approved_shipments" id="search_total_lost_approved_shipments">
                    <input type="hidden" name="search_total_lost_pending_shipments" id="search_total_lost_pending_shipments">
                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="col-3">

                            <div class="form-group">
                                <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                            </div>
                        </div>

                        <div class="col-3">
                            <fieldset class="form-group">
                                <select name="search_lost_status" id="search_lost_status" class="form-control select2" >
                                        <option value="0">Pending</option>
                                        <option value="1">Approved</option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required" >
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required">
                            </div>
                        </div>

                 

                        <div class="col-2 mt-2">
                            <button type="button" id="search_filter_btn" class="btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </form>
              
                    <div class="row justify-content-center" >
                        <div class="col-3">
                            <div class="card bg-gradient-directional-in_transit pull-up cursor-pointer" id="search_total_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-clock text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="total_of_shipments">
                                                    {{ $lost_shipments }}
                                                </h3>
                                                <span>Total Lost Shipments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-3">
                            <div class="card bg-gradient-directional-return_delivered pull-up cursor-pointer" id="search_total_approved_div" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Total Lost Approved Count Is Of Last 31 Days">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="la la-check icon-clock text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="total_of_approved_shipments">
                                                    {{ ($total_of_approved_shipments) == 0 ? '0' : $total_of_approved_shipments  }}
                                                </h3>
                                                <span>Total Lost Approved Shipments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card bg-gradient-directional-destination pull-up cursor-pointer" id="search_total_pending_div" >
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="la la-hourglass-3 icon-clock text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="total_of_pending_shipments">
                                                    {{ ($total_of_pending_shipments) == 0 ? '0' : $total_of_pending_shipments}}
                                                </h3>
                                                <span>Total Lost Pending Shipments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">

                            <div class="card bg-gradient-directional-rejected_shipment pull-up cursor-pointer" id="total_shipment_rejection_count" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Total Lost Rejection Count Is Of Last 31 Days">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="la la-close text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="rejection_shipments">
                                                    {{ $total_of_rejected_shipments }}
                                                </h3>
                                                <span>Total Shipment Reject Count</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                @endif
           
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Old Responsible Person(s)</th>
                        <th class="border-primary border-darken-1">New Responsible Person(s)</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Last Hub Name</th>
                        <th class="border-primary border-darken-1">Last Zone Name</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Lost Category</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Reference</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Status Date</th>
                        <th class="border-primary border-darken-1">Lost confirmation status</th>
                        <th class="border-primary border-darken-1">Marked By</th>
                        <th class="border-primary border-darken-1">Marked At</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_remarks_modal" role="dialog" aria-labelledby="add_remarks_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Remarks</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_remarks_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">

                        <div class="form-group">
                            <input type="text" name="add_remarks" id="add_remarks" class="form-control add_remarks" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks is required">

                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add Remarks</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="ReturnConfirmReasonModal" data-backdrop="static" role="dialog" aria-labelledby="ReturnConfirmReasonModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Return Confirm Reason</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="update_return_reason_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">

                        <div class="form-group">
                            @if($return_confirm_reasons)
                                <select id="return_reason_select" data-rule-required="true" data-msg-required="Reason is required">
                                    @foreach($return_confirm_reasons as $reason)
                                        <option value="{{$reason->id}}">{{$reason->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary update_return_confirm" value="Add">Update To Return Confirm</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>

    

    </div>

    <div class="modal fade text-left addLostResponsibleModal" data-backdrop="static" tabindex="-1" role="dialog">        

    </div>

    <div class="modal fade" id="rejectModal" data-backdrop="static" role="dialog" aria-labelledby="rejectModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reject</h4>

                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <span>Are You Sure you want to reject this lost request Note that either of these actions will apply towards the destination. Please Confirm further status.</span> <br> <br>
                    <button type="button" class="btn btn-info ml-2 confirm">Return Confirm</button>
                    <button type="button" class="btn btn-info ml-2 re-attempt" >Re-Attempt</button>
                </div>
            </div>
        </div>
    </div>
    

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .bg-gradient-directional-in_transit {
            background-image: linear-gradient(45deg, #535BE2, #9ea5ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_delivered {
            background-image: linear-gradient(45deg, #02c123, #99ff12d1);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-destination {
            background-image: linear-gradient(45deg, #027d8a, #01e4e4);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-rejected_shipment {
            background-image: linear-gradient(45deg, #7f8b96, #f52f2f);
            background-repeat: repeat-x;
        }


    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
    $(document).ready(function(){
        $('#return_reason_select').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Select Reason'
        });
        $('#search_lost_status').prepend('<option value="" selected></option>').select2({
            width:'100%',
            placeholder:"Select Lost Status",
            allowClear:true,
        });
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];
                var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.delivery.lost.list') }}',
                    data: params,
                    method: 'POST', 
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Tracking No.');
                        head.push('Responsible Person Trax ID');
                        head.push('Responsible Person Name');
                        head.push('Responsible Person Type');
                        head.push('Responsible Person Status');
                        head.push('Shipper');
                        head.push('Origin');
                        head.push('Destination');
                        head.push('Hub');
                        head.push('Last Hub Name');
                        head.push('Last Zone Name');
                        head.push('Consignee Name');
                        head.push('Phone');
                        head.push('Address');
                        head.push('Collection Amount');
                        head.push('Parcel Value');
                        head.push('Shipping Mode');
                        head.push('Service Type');
                        head.push('Lost Category');
                        head.push('Remarks');
                        head.push('Reference');
                        head.push('Arrival Date');
                        head.push('Status Date');
                        head.push('Lost Confirmation Status');
                        head.push('Marked By');
                        head.push('Marked At');

                        $.each(result.data, function(index, values) {
                            row = [];
                            var parcel_value = (values.parcel_value == null || values.parcel_value == "") ? 0 : values.parcel_value;

                            row.push(index + 1);
                            row.push(values.tracking_number);
                            row.push(values.excel_responsible_person_id);
                            row.push(values.excel_responsible_person_name);
                            row.push(values.excel_responsible_person_type);
                            row.push(values.excel_responsible_person_status);
                            row.push(values.shipper);
                            row.push(values.origin);
                            row.push(values.destination);
                            row.push(values.hub);
                            row.push(values.last_hub_name);
                            row.push(values.last_zone_name);
                            row.push(values.consignee_name);
                            row.push(values.phone);
                            row.push(values.consignee_address);
                            row.push(values.amount);
                            row.push(parcel_value);
                            row.push(values.shipping_mode);
                            row.push(values.service_type);
                            row.push($('<textarea/>').html(values.lost_category).text());
                            row.push(values.remarks);
                            row.push(values.reference);
                            row.push(values.arrival);
                            row.push(values.current_status_date);
                            row.push(values.lost_confirmation_status);
                            row.push(values.marked_by);
                            row.push(values.marked_at);

                            body.push(row);
                        });
                    },
                    async: false
                });

                return {body: body, header: head};
            }
        } );
        var confirm_reattempt;
        var selected_rows = [];
        var shipment_id;
        var action_selected_shipment = [];
        @if (session('role_id') == 1 || count(array_intersect([128, 129], session('permissions'))) !== 0)
         confirm_reattempt = 1
        @endif
        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',

            buttons: [
                @if (session('role_id') == 1 || count(array_intersect([128, 129], session('permissions'))) !== 0)
                    @if (session('role_id') == 1 || in_array(128, session('permissions')))
                        {
                            text: 'Return Confirm',
                            className: 'btn btn-primary confirm',
                            enabled: false,
                            action: function (e, dt, node, config) {
                                $('#ReturnConfirmReasonModal').modal('show');
                                $('#ReturnConfirmReasonModal').on('hide.bs.modal', function () {
                                    $('#return_reason_select').val(null).trigger('change');
                                });
                                $('#update_return_reason_form').validate({
                                    ignore: [],
                                    errorClass: 'danger',
                                    successClass: 'success',
                                    errorPlacement: function(error, element) {
                                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                                    },
                                    normalizer: function(value) {
                                        return $.trim(value);
                                    },
                                    submitHandler: function(form) {
                                        var return_reason_select = $('#return_reason_select').val();
                                        swal({
                                            title: 'Are You Sure?',
                                            text: 'Select Yes to change shipment status to Return-Confirm!',
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
                                                    url: '{{ route('admin.delivery.lost.confirm.status.lost') }}',
                                                    method:'POST',
                                                    data:{
                                                        'shipment_ids': (selected_rows.length == 0) ? action_selected_shipment : selected_rows,
                                                        'reason':return_reason_select,
                                                        '_token':'{{ csrf_token() }}'
                                                    }
                                                }).done(function (data) {
                                                    UnblockPagePermanently();
                                                    $('#ReturnConfirmReasonModal').modal('hide');
                                                    selected_rows = [];
                                                    action_selected_shipment = [];
                                                    table.button('.reject').disable();
                                                    table.button('.approve').disable();
                                                    table.button('.re-attempt').disable();
                                                    table.button('.confirm').disable();
                                                    table.draw('false');
                                                    table.rows().deselect();
                                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        },
                    @endif

                    @if (session('role_id') == 1 || in_array(129, session('permissions')))
                        {
                            text: 'Re-Attempt',
                            className: 'btn btn-primary re-attempt',
                            enabled: false,
                            action: function (e, dt, node, config) {
                                $('#add_remarks_modal').modal('show');
                                $('#add_remarks_modal').on('hide.bs.modal', function () {
                                    $('#add_remarks_form input.add_remarks').val('');
                                });
                                $('#add_remarks_form').validate({
                                    ignore: [],
                                    errorClass: 'danger',
                                    successClass: 'success',
                                    errorPlacement: function(error, element) {
                                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                                    },
                                    normalizer: function(value) {
                                        return $.trim(value);
                                    },
                                    submitHandler: function(form) {
                                        var remarks = $('#add_remarks').val();
                                        swal({
                                            title: 'Are You Sure?',
                                            text: 'Select Yes to change shipment status to Re-Attempt!',
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
                                                    url: '{{ route('admin.delivery.lost.reattempt.status.lost') }}',
                                                    method:'POST',
                                                    data:{
                                                        'shipment_ids': (selected_rows.length == 0) ? action_selected_shipment : selected_rows,
                                                        'remarks':remarks,
                                                        '_token':'{{ csrf_token() }}'
                                                    }
                                                }).done(function (data) {
                                                    UnblockPagePermanently();
                                                    $('#add_remarks_modal').modal('hide');
                                                    selected_rows = [];
                                                    action_selected_shipment = [];
                                                    table.button('.reject').disable();
                                                    table.button('.approve').disable();
                                                    table.button('.re-attempt').disable();
                                                    table.button('.confirm').disable();
                                                    
                                                    table.draw('false');
                                                    table.rows().deselect();
                                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                                });
                                            }
                                        });
                                    }
                                });
                                
                            }
                        },
                    @endif
                @endif

                @if (session('role_id') == 1 || in_array(944, session('permissions')))
                    {
                        text: 'Approve',
                        className: 'btn btn-primary approve',
                        enabled: false,
                        action: function(e, dt, node, config) {
                            if (selected_rows != '') {
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select yes to approve!',
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
                                }).then(function(confirm) {
                                    if (confirm) {
                                        blockPagePermanently();
                                
                                        $.ajax({
                                            url: '{!! route('admin.delivery.lost.approve.status.lost') !!}',
                                            method: 'POST',
                                            data: {
                                                'shipment_ids': (selected_rows.length == 0) ? action_selected_shipment : selected_rows,
                                                '_token': '{{ csrf_token() }}',
                                                'approve': '1',
                                            }
                                        }).done(function(data) {
                                            UnblockPagePermanently();
                                            table.rows().deselect();
                                            table.button('.reject').disable();
                                            table.button('.approve').disable();
                                            table.button('.re-attempt').disable();
                                            table.button('.confirm').disable();
                                            table.draw('false');
                                            if (data.status == 1) {
                                                UnblockPagePermanently();
                                                table.draw('false');
                                                selected_rows = [];
                                                action_selected_shipment = [];
                                                toastr.success(data.success,
                                                    'Success!', {
                                                        positionClass: 'toast-bottom-center',
                                                        containerId: 'toast-bottom-center'
                                                    });
                                            } else {
                                                UnblockPagePermanently();
                                                table.button('.reject').enable();
                                                table.button('.approve').enable();
                                                table.button('.re-attempt').enable();
                                                table.button('.confirm').enable();
                                                toastr.error(data.error,
                                                    'Error!', {
                                                        positionClass: 'toast-top-center',
                                                        containerId: 'toast-top-center'
                                                    });
                                            }

                                        });
                                    }
                                });

                            }
                        }
                    },
                    
                @endif

                    @if (session('role_id') == 1 || in_array(944, session('permissions')))
                        {
                            text: 'Reject',
                            className: 'btn btn-primary reject',
                            enabled: false,
                            action: function(e, dt, node, config) {
                                if (selected_rows != '') {                               
                                    $('#rejectModal').modal('show');
                                }
                            }
                        },
                    @endif

                {
                    extend: 'excel',
                    title: 'Lost Shipments',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                {
                    extend: 'selectAll',
                    text: 'Select All',
                    className: 'select_all',
                    action: function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                            var row = table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                id = parseInt(row.id());
                                approval = row.data().approval;
                                cleared = row.data().cleared;
                                permission = row.data().permission;
                                shipment_cleared = row.data().shipment_cleared;

                                if (id) {
                                    var index = $.inArray(id, selected_rows);
                                    // if ((index === -1 && approval >= 1 && cleared != 0) || (index === -1 && confirm_reattempt === 1) || (permission == 944 && shipment_cleared == null)) {
                                        selected_rows.push(id);
                                        row.select();
                                        table.button('.reject').enable();
                                        table.button('.approve').enable();
                                        table.button('.re-attempt').enable();
                                        table.button('.confirm').enable();
                                    // }
                                }
                            }
                        });
                    }
                },
                {
                    extend: 'selectNone',
                    text: 'Select None',
                    className: 'select_none',
                    action: function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                            var row = table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                row.deselect();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index !== -1) {
                                    selected_rows.splice(index, 1);
                                }

                                if (selected_rows.length == 0) {
                                    table.button('.reject').disable();
                                    table.button('.approve').disable();
                                    table.button('.re-attempt').disable();
                                    table.button('.confirm').disable();
                                }
                            }
                        });
                    }
                },
                'reset'
            ],


           
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
                url: '{{ route('admin.delivery.lost.list') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {    
                    // d.search_total_lost_shipments = $('#search_total_lost_shipments').val();
                    d.search_total_lost_approved_shipments = $('#search_total_lost_approved_shipments').val();
                    d.search_total_lost_pending_shipments = $('#search_total_lost_pending_shipments').val();
                    d.search_from = $('input[name="from_date_formatted"]').val();
                    d.search_to = $('input[name="to_date_formatted"]').val();
                    d.tracking_numbers = $('#search_form .tracking_numbers').val();
                    d.search_lost_status = $('#search_lost_status').val();


                }
            },
            rowId: 'shId',
            order: [[15, 'desc']],
            columns: [
                {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                {data: 'old_responsible_person', name: 'old_responsible_person', class: 'align-middle old_responisble_person'},
                {data: 'responsible_person_shipment', name: 'responsible_person_shipment', class: 'align-middle responsible_person_shipment_new'},
                {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                {data: 'last_hub_name', name: 'ci.name', class: 'align-middle last_hub_name'},
                {data: 'last_zone_name', name: 'zo.name', class: 'align-middle last_zone_name'},
                {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                {data: 'phone', name: 'shipments.consignee_phone_number_1', class: 'align-middle phone'},
                {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                {data: 'shipping_mode', name: 'shipping_mode', class: 'align-middle shipping_mode'},
                {data: 'service_type', name: 'service_type', class: 'align-middle service_type'},
                {data: 'lost_category', name: 'lcs.type', class: 'align-middle lost_category'},
                {data: 'remarks', name: 'shipments_journey.remarks', class: 'align-middle remarks'},
                {data: 'reference', name: 'shipments_journey.reference_1_id', class: 'align-middle reference'},
                {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
                {data: 'lost_confirmation_status', name: 'lost_confirmation_status', class: 'align-middle lost_confirmation_status'},
                {data: 'marked_by', name: 'ad.name', class: 'align-middle marked_by'},
                {data: 'marked_at', name: 'shipments_journey.updated_at', class: 'align-middle marked_at'},
                {data: 'action',name: 'action',class: 'text-center align-middle action p-1', orderable: false,searchable: false},
            ],
            rowCallback: function(row, data, index) {
                if (data.aging < 7) {
                    $('td:eq(0)', row).addClass('select-checkbox');
                }

                if(data.cleared == 0 && data.permission === 944){
                    $('td:eq(0)', row).addClass('select-checkbox');
                }else if (data.cleared == 1 && data.permission != 944){
                    $('td:eq(0)', row).addClass('select-checkbox');
                }else if(data.null_shipment == null){
                    $('td:eq(0)', row).addClass('select-checkbox');
                }else{
                    $('td:eq(0)', row).removeClass('select-checkbox');
                }

                var info = table.page.info();
                $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                if ($.inArray(data.shId, selected_rows) !== -1) {
                    table.row(row).select();
                }
            },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                    '</select>';
                var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();
                    if ($(header).is('.select') || $(header).is('.serial_number')) {
                        $(td).appendTo($(search));
                    }else if($(header).is('.status')){
                        $(drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.shipping_mode')){
                        $(mode_drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.service_type')){
                        $(service_drop_select).appendTo($(search))
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
                var data = $.map({!! $shipment_status !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                });


                $("#status_select").prepend('<option value="" selected></option>').select2({
                    data:data,
                    placeholder: "Select Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                var data1 = $.map({!! $shipping_mode !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.mode;
                    return obj;
                });

                $("#mode_select").prepend('<option value="" selected></option>').select2({
                    data:data1,
                    placeholder: "Select Mode",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                var data2 = $.map({!! $service_type !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.booking_type;
                    return obj;
                });

                $("#service_select").prepend('<option value="" selected></option>').select2({
                    data:data2,
                    placeholder: "Select Service",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }
        });
        $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
            var id = parseInt($(this).parent('tr').attr('id'));
            var rowData = table.row($(this).parents('tr')).data();
            var index = $.inArray(id, selected_rows);

            if ((index === -1 && rowData.approval != 1) ||  (index === -1 && confirm_reattempt === 1) || (index === -1 && rowData.approval >= 1 && rowData.cleared == 0)) {
                selected_rows.push(id);
            }
            else {
                selected_rows.splice(index, 1);
            }
    
            if (selected_rows.length > 0) {
                table.button('.reject').enable();
                table.button('.approve').enable();
                table.button('.re-attempt').enable();
                table.button('.confirm').enable();
            }
            else {
                table.button('.reject').disable();
                table.button('.approve').disable();
                table.button('.re-attempt').disable();
                table.button('.confirm').disable();
            }
        });
      


        $('#rejectModal .confirm').click(function(){
            $('#ReturnConfirmReasonModal').modal('show');
            $('#ReturnConfirmReasonModal').on('hide.bs.modal', function () {
                $('#return_reason_select').val(null).trigger('change');
            });
            $('#rejectModal').modal('hide');

            $('#update_return_reason_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    var return_reason_select = $('#return_reason_select').val();
                    swal({
                        title: 'Are You Sure?',
                        text: 'You are rejecting the Shipment Lost status, please confirm if you want to proceed this for Return.',
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
                                url: '{{ route('admin.delivery.lost.confirm.status.lost') }}',
                                method:'POST',
                                data:{
                                    'shipment_ids': (selected_rows.length == 0) ? action_selected_shipment : selected_rows,
                                    'reason':return_reason_select,
                                    '_token':'{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                UnblockPagePermanently();
                                $('#ReturnConfirmReasonModal').modal('hide');
                                selected_rows = [];
                                action_selected_shipment = [];
                                table.rows().deselect();
                                table.button('.reject').disable();
                                table.button('.approve').disable();
                                table.button('.re-attempt').disable();
                                table.button('.confirm').disable();
                                table.draw('false');
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            });
                        }
                    });
                }
            });
		});

     

        $('#rejectModal .re-attempt').click(function() {
            $('#add_remarks_modal').modal('show');
            $('#add_remarks_modal').on('hide.bs.modal', function() {
                $('#add_remarks_form input.add_remarks').val('');
            });
            $('#rejectModal').modal('hide');

            $('#add_remarks_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    var remarks = $('#add_remarks').val();
                    swal({
                        title: 'Are You Sure?',
                        text: 'You are rejecting the Shipment Lost status, please confirm if you want to proceed this for Reattempt.',
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
                    }).then(function(confirm) {
                        if (confirm) {
                            blockPagePermanently();
                            $.ajax({
                                url: '{{ route('admin.delivery.lost.reattempt.status.lost') }}',
                                method: 'POST',
                                data: {
                                    'shipment_ids': (selected_rows.length == 0) ? action_selected_shipment : selected_rows,
                                    '_token': '{{ csrf_token() }}',
                                    'remarks': remarks,
                                }
                            }).done(function(data) {
                                UnblockPagePermanently();
                                $('#add_remarks_modal').modal('hide');
                                selected_rows = [];
                                action_selected_shipment = [];
                                // Assuming 'table' is defined somewhere and it's DataTable
                                table.button('.reject').disable();
                                table.button('.approve').disable();
                                table.button('.re-attempt').disable();
                                table.button('.confirm').disable();
                                table.draw('false');
                                table.rows().deselect();
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            });
                        }
                    });
                }
            })
        });

        $('#search_total_div').on('click', function() {
            $('#search_total_lost_shipments').val(1);
            $('#search_total_lost_approved_shipments').val('');
            $('#search_total_lost_pending_shipments').val('');
            // table.draw();
        });


        $('#search_total_approved_div').on('click', function() {
            $('#search_total_lost_shipments').val('');
            $('#search_total_lost_approved_shipments').val(2);
            $('#search_total_lost_pending_shipments').val('');
            // table.draw();
        });

        $('#search_total_pending_div').on('click', function() {
            $('#search_total_lost_shipments').val('');
            $('#search_total_lost_approved_shipments').val('');
            $('#search_total_lost_pending_shipments').val(3);
            table.draw();
        });

  
        $('#datatable tbody').on('click', '.reject', function() {
            $('#rejectModal').modal('show');
            shipment_id = $(this).attr('data-id');
            action_selected_shipment.push(shipment_id); 
            selected_rows = [];
            action_selected_shipment = $.grep(action_selected_shipment, function(value) {
                return value !== null && value !== undefined;
            });

        });

        $('#datatable tbody').on('click', '.approve', function() {
            shipment_id = $(this).attr('data-id')
            action_selected_shipment.push(shipment_id);
            selected_rows = []; 
            action_selected_shipment = $.grep(action_selected_shipment, function(value) {
                return value !== null && value !== undefined;
            });
            swal({  
                title: 'Are You Sure?',
                text: 'Select yes to approve!',
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
            }).then(function(confirm) {
                if (confirm) {
                    blockPagePermanently();
            
                    $.ajax({
                        url: '{!! route('admin.delivery.lost.approve.status.lost') !!}',
                        method: 'POST',
                        data: {
                            'shipment_ids': (selected_rows.length == 0) ? action_selected_shipment : selected_rows,
                            '_token': '{{ csrf_token() }}',
                            'approve': '1',
                        }
                    }).done(function(data) {
                        table.draw('false');
                        if (data.status == 1) {
                            UnblockPagePermanently();
                            table.draw('false');
                            selected_rows = [];
                            action_selected_shipment = [];

                            toastr.success(data.success,
                                'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                        } else {
                            UnblockPagePermanently();
                            toastr.error(data.error,
                                'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                        }

                    });
                }
            });
        });
    
        var thirtydays = '{{ $thirtyday }}';
        var today = '{{ $today }}';
        var from_date = $('#from_date').pickadate({
            firstDay: 1,
            clear: 'Clear',
            max : new Date(today),
            format:'dd mmmm, yyyy',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onOpen: function() {
                $('#from_date_root').css('top','40px');
            },
            onSet: function(context) {
                var old_date_formatted = $('input[name="from_date_formatted"]').val();
                var contractMoment = moment(old_date_formatted);
                var current = moment(contractMoment).add(30, 'days');
                to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                to_date.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                to_date.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
            }
        });
        var to_date = $('#to_date').pickadate({
            firstDay: 1,
            clear: 'Clear',
            max : new Date(today),
            format:'dd mmmm, yyyy',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 23:59:59',
            hiddenSuffix: '_formatted',
            onOpen: function() {
                $('#to_date_root').css('top', '40px');
            },
            onSet: function(context) {
                // var current_date_formatted = $('input[name="to_date_formatted"]').val();
                // from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
            }
        });

        $('#search_filter_btn').on('click',function () {
            table.draw();
        });

        var shipment_id;
        $('#datatable tbody').on('click', '.responsible_person_shipment', function() {
            var shipment_id = $(this).attr('data-shipment-id');

            // Make an AJAX request
            $.ajax({
                url:  '{{ route('admin.delivery.lost.lost_shipment_responsible_list') }}',
                type: 'GET', 
                data: { shipment_id: shipment_id }, 
                success: function(response) {
                    var modalContent =  
                        '<div class="modal-dialog modal-xl" role="document">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header bg-primary white">' +
                        '<h4 class="modal-title white">New Lost Responsible for Shipment ID: ' + shipment_id + '</h4>' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>' +
                        '<div class="modal-body text-center">' +
                        '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                        '<table class="table table-bordered datatable" id="addLostResponsibleTable">' +
                        '<thead>' +
                        '<tr role="row" class="bg-primary white">' +
                        '<th class="border-primary border-darken-1">S. No.</th>' +
                        '<th class="border-primary border-darken-1">Employee ID</th>' +
                        '<th class="border-primary border-darken-1">Employee Name</th>' +
                        '<th class="border-primary border-darken-1">Employee Type</th>' +
                        '<th class="border-primary border-darken-1">Employee Status</th>' +
                        '<th class="border-primary border-darken-1">Marked At</th>' +

                        '</tr>' +
                        '</thead>' +
                        '<tbody>'; 

                        $.each(response.details, function(index, item) {
                            var employee = item;
                                modalContent += '<tr>';
                                modalContent += '<td>' + (index + 1) + '</td>'; 
                                modalContent += '<td>' + (employee.trax_id ? employee.trax_id : '') + '</td>'; 
                                modalContent += '<td>' + employee.name + '</td>'; 
                                modalContent += '<td>' + employee.type + '</td>'; 
                                modalContent += '<td>' + employee.status + '</td>';
                                modalContent += '<td>' + employee.marked_at + '</td>'; 
                                modalContent += '</tr>';                            
                        });


                    modalContent += '</tbody>' + // End of tbody
                        '</table>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                    $('.addLostResponsibleModal').html('');
                    $('.addLostResponsibleModal').append(modalContent);
                    $('.addLostResponsibleModal').modal('show');
                },
                error: function(xhr, status, error) {
                    // Handle errors if any
                }
            });
        });

        // for old responisbles
        $('#datatable tbody').on('click', '.old_responsible_person', function() {
            var shipment_id = $(this).attr('data-shipment-id');
            $.ajax({
                url:  '{{ route('admin.delivery.lost.old_lost_shipment_responsible_list') }}',
                type: 'GET', 
                data: { shipment_id: shipment_id }, 
                success: function(response) {
                    var modalContent =  
                        '<div class="modal-dialog modal-xl" role="document">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header bg-primary white">' +
                        '<h4 class="modal-title white">Old Lost Responsible for Shipment ID: ' + shipment_id + '</h4>' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>' +
                        '<div class="modal-body text-center">' +
                        '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                        '<table class="table table-bordered datatable" id="addLostResponsibleTable">' +
                        '<thead>' +
                        '<tr role="row" class="bg-primary white">' +
                        '<th class="border-primary border-darken-1">S. No.</th>' +
                        '<th class="border-primary border-darken-1">Employee ID</th>' +
                        '<th class="border-primary border-darken-1">Employee Name</th>' +
                        '<th class="border-primary border-darken-1">Employee Type</th>' +
                        '<th class="border-primary border-darken-1">Employee Status</th>' +
                        '<th class="border-primary border-darken-1">Marked At</th>' +

                        '</tr>' +
                        '</thead>' +
                        '<tbody>'; 

                        $.each(response.details, function(index, item) {
                            var employee = item;
                                modalContent += '<tr>';
                                modalContent += '<td>' + (index + 1) + '</td>'; 
                                modalContent += '<td>' + (employee.trax_id ? employee.trax_id : '') + '</td>'; 
                                modalContent += '<td>' + employee.name + '</td>'; 
                                modalContent += '<td>' + employee.type + '</td>'; 
                                modalContent += '<td>' + employee.status + '</td>';
                                modalContent += '<td>' + employee.marked_at + '</td>'; 
                                modalContent += '</tr>';                            
                        });


                    modalContent += '</tbody>' + // End of tbody
                        '</table>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                    $('.addLostResponsibleModal').html('');
                    $('.addLostResponsibleModal').append(modalContent);
                    $('.addLostResponsibleModal').modal('show');
                },
                error: function(xhr, status, error) {
                    // Handle errors if any
                }
            });
        });

        $('#search_filter_btn').click(function() {
            var from_date =$('input[name="from_date_formatted"]').val();
            var to_date =  $('input[name="to_date_formatted"]').val();

            if(from_date != '' && to_date !=''){
                    $.ajax({
                    url:  '{{ route('admin.delivery.lost.lost_data') }}',
                    method: 'POST',
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        var total = parseInt(response.details.total_of_pending_shipments == null ? 0 : response.details.total_of_pending_shipments) +
                            parseInt(response.details.total_of_approved_shipments == null ? 0 : response.details.total_of_approved_shipments) +
                            parseInt(response.details.total_rejections == null ? 0 : response.details.total_rejections);                       
                        $('#total_of_pending_shipments').text(response.details.total_of_pending_shipments == null ? 0 : response.details.total_of_pending_shipments);
                        $('#total_of_approved_shipments').text(response.details.total_of_approved_shipments == null ? 0 : response.details.total_of_approved_shipments);
                        $('#rejection_shipments').text(response.details.total_rejections == null ? 0 : response.details.total_rejections);
                        $('#total_of_shipments').text(total.toString());
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request failed');
                        console.error('Status:', status);
                        console.error('Error:', error);
                    }
                });
            }
       
        });

        //Selectize
        var select = $('#search_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
                onType: function (str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
        });
   
        $('#search_form').bind('submit', function (e) {
                e.preventDefault();
                var tracking_numbers = $('#search_form .tracking_numbers').val();
                if (tracking_numbers != '') {
                    table.draw();
                }
            });    
    
    });

    </script>
@endsection