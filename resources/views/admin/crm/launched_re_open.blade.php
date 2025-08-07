@extends('admin.layout.master')

@section('title', 'Launched/Re-Open Requests')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <h1 class="mb-1">
                        Launched/Re-Open Requests
                    </h1>

                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('admin.inc.messages')

                                <div class="col mt-2">
                                    <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                                        <div class="form-group">
                                            <input type="text" name="tracking_numbers" class="dt_search tracking_numbers"
                                                   placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                                  <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                  </span>
                                                </div>
                                                <input type="text" name="request_date"
                                                       class="form-control bg-primary border-primary white rounded-right"
                                                       id="request_date" placeholder="By Cut-Off Date">
                                            </div>
                                        </div>
                                        <div class="form-group justify-content-center">
                                            <button id="datatable_filter_btn" type="submit" class="ml-1 btn btn-outline-primary btn-min-width"><i
                                                        class="la la-search"></i> Search
                                            </button>
                                        </div>
                                    </form>
                                </div>


                                <div class="col justify-content-end">
                                    <div class="card-header">
                                        <div class="heading-elements">
                                            <ul class="list-inline" style="margin-top: -10px">
                                                <li class="primary border-primary round" value="0" id="star_shippers_filter"><a>
                                                        Star Shippers</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>


                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <b><label class="ml-1" id="count"></label></b>
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Request No.</th>
                                        <th class="border-primary border-darken-1">Tracking No.</th>
                                        <th class="border-primary border-darken-1">Shipper Name</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Hub</th>
                                        <th class="border-primary border-darken-1">Zone</th>
                                        <th class="border-primary border-darken-1">Arrival Date</th>
                                        <th class="border-primary border-darken-1">Arrival to Today (TAT)</th>
                                        <th class="border-primary border-darken-1">Shipment Status</th>
                                        <th class="border-primary border-darken-1">Last Status Date</th>
                                        <th class="border-primary border-darken-1">Last status to Today (TAT)</th>
                                        <th class="border-primary border-darken-1">Last status by</th>
                                        <th class="border-primary border-darken-1">Case Nature</th>
                                        <th class="border-primary border-darken-1">Case Nature Type</th>
                                        <th class="border-primary border-darken-1">Description</th>
                                        <th class="border-primary border-darken-1">Launched Date</th>
                                        <th class="border-primary border-darken-1">Aging (From Launched Date To Today)</th>
                                        <th class="border-primary border-darken-1">Responsible Hub</th>
                                        <th class="border-primary border-darken-1">Sub Hub</th>
                                        <th class="border-primary border-darken-1">Responsible Zone</th>
                                        <th class="border-primary border-darken-1">Agent</th>
                                        <th class="border-primary border-darken-1">Agent Assigned By</th>
                                        <th class="border-primary border-darken-1">Parcel Value</th>
                                        <th class="border-primary border-darken-1">Claim Amount</th>
                                        <th class="border-primary border-darken-1">COD Value</th>
                                        <th class="border-primary border-darken-1">Segment</th>
                                        <th class="border-primary border-darken-1">Weight</th>
                                        <th class="border-primary border-darken-1">Salesperson</th>
                                        <th class="border-primary border-darken-1">Key account category</th>
                                        <th class="border-primary border-darken-1">KAE</th>
                                        <th class="border-primary border-darken-1">Launched By</th>
                                        <th class="border-primary border-darken-1">Launched By Type</th>
                                        <th class="border-primary border-darken-1">Auto Tagged to Operation</th>
                                        <th class="border-primary border-darken-1">Manual Tagged To</th>
                                        <th class="border-primary border-darken-1">Tagged (Admin/Department)</th>
                                        <th class="border-primary border-darken-1">Last Comment By</th>
                                        <th class="border-primary border-darken-1">Last Comment</th>

                                        <th class="border-primary border-darken-1"></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                            <option value="{{ $agent->id }}" >
                                {{ $agent->trax_id ? $agent->trax_id . ' - ' : '' }}
                                {{ $agent->name ? $agent->name . ' - ' : '' }}
                                {{ $agent->department }}
                            </option>
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
    <div class="modal fade text-left" id="BulkExternalCommentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="BulkExternalCommentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add External Comment </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="bulk_comment_form" class="form-horizontal" method="POST" novalidate="novalidate">
                        <input type="text" value="0" name="bulk_comment_type" id="bulk_comment_type" hidden>
                        <div class="col">
                            <div class="form-group">
                                <textarea class="form-control" rows="5" id="bulk_comment" placeholder="Add External Comment"></textarea>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-success" id="bulkcommentSubmit">Save</button>
                                <button type="button" class="btn btn-info closebutton" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="InternalCommentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="InternalCommentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Internal Comment </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="internal_comment_form" class="form-horizontal" method="POST" novalidate="novalidate">
                        <div class="col">
                            <div class="form-group">
                                <input type="text" value="1" name="internal_comment_type" id="internal_comment_type" hidden>
                                <textarea class="form-control" rows="5" id="internal_comment" placeholder="Add Internal Comment"></textarea>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-success" id="internalcommentSubmit">Save</button>
                                <button type="button" class="btn btn-info closebutton" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--        --}}

    {{--<div class="modal fade text-left" id="UpdateRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="UpdateRequestModal"--}}
    {{--aria-hidden="true">--}}
    {{--<div class="modal-dialog modal-lg" role="document">--}}
    {{--<div class="modal-content">--}}
    {{--<div class="modal-header bg-primary white">--}}
    {{--<h4 class="modal-title white">Update Request</h4>--}}
    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
    {{--<span aria-hidden="true">&times;</span>--}}
    {{--</button>--}}
    {{--</div>--}}
    {{--<div class="modal-body text-center">--}}
    {{--<form id="update_request_form" method="post">--}}
    {{--@method('POST')--}}
    {{--@csrf--}}
    {{--<div class="container">--}}
    {{--<div class="row">--}}
    {{--<h3 class="heading">Tracking Number</h3>--}}
    {{--</div>--}}
    {{--<input type="hidden" id="update_request_id_selected">--}}
    {{--<div class="row old_scroll justify-content-center" id="requested_shipments">--}}
    {{--<h3 id="requested_shipment_tracking" class="text-center font-weight-bold"></h3>--}}
    {{--</div>--}}
    {{--<hr>--}}
    {{--<div class="row justify-content-center">--}}
    {{--<div class="col-8">--}}
    {{--<fieldset class="form-group">--}}
    {{--<select name="case_nature_select" id="case_nature_select" class="form-control select2">--}}
    {{--@foreach($case_nature as $nature)--}}
    {{--<option value="{{$nature->id}}">{{$nature->name}}</option>--}}
    {{--@endforeach--}}
    {{--</select>--}}
    {{--</fieldset>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="complaints d-none" id="request_complaints">--}}
    {{--<div class="row justify-content-center">--}}
    {{--<div class="col-6">--}}
    {{--<fieldset class="form-group">--}}
    {{--<select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2">--}}
    {{--@foreach($case_nature_complaints as $complaints)--}}
    {{--<option value="{{$complaints->id}}">{{$complaints->type}}</option>--}}
    {{--@endforeach--}}
    {{--</select>--}}
    {{--</fieldset>--}}
    {{--</div>--}}
    {{--<div class="col-6">--}}
    {{--<fieldset class="form-group">--}}
    {{--<select name="complaint_channel" id="complaint_channels" class="form-control select2">--}}
    {{--@foreach($channels as $channel1)--}}
    {{--<option value="{{$channel1->id}}">{{$channel1->channel}}</option>--}}
    {{--@endforeach--}}
    {{--</select>--}}
    {{--</fieldset>--}}
    {{--</div>--}}
    {{--<div class="col-6">--}}
    {{--<fieldset class="form-group">--}}
    {{--<textarea class="form-control info" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..." disabled></textarea>--}}
    {{--</fieldset>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="service d-none" id="request_service">--}}
    {{--<div class="row justify-content-center">--}}
    {{--<div class="col-6">--}}
    {{--<fieldset class="form-group">--}}
    {{--<select name="case_nature_request" id="case_nature_requests" class="form-control select2">--}}
    {{--@foreach($case_nature_service_requests as $service)--}}
    {{--<option value="{{$service->id}}">{{$service->type}}</option>--}}
    {{--@endforeach--}}
    {{--</select>--}}
    {{--</fieldset>--}}
    {{--</div>--}}
    {{--<div class="col-6">--}}
    {{--<fieldset class="form-group">--}}
    {{--<select name="request_channel" id="request_channels" class="form-control select2">--}}
    {{--@foreach($channels as $channel2)--}}
    {{--<option value="{{$channel2->id}}">{{$channel2->channel}}</option>--}}
    {{--@endforeach--}}
    {{--</select>--}}
    {{--</fieldset>--}}
    {{--</div>--}}
    {{--<div class="col-6">--}}
    {{--<fieldset class="form-group">--}}
    {{--<textarea class="form-control info" name="service_description" id="service_description" rows="5" placeholder="Enter Description Here..." disabled></textarea>--}}
    {{--</fieldset>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row justify-content-center">--}}
    {{--<div class="col-3">--}}
    {{--<button id="UpdateRequestBtn" type="submit" class="btn btn-primary btn-block d-none">Update</button>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</form>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    <div class="modal fade text-left" id="CloseReasonModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="CloseReasonModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Who’s at Fault</h4>
                </div>
                <input type="hidden" name="close_reason_crm_ids" id="close_reason_crm_ids" value="0">
                <div class="modal-body">
                    <select name="closed_reason_status" id="closed_reason_status" class="form-control select2">
                        @foreach($closed_reason_statuses as $closed_reason_status)
                            <option value="{{ $closed_reason_status->id }}" > {{ $closed_reason_status->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="closed_reason_submit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <style type="text/css">
        .selectize-control {
            width: 300px !important;
        }

        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = -1;
                    var jsonResult =
                        $.ajax({
                            url: '{{ route('admin.crm.launched_re_open.list') }}',
                            data: params,
                            method : 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (result) {
                                head = [];
                                head.push('S No.');
                                head.push('Request No.');
                                head.push('Tracking No.');
                                head.push('Shipper Name');
                                head.push('Origin');
                                head.push('Destination');
                                head.push('Hub');
                                head.push('Zone');
                                head.push('Arrival Date');
                                head.push('Arrival to Today (TAT)');
                                head.push('Shipment Status');
                                head.push('Last Status Date');
                                head.push('Last status to Today (TAT)');
                                head.push('Last status by');
                                head.push('Case Nature');
                                head.push('Case Nature Type');
                                head.push('Description');
                                head.push('Launched Date');
                                head.push('Aging (From Launched Date To Today)');
                                head.push('Responsible Hub');
                                head.push('Sub Hub');
                                head.push('Responsible Zone');
                                head.push('Agent');
                                head.push('Agent Assigned By');
                                head.push('Parcel Value');
                                head.push('Claim Amount');
                                head.push('COD Value');
                                head.push('Segment');
                                head.push('Weight');
                                head.push('Salesperson');
                                head.push('Key account category');
                                head.push('KAE');
                                head.push('Launched By');
                                head.push('Launched By Type');
                                head.push('Auto Tagged to Operation');
                                head.push('Manual Tagged To');
                                head.push('Tagged (Admin/Department)');
                                head.push('Last Comment By');
                                head.push('Last Comment');


                                $.each(result.data, function(index, values) {
                                    row = [];

                                    row.push(index + 1);
                                    row.push(values.id_padded); // {data: 'id_padded_link', name: 'crm_requests.id'}
                                    row.push(values.tracking_number); // {data: 'tracking_number_hyperlink', name: 's.tracking_number'}
                                    row.push(values.shipper_name); // {data: 'shipper_name', name: 'user.name'}
                                    row.push(values.origin); // {data: 'origin', name: 'oc.name'}
                                    row.push(values.destination); // {data: 'destination', name: 'dc.name'}
                                    row.push(values.hub); // {data: 'hub', name: 'dh.name'}
                                    row.push(values.zone); // {data: 'zone', name: 'zones'}
                                    row.push(values.arrival_date); // {data: 'arrival_date', name: 's.updated_at'}
                                    row.push(values.arrival_today); // {data: 'arrival_today', name: 'arrival_today'}
                                    row.push(values.shipment_status); // {data: 'shipment_status', name: 'shipment_status'}
                                    row.push(values.last_status_date); // {data: 'last_status_date', name: 'crm_requests.updated_at'}
                                    row.push(values.last_status_today); // {data: 'last_status_today', name: 's.updated_at'}
                                    row.push(values.last_status_updated_by); // {data: 'last_status_updated_by', name: 'last_status_upd_by.name'}
                                    row.push(values.case_nature); // {data: 'case_nature', name: 'crcn.id'}
                                    row.push(values.case_nature_type); // {data: 'case_nature_type', name: 'case_nature_type'}
                                    row.push(values.descr); // {data: 'description', name: 'crm_requests.description'}
                                    row.push(values.created); // {data: 'created_at', name: 'crm_requests.created_at'}
                                    row.push(values.current_tat); // {data: 'current_tat', name: 'current_tat'}
                                    row.push(values.responsible_hub); // {data: 'responsible_hub', name: 'responsible_hub'}
                                    row.push(values.sub_hub); // {data: 'sub_hub', name: 'ca.name'}
                                    row.push(values.responsible_zone); // {data: 'responsible_zone', name: 'responsible_zone'}
                                    row.push(values.agent); // {data: 'agent', name: 'ad.name'}
                                    row.push(values.agent_assigned_by); // {data: 'agent_assigned_by', name: 'resby.name'}
                                    row.push(values.parcel_value); // {data: 'parcel_value', name: 'parcel_value'}
                                    row.push(values.product_cost); // {data: 'product_cost', name: 'product_cost'}
                                    row.push(values.cod_value); // {data: 'cod_value', name: 'cod_value'}
                                    row.push(values.segment); // {data: 'segment', name: 'seg.name'}
                                    row.push(values.actual_weight); // {data: 'actual_weight', name: 's.actual_weight'}
                                    row.push(values.sale_person); // {data: 'sale_person', name: 'ad1.name'}
                                    row.push(values.shipper_category); // {data: 'shipper_category', name: 'shipper_category'}
                                    row.push(values.kae); // {data: 'kae', name: 'ad2.name'}
                                    row.push(values.launched_by_name); // {data: 'launched_by_name', name: 'launched_by_name'}
                                    row.push(values.added_by);  // {data: 'added_by'}
                                    row.push(values.tagged_to_operation); // {data: 'tagged_to_operation', name: 'tagged_to_operation'}
                                    row.push(values.manual_tagged_to); // {data: 'tagged_to_manual', name: 'tagged_to_manual'}
                                    row.push(values.tagged); // {data: 'tagged'}
                                    row.push(values.last_comment_name); // {data: 'last_comment_name', name: 'last_comment_name'}
                                    row.push(values.last_comment.replace(/<br>/gi, '\n')); // {data: 'last_comment', name: 'ccs.comment'}
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
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if(session('role_id') == 1 || in_array(201, session('permissions')))
                    {
                        text: 'Bulk Internal Comment',
                        className: 'btn btn-primary bulk_internal_comment',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $('#InternalCommentModal').modal('show');
                        }
                    },
                    {
                        text: 'Bulk External Comment',
                        className: 'btn btn-primary bulk_external_comment',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $('#BulkExternalCommentModal').modal('show');
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(787, session('permissions')))
                    {
                        text: 'Accept Ticket',
                        className: 'btn btn-primary valid',
                        enabled: false,
                        action: function (e, dt, node, config) {

                            mark_valid_invalid(1);

                        }
                    },{
                        text: 'In-Valid',
                        className: 'btn btn-danger in_valid',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $.ajax({
                                url: '{!! route('admin.crm.close_reason') !!}',
                                method: 'POST',
                                data: {
                                    // 'closed_reason_status':closed_reason_status,
                                    'crm_request_ids': selected_rows,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                                .done(function (data) {
                                    if(data.status == 1){
                                        $('#close_reason_crm_ids').val(data.crm_ids);
                                        $('#CloseReasonModal').modal('show');
                                    }else{
                                        mark_valid_invalid(0);
                                    }
                                });
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(179, session('permissions')))
                    {
                        text: 'Assign Agent',
                        className: 'btn btn-primary assign',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $('#AssignAgentModal').modal('show');

                            $('#AssignAgentModal').on('hide.bs.modal', function (e) {
                                $('#assign_agent').val('').trigger('change');
                            });
                            $('#assign_agentSubmit').on('click',function () {
                                var assign = parseInt($('#assign_agent').val());
                                swal({
                                    text: 'Are you sure, you want to Assign these Request(s)?',
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
                                                url: '{!! route('admin.crm.assign') !!}',
                                                method: 'POST',
                                                data: {
                                                    'admin_id': assign,
                                                    'crm_request_ids[]': selected_rows,
                                                    'multiple': 1,
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

                                                    table.rows().deselect();

                                                    table.draw('false');
                                                    table.button('.assign').disable();
                                                    table.button('.valid').disable();
                                                    table.button('.in_valid').disable();
                                                    table.button('.bulk_external_comment').disable();
                                                    table.button('.bulk_internal_comment').disable();
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
                        }
                    },
                        @endif
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
                                    table.button('.valid').enable();
                                    table.button('.in_valid').enable();
                                    table.button('.bulk_external_comment').enable();
                                    table.button('.bulk_internal_comment').enable();
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
                                        table.button('.valid').disable();
                                        table.button('.in_valid').disable();
                                        table.button('.bulk_external_comment').disable();
                                        table.button('.bulk_internal_comment').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'CRM Request (Launched/Re-Open)',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                paging: true,
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax: {
                    url: '{{ route('admin.crm.launched_re_open.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.request_date = $('input[name="request_date_formatted"]').val()
                        d.star_shipper_filter = $('#star_shippers_filter').val();
                    }
                },
                rowId: 'id',
                order: [[21, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id_padded_link', name: 'crm_requests.id', class: 'align-middle id_padded_link'},
                    {data: 'tracking_number_hyperlink', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper_name', name: 'user.name', class: 'align-middle shipper_name'}, // Shipper Name
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'}, // Origin
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'}, // Destination
                    {data: 'hub', name: 'dh.name', class: 'align-middle hub'}, // Hub
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'}, // Zone
                    {data: 'arrival_date', name: 's.updated_at', class: 'align-middle arrival_date'}, // Arrival Date
                    {data: 'arrival_today', name: 'sj.updated_at', class: 'align-middle arrival_today'}, // Arrival to Today (TAT)
                    {data: 'shipment_status', name: 'shipment_status', class: 'align-middle shipment_status'}, // Shipment Status
                    {data: 'last_status_date', name: 'crm_requests.updated_at', class: 'align-middle last_status_date'}, // Last Status Date
                    {data: 'last_status_today', name: 's.updated_at', class: 'align-middle last_status_today', orderable: false}, // Last status to Today (TAT)
                    {data: 'last_status_updated_by', name: 'last_status_upd_by.name', class: 'align-middle last_status_updated_by', orderable: false}, // Last status by
                    {data: 'case_nature', name: 'crcn.id', class: 'align-middle case_nature'}, // Case Nature
                    {data: 'case_nature_type', name: 'case_nature_type', class: 'align-middle case_nature_type'}, // Case Nature Type
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'}, // Description
                    {data: 'created', name: 'crm_requests.created_at', class: 'align-middle created_at'}, // Launched Date
                    {data: 'current_tat', name: 'current_tat', class: 'align-middle current_tat', orderable: false, searchable: false}, // Aging (From Launched Date To Today)
                    {data: 'responsible_hub', name: 'responsible_hub', class: 'align-middle responsible_hub'}, // Responsible Hub
                    {data: 'sub_hub', name: 'ca.name', class: 'align-middle sub_hub', orderable: false}, // Sub Hub
                    {data: 'responsible_zone', name: 'responsible_zone', class: 'align-middle responsible_zone'}, // Responsible Zone
                    {data: 'agent', name: 'ad.name', class: 'align-middle agent'}, // Agent
                    {data: 'agent_assigned_by', name: 'resby.name', class: 'align-middle agent_assigned_by'}, // Agent Assigned By
                    {data: 'parcel_value', name: 's.parcel_value', class: 'align-middle parcel_value'}, // Parcel Value
                    {data: 'product_cost', name: 'crm_requests.product_cost', class: 'align-middle product_cost'}, // Claim Amount
                    {data: 'cod_value', name: 's.amount', class: 'align-middle cod_value'}, // COD Value
                    {data: 'segment', name: 'seg.name', class: 'align-middle segment'}, // Segment
                    {data: 'actual_weight', name: 's.actual_weight', class: 'align-middle actual_weight'}, // Weight
                    {data: 'sale_person', name: 'ad1.name', class: 'align-middle sale_person'}, // Salesperson
                    {data: 'shipper_category', name: 'shipper_category', class: 'align-middle shipper_category'}, // Key account category
                    {data: 'kae', name: 'ad2.name', class: 'align-middle kae'}, // KAE
                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle name'}, // Launched By
                    {data: 'added_by', name: 'crm_requests.launched_by', class: 'align-middle added_by'}, // Launched By Type
                    {data: 'tagged_to_operation', name: 'tagged_to_operation', class: 'align-middle tagged_to_operation'},
                    {data: 'tagged_to_manual', name: 'tagged_to_manual', class: 'align-middle tagged_to_manual'}, // Manual Tagged To
                    {data: 'tagged', name: 'crt.crm_request_tagging_type_id', class: 'align-middle tagged'},
                    {data: 'last_comment_name', name: 'last_comment_name', class: 'align-middle last_comment_name'}, // Last Comment By
                    {data: 'last_comment', name: 'ccs.comment', class: 'align-middle last_comment'}, // Last Comment
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}


                ],
                rowCallback: function(row, data, index) {
                    $('td:eq(0)', row).addClass('select-checkbox');

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }

                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var case_nature = '<select name="case_nature" id="case_nature" class="select2 form-control"></select>';
                    var shipment_status = '<select name="shipment_status" id="shipment_status" class="select2 form-control"></select>';
                    var status = '<select name="status" id="status" class="select2 form-control"></select>';
                    var channel = '<select name="channel" id="channel" class="select2 form-control"></select>';
                    var case_nature_type = '<select name="case_nature_type" id="case_nature_type" class="select2 form-control"></select>';
                    var zones = '<select name="zones" id="zones" class="select2 form-control"></select>';
                    var added_by = '<select name="launched_by" id="added_by" class="select2 form-control">' +
                        '<option value="0">Admin</option>' +
                        '<option value="1">Shipper</option>' +
                        '<option value="2">Shipper Substitute User</option>' +
                        '<option value="3">Retail</option>' +
                        '<option value="4">Consignee</option>' +
                        // '<option value="4">External</option>' +
                        '<option value="5">Retail App</option>'+
                        '</select>';

                    var tagging_type = '<select name="tagging_type" id="tagging_type" class="select2 form-control">' +
                        '<option value="1">Department</option>' +
                        '<option value="2">Admin</option>' +
                        '</select>';

                    var kac = '<select name="key_account" id="key_account" class="select2 form-control">' +
                        '<option value="0">Non-Key Account</option>' +
                        '<option value="1">Key Account</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select') || $(header).is('.current_tat')|| $(header).is('.responsible_hub')|| $(header).is('.responsible_zone')  || $(header).is('.last_status_today') || $(header).is('.arrival_today')) {
                            $(td).appendTo($(search));

                        }
                        else if ($(header).is('.status')) {
                            $(status).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.zone')) {
                            $(zones).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.shipment_status')) {
                            $(shipment_status).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.case_nature')) {
                            $(case_nature).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.case_nature_type')) {
                            $(case_nature_type).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.channel')) {
                            $(channel).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.added_by')) {
                            $(added_by).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.tagged')) {
                            $(tagging_type).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }

                        else if ($(header).is('.shipper_category')) {
                            $(kac).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
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

                    var data = $.map({!! $status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });

                    var data = $.map({!! $status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#status').prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data2 = $.map({!! $channels !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });

                    var data2 = $.map({!! $channels !!}, function (obj) {
                        obj.text = obj.channel;

                        return obj;
                    });

                    $('#channel').prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Channel",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data2 = $.map({!! $case_nature !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });

                    var data2 = $.map({!! $case_nature !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#case_nature').prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Case Nature",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data3 = $.map({!! $case_nature_type !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });

                    var data3 = $.map({!! $case_nature_type !!}, function (obj) {
                        obj.text = obj.type;

                        return obj;
                    });

                    $('#case_nature_type').prepend('<option value="" selected></option>').select2({
                        data:data3,
                        placeholder: "Select Case Nature Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data4 = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });

                    var data4 = $.map({!! $shipment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#shipment_status').prepend('<option value="" selected></option>').select2({
                        data:data4,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data5 = $.map({!! $zones !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#zones').prepend('<option value="" selected></option>').select2({
                        data:data5,
                        placeholder: "Select Zone",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $('#added_by').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Launched By Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $('#key_account').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $('#tagging_type').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Tagged Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });
            table.on( 'select', function ( e, dt, type, indexes ) {
                var count = table.rows( { selected: true } ).count();
                var lblcount= document.getElementById('count');
                lblcount.textContent =count +' Row(s) selected';
            } );
            table.on( 'deselect', function ( e, dt, type, indexes ) {
                var count = table.rows( { selected: true } ).count();
                var lblcount= document.getElementById('count');
                lblcount.textContent =count +' Row(s) selected';
            } );
            $('.closebutton').on('click',function(){
                $("#BulkExternalCommentModal").on("hidden.bs.modal", function() {
                    $("#BulkExternalCommentModal #bulk_comment").val("");
                });
                $("#InternalCommentModal").on("hidden.bs.modal", function() {
                    $("#InternalCommentModal #internal_comment").val("");
                });

            });
            $('#closed_reason_submit').on('click',function () {
                mark_valid_invalid(0);
            });

            $('#bulkcommentSubmit').on('click',function () {
                var comment = $('#BulkExternalCommentModal #bulk_comment').val();
                var comment_type = $('#BulkExternalCommentModal #bulk_comment_type').val();
                if (comment) {
                    $.ajax({
                        url: '{!! route('admin.crm.comment.bulk') !!}',
                        method: 'POST',
                        data: {
                            'comment_type':comment_type,
                            'comment': comment,
                            'crm_request_ids': selected_rows,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if (data.status == 1) {
                                $('#BulkExternalCommentModal').modal('hide');
                                $("#BulkExternalCommentModal").on("hidden.bs.modal", function() {
                                    $("#BulkExternalCommentModal #bulk_comment").val("");
                                });
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
                            table.rows().deselect();
                            $('#bulk_comment').val('').trigger('change');
                            $('#BulkExternalCommentModal').modal('hide');
                            table.draw('false');
                            table.button('.assign').disable();
                            table.button('.valid').disable();
                            table.button('.in_valid').disable();
                            table.button('.bulk_external_comment').disable();
                            table.button('.bulk_internal_comment').disable();
                        });
                }
                else {
                    var error = "Add Comment First!";
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
            });
            $('#internalcommentSubmit').on('click',function () {
                var comment = $('#InternalCommentModal #internal_comment').val();
                var comment_type = $('#InternalCommentModal #internal_comment_type').val();
                if (comment) {
                    $.ajax({
                        url: '{!! route('admin.crm.comment.bulk') !!}',
                        method: 'POST',
                        data: {
                            'comment_type':comment_type,
                            'comment': comment,
                            'crm_request_ids': selected_rows,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if (data.status == 1) {
                                $('#InternalCommentModal').modal('hide');
                                $("#InternalCommentModal").on("hidden.bs.modal", function() {
                                    $("#InternalCommentModal #internal_comment").val("");
                                });
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
                            table.rows().deselect();
                            $('#internal_comment').val('').trigger('change');
                            $('#InternalCommentModal').modal('hide');
                            table.draw('false');
                            table.button('.assign').disable();
                            table.button('.valid').disable();
                            table.button('.in_valid').disable();
                            table.button('.bulk_external_comment').disable();
                            table.button('.bulk_internal_comment').disable();
                        });
                } else {
                    var error = "Add Internal Comment First!";
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
            });

            $("#assign_agent").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Agent",
                width:'100%',
                dropdownParent:$('#AssignAgentModal')
            });
            $("#closed_reason_status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Reason",
                width:'100%',
                dropdownParent:$('#CloseReasonModal')
            });
            $('#CloseReasonModal').on('hide.bs.modal', function (e) {
                $('#closed_reason_status').val('').trigger('change');
                $('#close_reason_crm_ids').val('');
            });

            //Selectize
            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
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
                    table.button('.valid').enable();
                    table.button('.in_valid').enable();
                    table.button('.bulk_external_comment').enable();
                    table.button('.bulk_internal_comment').enable();
                }
                else {
                    table.button('.assign').disable();
                    table.button('.valid').disable();
                    table.button('.in_valid').disable();
                    table.button('.bulk_external_comment').disable();
                    table.button('.bulk_internal_comment').disable();
                }
            });

            {{--$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.assign', function() {--}}
            {{--$('#AssignAgentModal').modal('show');--}}
            {{--var crm_request_id = parseInt($(this).parents('tr').attr('id'));--}}

            {{--$('#AssignAgentModal').on('shown.bs.modal',function (e) {--}}
            {{--});--}}
            {{--$('#AssignAgentModal').on('hide.bs.modal', function (e) {--}}
            {{--$('#assign_agent').val('').trigger('change');--}}
            {{--});--}}
            {{--$('#assign_agentSubmit').on('click',function () {--}}
            {{--var assign = parseInt($('#assign_agent').val());--}}
            {{--if(assign){--}}
            {{--$.ajax({--}}
            {{--url: '{!! route('admin.crm.assign') !!}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--'admin_id': assign,--}}
            {{--'crm_request_id':crm_request_id,--}}
            {{--'multiple': 0,--}}
            {{--'_token': '{{ csrf_token() }}'--}}
            {{--}--}}
            {{--})--}}
            {{--.done(function(data) {--}}
            {{--if(data.status == 0){--}}
            {{--$('#AssignAgentModal').modal('hide');--}}
            {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
            {{--}--}}
            {{--else {--}}
            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--$('#assign_agent').val('').trigger('change');--}}
            {{--table.draw('false');--}}
            {{--});--}}
            {{--}else{--}}
            {{--var error = "Agent Not Selected!";--}}
            {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}

            {{--});--}}
            {{--});--}}


            $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                dropdownParent:$('#update_request_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#request_service').addClass('d-none');
                    $('#request_complaints').removeClass('d-none');
                    $('#UpdateRequestBtn').removeClass('d-none');
                }else if(id === 2){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').removeClass('d-none');
                    $('#UpdateRequestBtn').removeClass('d-none');

                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#UpdateRequestBtn').addClass('d-none');

                }
            });
             $('#request_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                // onOpen: function() {
                //     $('#booking_from_date_root').css('top','40px');
                // },
                // onSet: function(context) {
                //     if (context.select) {
                //         $('#track_form #booking_to_date').pickadate('picker').set('min', $('#track_form #booking_from_date').pickadate('picker').get('select'));
                //     }
                // }
            });
            $('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#update_request_form')
            });
            $('#complaint_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#update_request_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Request Type",
                allowClear:true,
                dropdownParent:$('#update_request_form')
            });
            $('#request_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#update_request_form')
            });

            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                table.draw();
            });


            {{--$('body').on('click', '.dropdown-item.update_request', function () {--}}
            {{--var nature = parseInt($(this).parents('tr').attr('nature'));--}}
            {{--var request_id = parseInt($(this).parents('tr').attr('id'));--}}
            {{--if(nature == 1 || nature == 2){--}}
            {{--$('#UpdateRequestModal').modal('show');--}}
            {{--$('#update_request_id_selected').val(request_id);--}}
            {{--$.ajax({--}}
            {{--url: '{!! route('admin.crm.request.get_request') !!}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--'request_id': request_id,--}}
            {{--'_token': '{{ csrf_token() }}'--}}
            {{--}--}}
            {{--}).done(function (data) {--}}
            {{--if(data.status){--}}
            {{--if(data.details.case_nature_id == 1){--}}
            {{--$('#case_nature_select').val(data.details.case_nature_id).trigger('change');--}}
            {{--$('#case_nature_complaints').val(data.details.case_nature_type_id).trigger('change');--}}
            {{--$('#complaint_channels').val(data.details.channel_id).trigger('change');--}}
            {{--$('#complaint_description').val(data.details.description);--}}

            {{--$('#requested_shipment_tracking').text(data.tracking_number);--}}
            {{--}else if(data.details.case_nature_id == 2){--}}
            {{--$('#case_nature_select').val(data.details.case_nature_id).trigger('change');--}}
            {{--$('#case_nature_requests').val(data.details.case_nature_type_id).trigger('change');--}}
            {{--$('#request_channels').val(data.details.channel_id).trigger('change');--}}
            {{--$('#service_description').val(data.details.description);--}}

            {{--$('#requested_shipment_tracking').text(data.tracking_number);--}}
            {{--}--}}
            {{--}else{--}}
            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--});--}}
            {{--}--}}
            {{--});--}}

            {{--$( "#update_request_form" ).bind('submit', function (e) {--}}
            {{--e.preventDefault();--}}
            {{--var case_nature_id = parseInt($('#case_nature_select').val());--}}
            {{--var request_id = $('#update_request_id_selected').val();--}}
            {{--if(case_nature_id === 1){--}}
            {{--var nature_flag = true;--}}
            {{--var case_nature_complaint_id = $('#case_nature_complaints').val();--}}
            {{--var case_nature_channel_id = $('#complaint_channels').val();--}}

            {{--if(!case_nature_complaint_id){--}}
            {{--nature_flag = false;--}}
            {{--var error = "Please select Complaint type!";--}}
            {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--if(!case_nature_channel_id){--}}
            {{--nature_flag = false;--}}
            {{--var error = "Please select Channel!";--}}
            {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--if(nature_flag){--}}
            {{--$.ajax({--}}
            {{--url: '{!! route('admin.crm.request.update') !!}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--'_token': '{{ csrf_token() }}',--}}
            {{--'request_id' : request_id,--}}
            {{--'case_nature_id' : case_nature_id,--}}
            {{--'complaint_id' : case_nature_complaint_id,--}}
            {{--'channel_id': case_nature_channel_id--}}
            {{--}--}}
            {{--})--}}
            {{--.done(function(data) {--}}
            {{--if (data.status) {--}}
            {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
            {{--}--}}
            {{--else {--}}
            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--table.draw('false');--}}

            {{--$('#UpdateRequestModal').modal('hide');--}}
            {{--});--}}
            {{--}--}}

            {{--}else if(case_nature_id == 2){--}}
            {{--var nature_flag = true;--}}
            {{--var case_nature_complaint_id = $('#case_nature_requests').val();--}}
            {{--var case_nature_channel_id = $('#request_channels').val();--}}
            {{--if(!case_nature_complaint_id){--}}
            {{--nature_flag = false;--}}
            {{--var error = "Please select Complaint type!";--}}
            {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--if(!case_nature_channel_id){--}}
            {{--nature_flag = false;--}}
            {{--var error = "Please select Channel!";--}}
            {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}

            {{--if(nature_flag){--}}
            {{--$.ajax({--}}
            {{--url: '{!! route('admin.crm.request.update') !!}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--'_token': '{{ csrf_token() }}',--}}
            {{--'request_id' : request_id,--}}
            {{--'case_nature_id' : case_nature_id,--}}
            {{--'complaint_id' : case_nature_complaint_id,--}}
            {{--'channel_id': case_nature_channel_id--}}
            {{--}--}}
            {{--})--}}
            {{--.done(function(data) {--}}
            {{--if (data.status) {--}}
            {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
            {{--}--}}
            {{--else {--}}
            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--table.draw('false');--}}
            {{--$('#UpdateRequestModal').modal('hide');--}}
            {{--});--}}
            {{--}--}}
            {{--}else{--}}
            {{--var error = "Please select case nature!";--}}
            {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--});--}}
            function mark_valid_invalid(valid){

                if(valid == 0){
                    valid_text = 'Invalid';
                }else{
                    valid_text = 'Valid';

                }

                var closed_reason_status = $('#closed_reason_status').val();
                var close_reason_crm_ids = $('#close_reason_crm_ids').val();
                swal({
                    text: 'Are you sure, you want to Mark these Request(s) '+valid_text+'?',
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
                        $.ajax({
                            url: '{!! route('admin.crm.bulk_valid_invalid') !!}',
                            method: 'POST',
                            data: {
                                'crm_request_ids[]': selected_rows,
                                'closed_reason_status': closed_reason_status,
                                'close_reason_crm_ids': close_reason_crm_ids,
                                'valid': valid,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 1) {
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

                                table.rows().deselect();

                                table.draw();
                                $('#CloseReasonModal').modal('hide');

                            });
                    }
                });
            }


            $('#star_shippers_filter').on('click',function () {
                $('#star_shippers_filter').val(1);
                table.draw(true);
                $('#star_shippers_filter').val(0);
            });
        });
    </script>
@endsection