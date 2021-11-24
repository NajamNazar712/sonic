@extends('admin.layout.master')
@section('title','Return Confirmation Pending Shipments')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <h1 class="mb-1">
        Return Confirmation Pending Shipments
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center">

                    <div class="col-3">
                        <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                            @foreach($shipping_mode as $mode)
                                <option value="{{$mode->id}}">{{$mode->mode}}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-5">
                        <form id="track_form" class="form-inline mb-1 " novalidate="novalidate">
                            <div class="form-group">
                                <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                            </div>

                            <div class="form-group ml-1">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Shipper Phone(s)</th>
                        <th class="border-primary border-darken-1">Vendor</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Shipper Remarks</th>
                        <th class="border-primary border-darken-1">OSA Estimated Charges</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Status Date</th>
                        <th class="border-primary border-darken-1">Confirmation Required</th>
                        <th class="border-primary border-darken-1">Confirmation On</th>
                        <th class="border-primary border-darken-1">Re-Attempt Count</th>
                        <th class="border-primary border-darken-1">Assigned Agent</th>
                        <th class="border-primary border-darken-1">Assigned At</th>
                        <th class="border-primary border-darken-1">Assigned By</th>
						<th class="border-primary border-darken-1">Consolidation</th>
                        <th class="border-primary border-darken-1">Consolidated IDs</th>
                        <th class="border-primary border-darken-1">Actions</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <div class="modal fade" id="excel_upload_modal" data-backdrop="static" role="dialog" aria-labelledby="excel_upload_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="excel_upload_modal_title">Upload Excel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="return_status_form" class="form-horizontal" method="POST" action="{{ route('admin.return.excel.store') }}" novalidate="novalidate" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row align-items-center justify-content-center">
                            <div class="col">
                                <div class="form-group">
                                    <input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group text-left">
                                    <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                </div>
                            </div>

                            <div class="col ml-auto">
                                <div class="form-group text-right">
                                    <a href="{{ asset('file/Trax Return Status Template.xlsx') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="agent_assign_modal" data-backdrop="static" role="dialog" aria-labelledby="agent_assign_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="agent_assign_modal_title">Upload Excel for Agent Assigning</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="assign_agent_form" class="form-horizontal" method="POST" action="{{ route('admin.return.excel.assign_agent_excel') }}" novalidate="novalidate" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row align-items-center justify-content-center">
                            <div class="col">
                                <div class="form-group">
                                    <input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group text-left">
                                    <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                </div>
                            </div>

                            <div class="col ml-auto">
                                <div class="form-group text-right">
                                    <a href="{{ asset('file/Trax Agent Assign Template.xlsx') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row align-items-center justify-content-center">
                    <div class="col">
                        <table class="table table-bordered" id="agenttable">
                            <thead>
                            <tr role="row" class="bg-primary white text-center">
                                <th colspan="2" class="border-primary border-darken-1">Agents</th>
                            </tr>
                            <tr role="row" class="bg-primary bg-lighten-1 white">
                                <th class="text-center border-primary border-lighten-2">ID</th>
                                <th class="border-primary border-lighten-2">Name</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($agents as $agent)
                                    <tr role="row">
                                        <td class="text-center">{{$agent->id}}</td>
                                        <td>{{$agent->name}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="EditEstimateChargesModal" role="dialog" aria-labelledby="EditEstimateChargesModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Estimate Charges</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="update_charges_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">

                        <div class="form-group">
                            <input type="text" name="estimate_charges" id="estimated_charges_input" class="form-control decimal" placeholder="Enter Estimate Charges" data-rule-required="true" data-msg-required="Estimate Charge is required">

                        </div>
                        <input type="hidden" id="eec_shipment_id">
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary update_charges" value="Add">Update Charges</button>
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
                        <div class="form-group">
                            <input type="text" id="return_reason_shipment_remarks" maxlength="100" class="form-control" placeholder="Remarks">
                        </div>
                        <div class="form-group">
                            @if($return_confirm_reasons)
                                <select id="single_return_reason_select" data-rule-required="true" data-msg-required="Reason is required">
                                    @foreach($return_confirm_reasons as $reason)
                                        <option value="{{$reason->id}}">{{$reason->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="form-group ml-1">
                            <button type="button" name="add" class="btn btn-primary single_update_return_confirm" id="single_reason_update_btn">Update To Return Confirm</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AssignAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Assign Agent</h4>
                </div>
                <div class="modal-body">
                    <select name="Sale_person" id="assign_agent" class="form-control select2">
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="assign_agentSubmit">Assign</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="ConsigneeInformationModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConsigneeInformationModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Consignee</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="consignee_info_div"></div>
                    <form id="label_update_form" class="mb-1 mt-2" method="POST" action="{{ route('admin.settings.blacklist.search.update') }}" novalidate="novalidate">
                        {{ csrf_field() }}

                        <input type="hidden" id="consignee_information_id" name="consignee_information_id">
                        <div class="row justify-content-center">
                            <div class="col-4">
                                <div class="form-group">
                                    <select name="label_select" id="label_select" class="form-control" data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($blacklists as $blacklist)
                                            <option value="{{ $blacklist->id }}">{{ $blacklist->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <div class="form-group">
                                    <button type="submit" name="action" class="btn btn-primary btn-block" id="label_btn" value="label">Label</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        table.dataTable {
            font-size: 12px;
        }
        
        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
        .selectize-control {
            width: 300px !important;
        }
        .goldClass{
            background-color: gold;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">

        var selected_rows = [];
        var restricted_rows = [];
        var tableagent = $('#agenttable').DataTable({
                scrollY: '200px',
                });
            $('#agent_assign_modal').on('shown.bs.modal', function () {
                tableagent.columns.adjust();
            });
        @php $permission = (in_array(490, session('permissions'))); if($permission){ $permission = 1; }else{ $permission = 0; } @endphp
        $(document).ready(function () {
            $('#label_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Labeling*'
            });
            $("#assign_agent").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Agent",
                width:'100%',
                dropdownParent:$('#AssignAgentModal')
            });

            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Shipping Mode',
                allowClear:true
            }).bind('change', function() {
                table.draw();
            });
            $('#return_reason_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Reason'
            });
            $('#single_return_reason_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Reason'
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Shipper Name');
                            head.push('Shipper Phone');
                            head.push('Vendor');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Consignee Name');
                            head.push('Consignee Phone');
                            head.push('Address');
                            head.push('Collection Amount');
                            head.push('Shipping Mode');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Reason');
                            head.push('Remarks');
                            head.push('Shipper Remarks');
                            head.push('OSA Estimated Charges');
                            head.push('Arrival Date');
                            head.push('Status Date');
                            head.push('Confirmation Required');
                            head.push('Confirmation On');
                            head.push('Re-Attempt Count');
                            head.push('Assigned Agent');
                            head.push('Assigned At');
                            head.push('Assigned By');
							head.push('Consolidation');
                            head.push('Consolidation IDs');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.order_id);
                                row.push(values.shipper);
                                row.push(values.shipper_phone);
                                row.push(values.vendor_name);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone_number_1 + '|' + values.consignee_phone_number_2);
                                row.push(values.consignee_address);
                                row.push(values.amount);
                                row.push(values.mode);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.reason);
                                row.push(values.remarks);
                                row.push(values.shipper_remarks);
                                row.push(values.nsa_osa_estimated_charges);
                                row.push(values.arrival);
                                row.push(values.last_status_date);
                                row.push(values.confirmation_req);
                                row.push(values.confirmation_on);
                                row.push(values.reattempts);
                                row.push(values.assigned_agent);
                                row.push(values.assigned_at);
                                row.push(values.assigned_by);
								row.push(values.consolidation);
                                row.push(values.consolidated_id);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var return_confirm_reasons = @json($return_confirm_reasons);
            var shipment_remarks = {};
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([45, 46, 316], session('permissions'))) !== 0)
                buttons: [
                        @if (session('role_id') == 1 || in_array(316, session('permissions')))
                    {
                        text: 'Assign Agent',
                        className: 'btn btn-primary assign',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows !== ''){
                                $('#AssignAgentModal').modal('show');

                                $('#assign_agentSubmit').on('click',function () {
                                    var assign = parseInt($('#assign_agent').val());
                                    swal({
                                        text: 'Are you sure, you want to Assign these shipments(s)?',
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
                                        if (confirm) {
                                            if (assign) {
                                                $.ajax({
                                                    url: '{!! route('admin.return.assign.agent') !!}',
                                                    method: 'POST',
                                                    data: {
                                                        'admin_id': assign,
                                                        'shipment_ids[]': selected_rows,
                                                        '_token': '{{ csrf_token() }}'
                                                    }
                                                })
                                                    .done(function (data) {
                                                        if (data.status == 0) {
                                                            $('#AssignAgentModal').modal('hide');
                                                            toastr.success(data.success, 'Success!', {
                                                                positionClass: 'toast-bottom-center',
                                                                containerId: 'toast-bottom-center'
                                                            });
                                                        } else {
                                                            toastr.error(data.error, 'Error!', {
                                                                positionClass: 'toast-top-center',
                                                                containerId: 'toast-top-center'
                                                            });
                                                        }
                                                        selected_rows = [];
                                                        restricted_rows = [];

                                                        table.rows().deselect();

                                                        table.draw(true);
                                                        table.button('.assign').disable();
                                                        table.button('.confirm').disable();
                                                        table.button('.re-attempt').disable();
                                                        table.button('.un-assign').disable();

                                                    });
                                            } else {
                                                var error = "Agent Not Selected!";
                                                toastr.error(error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                        }
                                    });
                                });

                            }else{
                                var error = "Not selected any shipments!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || in_array(316, session('permissions')))
                        {
                        text: 'Un Assign Agent',
                        className: 'btn btn-primary un-assign',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != '' && restricted_rows.length == 0){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Un Assign Agent!',
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
                                        table.rows().nodes().each(function(index) {
                                            var row = table.row(index);

                                            if ($(row.node()).hasClass('selected')) {
                                                var id = parseInt(row.id());
                                                // var remark = $(row.node()).find('td.shipment_remarks textarea').val();
                                                // shipment_remarks[id] = remark;
                                            }
                                        });

                                        $.ajax({
                                            url: '{!! route('admin.return.unassign.agent') !!}',
                                            method:'POST',
                                            data:{
                                                'shipment_ids':selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                                'action': 'un-assign',
                                            }
                                        }).done(function (data) {
                                            UnblockPagePermanently();
                                            selected_rows = [];
                                            restricted_rows = [];
                                            table.rows().deselect();
                                            table.draw('false');
                                            table.button('.confirm').disable();
                                            table.button('.re-attempt').disable();
                                            table.button('.assign').disable();
                                            table.button('.un-assign').disable();

                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                        });
                                    }
                                });

                            }
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || in_array(45, session('permissions')))
                    {
                        text: 'Confirm',
                        className: 'btn btn-primary confirm',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows !== '' && restricted_rows.length == 0){
                                $('#ReturnConfirmReasonModal').modal('show');

                            }else{
                                var error = "Not selected any shipments!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                        @endif

                        @if (session('role_id') == 1 || in_array(46, session('permissions')))
                    {
                        text: 'Re-Attempt',
                        className: 'btn btn-primary re-attempt',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != '' && restricted_rows.length == 0){
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
                                        table.rows().nodes().each(function(index) {
                                            var row = table.row(index);

                                            if ($(row.node()).hasClass('selected')) {
                                                var id = parseInt(row.id());
                                                var remark = $(row.node()).find('td.shipment_remarks textarea').val();
                                                shipment_remarks[id] = remark;
                                            }
                                        });

                                        $.ajax({
                                            url:"{{route('admin.return.reattempt.status')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids':selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                                'action': 'reattempt',
                                                'remark': shipment_remarks
                                            }
                                        }).done(function (data) {
                                            UnblockPagePermanently();
                                            selected_rows = [];
                                            restricted_rows = [];
                                            shipment_remarks = {};
                                            table.button('.confirm').disable();
                                            table.button('.re-attempt').disable();
                                            table.button('.assign').disable();
                                            table.button('.un-assign').disable();
                                            table.rows().deselect();
                                            table.draw('false');
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                        });
                                    }
                                });

                            }
                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'Return Marked',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }, {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    var assigned_agent_id = row.data().assigned_agent_id;
                                    var tat = row.data().confirmation_on;

                                    hub_id = $(row.node()).data('hub');

                                    var allow = false;

                                    if(hub_ids.length == 0) {
                                        hub_ids.push(hub_id);

                                        allow = true;
                                    }
                                    else if(hub_ids[0] == hub_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }


                                        if({{session('role_id')}} != 1 && {{$permission}} == 0 && assigned_agent_id != {{auth()->id()}} && tat > 0)
                                        {
                                            restricted_index = $.inArray(id, restricted_rows);

                                            if (restricted_index === -1) {
                                                restricted_rows.push(id);
                                            }
                                        }

                                        if(restricted_rows.length == 0)
                                        {
                                            table.button('.confirm').enable();
                                            table.button('.re-attempt').enable();
                                        }
                                        table.button('.assign').enable();
                                        table.button('.un-assign').enable();
                                    }
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

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    var restricted_index = $.inArray(id,restricted_rows);

                                    if(restricted_index !== -1)
                                    {
                                        restricted_rows.splice(restricted_index,1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.confirm').disable();
                                        table.button('.assign').disable();
                                        table.button('.re-attempt').disable();
                                        table.button('.un-assign').disable();
                                        hub_ids.splice(index, 1);
                                    }
                                }
                            });
                        }
                    },
                    {
                        title: 'Upload',
                        className: 'btn btn-primary excel-upload',
                        text: '<i class="la la-file-excel-o"></i> Upload',
                        action : function(e) {
                            $('#excel_upload_modal').modal('show');
                        }
                    },
                    {
                        title: 'Upload Agent',
                        className: 'btn btn-primary excel-upload',
                        text: '<i class="la la-file-excel-o"></i> Upload Agent',
                        action : function(e) {
                            $('#agent_assign_modal').modal('show');
                        }
                    },
                    'reset'
                ],
                @else
                buttons:[{
                    extend: 'excel',
                    title: 'Return Marked',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                @endif
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
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
                    url: '{{ route('admin.return.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                    }
                },
                rowId: 'shId',
                order: [[21, 'desc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id',defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'shipper_phone', name: 'shipper_phone', class: 'align-middle shipper_phone'},
                    {data: 'vendor_name', name: 'usi.vendor', class: 'align-middle vendor_name'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 'consignee_phone', class: 'align-middle consignee_phone'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'mode', name: 'sm.id', class: 'align-middle mode'},
                    {data: 'service_type', name: 'bt.id', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'shipment_remarks', name: 'admin_journey.remarks', class: 'align-middle shipment_remarks'},
                    {data: 'shipper_remarks', name: 'shipments_journey.remarks', class: 'align-middle shipper_remarks'},
                    {data: 'nsa_osa_estimated_charges', name: 'nsa_osa_estimated_charges', class: 'align-middle nsa_osa_estimated_charges'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
                    {data: 'confirmation_req', name: '', class: 'align-middle confirmation_req', orderable: false, searchable: false},
                    {data: 'confirmation_on', name: '', class: 'align-middle confirmation_on', orderable: false, searchable: false},
                    {data: 'reattempts', name: 'sret.created_at', class: 'align-middle reattempts',orderable: false, searchable: false},
                    {data: 'assigned_agent', name: 'asad.name', class: 'align-middle assigned_agent'},
                    {data: 'assigned_at', name: 'ras.created_at', class: 'align-middle assigned_at'},
                    {data: 'assigned_by', name: 'asadby.name', class: 'align-middle assigned_by'},
					{data:'consolidation' ,name: 'consolidation', class: 'align-middle consolidation', orderable: false, searchable: false},
                    {data:'consolidated_id' ,name: 'consolidations.consolidation_id', class: 'align-middle consolidated_id', orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
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
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.shipment_remarks')|| $(header).is('.reattempts') || $(header).is('.consolidation')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.mode')){
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

            $('#AssignAgentModal').on('shown.bs.modal',function (e) {
            });
            $('#AssignAgentModal').on('hide.bs.modal', function (e) {
                $('#assign_agent').val('').trigger('change');
            });

            var hub_ids = [];

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {

                var id = parseInt($(this).parent('tr').attr('id'));
                var hub_id = $(this).parents('tr').data('hub');
                var con_id = parseInt($(this).parent('tr').attr('consolidation_id'));
                var assigned_agent_id  = table.row($(this).parents('tr')).data().assigned_agent_id;
                var tat  = table.row($(this).parents('tr')).data().confirmation_on;

                if(con_id){
                    if(hub_ids.length == 0){
                        hub_ids.push(hub_id);
                    }else if(hub_ids[0] != hub_id){
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        return false;
                    }
                    table.rows().nodes().each(function(index) {
                        var row = table.row(index);
                        if ($(row.node()).attr('consolidation_id') == con_id) {
                            var rid = parseInt($(row.node()).attr('id'));
                            var rindex = $.inArray(rid, selected_rows);

                            if (rindex === -1) {
                                selected_rows.push(rid);
                                if(id != rid){

                                    table.row(row).select();
                                }
                            }
                            else {
                                if(id != rid){

                                    row.deselect();
                                }
                                selected_rows.splice(rindex, 1);
                            }
                            if (selected_rows.length > 0) {
                                table.button('.confirm').enable();
                                table.button('.assign').enable();
                                table.button('.re-attempt').enable();
                                table.button('.un-assign').enable();
                            }
                            else {
                                table.button('.confirm').disable();
                                table.button('.assign').disable();
                                table.button('.re-attempt').disable();
                                table.button('.un-assign').disable();
                            }
                        }
                    });
                }else{
                    if(hub_ids.length == 0){
                        hub_ids.push(hub_id);
                        var index = $.inArray(id, selected_rows);

                        if (index === -1) {
                            selected_rows.push(id);
                        }
                        else {
                            selected_rows.splice(index, 1);
                        }

                        if({{session('role_id')}} != 1 && {{$permission}} == 0 && assigned_agent_id != {{auth()->id()}} && tat > 0)
                        {
                            var restricted_index = $.inArray(id, restricted_rows);

                            if (restricted_index === -1) {
                                restricted_rows.push(id);
                            } else {
                                restricted_rows.splice(restricted_index, 1);
                            }
                        }

                        if (selected_rows.length > 0) {
                            if(restricted_rows.length == 0)
                            {
                                table.button('.re-attempt').enable();
                                table.button('.confirm').enable();
                            }
                            else{
                                table.button('.re-attempt').disable();
                                table.button('.confirm').disable();
                            }
                            table.button('.assign').enable();
                            table.button('.un-assign').enable();
                        }
                        else {
                            table.button('.confirm').disable();
                            table.button('.assign').disable();
                            table.button('.re-attempt').disable();
                            table.button('.un-assign').disable();
                        }
                    }else{
                        if(hub_ids[0] == hub_id){
                            var index = $.inArray(id, selected_rows);

                            if (index === -1) {
                                selected_rows.push(id);
                            }
                            else {
                                selected_rows.splice(index, 1);
                            }

                            if({{session('role_id')}} != 1 && {{$permission}} == 0 && assigned_agent_id != {{auth()->id()}} && tat > 0)
                            {
                                var restricted_index = $.inArray(id, restricted_rows);

                                if (restricted_index === -1) {
                                    restricted_rows.push(id);
                                } else {
                                    restricted_rows.splice(restricted_index, 1);
                                }
                            }


                            if (selected_rows.length > 0) {
                                if(restricted_rows.length == 0)
                                {
                                    table.button('.re-attempt').enable();
                                    table.button('.confirm').enable();
                                }
                                else{
                                    table.button('.re-attempt').disable();
                                    table.button('.confirm').disable();
                                }
                                table.button('.assign').enable();
                                table.button('.un-assign').enable();
                            }
                            else {
                                table.button('.confirm').disable();
                                table.button('.assign').disable();
                                table.button('.re-attempt').disable();
                                table.button('.un-assign').disable();
                            }
                        }else{
                            var error = "Selected hubs should be the same!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            return false;
                        }

                    }
                }

            });
            $('body').on('click','.returnMarkStatus',function () {
                var action = $(this).data('action');
                var row_id = $(this).parents('tr').attr('id');
                var remark = $(this).parents('tr').find('td.shipment_remarks textarea').val();
                if(action === 'confirm'){
                    atext = 'Select Yes to change shipment status to Return-Confirm!';
                    $('#ReturnConfirmReasonSingleModal').modal('show');
                    $('#return_reason_shipment_id').val(row_id);
                    $('#return_reason_shipment_remarks').val(remark);
                }else if(action === 'reattempt'){
                    atext = 'Select Yes to change shipment status to Re-Attempt!';
                }

                if(row_id != '' && action === 'reattempt'){
                    swal({
                        title: 'Are You Sure?',
                        text: atext,
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
                                url:"{{route('admin.return.marked.status.single')}}",
                                method:'POST',
                                data:{
                                    'shipment_id':row_id,
                                    '_token':'{{ csrf_token() }}',
                                    'action': action,
                                    'remark':remark
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

            $('body').on('click','.intercept',function () {
                var row_id = $(this).parents('tr').attr('id');

                if(row_id != ''){
                    var redirect = '{!! route('admin.intercept.index', ':id') !!}';
                    var url = redirect.replace(':id', row_id);
                    window.open(url);
                }
            });

            $.validator.addMethod('maxsize', function(value, element, params) {
                if ($(element).attr('type') === 'file') {
                    if (element.files && element.files.length) {
                        for (var c = 0; c < element.files.length; c++) {
                            if (element.files[c].size > params) {
                                return false;
                            }
                        }
                    }
                }

                return true;
            }, $.validator.format("File Size must not exceed {0} bytes."));

            $('#return_status_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Your shipment(s) are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#assign_agent_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Your shipment(s) are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#update_return_reason_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
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
                            $(form).find('button[type=submit]').attr('disabled', 'disabled');
                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                if ($(row.node()).hasClass('selected')) {
                                    var id = parseInt(row.id());
                                    var remarks = $(row.node()).find('td.shipment_remarks textarea').val();
                                    shipment_remarks[id] = remarks;
                                }
                            });

                            $.ajax({
                                url:"{{route('admin.return.confirm.status')}}",
                                method:'POST',
                                data:{
                                    'shipment_ids':selected_rows,
                                    '_token':'{{ csrf_token() }}',
                                    'action': 'confirm',
                                    'remark': shipment_remarks,
                                    'return_reason_select': return_reason_select
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
                                table.rows().deselect();
                                selected_rows = [];
                                shipment_remarks = {};
                                table.button('.confirm').disable();
                                table.button('.assign').disable();
                                table.button('.re-attempt').disable();
                                table.button('.un-assign').disable();
                                $('#ReturnConfirmReasonModal').modal('hide');
                                $('#return_reason_select').val(null).trigger('change');
                                $('button.update_return_confirm').attr('disabled', false);
                            });
                        }
                    });

                }
            });

            $('#single_reason_update_btn').on('click', function(){
                var shipment_id = $('#return_reason_shipment_id').val();
                var remarks = $('#return_reason_shipment_remarks').val();
                var single_return_reason_select = $('#single_return_reason_select').val();
                if(single_return_reason_select === ''){
                    var error = 'Select a reason!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }else{
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
                            var action = 'confirm';
                            blockPagePermanently();
                            $.ajax({
                                url:"{{route('admin.return.marked.status.single')}}",
                                method:'POST',
                                data:{
                                    'shipment_id':shipment_id,
                                    '_token':'{{ csrf_token() }}',
                                    'action': action,
                                    'remark':remarks,
                                    'single_return_reason_select': single_return_reason_select
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
                                $('#single_return_reason_select').val(null).trigger('change');
                                $('#return_reason_shipment_id').val('');
                                $('#return_reason_shipment_remarks').val('');
                                $('#ReturnConfirmReasonSingleModal').modal('hide');

                            });
                        }
                    });


                }

            });



            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
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

            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                var tracking_numbers = $('#track_form .tracking_numbers').val();

                if (tracking_numbers != '') {
                    table.draw();
                }

            });

            $('#datatable').on('click', '.selfCollection', function () {
                var row_id = $(this).parents('tr').attr('id');
                var remark = $.trim($('tr#' + row_id).find('td.shipment_remarks textarea').val());
                if(row_id){
                    swal({
                        text: 'Are you sure you want to mark shipment for Self-Collection?',
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
                            blockPagePermanently();
                            $.ajax({
                                url:"{{route('admin.return.marked.self_collection')}}",
                                method:'POST',
                                data:{
                                    'shipment_id':row_id,
                                    'remark':remark,
                                    '_token':'{{ csrf_token() }}',
                                }
                            }).done(function (data) {
                                UnblockPagePermanently();
                                if(data.status == 1){
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }else{
                                    table.draw(false);
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });
                        }
                    });
                }
            });


            $('.decimal').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 100000
            });


            $('#datatable').on('click', '.editEstimateCharges', function () {
                var id =  $(this).parents('tr').attr('id');
                if(id){
                    $('#EditEstimateChargesModal').modal('show');
                    $('#eec_shipment_id').val(id);
                }

            });
            $('#update_charges_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    // $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update Estimated Charges!',
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
                            var charges = $('#estimated_charges_input').val();
                            var shipment_id = $('#eec_shipment_id').val();
                            $.ajax({
                                url: '{!! route('admin.return.edit.estimated_charges') !!}',
                                method: 'POST',
                                data: {
                                    'charges': charges,
                                    'shipment_id': shipment_id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                UnblockPagePermanently();
                                $('#EditEstimateChargesModal').modal('hide');

                                if(data.status){
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }else{
                                    table.draw(false);
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                $('#estimated_charges_input').val('');
                                $('#eec_shipment_id').val('');
                            });

                        }
                    });
                }
            });


            $('body').on('click', 'button.consignee_info_label', function () {
                var phone = $(this).attr('rel');
                if(phone){
                    $.ajax({
                        url: '{!! route('admin.settings.blacklist.search.consignee') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'phone': phone
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {
                                details = data.details;
                                $('#consignee_information_id').val(details.consignee.id);
                                var html = '<div class="row mb-1">';

                                html += '<div class="col-4">Consignee Name :</div><div class="col-8">'+ details.consignee.name +'</div>';
                                html += '<div class="col-4">Consignee Phone Number 1 :</div><div class="col-8">'+ details.consignee.phone +'</div>';
                                var consignee_phone = '';
                                if(details.consignee.phone2 != null){
                                    consignee_phone = details.consignee.phone2;
                                }
                                html += '<div class="col-4">Consignee Phone Number 2 :</div><div class="col-8">'+ consignee_phone +'</div>';
                                html += '<div class="col-4">Consignee Address :</div><div class="col-8">'+ details.consignee.address +'</div>';
                                html += '<div class="col-4">Consignee City :</div><div class="col-8">'+ details.consignee.city +'</div>';

                                html += '</div>';
                                if ('blacklist' in details) {
                                    html += '<div class="row p-1" style="background-color: '+ details.blacklist.color +'; color:white;">';
                                    html += '<div class="col-12">';
                                    html += '<table class="table table-sm table-bordered mb-0">';
                                    html += '<tbody>';
                                    html += '<tr>';
                                    html += '<td><strong>Total Shipments</strong></td>';
                                    html += '<td><strong>Delivered</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '<td><strong>Undelivered</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '<td><strong>Return Confirmed</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '</tr>';
                                    html += '<tr>';
                                    html += '<td>' + details.blacklist.total_shipments + '</td>';
                                    html += '<td>' + details.blacklist.delivered + '</td>';
                                    html += '<td>' + details.blacklist.delivered_ratio + ' %</td>';
                                    html += '<td>' + details.blacklist.undelivered + '</td>';
                                    html += '<td>' + details.blacklist.undelivered_ratio + ' %</td>';
                                    html += '<td>' + details.blacklist.return + '</td>';
                                    html += '<td>Rs. ' + details.blacklist.return_ratio + ' %</td>';
                                    html += '</tr>';
                                    html += '</tbody>';
                                    html += '</table>';
                                    html += '</div></div>';
                                }

                                $('#consignee_info_div').html(html);

                                $('#label_select').val('').trigger('change');
                                $('#ConsigneeInformationModal').modal('show');

                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                $(form).find('button.search').prop('disabled', false);

                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
            });
            $('#label_update_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Category is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
       

      
        });
    </script>
@endsection