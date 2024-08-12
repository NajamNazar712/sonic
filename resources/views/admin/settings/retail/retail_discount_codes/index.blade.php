@extends('admin.layout.master')

@section('title', 'Retail Discount Codes')

@section('content')
    <h1 class="mb-1">
        Retail Discount Codes
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row upload_discount_codes_form_div px-1" style="display: none">
                    <form id="upload_shippers_form" class="form-horizontal w-100 p-2" method="POST" action="{{ route('admin.settings.shippers.base_rate_revisions.bulk_store') }}" novalidate="novalidate" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="file" name="shippers" class="w-100 border-primary rounded" style="padding: 6px" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary mb-2">Upload</button>
                                </div>
                            </div>

                            <div class="col-md-2 justify-content-end">
                                <div class="form-group text-right">
                                    <a href="{{ asset('file/Retail Discount Codes Template.xlsx') }}" class="btn btn-primary btn-block"><i class="la la-download"></i> Download Template</a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Code</th>
                            <th class="border-primary border-darken-1">Discount %</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/icheck/icheck.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <style type="text/css">
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

        .danger-text {
            color: red; /* Error message text color */
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/icheck/icheck.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $('#upload_shippers_form').validate({
				errorClass: 'danger-text',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
			});

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.retail.retail_discount_codes.list') }}',
                        data: params,
                        success: function(result) {
                            head = [];
                            head.push('S.No');
                            head.push('Code');
                            head.push('Discount %');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.code);
                                row.push(values.discount_percentage);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {
                        body: body,
                        header: head
                    };
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true,
                scrollY: '500px',
                buttons: [
                    {
                        text: '<i class="la la-file-excel-o"></i> Upload File',
                        className: 'btn btn-secondary',
                        action: function(e, dt, node, config) {
                            $('.upload_discount_codes_form_div').toggle();
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Retail Discount Codes',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary',
                    },
                    'reset'
                ],
                lengthMenu: [
                    [50, 100, 500, 1000, -1],
                    [50, 100, 500, 1000, 'All']
                ],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                autoWidth: false,
                ajax: '{{ route('admin.retail.retail_discount_codes.list') }}',
                rowId: 'id',
                // order: [
                //     [1, 'desc']
                // ],
                columns: [{
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle text-center serial_number',
                        width: '10%',
                        targets: 0,
                        render: function(data, type, row) {
                            return '';
                        }
                    },
                    {
                        searchable: false,
                        data: 'code',
                        name: 'code',
                        class: 'align-middle text-center code',
                    },
                    {
                        searchable: false,
                        data: 'discount_percentage',
                        name: 'discount_percentage',
                        class: 'align-middle text-center discount_percentage',
                        orderable: false,
                    }

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

        });
    </script>
@endsection
