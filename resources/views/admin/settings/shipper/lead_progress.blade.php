@extends('admin.layout.master')

@section('title', 'Lead Progress Setting')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Lead Progress Setting
                </h1>
                {{--{{dd($case_nature)}}--}}

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">Progress ID</th>
                                    <th class="border-primary border-darken-1">Stage</th>
                                    <th class="border-primary border-darken-1">Trigger</th>
                                    <th class="border-primary border-darken-1">Percent</th>
                                    <th class="border-primary border-darken-1">Color</th>
                                    <th class="border-primary border-darken-1">Updated_at</th>
                                    <th class="border-primary border-darken-1">Updated_by</th>
                                    <th class="border-primary border-darken-1">Action</th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="colorModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="colorModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Color Percentage Setting</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="color_percent_form" method="post" action="{{ route('admin.settings.shippers.lead_progress.update') }}">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="form-group row align-items-center">

                                <input type="hidden" name="id" id="id">
                                <label for="colorPicker" class="col-md-4 col-form-label">Color:</label>
                                <div class="col-md-6">
                                    <input type="color" id="colorHex" class="form-control" value="1" name="colorHex">
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="percent" class="col-md-4 col-form-label">Percent:</label>
                                <div class="col-md-6">
                                    <input type="text" id="percent" class="form-control" name="percent" value="1">
                                </div>
                                <div class="col-md-2">
                                    <span class="opacity-percent">%</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="color_percent_form" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>



    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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

        .modal-header.bg-primary {
            background-color: #007bff;
            color: #fff;
        }

        .modal-header .close {
            color: #fff;
            opacity: 1;
        }

        .modal-header .close:hover {
            color: #ddd;
        }

        .modal-body {
            padding: 30px;
        }

        .container {
            max-width: 600px;
            margin: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
        }

        #colorPicker {
            width: 100%;
            height: 45px;
            border: 2px solid #ddd;
            border-radius: 5px;
        }

        #hexValue {
            background-color: #f8f9fa;
            border: 2px solid #ddd;
            border-radius: 5px;
        }

        #opacityInput {
            width: 100%;
            border: 2px solid #ddd;
            border-radius: 5px;
        }

        .opacity-percent {
            display: inline-block;
            line-height: 38px;
            font-weight: bold;
        }

        .modal-dialog {
            margin-top: 100px;
        }

        .modal-content {
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .modal-dialog {
                width: 100%;
                margin: 10px;
            }
            .container {
                width: 100%;
            }
        }



    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.shippers.lead_progress.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Stage');
                            head.push('Trigger');
                            head.push('Percent');
                            head.push('Color');
                            head.push('Updated_at');
                            head.push('Updated_By');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.stage);
                                row.push(values.trigger);
                                row.push(values.percent);
                                row.push(values.color);
                                row.push(values.updated_at);
                                row.push(values.updated_by);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                language: {
                    processing: data_table_loader
                },
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.settings.shippers.lead_progress.list') }}',
                order: [[0, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'stage', name: 'lead_progress_settings.stage', class: 'align-middle name'},
                    {data: 'trigger', name: 'lead_progress_settings.trigger', class: 'align-middle name'},
                    {data: 'percent', name: 'lead_progress_settings.percent', class: 'align-middle percent'},
                    {data: 'color', name: 'lead_progress_settings.color', class: 'align-middle color'},
                    {data: 'updated_at', name: 'lead_progress_settings.updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
                    {data: 'action', name: 'action', class: 'align-middle action'},


                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    $('td:eq(4)', row).css('background-color', data.color); // Assuming 'color' contains a valid color value

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

            $(document).on('click', '.edit_color_percent', function(){
                var id = $(this).data('id');
                var color = $(this).data('color');
                var percent = $(this).data('percent');
                $('#colorHex').val(color);
                $('#percent').val(percent);
                $('#id').val(id);

                $('#colorModal').modal('show')

            });

        });

    </script>
@endsection