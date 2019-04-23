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

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Request No.</th>
                                    <th class="border-primary border-darken-1">Tracking No.</th>
                                    <th class="border-primary border-darken-1">Shipment Status</th>
                                    <th class="border-primary border-darken-1">Case Nature</th>
                                    <th class="border-primary border-darken-1">Case Nature Type</th>
                                    <th class="border-primary border-darken-1">Description</th>
                                    <th class="border-primary border-darken-1">Channel</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Agent</th>
                                    <th class="border-primary border-darken-1">Launched By</th>
                                    <th class="border-primary border-darken-1">Launched By Type</th>
                                    <th class="border-primary border-darken-1">Launched Date</th>
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

    <div class="modal fade text-left" id="UpdateRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="UpdateRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="update_request_form" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            {{--<div class="row">--}}
                                {{--<h3 class="heading">Tracking Number</h3>--}}
                            {{--</div>--}}
                            <input type="hidden" id="update_request_id_selected">
                            <div class="row old_scroll justify-content-center" id="requested_shipments">
                            <h3 id="requested_shipment_tracking" class="text-center font-weight-bold"></h3>
                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select" class="form-control select2">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="complaints d-none" id="request_complaints">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2">
                                                @foreach($case_nature_complaints as $complaints)
                                                    <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="complaint_channel" id="complaint_channels" class="form-control select2">
                                                @foreach($channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <textarea class="form-control info" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..." disabled></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="service d-none" id="request_service">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_request" id="case_nature_requests" class="form-control select2">
                                                @foreach($case_nature_service_requests as $service)
                                                    <option value="{{$service->id}}">{{$service->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="request_channel" id="request_channels" class="form-control select2">
                                                @foreach($channels as $channel2)
                                                    <option value="{{$channel2->id}}">{{$channel2->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <textarea class="form-control info" name="service_description" id="service_description" rows="5" placeholder="Enter Description Here..." disabled></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="UpdateRequestBtn" type="submit" class="btn btn-primary btn-block d-none">Update</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.crm.launched_re_open.list') }}',
                        success: function (result) {
                            head = [];

                            head.push('S No.');
                            head.push('Request No.');
                            head.push('Tracking No.');
                            head.push('Shipment Status');
                            head.push('Case Nature');
                            head.push('Case Nature Type');
                            head.push('Description');
                            head.push('Channel');
                            head.push('Status');
                            head.push('Agent');
                            head.push('Launched By');
                            head.push('Launched By Type');
                            head.push('Launched Date');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.tracking_number);
                                row.push(values.status);
                                row.push(values.case_nature);
                                row.push(values.case_nature_type);
                                row.push(values.description);
                                row.push(values.channel);
                                row.push(values.status);
                                row.push(values.agent);
                                row.push(values.launched_by_name);
                                row.push(values.added_by);
                                row.push(values.created_at);

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
                scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(179, session('permissions')))
                    {
                        text: 'Assign Agent',
                        className: 'btn btn-primary assign',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $('#AssignAgentModal').modal('show');

                            $('#AssignAgentModal').on('shown.bs.modal',function (e) {
                            });
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
                    }],
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
                ajax: '{{ route('admin.crm.launched_re_open.list') }}',
                rowId: 'id',
                order: [[12, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id_padded', name: 'crm_requests.id', class: 'align-middle request_id'},
                    {data: 'tracking_number_hyperlink', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipment_status', name: 'ss.name', class: 'align-middle shipment_status'},
                    {data: 'case_nature', name: 'crcn.id', class: 'align-middle case_nature'},
                    {data: 'case_nature_type', name: 'case_nature_type', class: 'align-middle case_nature_type'},
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'},
                    {data: 'channel', name: 'crc.id', class: 'align-middle channel'},
                    {data: 'status', name: 'crs.id', class: 'align-middle status'},
                    {data: 'agent', name: 'ad.name', class: 'align-middle agent'},
                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle name'},
                    {data: 'added_by', name: 'crm_requests.launched_by', class: 'align-middle added_by'},
                    {data: 'created_at', name: 'crm_requests.created_at', class: 'align-middle created_at'},
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
                    var added_by = '<select name="launched_by" id="added_by" class="select2 form-control">' +
                        '<option value="0">Admin</option>' +
                        '<option value="1">Shipper</option>' +
                        '<option value="2">Shipper Substitute User</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.status')) {
                            $(status).appendTo($(search))
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

                    $('#added_by').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Launched By Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });


                    this.api().table().columns.adjust();
                }
            });

            $("#assign_agent").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Agent",
                width:'100%',
                dropdownParent:$('#AssignAgentModal')
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
                }
                else {
                    table.button('.assign').disable();
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


            $('body').on('click', '.dropdown-item.update_request', function () {
                var nature = parseInt($(this).parents('tr').attr('nature'));
                var request_id = parseInt($(this).parents('tr').attr('id'));
                if(nature == 1 || nature == 2){
                    $('#UpdateRequestModal').modal('show');
                    $('#update_request_id_selected').val(request_id);
                    $.ajax({
                        url: '{!! route('admin.crm.request.get_request') !!}',
                        method: 'POST',
                        data: {
                            'request_id': request_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                            if(data.status){
                                if(data.details.case_nature_id == 1){
                                    $('#case_nature_select').val(data.details.case_nature_id).trigger('change');
                                    $('#case_nature_complaints').val(data.details.case_nature_type_id).trigger('change');
                                    $('#complaint_channels').val(data.details.channel_id).trigger('change');
                                    $('#complaint_description').val(data.details.description);

                                    $('#requested_shipment_tracking').text(data.tracking_number);
                                }else if(data.details.case_nature_id == 2){
                                    $('#case_nature_select').val(data.details.case_nature_id).trigger('change');
                                    $('#case_nature_requests').val(data.details.case_nature_type_id).trigger('change');
                                    $('#request_channels').val(data.details.channel_id).trigger('change');
                                    $('#service_description').val(data.details.description);

                                    $('#requested_shipment_tracking').text(data.tracking_number);
                                }
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                    });
                }
            });

            $( "#update_request_form" ).bind('submit', function (e) {
                e.preventDefault();
                var case_nature_id = parseInt($('#case_nature_select').val());
                var request_id = $('#update_request_id_selected').val();
                if(case_nature_id === 1){
                    var nature_flag = true;
                    var case_nature_complaint_id = $('#case_nature_complaints').val();
                    var case_nature_channel_id = $('#complaint_channels').val();

                    if(!case_nature_complaint_id){
                        nature_flag = false;
                        var error = "Please select Complaint type!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(!case_nature_channel_id){
                        nature_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(nature_flag){
                        $.ajax({
                            url: '{!! route('admin.crm.request.update') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'request_id' : request_id,
                                'case_nature_id' : case_nature_id,
                                'complaint_id' : case_nature_complaint_id,
                                'channel_id': case_nature_channel_id
                            }
                        })
                            .done(function(data) {
                                if (data.status) {
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                                table.draw('false');

                                $('#UpdateRequestModal').modal('hide');
                            });
                    }

                }else if(case_nature_id == 2){
                    var nature_flag = true;
                    var case_nature_complaint_id = $('#case_nature_requests').val();
                    var case_nature_channel_id = $('#request_channels').val();
                    if(!case_nature_complaint_id){
                        nature_flag = false;
                        var error = "Please select Complaint type!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(!case_nature_channel_id){
                        nature_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    if(nature_flag){
                        $.ajax({
                            url: '{!! route('admin.crm.request.update') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'request_id' : request_id,
                                'case_nature_id' : case_nature_id,
                                'complaint_id' : case_nature_complaint_id,
                                'channel_id': case_nature_channel_id
                            }
                        })
                            .done(function(data) {
                                if (data.status) {
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                                table.draw('false');
                                $('#UpdateRequestModal').modal('hide');
                            });
                    }
                }else{
                    var error = "Please select case nature!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

        });
    </script>
@endsection