@extends('admin.layout.master')

@section('title', 'International Rates Excel Upload')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    International Rates Excel Upload
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="international_rates_upload_form" class="form-horizontal" method="POST" action="{{ route('admin.settings.international_rates.upload.excel') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="file" name="rates" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>

                                    <div class="col ml-auto">
                                        <div class="form-group text-right">
                                            <a href="{{ asset('file/International Standard Rates Upload Template.xlsx') }}?v=26_09_2024" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">Range Up</th>
                                    <th class="border-primary border-darken-1">Range Down</th>
                                      @foreach ($zoneColumnsArray as $key => $value) 
                                        <th><?php echo ucfirst(str_replace('_', ' ', $value)) ?></th>
                                    @endforeach


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
        var zoneColumns = @json($zoneColumnsArray);
        $(document).ready(function() {
            $('#international_rates_upload_form').validate({
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

         jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                    if (this.context.length) {
                        let body = [];
                        let head = ['S.No', 'Range Up', 'Range Down']; // Default columns

                        var params = table.ajax.params();
                        params.start = 0;
                        params.length = -1;

                        var jsonResult = $.ajax({
                            url: '{{ route('admin.settings.international_rates.upload.list') }}',
                            data: params,
                            async: false, // ✅ Corrected placement of async
                            success: function (result) {
                                if (result.data.length > 0) {
                                    // Dynamically extract zone columns from the first result row
                                    let firstRow = result.data[0];
                                    let zoneColumns = Object.keys(firstRow).filter(key => key.startsWith("zone_"));

                                    // Add dynamically found zone columns to the header
                                    head.push(...zoneColumns.map(zone => zone.replace('_', ' ').toUpperCase()));

                                    // Process data rows
                                    $.each(result.data, function (index, values) {
                                        let row = [];

                                        row.push(index + 1); // Serial number
                                        row.push(values.range_up);
                                        row.push(values.range_down);

                                        // Add dynamic zone values
                                        zoneColumns.forEach(zone => {
                                            row.push(values[zone]);
                                        });

                                        body.push(row);
                                    });
                                }
                            }
                        });

                        return { body: body, header: head };
                    }
            });

        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: true, scrollY: '500px',
            buttons: [
                {
                    extend: 'excel',
                    title: 'International Standard DHL Rates',
                    className:'btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },

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
            ajax: '{{ route('admin.settings.international_rates.upload.list') }}',
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'range_up', name: 'range_up', class: 'align-middle range_up'},
                {data: 'range_down', name: 'range_down', class: 'align-middle range_down'},
            ].concat(zoneColumns.map(zone => ({
                    data: zone,
                    name: zone,
                    class: 'align-middle ' + zone
                }))),
            rowCallback: function(row, data, index) {

                var info = table.page.info();
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