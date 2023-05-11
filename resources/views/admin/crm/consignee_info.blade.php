@extends('admin.layout.master')

@section('title', 'Consignee Info Requests')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <h1 class="mb-1">
                        Consignee Info Requests
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

                                <div class="col justify-content-end mb-3">
                                    <div class="card-header">
                                        <div class="heading-elements">
                                            <ul class="list-inline">
                                                <li class="primary border-primary round" value="0" id="star_shippers_filter"><a>
                                                        Star Shippers</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Request No.</th>
                                        <th class="border-primary border-darken-1">Tracking No.</th>
                                        <th class="border-primary border-darken-1">Shipper Name</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Hub</th>
                                        <th class="border-primary border-darken-1">Zone</th>
                                        <th class="border-primary border-darken-1">Shipment Status</th>
                                        <th class="border-primary border-darken-1">Case Nature</th>
                                        <th class="border-primary border-darken-1">Case Nature Type</th>
                                        <th class="border-primary border-darken-1">Description</th>
                                        <th class="border-primary border-darken-1">Channel</th>
                                        <th class="border-primary border-darken-1">Agent</th>
                                        <th class="border-primary border-darken-1">Launched By</th>
                                        <th class="border-primary border-darken-1">Launched By Type</th>
                                        <th class="border-primary border-darken-1">Tagged (Admin/Department)</th>
                                        <th class="border-primary border-darken-1">Tagged To</th>
                                        <th class="border-primary border-darken-1">Tagged At</th>
                                        <th class="border-primary border-darken-1">Launched Date</th>
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            function print(shipment_id, crm_request_id) {
                    $.ajax({
                        url: '{!! route('admin.crm.consignee_info.print_air_waybill') !!}',
                        method: 'POST',
                        data: {
                            'shipment_id': shipment_id,
                            'crm_request_id': crm_request_id,
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
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.crm.consignee_info.list') }}',
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
                            head.push('Shipment Status');
                            head.push('Case Nature');
                            head.push('Case Nature Type');
                            head.push('Description');
                            head.push('Channel');
                            head.push('Agent');
                            head.push('Launched By');
                            head.push('Launched By Type');
                            head.push('Tagged (Admin/Department)');
                            head.push('Tagged To');
                            head.push('Launched Date');
                            head.push('Agent Assigned Date');
                            head.push('Agent Assigned By');
                            head.push('Address');
                            head.push('Address Latitude');
                            head.push('Agent Longitude');
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
                                row.push(values.status);
                                row.push(values.case_nature);
                                row.push(values.case_nature_type);
                                row.push(values.description);
                                row.push(values.channel);
                                row.push(values.agent);
                                row.push(values.launched_by_name);
                                row.push(values.added_by);
                                row.push(values.tagged);
                                row.push(values.tagged_to);
                                row.push(values.created_at);
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

            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Consignee Info',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax: {
                    url: '{{ route('admin.crm.consignee_info.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.star_shipper_filter = $('#star_shippers_filter').val();
                    }
                },
                rowId: 'id',
                order: [[19, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id_padded_link', name: 'crm_requests.id', class: 'align-middle id_padded_link'},
                    {data: 'tracking_number_hyperlink', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper_name', name: 'user.name', class: 'align-middle shipper_name'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'dh.name', class: 'align-middle hub'},
                    {data: 'zone', name: 'z.id', class: 'align-middle zone'},
                    {data: 'status', name: 'status', class: 'align-middle shipment_status'},
                    {data: 'case_nature', name: 'crcn.id', class: 'align-middle case_nature'},
                    {data: 'case_nature_type', name: 'case_nature_type', class: 'align-middle case_nature_type'},
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'},
                    {data: 'channel', name: 'crc.id', class: 'align-middle channel'},
                    {data: 'agent', name: 'ad.name', class: 'align-middle agent'},
                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle name'},
                    {data: 'added_by', name: 'crm_requests.launched_by', class: 'align-middle added_by'},
                    {data: 'tagged', name: 'crt.crm_request_tagging_type_id', class: 'align-middle tagged'},
                    {data: 'tagged_to', name: 'tagged_to', class: 'align-middle tagged_to'},
                    {data: 'tagged_date', name: 'crth.created_at', class: 'align-middle tagged_date'},
                    {data: 'created_at', name: 'crm_requests.created_at', class: 'align-middle created_at'},
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
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
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

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select') || $(header).is('.current_tat') ) {
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

            $('body').on('click','.intercept',function () {
                var shipment_id = table.row($(this).parents('tr')).data().shipment_id;

                if(shipment_id != ''){
                    var redirect = '{!! route('admin.intercept.index', ':id') !!}';
                    var url = redirect.replace(':id', shipment_id);
                    window.open(url);
                }
            });

            $('body').on('click','.print',function () {
                var row_id = $(this).parents('tr').attr('id');
                var shipment_id = table.row($(this).parents('tr')).data().shipment_id;
                if(row_id != '' && shipment_id != ''){
                    print(shipment_id, row_id);
                }
            });

            $('body').on('click','.resolve',function () {
                var row_id = $(this).parents('tr').attr('id');
                swal({
                    text: 'Are you sure, you want to Resolve these Request(s)?',
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
                        swal({
                            title: 'Please Wait!',
                            text: 'Request is being Resolved.',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        $.ajax({
                            url: '{!! route('admin.crm.consignee_info.resolve') !!}',
                            method: 'POST',
                            data: {
                                'crm_request_id': row_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if(data.status == 1){
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);
                                }
                                else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                });
            });

            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                table.draw();
            });

            $('#star_shippers_filter').on('click',function () {
                $('#star_shippers_filter').val(1);
                table.draw(true);
                $('#star_shippers_filter').val(0);
            });

        });
    </script>
@endsection