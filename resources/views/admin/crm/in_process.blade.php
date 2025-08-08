@extends('admin.layout.master')

@section('title', 'In-Process Requests')

@section('content')
    <section>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    In-Process Requests
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="form-group">
                                    <input type="text" name="tracking_numbers" class="dt_search tracking_numbers"
                                           placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">
                                </div>
                                <div class="form-group justify-content-center">
                                    <button id="datatable_filter_btn" type="submit" class="ml-1 btn btn-outline-primary btn-min-width"><i
                                                class="la la-search"></i> Search
                                    </button>
                                </div>
                            </form>

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
                                    <th class="border-primary border-darken-1">Last Comment Date</th>
                                    <th class="border-primary border-darken-1">Last Rider</th>
                                    <th class="border-primary border-darken-1">Last Reason</th>
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
                                <button type="button" class="btn btn-success" id="bulkcommentSubmit">Add External Comment</button>
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
                                <button type="button" class="btn btn-success" id="internalcommentSubmit">Add Internal Comment</button>
                                <button type="button" class="btn btn-info closebutton" data-dismiss="modal">Close</button>
                            </div>
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
    <div class="modal fade text-left" id="tagModal" data-backdrop="static" tabindex="-1" role="dialog"
             aria-labelledby="tagModal"
             aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h4 class="modal-title">Tag</h4>
                    </div>
                    <div class="modal-body text-center">
                        <form id="tag_submit_form" method="post">
                            @method('POST')
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-11">
                                    <fieldset class="form-group d-none">
                                        <input type="hidden" id="crm_request_ids" value="">
                                        <input type="hidden" id="prev_status" name="prev_status"
                                               value="">
                                        <select name="tag_type" id="tag_type" class="form-control select2">
                                            @foreach($types as $type)
                                                <option value="{{$type->id}}"> {{$type->name}} </option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <div class="d-none" id="admin_tag_div">
                                            <div class="">
                                                <select id="admin_tag_department"
                                                        class="form-control  select2">
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}"> {{$department->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mt-1">
                                                <select id="admin_tag_hub"
                                                        class="form-control select2">
                                                    @foreach($hubs as $hub)
                                                        <option value="{{$hub->id}}"> {{$hub->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mt-1">
                                                <select name="tag_admin" id="tag_admin" class="form-control select2">

                                                </select>
                                            </div>
                                        </div>

                                        <div class="d-none" id="department_tag_div">
                                            <div class="">
                                                <select name="tag_department" id="tag_department"
                                                        class="form-control  select2">
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}"> {{$department->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mt-1">
                                                <select name="tag_hub" id="tag_hub"
                                                        class="form-control select2">
                                                    @foreach($hubs as $hub)
                                                        <option value="{{$hub->id}}"> {{$hub->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success width-25-per" id="tag_adminSubmit">Tag</button>
                        <button type="button" class="btn btn-info width-25-per" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    <div class="modal fade" id="ViewRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Special Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">

                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button class="btn btn-grey" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>   
        <div class="modal fade text-left" id="CloseReasonModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="CloseReasonModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Who’s at Fault</h4>
                </div>
                <input type="hidden" name="close_reason_type" id="close_reason_type" value="">
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


            <div class="modal fade text-left" id="CloseReasonModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="CloseReasonModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Who’s at Fault</h4>
                </div>
                <input type="hidden" name="close_reason_type" id="close_reason_type" value="">
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


<div class="modal fade text-left" id="claimInvalidModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="claimInvalidModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Claim Invalid Reason</h4>
            </div>

            <div class="modal-body">
                <select name="claim_invalid_reasons[]" id="claim_invalid_reasons" class="form-control select2" multiple>
                    @foreach($invalid_reasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                    @endforeach
                </select>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="invalid_submit">Submit</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

 <div class="modal fade text-left" id="claimResolvedModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="claimResolvedModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Claim Resolved Reason</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="claim_resolved_reason">Select Reason</label>
                        <select name="claim_resolved_reason" id="claim_resolved_reason" class="form-control select2">
                            <option value="" disabled selected> Select a reason </option>
                            @foreach($resolved_reasons as $reason)
                                <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-3" id="sub_reason_wrapper" style="display: none;">
                        <label for="claim_resolved_sub_reasons">Select Sub Reason(s)</label>
                        <select name="claim_resolved_sub_reasons[]" id="claim_resolved_sub_reasons" class="form-control select2" multiple>
                            @foreach($resolved_sub_reasons as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="resolved_submit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
</div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    
    <style type="text/css">
        .selectize-control {
            width: 500px !important;
        }

        .select-checkbox{
            border-color: #64a0d2;
        }

        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
        tr.highalert_row{
            background-color: #ff6326;
            color: whitesmoke;
        }
        tr.highalert_row a{
            color: whitesmoke;
        }
        .select2-search__field{
            width: 140px !important;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 48px !important;      
            max-height: 100px !important;   
            overflow-y: auto !important;   
            padding-bottom: 5px;
        }

    </style>
@endsection

@section('js')
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
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.crm.in_process.list') }}',
                        method:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
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
                            head.push('Arrival to Today (TAT)'); // Newly added based on your list
                            head.push('Shipment Status');
                            head.push('Last Status Date');       // Newly added based on your list
                            head.push('Last status to Today (TAT)'); // Newly added based on your list
                            head.push('Last status by');         // Newly added based on your list
                            head.push('Case Nature');
                            head.push('Case Nature Type');
                            head.push('Description');
                            head.push('Launched Date');
                            head.push('Aging (From Launched Date To Today)'); // Newly added based on your list
                            head.push('Responsible Hub');
                            head.push('Sub Hub');                // Newly added based on your list
                            head.push('Responsible Zone');
                            head.push('Agent');
                            head.push('Agent Assigned By');
                            head.push('Parcel Value');           // Newly added based on your list
                            head.push('Claim Amount');           // Newly added based on your list
                            head.push('COD Value');              // Newly added based on your list
                            head.push('Segment');                // Newly added based on your list
                            head.push('Weight');                 // Newly added based on your list
                            head.push('Salesperson');            // Newly added based on your list
                            head.push('Key account category');   // Newly added based on your list
                            head.push('KAE');
                            head.push('Launched By');
                            head.push('Launched By Type');
                            head.push('Auto Tagged to Operation');
                            head.push('Manual Tagged To');
                            head.push('Tagged (Admin/Department)');
                            head.push('Last Comment By');
                            head.push('Last Comment');
                            head.push('Last Comment Date');
                            head.push('Last Rider');
                            head.push('Last Reason');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id_padded);
                                row.push(values.tracking_number);                     // Tracking No.
                                row.push(values.shipper_name);                                 // Shipper Name
                                row.push(values.origin);                                      // Origin
                                row.push(values.destination);                                 // Destination
                                row.push(values.hub);                                         // Hub
                                row.push(values.zone);                                        // Zone
                                row.push(values.arrival);                                     // Arrival Date
                                row.push(values.arrival_today);                                // Arrival to Today (TAT)
                                row.push(values.status);                                      // Shipment Status
                                // row.push(values.last_status_date);                            // Last Status Date
                                row.push(values.last_date_status);                            // Last Status Date
                                row.push(values.last_status_today);                            // Last status to Today (TAT)
                                row.push(values.last_status_updated_by);                      // Last status by
                                row.push(values.case_nature);                                 // Case Nature
                                row.push(values.case_nature_type);                            // Case Nature Type
                                row.push(values.description);                                 // Description
                                row.push(values.created);                                  // Launched Date
                                row.push(values.current_tat);                                 // Aging (From Launched Date To Today)
                                row.push(values.responsible_hub);                             // Responsible Hub
                                row.push(values.sub_hub);                                     // Sub Hub
                                row.push(values.responsible_zone);                            // Responsible Zone
                                row.push(values.agent);                                       // Agent
                                row.push(values.agent_assigned_by);                           // Agent Assigned By
                                row.push(values.parcel_value);                                // Parcel Value
                                row.push(values.product_cost);                                // Claim Amount
                                row.push(values.cod_value);                                   // COD Value
                                row.push(values.segment);                                    // Segment
                                row.push(values.actual_weight);                               // Weight
                                row.push(values.sale_person);                                 // Salesperson
                                row.push(values.shipper_category);                            // Key account category
                                row.push(values.kae);                                        // KAE
                                row.push(values.launched_by_name);                            // Launched By
                                row.push(values.added_by);                                 // Launched By Type
                                row.push(values.tagged_to_operation);                         // Tagged To Operation
                                row.push(values.tagged_to_manual);                            // Manual Tagged To
                                row.push(values.tagged);                                    // Tagged (Admin/Department)
                                row.push(values.last_comment_name);                           // Last Comment By
                                row.push(values.last_comment.replace(/<br>/gi, '\n'));       // Last Comment
                                row.push(values.last_comment_date);                           // Last Comment Date
                                row.push(values.last_updated_rider);                                  // Last Rider
                                row.push(values.last_rider_reason);                                 // Last Reason

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

                    // bulk resolve button
                    @if (session('role_id') == 1 || in_array(1013, session('permissions')))
                    {
                        text: 'Resolve',
                        className: 'btn btn-primary bulk_resolve',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $.ajax({
                                url: '{{ route("admin.crm.bulk_resolve") }}',
                                method: 'POST',
                                data: {
                                    crm_request_ids: selected_rows,
                                    inprocess: true,
                                    _token: '{{ csrf_token() }}'
                                }
                            })
                            .done(function (data) {
                                if (data.status === 1 && data.errors && Array.isArray(data.errors)) {
                                    data.errors.forEach(function (error) {
                                        if (Array.isArray(error) && error.length > 0) {
                                            toastr.error(error[0], 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center',
                                            });
                                        } else if (typeof error === 'string') {
                                            toastr.error(error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center',
                                            });
                                        }
                                    });
                                }else if (data.status === 2) {
                                    swal({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Kindly select all claim',
                                        confirmButtonColor: '#d33'
                                    });
                                } else if (data.status === 3) {
                                     const fakeForm = $('<form>', {
                                        method: 'POST',
                                        action: '{{ route("admin.crm.bulk_resolve") }}'
                                    });

                                    fakeForm.append($('<input>', {
                                        type: 'hidden',
                                        name: '_token',
                                        value: '{{ csrf_token() }}'
                                    }));

                                    selected_rows.forEach(function (id) {
                                        fakeForm.append($('<input>', {
                                            type: 'hidden',
                                            name: 'crm_request_ids[]',
                                            value: id
                                        }));
                                    });

                                    $('body').append(fakeForm); 
                                    pendingForm = fakeForm[0]; 
                                    $('#claimResolvedModal').modal('show');
                                } else {
                                    swal({
                                        title: 'Are you sure?',
                                        text: 'Are you sure you want to mark them as resolved?',
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
                                    }).then((result) => {
                                        if (result) {
                                            window.location.href = '{{ route("admin.crm.resolved.index") }}';
                                        }
                                    });
                                }
                            })
                            .fail(function () {
                                toastr.error('Something went wrong. Please try again later.', 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center',
                                });
                            });
                        }
                    },
                    @endif

                    @if (session('role_id') == 1 || session('role_id') == 6 || in_array(787, session('permissions')))
                    {
                        text: 'In-Valid',
                        className: 'btn btn-danger in_valid',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $.ajax({
                                url: '{!! route('admin.crm.close_reason') !!}',
                                method: 'POST',
                                data: {
                                    // 'closed_reason_status':closed_reason_status,
                                    'inprocess': true,
                                    'crm_request_ids': selected_rows,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function (data) {
                                if(data.status == 1){
                                    $('#close_reason_crm_ids').val(data.crm_ids);
                                    $('#close_reason_type').val(1);
                                    $('#CloseReasonModal').modal('show');
                                }else if(data.status == 2){
                                     swal({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Kindly select all claim',
                                        confirmButtonColor: '#d33'
                                    });
                                }else if(data.status == 3){                           
                                    $('#claimInvalidModal').modal('show');
                                } 
                                else{
                                    mark_valid_invalid(0);
                                }
                            });
                        }
                    },
                    @endif
                    
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(309, session('permissions')))
                        {
                        text: 'Tag',
                        className: 'btn btn-primary tag',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                $('#tagModal').modal('show');
                            }
                        }
                    },
                    {
                        text: 'Un Tag',
                        className: 'btn btn-primary un_tag',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                text: 'Are you sure, you want to un tag these Request(s)?',
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
                                        url: '{!! route('admin.crm.in_process.un_tag') !!}',
                                        method: 'POST',
                                        data: {
                                            'crm_request_ids[]': selected_rows,
                                            'multiple': 1,
                                            '_token': '{{ csrf_token() }}'
                                        }
                                    })
                                        .done(function (data) {
                                            if (data.status == 0) {
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
                                            table.rows().nodes().each(function(index) {
                                                var row = table.row(index);

                                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                                    row.deselect();

                                                    id = parseInt(row.id());

                                                    var index = $.inArray(id, selected_rows);

                                                    if (index !== -1) {
                                                        selected_rows.splice(index, 1);
                                                    }
                                                }
                                            });
                                            if (selected_rows.length == 0) {
                                                table.button('.assign').disable();
                                                table.button('.un_tag').disable();
                                                table.button('.close_request').disable();
                                                table.button('.tag').disable();
                                                table.button('.bulk_external_comment').enable();
                                                table.button('.bulk_internal_comment').enable();

                                            }
                                            table.draw('false');
                                        });
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
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(787, session('permissions')))
                    {
                        text: 'Close Requests',
                        className: 'btn btn-danger close_request',
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
                                    $('#close_reason_type').val(2);
                                    $('#CloseReasonModal').modal('show');
                                }else{
                                    mark_close();
                                }
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
                                    table.button('.close_request').enable();
                                    table.button('.in_valid').enable();
                                    table.button('.bulk_resolve').enable();
                                    table.button('.tag').enable();
                                    table.button('.un_tag').enable();
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
                                        table.button('.close_request').disable();
                                        table.button('.in_valid').disable();
                                        table.button('.bulk_resolve').disable();
                                        table.button('.tag').disable();
                                        table.button('.un_tag').disable();
                                        table.button('.bulk_external_comment').enable();
                                        table.button('.bulk_internal_comment').enable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'CRM Request (In-Process)',
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
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax: {
                    url: '{{ route('admin.crm.in_process.list') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.star_shipper_filter = $('#star_shippers_filter').val();
                    }
                },
                rowId: 'id',
                order: [[32, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id_padded_link', name: 'crm_requests.id', class: 'align-middle id_padded_link'},
                    {data: 'tracking_number_hyperlink', name: 's.tracking_number', class: 'align-middle tracking_number'}, // Tracking No.
                    {data: 'shipper_name', name: 'user.name', class: 'align-middle shipper_name'},                         // Shipper Name
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},                                       // Origin
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},                             // Destination
                    {data: 'hub', name: 'dh.name', class: 'align-middle hub'},                                             // Hub
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'},                                             // Zone
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},                               // Arrival Date
                    {data: 'arrival_today', name: 'arrival_today', class: 'align-middle arrival_today', orderable: false, searchable: false}, // Arrival to Today (TAT)
                    {data: 'status', name: 'status', class: 'align-middle shipment_status'},                               // Shipment Status
                    // {data: 'last_status_date', name: 'crm_requests.updated_at', class: 'align-middle last_status_date'},          // Last Status Date
                    {data: 'last_date_status', name: 'last_updated_sj.created_at', class: 'align-middle last_date_status'},          // Last Status Date from shipments journey
                    {data: 'last_status_today', name: 's.updated_at', class: 'align-middle last_status_today', orderable: false}, // Last status to Today (TAT)
                    {data: 'last_status_updated_by', name: 'last_status_upd_by.name', class: 'align-middle last_status_updated_by'},                // Last status by
                    {data: 'case_nature', name: 'crcn.id', class: 'align-middle case_nature'},                             // Case Nature
                    {data: 'case_nature_type', name: 'case_nature_type', class: 'align-middle case_nature_type'},          // Case Nature Type
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'},            // Description
                    {data: 'created', name: 'crm_requests.created_at', class: 'align-middle created_at'}, // Launched Date
                    {data: 'current_tat', name: 'current_tat', class: 'align-middle current_tat', orderable: false, searchable: false}, // Aging (From Launched Date To Today)
                    {data: 'responsible_hub', name: 'responsible_hub', class: 'align-middle responsible_hub', orderable: false, searchable: false}, // Responsible Hub
                    {data: 'sub_hub', name: 'sub_hub', class: 'align-middle sub_hub', orderable: false, searchable: false}, // Sub Hub
                    {data: 'responsible_zone', name: 'responsible_zone', class: 'align-middle responsible_zone', orderable: false, searchable: false}, // Responsible Zone
                    {data: 'agent', name: 'ad.name', class: 'align-middle agent'},                                         // Agent
                    {data: 'agent_assigned_by', name: 'resby.name', class: 'align-middle agent_assigned_by'},              // Agent Assigned By
                    {data: 'parcel_value', name: 's.parcel_value', class: 'align-middle parcel_value'},                      // Parcel Value
                    {data: 'product_cost', name: 'crm_requests.product_cost', class: 'align-middle product_cost'},          //Claim Amount
                    {data: 'cod_value', name: 's.amount', class: 'align-middle cod_value'},                              // COD Value
                    {data: 'segment', name: 'seg.name', class: 'align-middle segment'}, // Segment
                    {data: 'actual_weight', name: 's.actual_weight', class: 'align-middle actual_weight'}, // Weight
                    {data: 'sale_person', name: 'ad1.name', class: 'align-middle sale_person'}, // Salesperson
                    {data: 'shipper_category', name: 'shipper_category', class: 'align-middle shipper_category'}, // Key account category
                    {data: 'kae', name: 'ad2.name', class: 'align-middle kae'}, // KAE
                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle name'},                      // Launched By
                    {data: 'added_by', name: 'crm_requests.launched_by', class: 'align-middle added_by'}, // Launched By Type
                    {data: 'tagged_to_operation', name: 'tagged_to_operation', class: 'align-middle tagged_to_operation'}, //Tagged To Operation
                    {data: 'tagged_to_manual', name: 'tagged_to_manual', class: 'align-middle tagged_to_manual'}, // Manual Tagged To
                    {data: 'tagged', name: 'crt.crm_request_tagging_type_id', class: 'align-middle tagged'},
                    {data: 'last_comment_name', name: 'last_comment_name', class: 'align-middle last_comment_name'},       // Last Comment By
                    {data: 'last_comment', name: 'ccs.comment', class: 'align-middle last_comment'},                       // Last Comment
                    {data: 'last_comment_date', name: 'ccs.created_at', class: 'align-middle last_comment_date'},          // Last Comment Date
                    {data: 'last_updated_rider', name: 'last_rider_status_upd_by.name', class: 'align-middle last_updated_rider'},                           // Last Rider
                    {data: 'last_rider_reason', name: 'ssr.name', class: 'align-middle last_rider_reason'}, // Last Reason
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
                    var shipment_status = '<select name="shipment_status" id="shipment_status" class="select2 form-control"></select>';
                    var case_nature = '<select name="case_nature" id="case_nature" class="select2 form-control"></select>';
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

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select') || $(header).is('.current_tat') || $(header).is('.responsible_hub')|| $(header).is('.responsible_zone') || $(header).is('.last_status_today') || $(header).is('.arrival_today')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.case_nature')) {
                            $(case_nature).appendTo($(search))
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
                        else if ($(header).is('.zone')) {
                            $(zones).appendTo($(search))
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

                    $('#added_by').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Launched By Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#tagging_type').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Admin/Department",
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
                            table.button('.bulk_resolve').disable();
                            table.button('.bulk_external_comment').disable();
                            table.button('.bulk_internal_comment').disable();
                            table.button('.tag').disable();
                            table.button('.un_tag').disable();
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
                            table.button('.bulk_resolve').disable();
                            table.button('.bulk_external_comment').disable();
                            table.button('.bulk_internal_comment').disable();
                            table.button('.tag').disable();
                            table.button('.un_tag').disable();
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
            
            $('#AssignAgentModal').on('hide.bs.modal', function (e) {
                $('#assign_agent').val('').trigger('change');
            });

            $('#CloseReasonModal').on('hide.bs.modal', function (e) {
                $('#closed_reason_status').val('').trigger('change');
                $('#close_reason_crm_ids').val('');
                $('#close_reason_type').val();
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
                                                table.button('.un_tag').disable();
                                                table.button('.close_request').disable();
                                                table.button('.tag').disable();
                                            }
                                        }
                                    });
                                    $('#assign_agent').val('').trigger('change');
                                    table.draw('false');
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

            $('#closed_reason_submit').on('click',function () {
                //mark_close
                $close_type = $('#close_reason_type').val();

                if($close_type == 1)
                {
                    mark_valid_invalid();
                }
                else{
                    mark_close();
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
                    table.button('.close_request').enable();
                    table.button('.in_valid').enable();
                    table.button('.bulk_resolve').enable();
                    table.button('.tag').enable();
                    table.button('.un_tag').enable();
                    table.button('.bulk_external_comment').enable();
                    table.button('.bulk_internal_comment').enable();
                }
                else {
                    table.button('.assign').disable();
                    table.button('.close_request').disable();
                    table.button('.in_valid').disable();
                    table.button('.bulk_resolve').disable();
                    table.button('.tag').disable();
                    table.button('.un_tag').disable();
                    table.button('.bulk_external_comment').disable();
                    table.button('.bulk_internal_comment').disable();
                }
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
                },
            });


            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                table.draw();
            });

            $("#tag_admin").prepend('<option value="" selected></option>').select2({
                placeholder: "Select User",
                width: '100%',
                dropdownParent: $('#tagModal')
            });

            $("#tag_department , #admin_tag_department").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Department",
                width: '100%',
                dropdownParent: $('#tagModal')
            });

            $("#tag_hub , #admin_tag_hub").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Hub",
                width: '100%',
                dropdownParent: $('#tagModal')
            });

            $("#admin_tag_department , #admin_tag_hub").on('change',function (){
                let dept = $("#admin_tag_department").val();
                let hub = $("#admin_tag_hub").val();

                if(dept != "" && hub != "")
                {
                    $.ajax({
                        url: '{!! route('admin.crm.tag.get_admins') !!}',
                        method: 'POST',
                        data: {
                            'hub': hub,
                            'dept': dept,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if (data.status == 0) {
                                $("#tag_admin").html("<option value='' selected></option>");
                                $.each(data.admins,function (i,admin) {
                                    $("#tag_admin").append("<option value='"+admin.id+"'>"+admin.name+"</option>");
                                });

                                $("#tag_admin").trigger('change');
                            }
                            else {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                }
            });

            $("#tag_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Type",
                width: '100%',
                dropdownParent: $('#tagModal')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if (id === 1) {
                    $('#department_tag_div').removeClass('d-none');
                } else if (id === 2) {
                    $('#department_tag_div').addClass('d-none');
                    $('#admin_tag_div').removeClass('d-none');
                } else {
                    $('#department_tag_div').addClass('d-none');
                }
            });
            // $('#tag').on('click', function (e) {
            //     e.preventDefault();
            //     $('#tagModal').modal('show');
            // });
            $('#tagModal').on('hide.bs.modal', function (e) {
                $('#tag_type').val('').trigger('change');
                $('#admin_tag_hub').val('').trigger('change');
                $('#admin_tag_department').val('').trigger('change');
                $('#tag_admin').val('').trigger('change');
                $('#department_tag_div').addClass('d-none');
            });
            $('#admin_tag_div').removeClass('d-none');

            $('#tag_adminSubmit').on('click', function () {
                var type = parseInt($('#tag_type').val()) || 0;
                var tag_hub = $('#admin_tag_hub').val();
                tag_hub = tag_hub ? parseInt(tag_hub) : null;

                var dept = parseInt($('#admin_tag_department').val()) || 0;
                var admin = parseInt($('#tag_admin').val()) || 0;

                var tag = 0; 

                if (admin !== 0) {
                    tag = admin;
                } else if (dept !== 0) {
                    tag = dept; 
                }

                if ((tag)) {
                    $('#tag_adminSubmit').attr('disabled', true);
                    swal({
                        title: 'Please Wait!',
                        text: 'Request(s) are being tagged.',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    $.ajax({
                        url: '{!! route('admin.crm.in_process.tag') !!}',
                        method: 'POST',
                        data: {
                            'tagged_id': tag,
                            'tagged_hub': tag_hub,
                            'admin_id': admin,
                            'crm_request_ids[]': selected_rows,
                            'crm_request_tagging_type_id': type,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if (data.status == 0) {
                                $('#tagModal').modal('hide');
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            }
                            else {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            swal.close();
                            $('#tag_adminSubmit').attr('disabled', false);
                            selected_rows = [];

                            table.rows().deselect();

                            table.draw('false');
                        });
                }
                else {  
                    error = 'Please select only one: either Admin or Department';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            });

            $('#datatable tbody').on('click', 'tr td.special_request button', function() {
                var crm_request_id = table.row($(this).parents('tr')).data().id;

                $.ajax({
                    url: '{!! route('admin.crm.in_process.special_request_tag') !!}',
                    method: 'POST',
                    data: {
                        'crm_request_id': crm_request_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {

                    if (data.status === 1) {
                        var html = '';
                        html += '<table class="table table-sm datatable text-center">';
                        html += '<thead><tr><th>S No.</th><th><strong>Admin</strong></th></tr></thead>';
                        html += '<tbody>';
                        $.each(data.admin, function (index, value) {
                            var ind = index + 1;
                            html += '<tr class=""><td>' + ind + '</td>';
                            html += '<td>' + value + '</td>';

                        });
                        html += '</tbody></table>';

                        $('#ViewRequestModal .modal-body').html(html);
                        $('#ViewRequestModal').modal('show');
                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });

            function mark_close(){
                var closed_reason_status = $('#closed_reason_status').val();
                var close_reason_crm_ids = $('#close_reason_crm_ids').val();
                
                swal({
                        text: 'Are you sure, you want to Close these Request(s)?',
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
                            swal({
                                title: 'Please Wait!',
                                text: 'Request(s) are being marked Closed.',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            if (confirm) {
                                $.ajax({
                                    url: '{!! route('admin.crm.close') !!}',
                                    method: 'POST',
                                    data: {
                                        'crm_request_ids[]': selected_rows,
                                        'closed_reason_status': closed_reason_status,
                                        'close_reason_crm_ids': close_reason_crm_ids,
                                        
                                        '_token': '{{ csrf_token() }}'
                                    }
                                }).done(function (data) {
                                    if (data.status == 0) {
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
                                                table.button('.un_tag').disable();
                                                table.button('.close_request').disable();
                                                table.button('.tag').disable();
                                                table.button('.bulk_external_comment').enable();
                                                table.button('.bulk_internal_comment').enable();

                                            }
                                        }
                                    });

                                    table.draw('false');
                                    $('#CloseReasonModal').modal('hide');

                                    swal.close();
                                });
                            }
                        }
                    });
                
            }

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
                            'selected_invalid_reason_ids': selected_invalid_reason_ids,
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
                            $('#claimInvalidModal').modal('hide');

                        });
                    }
                });
            }

            $('#star_shippers_filter').on('click',function () {
                $('#star_shippers_filter').val(1);
                table.draw(true);
                $('#star_shippers_filter').val(0);
            });

           let selected_invalid_reason_ids = [];

            $('#claim_invalid_reasons').select2({
                width: '100%',
                placeholder: 'Select Invalid Reason(s)',
                allowClear: true,
                dropdownParent: $('#claimInvalidModal')
            }).on('change', function () {
                const currentSelection = $(this).val() || [];

                currentSelection.forEach(id => {
                    if (!selected_invalid_reason_ids.includes(id)) {
                        selected_invalid_reason_ids.push(id);
                    }
                });

                selected_invalid_reason_ids = selected_invalid_reason_ids.filter(id => currentSelection.includes(id));

            });


          $('#invalid_submit').on('click', function () {
                mark_valid_invalid(0); 
            });


            $('#claimInvalidModal').on('hide.bs.modal', function (e) {
                selected_invalid_reason_ids = [];
                $('#claim_invalid_reasons').val(null).trigger('change');
            });


             $('#claim_resolved_reason')
                .select2({
                    placeholder: 'Select a reason',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: Infinity
            }).on('change', function () {
                    const selected = $(this).val();
                    selected_resolved_reason_ids = selected ? [selected] : [];

                    if (selected) {
                        $('#sub_reason_wrapper').slideDown();
                    } else {
                        $('#sub_reason_wrapper').slideUp();
                        $('#claim_resolved_sub_reasons').val(null).trigger('change');
                    }
            });

            $('#claim_resolved_sub_reasons').select2({
                placeholder: 'Select sub reason(s)',
                allowClear: true,
                width: '100%'
            }).on('change', function () {
                selected_resolved_sub_reason_ids = $(this).val() || [];
            });


            $('#resolved_submit').on('click', function () {
                var selectedReason = $('#claim_resolved_reason').val();
                var selectedSubReasons = $('#claim_resolved_sub_reasons').val();

                if (pendingForm) {
                    $(pendingForm).find('input[name="claim_resolved_reason"]').remove();
                    $(pendingForm).find('input[name="claim_resolved_sub_reasons[]"]').remove();

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'claim_resolved_reason',
                        value: selectedReason
                    }).appendTo(pendingForm);

                    selectedSubReasons.forEach(function (sub) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'claim_resolved_sub_reasons[]',
                            value: sub
                        }).appendTo(pendingForm);
                    });

                    $('#claimResolvedModal').modal('hide'); 
                    pendingForm.submit();                  
                    pendingForm = null;   
                }
            });

             $('#claimResolvedModal').on('hide.bs.modal', function (e) {
                selected_resolved_reason_ids = [];
                selected_resolved_sub_reason_ids = [];

                $('#claim_resolved_reason').val(null).trigger('change');
                $('#claim_resolved_sub_reasons').val(null).trigger('change');
                 $('#sub_reason_wrapper').slideUp();
            });

        });
    </script>
@endsection