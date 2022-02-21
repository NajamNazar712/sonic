@extends('admin.layout.master')

@section('title', 'International Rates Excel Upload')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Retail International Rates Excel Upload
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="retail_international_rates_upload_form" class="form-horizontal" method="POST" action="{{ route('admin.retail.international.rates.excel') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="file" name="document_rates" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>

                                    <div class="col ml-auto">
                                        <div class="form-group text-right">
                                            <a href="{{ asset('file/Retail International Standard Rates Upload Template.xlsx') }}?v=14_04_2021" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row justify-content-center">
                                <h3>For Document</h3>
                            </div>
                            <table class="table table-stripped table-bordered datatable" id="document_datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">Range Up</th>
                                    <th class="border-primary border-darken-1">Range Down</th>
                                    <th class="border-primary border-darken-1">Zone 1 </th>
                                    <th class="border-primary border-darken-1">Zone 2 </th>
                                    <th class="border-primary border-darken-1">Zone 3 </th>
                                    <th class="border-primary border-darken-1">Zone 4 </th>
                                    <th class="border-primary border-darken-1">Zone 5 </th>
                                    <th class="border-primary border-darken-1">Zone 6 </th>
                                    <th class="border-primary border-darken-1">Zone 7 </th>
                                    <th class="border-primary border-darken-1">Zone 8 </th>
                                    <th class="border-primary border-darken-1">Zone 9 </th>
                                    <th class="border-primary border-darken-1">Zone 10 </th>
                                    <th class="border-primary border-darken-1">Zone 11 </th>

                                </tr>
                                </thead>
                            </table>

                            <form id="non_document_upload_form" class="form-horizontal" method="POST" action="{{ route('admin.retail.international.rates.excel') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="file" name="non_document_rates" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row justify-content-center">
                                <h3>For Non-Document</h3>
                            </div>
                            <table class="table table-stripped table-bordered datatable" id="non_document_datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">Range Up</th>
                                    <th class="border-primary border-darken-1">Range Down</th>
                                    <th class="border-primary border-darken-1">Zone 1 </th>
                                    <th class="border-primary border-darken-1">Zone 2 </th>
                                    <th class="border-primary border-darken-1">Zone 3 </th>
                                    <th class="border-primary border-darken-1">Zone 4 </th>
                                    <th class="border-primary border-darken-1">Zone 5 </th>
                                    <th class="border-primary border-darken-1">Zone 6 </th>
                                    <th class="border-primary border-darken-1">Zone 7 </th>
                                    <th class="border-primary border-darken-1">Zone 8 </th>
                                    <th class="border-primary border-darken-1">Zone 9 </th>
                                    <th class="border-primary border-darken-1">Zone 10 </th>
                                    <th class="border-primary border-darken-1">Zone 11 </th>

                                </tr>
                                </thead>
                            </table>

                            <form id="box_upload_form" class="form-horizontal" method="POST" action="{{ route('admin.retail.international.rates.excel') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row align-items-center justify-content-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="file" name="box_rates" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row justify-content-center">
                                <h3>For Box</h3>
                            </div>
                            <table class="table table-stripped table-bordered datatable" id="box_datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">Range Up</th>
                                    <th class="border-primary border-darken-1">Range Down</th>
                                    <th class="border-primary border-darken-1">Zone 1 </th>
                                    <th class="border-primary border-darken-1">Zone 2 </th>
                                    <th class="border-primary border-darken-1">Zone 3 </th>
                                    <th class="border-primary border-darken-1">Zone 4 </th>
                                    <th class="border-primary border-darken-1">Zone 5 </th>
                                    <th class="border-primary border-darken-1">Zone 6 </th>
                                    <th class="border-primary border-darken-1">Zone 7 </th>
                                    <th class="border-primary border-darken-1">Zone 8 </th>
                                    <th class="border-primary border-darken-1">Zone 9 </th>
                                    <th class="border-primary border-darken-1">Zone 10 </th>
                                    <th class="border-primary border-darken-1">Zone 11 </th>

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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#retail_international_rates_upload_form').validate({
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
                        text: 'Your Rates are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('#non_document_upload_form').validate({
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
                        text: 'Your Rates are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('#box_upload_form').validate({
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
                        text: 'Your Rates are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
        });

        /* jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
             if ( this.context.length ) {
                 body = [];
                 var params = table.ajax.params();
                 params.start = 0;
                 params.length = -1;
                 params.excel = true;
                 var jsonResult = $.ajax({
                     url: '{{ route('admin.retail.international.rates.list') }}',
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Range Up');
                        head.push('Range Down');
                        head.push('Zone 1');
                        head.push('Zone 2');
                        head.push('Zone 3');
                        head.push('Zone 4');
                        head.push('Zone 5');
                        head.push('Zone 6');
                        head.push('Zone 7');
                        head.push('Zone 8');
                        head.push('Zone 9');
                        head.push('Zone 10');
                        head.push('Zone 11');
                        $.each(result.data, function(index, values) {
                            row = [];


                            row.push(index + 1);
                            row.push(values.range_up);
                            row.push(values.range_down);
                            row.push(values.zone_1);
                            row.push(values.zone_2);
                            row.push(values.zone_3);
                            row.push(values.zone_4);
                            row.push(values.zone_5);
                            row.push(values.zone_6);
                            row.push(values.zone_7);
                            row.push(values.zone_8);
                            row.push(values.zone_9);
                            row.push(values.zone_10);
                            row.push(values.zone_11);

                            body.push(row);
                        });
                    },
                    async: false
                });

                return {body: body, header: head};
            }
        } );*/

        var table1 = $('#document_datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: false, scrollY: '500px',
            buttons: [
                /* {
                     extend: 'excel',
                     title: 'International Standard DHL Rates',
                     className:'btn-primary',
                     text: '<i class="la la-file-excel-o"></i> Excel',
                 },*/

                'reset'
            ],
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            rowId: 'id',
            order: [[1, 'asc']],
            ajax: {
                url: '{{ route('admin.retail.international.rates.list') }}',
                data: function (d) {
                    d.shipping_mode_id = 1;
                }
            },
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'range_up', name: 'range_up', class: 'align-middle range_up'},
                {data: 'range_down', name: 'range_down', class: 'align-middle range_down'},
                {data: 'zone_1', name: 'zone_1', class: 'align-middle zone_1'},
                {data: 'zone_2', name: 'zone_2', class: 'align-middle zone_2'},
                {data: 'zone_3', name: 'zone_3', class: 'align-middle zone_3'},
                {data: 'zone_4', name: 'zone_4', class: 'align-middle zone_4'},
                {data: 'zone_5', name: 'zone_5', class: 'align-middle zone_5'},
                {data: 'zone_6', name: 'zone_6', class: 'align-middle zone_6'},
                {data: 'zone_7', name: 'zone_7', class: 'align-middle zone_7'},
                {data: 'zone_8', name: 'zone_8', class: 'align-middle zone_8'},
                {data: 'zone_9', name: 'zone_9', class: 'align-middle zone_9'},
                {data: 'zone_10', name: 'zone_10', class: 'align-middle zone_10'},
                {data: 'zone_11', name: 'zone_11', class: 'align-middle zone_11'}
            ],
            rowCallback: function(row, data, index) {

                var info = table1.page.info();
                $('td:eq(0)', row).html(index + 1 + info.page * info.length);
            },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';


                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.serial_number')) {
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

        var table2 = $('#non_document_datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: false, scrollY: '500px',
            buttons: [
                /*{
                    extend: 'excel',
                    title: 'International Standard Non Document Rates',
                    className:'btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },*/

                'reset'
            ],
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            rowId: 'id',
            order: [[1, 'asc']],
            ajax: {
                url: '{{ route('admin.retail.international.rates.list') }}',
                data: function (d) {
                    d.shipping_mode_id = 2;
                }
            },
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'range_up', name: 'range_up', class: 'align-middle range_up'},
                {data: 'range_down', name: 'range_down', class: 'align-middle range_down'},
                {data: 'zone_1', name: 'zone_1', class: 'align-middle zone_1'},
                {data: 'zone_2', name: 'zone_2', class: 'align-middle zone_2'},
                {data: 'zone_3', name: 'zone_3', class: 'align-middle zone_3'},
                {data: 'zone_4', name: 'zone_4', class: 'align-middle zone_4'},
                {data: 'zone_5', name: 'zone_5', class: 'align-middle zone_5'},
                {data: 'zone_6', name: 'zone_6', class: 'align-middle zone_6'},
                {data: 'zone_7', name: 'zone_7', class: 'align-middle zone_7'},
                {data: 'zone_8', name: 'zone_8', class: 'align-middle zone_8'},
                {data: 'zone_9', name: 'zone_9', class: 'align-middle zone_9'},
                {data: 'zone_10', name: 'zone_10', class: 'align-middle zone_10'},
                {data: 'zone_11', name: 'zone_11', class: 'align-middle zone_11'}
            ],
            rowCallback: function(row, data, index) {

                var info = table2.page.info();
                $('td:eq(0)', row).html(index + 1 + info.page * info.length);
            },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';


                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.serial_number')) {
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

        var table3 = $('#box_datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: false, scrollY: '500px',
            buttons: [
                /*{
                    extend: 'excel',
                    title: 'Retail International Standard Box Rates',
                    className:'btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },*/

                'reset'
            ],
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            rowId: 'id',
            order: [[1, 'asc']],
            ajax: {
                url: '{{ route('admin.retail.international.rates.list') }}',
                data: function (d) {
                    d.shipping_mode_id = 3;
                }
            },
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'range_up', name: 'range_up', class: 'align-middle range_up'},
                {data: 'range_down', name: 'range_down', class: 'align-middle range_down'},
                {data: 'zone_1', name: 'zone_1', class: 'align-middle zone_1'},
                {data: 'zone_2', name: 'zone_2', class: 'align-middle zone_2'},
                {data: 'zone_3', name: 'zone_3', class: 'align-middle zone_3'},
                {data: 'zone_4', name: 'zone_4', class: 'align-middle zone_4'},
                {data: 'zone_5', name: 'zone_5', class: 'align-middle zone_5'},
                {data: 'zone_6', name: 'zone_6', class: 'align-middle zone_6'},
                {data: 'zone_7', name: 'zone_7', class: 'align-middle zone_7'},
                {data: 'zone_8', name: 'zone_8', class: 'align-middle zone_8'},
                {data: 'zone_9', name: 'zone_9', class: 'align-middle zone_9'},
                {data: 'zone_10', name: 'zone_10', class: 'align-middle zone_10'},
                {data: 'zone_11', name: 'zone_11', class: 'align-middle zone_11'}
            ],
            rowCallback: function(row, data, index) {

                var info = table3.page.info();
                $('td:eq(0)', row).html(index + 1 + info.page * info.length);
            },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';


                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.serial_number')) {
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
    </script>
@endsection

