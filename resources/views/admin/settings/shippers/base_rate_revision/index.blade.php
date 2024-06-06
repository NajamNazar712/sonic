@extends('admin.layout.master')

@section('title', 'Base Rate Revision')

@section('content')
    <h1 class="mb-1">
        Base Rate Revision
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row upload_shippers_form_div px-1" style="display: none">
                    <form id="upload_shippers_form" class="form-horizontal w-100 p-2" method="POST" action="{{ route('admin.settings.shippers.base_rate_revisions.bulk_store') }}" novalidate="novalidate" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="file" name="shippers" class="w-100 border-primary rounded" style="padding: 6px" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="adjustment_type" id="adjustment_types" data-rule-required="true" data-msg-required="Rate Type is required">
                                        <option disabled selected>Select Rate Type</option>
                                        @foreach ($baseRateTypes as $baseRateType)
                                        <option value="{{$baseRateType->id}}">{{$baseRateType->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary mb-2">Upload</button>
                                </div>
                            </div>

                            <div class="col-md-2 justify-content-end">
                                <div class="form-group text-right">
                                    <a href="{{ asset('file/Base Rate Revisions Template.xlsx') }}" class="btn btn-primary btn-block"><i class="la la-download"></i> Download Template</a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">No of Shippers</th>
                            <th class="border-primary border-darken-1">Rate Apply For</th>
                            <th class="border-primary border-darken-1">File</th>
                            <th class="border-primary border-darken-1">Added At</th>
                            <th class="border-primary border-darken-1">Added By</th>
                            <th class="border-primary border-darken-1">Approved/Rejected By</th>
                            <th class="border-primary border-darken-1">Approved/Rejected At</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Approved/Rejected By Finance</th>
                            <th class="border-primary border-darken-1">Approved/Rejected At (Finance)</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- call history log modal --}}
    <div class="modal fade" id="shipperModal" role="dialog" aria-labelledby="shipperModal_title"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title font-weight-bold">Shippers With Rate Changes <span class="text-muted" id="rate_type_modal_heading"></span></h4>
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

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/icheck/icheck.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
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
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
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
                        url: '{{ route('admin.settings.shippers.base_rate_revisions.list') }}',
                        data: params,
                        success: function(result) {
                            head = [];
                            head.push('S.No');
                            head.push('No of Shippers');
                            head.push('Rate Apply For');
                            head.push('Added At');
                            head.push('Added By');
                            head.push('Approved By');
                            head.push('Approved At');
                            head.push('Status');
                            head.push('Approved By Finace');
                            head.push('Approved At Finace');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.shippers_with_rate_change_count);
                                row.push(values.rate_type_id);
                                row.push(values.created_at);
                                row.push(values.added_by_admin);
                                row.push(values.approved1_by_admin);
                                row.push(values.approval1_at);
                                row.push(values.approval1_status);
                                row.push(values.approved2_by_admin);
                                row.push(values.approval2_at);
                                row.push(values.approval2_status);

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
                            $('.upload_shippers_form_div').toggle();
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Base Rate Revision',
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
                ajax: '{{ route('admin.settings.shippers.base_rate_revisions.list') }}',
                rowId: 'id',
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle text-center serial_number',
                        targets: 0,
                        render: function(data, type, row) {
                            return '';
                        }
                    },
                    {
                        data: 'shippers_with_rate_change_count',
                        name: 'shippers_with_rate_change_count',
                        class: 'align-middle text-center shippers_with_rate_change_count',
                    },
                    {
                        data: 'rate_type_id',
                        name: 'rate_type_id',
                        class: 'align-middle text-center rate_type_id',
                        orderable: false,
                    },
                    {
                        data: 'file_view',
                        name: 'file_view',
                        class: 'align-middle text-center file_view',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        class: 'align-middle text-center created_at',
                    },
                    {
                        data: 'added_by_admin',
                        name: 'addedByAdmin.name',
                        class: 'align-middle text-center added_by_admin',
                        orderable: false,
                    },
                    {
                        data: 'approved1_by_admin',
                        name: 'approved1_by_admin',
                        class: 'align-middle text-center approved1_by_admin',
                        orderable: false,
                    },
                    {
                        data: 'approval1_at',
                        name: 'approval1_at',
                        class: 'align-middle text-center approval1_at',
                        orderable: false,
                    },
                    {
                        data: 'approval1_status',
                        name: 'approval1_status',
                        class: 'align-middle text-center approval1_status',
                        orderable: false,
                    },
                    {
                        data: 'approved2_by_admin',
                        name: 'approved2_by_admin',
                        class: 'align-middle text-center approved2_by_admin',
                        orderable: false,
                    },
                    {
                        data: 'approval2_at',
                        name: 'approval2_at',
                        class: 'align-middle text-center approval2_at',
                        orderable: false,
                    },
                    {
                        data: 'approval2_status',
                        name: 'approval2_status',
                        class: 'align-middle text-center approval2_status',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        class: 'align-middle text-center action',
                        orderable: false,
                        searchable: false
                    }

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                        .appendTo(this.api().table().header());

                    var td =
                        '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input =
                        '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon =
                        '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    var approvalStatuses =
                        '<select name="approvalStatuses" class="select2 form-control approvalStatuses"></select>';

                    var baseRateTypeStatuses =
                        '<select name="base_rate_type_status" id="base_rate_type_status" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.file_view'))
                        {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.rate_type_id')) {
                            $(baseRateTypeStatuses).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);
                        }
                        else if ($(header).is('.approval1_status') || $(header).is('.approval2_status')) {
                            $(approvalStatuses).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);
                        }
                        else
                        {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);
    
                            if (column.search()) {
                                current.val(column.search());
                            }
                        }

                    });

                    //For Approval Statuses
                    var data = $.map({!! $baseRateRevisionApprovalStatuses !!}, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.name;
                            return obj;
                        });

                    $(".approvalStatuses").prepend('<option value="" selected></option>').select2({
                            data: data,
                            placeholder: "Select Status",
                            width: '100%',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        });

                    //for Base Rate Type Status
                    var baseRateTypes = $.map({!! $baseRateTypes !!}, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.name;
                            return obj;
                        });

                    $("#base_rate_type_status").prepend('<option value="" selected></option>').select2({
                            data: baseRateTypes,
                            placeholder: "Select Rate Type",
                            width: '100%',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        });
                    
                    this.api().table().columns.adjust();
                }
            });

            // shipperModal datatable function
            $('#datatable').on('click', '.fileViewButton', function() {
                    var baseRateRevisionId = table.row($(this).parents('tr')).data().id;
                    var rateType = table.row($(this).parents('tr')).data().rate_type_id;

                    $('#shipperModal .modal-body').html('');
                    $('#shipperModal').modal('show');
                    $('#rate_type_modal_heading').html('('+rateType+')');

                    $.ajax({
                            url: '{{ route('admin.settings.shippers.base_rate_revisions.shippers_with_rates', ':id') }}'.replace(':id', baseRateRevisionId),
                        })
                        .done(function(response) {
                            if (response) {
                                var modalBody = $('#shipperModal .modal-body');
                                modalBody.html('');
                                
                                var tableHtml =
                                    '<table id="shippersWithRateChangeTable" class="table-striped table-bordered" style="width:100%">';
                                tableHtml +=
                                    '<thead class="text-center"><tr><th class="p-1">Shipper Id</th><th class="p-1">Shipper Name</th><th>Rate Change (%)</th></tr></thead>';
                                    tableHtml += '<tbody class="text-center">';
                                $.each(response.data, function(index, value) {
                                    var shipperId = value.shipper_id;
                                    var shipperName = value.shipper.name;
                                    var rateChangePercent = value.rate_change_percent;
                                    
                                    tableHtml += 
                                    '<tr><td class="p-1">' + shipperId +
                                    '</td><td class="p-1">' + shipperName +
                                    '</td><td>' + rateChangePercent + '</td></tr>';
                                });

                                tableHtml += '</tbody></table>';

                                modalBody.append(tableHtml);

                                $('#shipperModal').modal('show');
                            }
                    });
            });

        });
    </script>
@endsection
