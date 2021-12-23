@extends('admin.layout.master')

@section('title', 'Return Revert Log Report')

@section('content')
    <h1 class="mb-1">
        Return Revert Log Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12">
                        <div class="row justify-content-center">
                            <div class="col-4">
                                <div class="form-group input-group ">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                    </div>
                                    <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" data-rule-required="true" data-msg-required="Date is required">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group ">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                    </div>
                                    <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-rule-required="true" data-msg-required="Date is required">
                                </div>
                            </div>
                            <div class="col-2">
                                <button type="submit" id="search_filter_btn" class=" btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                            </div>
                        </div>    
                    </div>
                </div>
                <div class="" id="report_data">

                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Return Note No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Updated By</th>
                            <th class="border-primary border-darken-1">Updated At</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        span.font-13{
            font-size: 13px;
        }
    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
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
                        url: '{{ route('admin.reports.revert.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Return Note No.');
                            head.push('Tracking No.');
                            head.push('Shipper');
                            head.push('Updated By');
                            head.push('Updated At');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.return_note);
                                row.push(values.tracking);
                                row.push(values.shipper);
                                row.push(values.updated_by);
                                row.push(values.updated_at);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'OSA charges Log Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                "autoWidth": false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.revert.list') }}',
                    data:function (d){
                        d.search_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[5, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'return_note' ,name: 'rr.return_note', class: 'align-middle return_note'},
                    { data:'tracking_number' ,name: 'shipments.tracking_number', class: 'align-middle tracking_number', sortable: false, orderable: false},
                    { data:'shipper' ,name: 'rr.shipper', class: 'align-middle shipper'},
                    { data:'updated_by' ,name: 'a.name', class: 'align-middle updated_by'},
                    { data:'updated_at' ,name: 'rr.updated_at', class: 'align-middle updated_at'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
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

            $('#search_filter_btn').on('click',function () {
                table.draw(true);
            });
        });
    </script>
@endsection
