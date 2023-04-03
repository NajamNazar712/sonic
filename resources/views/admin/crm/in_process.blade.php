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
                                    <th class="border-primary border-darken-1">Responsible Hub</th>
                                    <th class="border-primary border-darken-1">Responsible Zone</th>
                                    <th class="border-primary border-darken-1">Shipment Status</th>
                                    <th class="border-primary border-darken-1">Arrival Date</th>
                                    <th class="border-primary border-darken-1">Case Nature</th>
                                    <th class="border-primary border-darken-1">Case Nature Type</th>
                                    <th class="border-primary border-darken-1">Description</th>
                                    <th class="border-primary border-darken-1">Channel</th>
                                    <th class="border-primary border-darken-1">Agent</th>
                                    <th class="border-primary border-darken-1">Launched By</th>
                                    <th class="border-primary border-darken-1">Launched By Type</th>
                                    <th class="border-primary border-darken-1">Tagged (Admin/Department)</th>
                                    <th class="border-primary border-darken-1">Manual Tagged To</th>
                                    <th class="border-primary border-darken-1">Tagged At</th>
                                    <th class="border-primary border-darken-1">Auto Tagged To KAE</th>
                                    <th class="border-primary border-darken-1">Auto Tagged To Operation</th>
                                  {{--  <th class="border-primary border-darken-1">Special Request</th>--}}
                                    <th class="border-primary border-darken-1">Launched Date</th>
                                    <th class="border-primary border-darken-1">Complaint Re-Open Date</th>
                                    <th class="border-primary border-darken-1">Agent Assigned Date</th>
                                    <th class="border-primary border-darken-1">Agent Assigned By</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">Address Latitude</th>
                                    <th class="border-primary border-darken-1">Address Longitude</th>
                                    <th class="border-primary border-darken-1">Valid Date</th>
                                    <th class="border-primary border-darken-1">Launched To Today (TAT)</th>
                                    <th class="border-primary border-darken-1">Last Comment By</th>
                                    <th class="border-primary border-darken-1">Last Comment</th>
                                    <th class="border-primary border-darken-1">Last Comment Date</th>
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
                                    <fieldset class="form-group">
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
        tr.highalert_row{
            background-color: #ff6326;
            color: whitesmoke;
        }
        tr.highalert_row a{
            color: whitesmoke;
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
                            head.push('Responsible Hub');
                            head.push('Responsible Zone');
                            head.push('Shipment Status');
                            head.push('Arrival Date');
                            head.push('Case Nature');
                            head.push('Case Nature Type');
                            head.push('Description');
                            head.push('Channel');
                            head.push('Agent');
                            head.push('Launched By');
                            head.push('Launched By Type');
                            head.push('Tagged (Admin/Department)');
                            head.push('Manual Tagged To');
                            head.push('Tagged At');
                            head.push('Auto Tagged To KAE');
                            head.push('Auto Tagged To Operation');
                            
                            head.push('Launched Date');
                            head.push('Complaint Re-Open Date');
                            head.push('Agent Assigned Date');
                            head.push('Agent Assigned By');
                            head.push('Address');
                            head.push('Address Latitude');
                            head.push('Address Longitude');
                            head.push('Valid Date');
                            head.push('Launched To Today (TAT)');
                            head.push('Last Comment By');
                            head.push('Last Comment');
                            head.push('Last Comment Date');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id_padded);
                                row.push(values.tracking_number);
                                row.push(values.shipper_name);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.zone);
                                row.push(values.responsible_hub);
                                row.push(values.responsible_zone);
                                row.push(values.status);
                                row.push(values.arrival);
                                row.push(values.case_nature);
                                row.push(values.case_nature_type);
                                row.push(values.description);
                                row.push(values.channel);
                                row.push(values.agent);
                                row.push(values.launched_by_name);
                                row.push(values.added_by);
                                row.push(values.tagged);
                                row.push(values.tagged_to_manual);
                                row.push(values.tagged_date);
                                row.push(values.tagged_to_kae);
                                row.push(values.tagged_to_operation);
                                
                                row.push(values.created_at);
                                row.push(values.reopen_date);
                                row.push(values.agent_assigned_date);
                                row.push(values.agent_assigned_by);
                                row.push(values.address);
                                row.push(values.address_latitude);
                                row.push(values.address_longitude);
                                row.push(values.valid_date);
                                row.push(values.current_tat);
                                row.push(values.last_comment_name);
                                row.push(values.last_comment.replace(/<br>/gi, '\n'));
                                row.push(values.last_comment_date);

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
                    }
                },
                rowId: 'id',
                order: [[32, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id_padded_link', name: 'crm_requests.id', class: 'align-middle id_padded_link'},
                    {data: 'tracking_number_hyperlink', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper_name', name: 'user.name', class: 'align-middle shipper_name'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'dh.name', class: 'align-middle hub'},
                    {data: 'zone', name: 'z.id', class: 'align-middle zone'},
                    {data: 'responsible_hub', name: 'responsible_hub', class: 'align-middle responsible_hub' ,orderable: false, searchable: false,},
                    {data: 'responsible_zone', name: 'responsible_zone', class: 'align-middle responsible_zone',orderable: false, searchable: false,},
                    {data: 'status', name: 'status', class: 'align-middle shipment_status'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'case_nature', name: 'crcn.id', class: 'align-middle case_nature'},
                    {data: 'case_nature_type', name: 'case_nature_type', class: 'align-middle case_nature_type'},
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'},
                    {data: 'channel', name: 'crc.id', class: 'align-middle channel'},
                    {data: 'agent', name: 'ad.name', class: 'align-middle agent'},
                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle name'},
                    {data: 'added_by', name: 'crm_requests.launched_by', class: 'align-middle added_by'},
                    {data: 'tagged', name: 'crt.crm_request_tagging_type_id', class: 'align-middle tagged'},
                    {data: 'tagged_to_manual', name: 'tagged_to_manual', class: 'align-middle tagged_to_manual'},
                  /*  {data: 'special_request', name: 'sar.admin_id', class: 'align-middle special_request'},*/
                    {data: 'tagged_date', name: 'crth.created_at', class: 'align-middle tagged_date'},
                    {data: 'tagged_to_kae', name: 'tagged_to_kae', class: 'align-middle tagged_to_kae'},
                    {data: 'tagged_to_operation', name: 'tagged_to_operation', class: 'align-middle tagged_to_operation'},
                    {data: 'created_at', name: 'crm_requests.created_at', class: 'align-middle created_at'},
                    {data: 'reopen_date', name: 'crsh.created_at', class: 'align-middle reopen_date'},
                    {data: 'agent_assigned_date', name: 'resa.created_at', class: 'align-middle agent_assigned_date'},
                    {data: 'agent_assigned_by', name: 'resby.name', class: 'align-middle agent_assigned_by'},
                    {data: 'address', name: 'crm_requests.address', class: 'align-middle address'},
                    {data: 'address_latitude', name: 'crm_requests.address_latitude', class: 'align-middle address_latitude'},
                    {data: 'address_longitude', name: 'crm_requests.address_longitude', class: 'align-middle address_longitude'},
                    {data: 'valid_date', name: 'res.created_at', class: 'align-middle valid_date'},
                    {data: 'current_tat', name: 'current_tat', class: 'align-middle current_tat', orderable: false, searchable: false},
                    {data: 'last_comment_name', name: 'last_comment_name', class: 'align-middle last_comment_name'},
                    {data: 'last_comment', name: 'ccs.comment', class: 'align-middle last_comment'},
                    {data: 'last_comment_date', name: 'ccs.created_at', class: 'align-middle last_comment_date'},
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
                        '<option value="3">Consignee</option>' +
                        '</select>';
                    var tagging_type = '<select name="tagging_type" id="tagging_type" class="select2 form-control">' +
                        '<option value="1">Department</option>' +
                        '<option value="2">Admin</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select') || $(header).is('.current_tat') || $(header).is('.responsible_hub')|| $(header).is('.responsible_zone') ) {
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
                mark_close();
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
                    table.button('.tag').enable();
                    table.button('.un_tag').enable();
                    table.button('.bulk_external_comment').enable();
                    table.button('.bulk_internal_comment').enable();
                }
                else {
                    table.button('.assign').disable();
                    table.button('.close_request').disable();
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
                    $('#admin_tag_div').addClass('d-none');
                    $('#department_tag_div').removeClass('d-none');
                } else if (id === 2) {
                    $('#department_tag_div').addClass('d-none');
                    $('#admin_tag_div').removeClass('d-none');
                } else {
                    $('#admin_tag_div').addClass('d-none');
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
                $('#admin_tag_div').addClass('d-none');
                $('#department_tag_div').addClass('d-none');
            });
            $('#tag_adminSubmit').on('click', function () {
                var type = parseInt($('#tag_type').val());
                var tag_hub = null;
                if (type === 1) {
                    var tag = parseInt($('#tag_department').val());
                    tag_hub = parseInt($('#tag_hub').val());
                    if(!tag_hub){
                        tag_hub = null;
                    }
                }
                else if (type === 2) {
                    var tag = parseInt($('#tag_admin').val());
                }
                if (tag) {
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
                    if (type === 1) {
                        var error = "Department Not Selected!";
                    }
                    else if (type === 2) {
                        var error = "User Not Selected!";
                    }
                    else {
                        error = "Type Not Selected!";
                    }
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
        });
    </script>
@endsection