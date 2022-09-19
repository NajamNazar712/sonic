@extends('admin.layout.master')
@section('title','Vigilance Verification History')
@section('content')
    <h1 class="mb-1">
        Vigilance Verification History
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                            </div>
                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" data-value="{{ Carbon\Carbon::today() }}" placeholder="Search Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" data-value="{{ Carbon\Carbon::today() }}" placeholder="Search Date (To)">
                        </div>
                    </div>

                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Delivery Note No.</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">No. Of Shipment(s)</th>
                        <th class="border-primary border-darken-1">Excess Shipment(s)</th>
                        <th class="border-primary border-darken-1">Verify Shipment(s)</th>
                        <th class="border-primary border-darken-1">Unverify Shipment(s)</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Updated Date</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Assigned By</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Shipment(s)</h4>

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

    <!--Excess Shipments popup -->
    <div class="modal fade" id="excess_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="excess_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="excess_shipments_modal_title">Excess Shipment(s)</h4>

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

    <!--Excess Shipments popup -->
    <div class="modal fade" id="verify_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="verify_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="verify_shipments_modal_title">Verify Shipment(s)</h4>

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

    <div class="modal fade" id="unverify_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="unverify_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="unverify_shipments_modal_title">Unverify Shipment(s)</h4>

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

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.vigilance.verification.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Delivery Note No.');
                            head.push('Status');
                            head.push('Hub');
                            head.push('Rider');
                            head.push('No. Of Shipments');
                            head.push('Excess Shipments');
                            head.push('Verify Shipments');
                            head.push('Unverify Shipments');
                            head.push('Updated By');
                            head.push('Updated Date');
                            head.push('Assigned Date');
                            head.push('Assigned By');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.delivery_note_id_padded);
                                row.push(values.main_status);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.shipments_count);
                                row.push(values.excess_shipments_count);
                                row.push(values.verify_shipments_count);
                                row.push(values.unverify_shipments);
                                row.push(values.created_by);
                                row.push(values.created_at);
                                row.push(values.asigned_date);
                                row.push(values.asignee);
                               
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
                buttons:[{
                    extend: 'excel',
                    title: 'Vigilance Verification History',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                scrollX: true, scrollY: '500px',
                autoWidth:false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.vigilance.verification.history.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'vigilance_id',
                order: [[9, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'delivery_note' ,name: 'delivery_notes.id', class: 'align-middle text-center delivery_note'},
                    { data:'main_status' ,name: 'main_status', class: 'align-middle status',orderable:false},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'shipments_count_link' ,name: 'delivery_notes.shipments_count', class: 'align-middle shipments_count_link text-center'},
                    { data:'excess_shipments_link' ,name: 'vigilance_verifications.excess_shipments_count', class: 'align-middle excess_shipments_link text-center'},
                    { data:'verify_shipments_link' ,name: 'vigilance_verifications.verify_shipments_count', class: 'align-middle verify_shipments_link text-center'},
                    { data:'unverify_shipments_link' ,name: 'unverify_shipments_link', class: 'align-middle unverify_shipments_link text-center',orderable: false, searchable: false},
                    { data:'created_by' ,name: 'cb.name', class: 'align-middle created_by'},
                    { data:'created_at' ,name: 'created_at', class: 'align-middle created_at'},
                    { data:'asigned_date' ,name: 'delivery_notes.created_at', class: 'align-middle asigned_date'},
                    { data:'asignee' ,name: 'ad.name', class: 'align-middle asignee'},
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Pending for Update</option>' +
                        '<option value="1">Pending for Verification</option>' +
                        '<option value="2">Cash Collected</option>' +
                        '<option value="3">Completed</option>' +
                        '<option value="4">Verified</option>' +
                        '<option value="5">Canceled</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.unverify_shipments_link')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
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
            $('body').on('click','.printdeliverynote',function () {
                var deliverynote = parseInt($(this).attr('noteId'));

                print(deliverynote);
            });
            function printDNCC(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.dncc.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
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

            $('body').on('click','.printDNCC',function () {
                var note_id = parseInt($(this).attr('noteId'));
                printDNCC(note_id);
            });



            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.shipments_count_link button',function () {
                var id = parseInt($(this).attr('noteId'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.history.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });

            });
            $('#datatable tbody').on('click','tr td.excess_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#excess_shipments_modal .modal-body').html('');
                $('#excess_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.vigilance.verification.excess_cns') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'verify_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#excess_shipments_modal .modal-body').html(html);
                        }
                    });

            });

            $('#datatable tbody').on('click','tr td.verify_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#verify_shipments_modal .modal-body').html('');
                $('#verify_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.vigilance.verification.verify_cns') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'verify_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#verify_shipments_modal .modal-body').html(html);
                        }
                    });

            });

            $('#datatable tbody').on('click','tr td.unverify_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#unverify_shipments_modal .modal-body').html('');
                $('#unverify_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.vigilance.verification.unverify_cns') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'verify_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#unverify_shipments_modal .modal-body').html(html);
                        }
                    });

            });

        });
    </script>
@endsection