@extends('admin.layout.master')

@section('title', 'Agent Vise Report ')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Agent Vise Report
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')


                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1 ">Receiving Sheet</th>
                                    <th class="border-primary border-darken-1 ">Shipper Name</th>
                                    <th class="border-primary border-darken-1">Booked</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Booking Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
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

        .selectize-control {
            width: 100%;
        }

        /* .selectize-control .selectize-input {
             vertical-align: middle;
         }

         .selectize-control .selectize-input .item {
             word-break: break-all;
         }*/
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            var search_date_from = $('#track_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #search_date_to').pickadate('picker').set('min', $('#track_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_to = $('#track_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #search_date_from').pickadate('picker').set('max', $('#track_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.crm_agent_vise_report.crm_agent_vise_report_list') }}',
                        data: params,
                        success: function (result)
                        {
                            head = [];
                            head.push('S.No');
                            head.push('Receiving Sheet');
                            head.push('Shipper Name');
                            head.push('Booked');
                            head.push('Address');
                            head.push('Origin');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.email);
                                row.push(values.phne_number);
                                row.push(values.cnic);
                                row.push(values.trax_id);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );


            var table = $('#datatable').DataTable({
                scrollX: false, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Receiving Sheet',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o "></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.crm_agent_vise_report.crm_agent_vise_report_list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'receiving_sheets.id', class: 'align-middle text_center receiving_sheet_id '},
                    {data: 'email', name: 'u.name', class: 'align-middle text_center shipper_name'},
                    {data: 'cnic', name: 'receiving_sheets.booked', class: 'align-middle text_center bookings'},
                    {data: 'phone_number', name: 'usi.pickup_address', class: 'text_center align-middle address'},
                    {data: 'trax_id', name: 'c.name', class: 'text_center align-middle origin'},
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
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
                    this.api().table().columns.adjust();
                }

            });


            $('#track_form').bind('submit', function (e) {
                e.preventDefault();

                table.draw();
            });

            $('.datatable tbody').on('click', 'tr td.receiving_sheet_id button.print', function() {
                print(parseInt($(this).children('.id').html()));
            });


        });
    </script>
@endsection
