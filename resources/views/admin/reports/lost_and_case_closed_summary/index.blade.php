@extends('admin.layout.master')
@section('title', 'Lost/Case Closed Summary Report')

@section('content')
    <h1 class="mb-1">
        Lost/Case Closed Summary Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="track_form" class="mb-1" novalidate="novalidate">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" name="tracking_numbers" id="tracking_number"
                                    class="dt_search tracking_numbers" placeholder="Tracking Number(s)"
                                    data-tags-input-name="tracking_number">
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Defaulter Name</th>
                            <th class="border-primary border-darken-1">Trax Id</th>
                            <th class="border-primary border-darken-1">Active or Inactive Status</th>
                            <th class="border-primary border-darken-1">Responsible City</th>
                            <th class="border-primary border-darken-1">Shipment Last Status</th>
                            <th class="border-primary border-darken-1">Lost Requested By</th>
                            <th class="border-primary border-darken-1">Approved By</th>
                            <th class="border-primary border-darken-1">COD Amount</th>
                            <th class="border-primary border-darken-1">Parcel Value</th>
                            <th class="border-primary border-darken-1">Case Closed Remarks</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">


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

        .table>thead>tr>th {
            width: calc(1500px / 5);
            text-align: left;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
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

        .selectize-control {
            width: 100%;
        }

        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }

        .green-row {
            background-color: #90ee90;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/tags/tagging.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
            if (this.context.length) {
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.reports.lost_and_case_closed_summary_report.list') }}',
                    data: function(d) {
                        d.tracking_numbers = $('#tracking_number').val();
                    },
                    data: params,
                    success: function(result) {
                        head = [];
                        head.push('S.No');
                        head.push('Tracking .No');
                        head.push('Shipper Name');
                        head.push('Defaulter Name');
                        head.push('Trax Id');
                        head.push('Active or Inactive Status');
                        head.push('Responsible City');
                        head.push('Shipment Last Status');
                        head.push('Lost Requested By');
                        head.push('Approved By');
                        head.push('COD Amount');
                        head.push('Parcel Value');
                        head.push('Case Closed Remarks');

                        $.each(result.data, function(index, values) {
                            row = [];
                            row.push(index + 1);
                            row.push(values.shipment_tracking_number);
                            row.push(values.shipper_name);
                            row.push(values.defaulter_name);
                            row.push(values.trax_id);
                            row.push(values.employee_status);

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
            buttons: [{
                    extend: 'excel',
                    title: 'Lost/Case Closed Summary Report',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'
            ],
            scrollX: true,
            scrollY: '500px',
            lengthMenu: [
                [50, 100, 500, 1000, -1],
                [50, 100, 500, 1000, 'All']
            ],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            deferLoading: 0,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax: {
                url: '{{ route('admin.reports.lost_and_case_closed_summary_report.list') }}',
                data: function(d) {
                    d.tracking_numbers = $('#tracking_number').val();
                }
            },
            rowId: 'shId',
            order: [
                [1, 'desc']
            ],
            columns: [
                {
                    orderable: false,
                    searchable: false,
                    name: 'serial_number',
                    class: 'align-middle serial_number',
                    targets: 0,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'shipment_tracking_number',
                    name: 'shipment_tracking_number'
                },
                {
                    data: 'shipper_name',
                    name: 'shipper_name'
                },
                {
                    data: 'defaulter_name',
                    name: 'defaulter_name'
                },
                {
                    data: 'trax_id',
                    name: 'trax_id'
                },
                {
                    data: 'employee_status',
                    name: 'employee_status'
                },
                {
                    data: 'responsible_city',
                    name: 'responsible_city'
                },
                {
                    data: 'latest_shipment_status',
                    name: 'latest_shipment_status'
                },
                {
                    data: 'lost_requested_by',
                    name: 'lost_requested_by'
                },
                {
                    data: 'lost_approved_by',
                    name: 'lost_approved_by'
                },
                {
                    data: 'cod_amount',
                    name: 'cod_amount'
                },
                {
                    data: 'parcel_value',
                    name: 'parcel_value'
                },
                {
                    data: 'case_closed_remarks',
                    name: 'case_closed_remarks'
                }
            ],
        });

        //Selectize
        var select = $('#tracking_number').selectize({
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
                if (input.length >= 1 && Math.floor(input) == input && $.isNumeric(input)) {
                    return {
                        value: input,
                        text: input
                    }
                } else {
                    return false;
                }
            },
        });

        $('#track_form').bind('submit', function(e) {
            var tracking_numbers = $('#track_form .tracking_numbers').val();
            if (tracking_numbers != '') {
                table.draw();
            }
            e.preventDefault();
        });
    </script>
@endsection
