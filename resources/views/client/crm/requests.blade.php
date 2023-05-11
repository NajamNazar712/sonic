@extends('client.layout.master')

@section('title', 'Requests')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <h1 class="mb-1">
                        Requests
                    </h1>

                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('client.inc.messages')
                                <form id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o"></span>
                                                </span>
                                            </div>
                
                                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right require_one search_date_from" id="search_date_from" placeholder="Date (From)">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o"></span>
                                                </span>
                                            </div>
                
                                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right search_date_to" id="search_date_to" placeholder="Date (To)">
                                        </div>
                                    </div>
                
                                    <div class="w-100"></div>
                
                                    <div class="col-2">
                                        <button type="submit" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                    </div>
                                </form>
                                <div id="report_data">
                                    <div class="row justify-content-center">
                                        <div class="col-3">
                                            <div class="card bg-gradient-directional-info pull-up cursor-pointer">
                                                <div class="card-content" id="total_launched">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="icon-flag text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white" id="launched">{{$launched}}</h3>
                                                                <span class="font-13">Launched</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="card bg-gradient-directional-warning pull-up cursor-pointer">
                                                <div class="card-content" id="total_in_process">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white" id="in_process">{{$in_process}}</h3>
                                                                <span>In Process</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="card bg-gradient-directional-red pull-up cursor-pointer">
                                                <div class="card-content" id="total_closed">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="icon-close text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white" id="closed">{{$closed}}</h3>
                                                                <span>Closed</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        {{--<th class="border-primary border-darken-1"></th>--}}
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Request No.</th>
                                        <th class="border-primary border-darken-1">Tracking No.</th>
                                        <th class="border-primary border-darken-1">Shipment Status</th>
                                        <th class="border-primary border-darken-1">Case Nature</th>
                                        <th class="border-primary border-darken-1">Case Nature Type</th>
                                        <th class="border-primary border-darken-1">Description</th>
                                        <th class="border-primary border-darken-1">Channel</th>
                                        <th class="border-primary border-darken-1">Request Status</th>
                                        <th class="border-primary border-darken-1">Who's at Fault</th>
                                        <th class="border-primary border-darken-1">Launched By</th>
                                        <th class="border-primary border-darken-1">Launched By Name</th>
                                        {{--<th class="border-primary border-darken-1">Launched By Type</th>--}}
                                        <th class="border-primary border-darken-1">Launched Date</th>
                                        <th class="border-primary border-darken-1">Shipper Name</th>
                                        <th class="border-primary border-darken-1">Closed Date</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            var from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                // clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                        var currentDate = moment(old_date_formatted);

                        var to_date_formatted = $('input[name="search_date_to_formatted"]').val();
                        var toDate = moment(to_date_formatted);

                        if (currentDate.format('x') > toDate.format('x')) {
                            to_date.pickadate('picker').clear();
                        }

                        var afterDate = currentDate.add(31, 'days');
                        to_date.pickadate('picker').set({'max': afterDate.toDate()},{muted: true});

                        from_date.valid();
                    }
                }
            });
            var to_date = $('#search_date_to').pickadate({
                firstDay: 1,
                // clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var current_date_formatted = $('input[name="search_date_to_formatted"]').val();
                        var currentDate = moment(current_date_formatted);

                        var from_date_formatted = $('input[name="search_date_from_formatted"]').val();
                        var fromDate = moment(from_date_formatted);

                        if (currentDate.format('x') < fromDate.format('x')) {
                            from_date.pickadate('picker').clear();
                        }

                        var beforeDate = currentDate.subtract(31, 'days');
                        from_date.pickadate('picker').set({'min': beforeDate.toDate()},{muted: true});

                        to_date.valid();
                    }
                }
            });

            function get_summary_cards_data() {
                var from_date = $('input[name="search_date_from_formatted"]').val();
                var to_date = $('input[name="search_date_to_formatted"]').val();
               
                $.ajax({
                    url: '{!! route('cod.crm.request.card_data') !!}',
                    method: 'post',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'from_date': from_date,
                        'to_date': to_date,

                    }
                }).done(function (data) {
                    if(data.status){
                        console.log(data.card_data);
                        $('#launched').text(data.card_data.launched);
                        $('#in_process').text(data.card_data.in_process);
                        $('#closed').text(data.card_data.closed);

                    }else{
                        $('#launched').text(0);
                        $('#in_process').text(0);
                        $('#cancclosedeled').text(0);

                    }
                });
            }

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.crm.request.list') }}',
                        data:params,
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
                            head.push('Request Status');
                            head.push('Who\'s at Fault');

                            head.push('Launched By');
                            head.push('Launched By Name');
                            head.push('Launched Date');
                            head.push('Shipper Name');
                            head.push('Closed Date');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id_padded);
                                row.push(values.tracking_number);
                                row.push(values.shipment_status);
                                row.push(values.case_nature);
                                row.push(values.case_nature_type);
                                row.push(values.descr);
                                row.push(values.channel);
                                row.push(values.status);
                                row.push(values.at_fault);
                                row.push(values.added_by);
                                row.push(values.launched_by_name);
                                row.push(values.created_at);
                                row.push(values.shipper_name);
                                row.push(values.closed_at);

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
                    {
                        extend: 'excel',
                        title: 'Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax:{
                    url: '{{ route('cod.crm.request.list') }}',
                    data: function (d) {
                        
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                    order: [[10, 'desc']],
                columns: [
                    // {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id_padded_link', name: 'crm_requests.id', class: 'align-middle request_id'},
                    {data: 'tracking_number_hyperlink', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipment_status', name: 'shipment_status', class: 'align-middle shipment_status'},
                    {data: 'case_nature', name: 'crcn.id', class: 'align-middle case_nature'},
                    {data: 'case_nature_type', name: 'case_nature_type', class: 'align-middle case_nature_type'},
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'},
                    {data: 'channel', name: 'crc.id', class: 'align-middle channel'},
                    {data: 'status', name: 'crs.id', class: 'align-middle status'},
                    {data: 'at_fault', name: 'crmcr.status_id', class: 'align-middle at_fault'},
                    // {data: 'name', name: 'a.name', class: 'align-middle name'},
                    {data: 'added_by', name: 'crm_requests.launched_by', class: 'align-middle added_by'},
                    {data: 'launched_by_name', name: 'crm_requests.launched_by_id', class: 'align-middle launched_by_name',orderable: false, searchable: false},
                    {data: 'created_at', name: 'crm_requests.created_at', class: 'align-middle created_at'},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                    {data: 'closed_at', name: 'crmst.created_at', class: 'align-middle closed_at'},

                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    // $('td:eq(0)', row).addClass('select-checkbox');

                    //
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var case_nature = '<select name="case_nature" id="case_nature" class="select2 form-control"></select>';
                    var shipment_status = '<select name="shipment_status" id="shipment_status" class="select2 form-control"></select>';
                    var status = '<select name="status" id="status" class="select2 form-control"></select>';
                    var close_reason_status = '<select name="close_reason_status" id="close_reason_status" class="select2 form-control"></select>';
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

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select') || $(header).is('.launched_by_name') ) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.status')) {
                            $(status).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.at_fault')) {
                            $(close_reason_status).appendTo($(search))
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

                    var data4 = $.map({!! $case_nature !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $('#case_nature').prepend('<option value="" selected></option>').select2({
                        data:data4,
                        placeholder: "Select Case Nature",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data3 = $.map({!! $case_nature_type !!}, function (obj) {
                        obj.id = obj.id;
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
                        placeholder: "Select Launched By",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var datac = $.map({!! $closed_reason_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $('#close_reason_status').prepend('<option value="" selected></option>').select2({
                        data:datac,
                        placeholder: "Select Reason",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });


                    this.api().table().columns.adjust();
                }
            });
            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    get_summary_cards_data();
                    table.draw();
                }
            });

        });
    </script>
@endsection