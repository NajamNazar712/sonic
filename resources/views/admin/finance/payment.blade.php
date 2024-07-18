@extends('admin.layout.master')

@section('title', 'Make Payments')

@section('content')

    <div class="content-body">
        <h1 class="mb-1">
            Make Payments
        </h1>
        <div class="card">
            <div class="card-content" aria-expanded="true">
                <div class="card-body">
                    @include('admin.inc.messages')
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span
                                            class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                    </div>
                                    <input type="text" name="requested_from_date"
                                        class="form-control bg-primary border-primary white rounded-right"
                                        id="requested_from_date" placeholder="Requested Date From">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span
                                            class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                    </div>
                                    <input type="text" name="requested_to_date"
                                        class="form-control bg-primary border-primary white rounded-right"
                                        id="requested_to_date" placeholder="Requested Date To">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group input-group" style="margin-top: -20px">
                                    <button type="button" id="search_filter_btn"
                                        class="float-right mb-1 mt-2 btn btn-outline-primary btn-min-width">
                                        <i class="la la-search" style="margin-right: 10px"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered datatable" id="make_payments_datatable" style="z-index: 3;">
                            <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Account Type</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Shipment</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Type</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Delivery / Return Datetime</th>
                                    <th class="border-primary border-darken-1">Aging</th>
                                    <th class="border-primary border-darken-1">Amount</th>
                                    <th class="border-primary border-darken-1">Charges</th>
                                    <th class="border-primary border-darken-1">GST</th>
                                    <th class="border-primary border-darken-1">WHT</th>
                                    <th class="border-primary border-darken-1">Fintech Charges</th>
                                    <th class="border-primary border-darken-1">Packing Charges</th>
                                    <th class="border-primary border-darken-1">Deductible</th>
                                    <th class="border-primary border-darken-1">Payable</th>
                                    <th class="border-primary border-darken-1">Faf Charges</th>
                                    <th class="border-primary border-darken-1">Arrival Date</th>
                                    {{-- <th class="border-primary border-darken-1">Action</th> --}}
                                    {{-- <th class="border-primary border-darken-1"></th> --}}
                                    {{-- <th class="border-primary border-darken-1"></th> --}}
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <form id="make_payments_form" class="form-inline my-1 justify-content-center" novalidate="novalidate"
                        method="POST" action="{{ route('admin.finance.make_payments.make_payments_store_new') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="pending_payment_shipment_ids" class="pending_payment_shipment_ids">

                        <div class="readonly_div">
                            <div class="row mt-3 readonly_section">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Amount</label>
                                        <input type="text" name="total_amount"
                                            class="form-control text-center total_amount" placeholder="Total Amount"
                                            readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Charges</label>
                                        <input type="text" name="total_charges"
                                            class="form-control text-center total_charges" placeholder="Total Charges"
                                            readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total GST</label>
                                        <input type="text" name="total_gst" class="form-control text-center total_gst"
                                            placeholder="Total GST" readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total IBFT Charges</label>
                                        <input type="text" name="total_ibft" class="form-control text-center total_ibft"
                                            placeholder="Total IBFT" readonly="readonly">
                                    </div>
                                </div>
                                <div class="col-2 mt-2">
                                    <div class="form-group">
                                        <fieldset class="form-group">
                                            <select name="company_bank_id" id="company_bank"
                                                class="form-control select2 company_bank"
                                                data-rule-required="true"
                                                data-msg-required="Bank is required">
                                                @foreach ($company_banks as $bank)
                                                    <option value="{{ $bank->id }}">
                                                        {{ $bank->name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3 readonly_section">
                                <div class="col">
                                    <div class="form-group">
                                        <label>WHT(3%)</label>
                                        <input type="text" name="wht" class="form-control text-center wht"
                                            placeholder="With Holding Tax" readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Deductible</label>
                                        <input type="text" name="total_deductable"
                                            class="form-control text-center total_deductable"
                                            placeholder="Total Deductable" readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Payable</label>
                                        <input type="text" name="total_payable"
                                            class="form-control text-center total_payable" placeholder="Total Payable"
                                            readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Hold</label>
                                        <input type="text" name="total_hold"
                                            class="form-control text-center total_hold" placeholder="Total Hold"
                                            readonly="readonly">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" name="make_invoice" onclick="verify_make_invoice_payments()"
                                class="btn btn-primary mr-2 make_invoice">Corporate Invoice for Negative Payable</button>
                            <button type="submit" name="make" class="btn btn-primary make">Make & Export Bank
                                Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">

    <style>
        .card-body {
            overflow-x: auto;
        }

        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
        }

        .readonly_section {
            width: 110rem;
        }

        .readonly_div {
            margin: 0px 0px 40px 0px;
        }
    </style>

@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

            $('#requested_from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#requested_to_date').pickadate('picker').set('min', $('#requested_from_date')
                            .pickadate('picker').get('select'));
                    }
                }
            });

            $('#requested_to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date')
                            .pickadate('picker').get('select'));
                    }
                }
            });

            $('#make_payments_form button.make').prop('disabled', true);
            $('#make_payments_form button.make_invoice').prop('disabled', true);
            $('#make_payments_form button.export_bank_order').prop('disabled', true);

                window.addEventListener('message', function(event) {
                var selectedData = event.data;
                var ids = selectedData;

                $('#make_payments_form .total_amount').val(0);
                $('#make_payments_form .total_charges').val(0);
                $('#make_payments_form .total_gst').val(0);
                $('#make_payments_form .total_deductable').val(0);
                $('#make_payments_form .total_payable').val(0);
                $('#make_payments_form .total_hold').val(0);
                $('#make_payments_form .wht').val(0);

                var total_payable_amt = 0;
                var shipper_cap = @json($shipper_cap);
                var shipper_limit = parseFloat(shipper_cap.setting_value).toFixed(2);

                var selected_rows = [];
                var selected_rows_shipments = [];
                var initial_total_hold = 0;
                var initial_ibft_charges = 0;

                //Make Payment Modal Datatable
                var make_payments_table = $('#make_payments_datatable').DataTable({
                    dom: '<"pull-right"B>tr',
                    buttons: [
                        {
                            text: 'Export Selected',
                            className: 'export_selected',
                            action: function(e) {
                                e.preventDefault();
                                if (selected_rows_shipments.length != 0) {
                                    window.open('{!! route('admin.finance.make_payments.make_payments_shipment_export_selected_new') !!}?ids=' +
                                        selected_rows_shipments, '_blank');
                                }
                            }
                        },

                        {
                            extend: 'selectAll',
                            text: 'Select All',
                            className: 'select_all',
                            action: function(e, dt, node, config) {
                                e.preventDefault();
                                dt.rows({ search: 'applied' }).select();
                                dt.rows({ search: 'applied', selected: true }).every(function() {
                                    var $row = $(this.node());
                                    var $checkbox = $row.find('td.select-checkbox input[type="checkbox"]');
                                    if ($checkbox.length > 0) {
                                        $checkbox.prop('checked', true);
                                        $row.addClass('selected bg-primary bg-lighten-5 primary');
                                        $checkbox.parent().addClass('selected');
                                    } else {
                                        $row.nextUntil(':not(.details-row)').find('td.select-checkbox input[type="checkbox"]')
                                            .prop('checked', true);
                                        $row.nextUntil(':not(.details-row)').addClass('selected bg-primary bg-lighten-5 primary');
                                        $row.nextUntil(':not(.details-row)').find('input[type="checkbox"]').parent().addClass('selected');
                                    }
                                    calculation($row);
                                });

                                shipper_limit = parseFloat(shipper_limit).toFixed(2);
                                total_payable_amt = total_payable_amt.toFixed(2);
                                if (total_payable_amt > shipper_limit) {
                                    scan_sound(2);
                                    toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                    $('#make_payments_form button.make').prop('disabled', true);
                                    $('#make_payments_form button.export_bank_order').prop('disabled', true);
                                }
                                if (total_payable_amt < 0) {
                                    $('#make_payments_form button.make_invoice').prop('disabled', true);
                                }
                                check_reimbursement();
                            }
                        },

                        // {
                        //     extend: 'selectNone',
                        //     text: 'Select None',
                        //     className: 'select_none',
                        //     action: function(e, dt, node, config) {
                        //         e.preventDefault();
                        //         dt.rows({ selected: true }).deselect();
                        //         dt.rows().every(function() {
                        //             var $row = $(this.node());
                        //             $row.removeClass('selected bg-primary bg-lighten-5 primary');
                        //             $row.find('td.select-checkbox input[type="checkbox"]').prop('checked', false);
                        //             $row.find('td.select-checkbox').removeClass('selected');
                        //             // Handle details rows (dynamically added)
                        //             $row.nextUntil(':not(.details-row)').removeClass('selected bg-primary bg-lighten-5 primary');
                        //             $row.nextUntil(':not(.details-row)').find('td.select-checkbox input[type="checkbox"]').prop('checked', false);
                        //             $row.nextUntil(':not(.details-row)').find('td.select-checkbox').removeClass('selected');
                        //             calculation($row);
                        //         });
                        //         check_reimbursement();
                        //     }
                        // }

                        {
                            extend: 'selectNone',
                            text: 'Select None',
                            className: 'select_none',
                            action: function(e) {
                                e.preventDefault();
                                make_payments_table.rows().nodes().each(function(index) {
                                    var row = make_payments_table.row(index);
                                    if ($(row.node().firstChild).hasClass('select-checkbox') &&
                                        $(row.node()).hasClass('selected')) {
                                        row.deselect();
                                        var parent = $(row.node());
                                        calculation(parent);
                                    }
                                });
                                check_reimbursement();
                            }
                        }

                    ],
                    scrollX: true,
                    paging: false,
                    select: {
                        info: false,
                        style: 'multi',
                        selector: 'td.select-checkbox',
                        className: 'selected bg-primary bg-lighten-5 primary'
                    },
                    processing: true,
                    language: {
                        processing: data_table_loader
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.finance.make_payments.payment_list') }}',
                        data: function(d) {
                            d.ids = ids;
                            d.requested_from_date = $(
                                'input[name="requested_from_date_formatted"]').val();
                            d.requested_to_date = $('input[name="requested_to_date_formatted"]')
                                .val();
                        }
                    },
                    rowId: 'id',
                    order: [
                        [3, 'desc']
                    ],
                    columns: [
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            class: 'text-center align-middle select select-checkbox p-1',
                            targets: 0,
                            render: function(data, type, row) {
                                return '';
                            }
                        },
                        {
                            data: 'serial_number',
                            orderable: false,
                            searchable: false,
                            name: 'serial_number',
                            class: 'align-middle serial_number',
                            targets: 1,
                            render: function(data, type, row) {
                                return '';
                            }
                        },
                        {
                            data: 'account_type_id',
                            name: 'u.account_type_id',
                            class: 'align-middle text-center account_type_id',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'shipper',
                            name: 'u.name',
                            class: 'align-middle shipper',
                        },
                        {
                            data: 'shipment',
                            name: 's.tracking_number',
                            class: 'align-middle shipment'
                        },
                        {
                            data: 'origin',
                            name: 'oc.name',
                            class: 'align-middle origin'
                        },
                        {
                            data: 'type',
                            name: 'type',
                            class: 'align-middle type'
                        },
                        {
                            data: 'status',
                            name: 'ss.name',
                            class: 'align-middle status'
                        },
                        {
                            data: 'created_at',
                            name: 'pending_payment_shipments.created_at',
                            class: 'align-middle created_at'
                        },
                        {
                            data: 'aging',
                            name: 'aging',
                            class: 'align-middle aging',
                            orderable: false
                        },
                        {
                            data: 'amount',
                            name: 'pending_payment_shipments.amount',
                            class: 'align-middle amount'
                        },
                        {
                            data: 'charges',
                            name: 'pending_payment_shipments.charges',
                            class: 'align-middle charges'
                        },
                        {
                            data: 'gst',
                            name: 'pending_payment_shipments.gst',
                            class: 'align-middle gst'
                        },
                        {
                            data: 'wht',
                            name: 'pending_payment_shipments.wht',
                            class: 'align-middle wht'
                        },
                        {
                            data: 'fintech_charges',
                            name: 'fintech_charges',
                            class: 'align-middle fintech_charges'
                        },
                        {
                            data: 'packaging_charges',
                            name: 'packaging_charges',
                            class: 'align-middle packaging_charges'
                        },
                        {
                            data: 'deductable',
                            name: 'deductable',
                            class: 'align-middle deductable'
                        },
                        {
                            data: 'payable',
                            name: 'pending_payment_shipments.payable',
                            class: 'align-middle payable'
                        },
                        {
                            data: 'faf_charges',
                            name: 'sac.faf_charges',
                            class: 'align-middle faf_charges'
                        },
                        {
                            data: 'arrival_date',
                            name: 'sj.created_at',
                            class: 'align-middle arrival_date'
                        }
                        
                        // {
                        //     data: 'shipper_id',
                        //     name: 'u.id',
                        //     class: 'align-middle shipper_id d-none',
                        //     searchable: false
                        // },

                        // // Accordion button
                        // {
                        //     class: 'align-middle shipper_id',
                        //     render: function(data, type, row) {
                        //         return '<button type="button" class="btn btn-sm btn-primary view-details" data-id="' + row.id + '" data-shipper="' + row.shipper + '">View Details</button>';
                        //     }
                        // },
                    ],

                    rowCallback: function(row, data, index) {
                        $('td:eq(1)', row).html(index + 1);
                        if (selected_rows_shipments.length != 0) {
                            if ($.inArray(data.id, selected_rows_shipments) !== -1) {
                                make_payments_table.row(row).select();
                            }
                        }
                    },
                    initComplete: function() {
                        var search = $(
                                '<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                            .appendTo(this.api().table().header());

                        var td =
                            '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                        var input =
                            '<input type="text" class="form-control form-control-sm input-sm primary">';
                        var icon =
                            '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                        var drop_select =
                            '<select name="status_select" id="status_select" class="select2 form-control">' +
                            '<option value="4">All</option>' +
                            '<option value="0">Delivered</option>' +
                            '<option value="1">Returned</option>' +
                            '<option value="2">Adjusted</option>' +
                            '<option value="3">Arrival</option>' +
                            '</select>';
                        this.api().columns().every(function(column_id) {
                            var column = this;
                            var header = column.header();

                            if ($(header).is('.select') || $(header).is(
                                    '.serial_number') || $(
                                    header).is('.aging')) {
                                $(td).appendTo($(search));
                            } else if ($(header).is('.type')) {
                                $(drop_select).appendTo($(search))
                                    .on('change', function() {
                                        column.search($(this).val(), false, false,
                                            true).draw();
                                    }).wrap(td);
                            } else {
                                var current = $(input).appendTo($(search)).on('change',
                                    function() {
                                        column.search($(this).val(), false, false,
                                            true).draw();
                                    }).wrap(td).after(icon);

                                if (column.search()) {
                                    current.val(column.search());
                                }
                            }
                        });
                        $("#status_select").prepend('<option value="" selected></option>')
                            .select2({
                                placeholder: "Select Type",
                                width: '100%',
                                containerCssClass: 'select-xs',
                                dropdownCssClass: 'form-control-sm p-0'
                            });
                        this.api().table().columns.adjust();
                    },

                    drawCallback: function() {
                        var api = this.api();
                        var userRowCount = {};
                        api.rows().every(function(rowIdx, tableLoop, rowLoop) {
                            var userId = this.data().shipper_id;
                            userRowCount[userId] = (userRowCount[userId] || 0) + 1;
                            $(this.node()).show();
                        });

                        $('#make_payments_datatable tbody').on('click', 'tr.details-control', function() {
                            var tr = $(this).prev('tr');
                            var row = api.row(tr);
                            var userId = row.data().shipper_id;

                            api.rows().every(function() {
                                if (this.data().shipper_id === userId) {
                                    $(this.node()).toggle();
                                }
                            });
                            $(this).remove();
                        });

                        if (selected_rows_shipments.length == 0) {
                            initial_total_hold = api.column('.payable').data().reduce(function(a, b) {
                                return parseFloat(a.toString().replace(/,/g, '')) + parseFloat(b.toString().replace(/,/g, ''));
                            }, 0);

                            $('#make_payments_form .total_hold').val(parseFloat(initial_total_hold).toFixed(2));
                        }
                    }

                });
                //End Make Payment Modal Datatable

                $.ajax({
                    url: '{{ route('admin.finance.make_payments.fetch_shipper_ibft_charges_new') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        selected_shippers_id: ids
                    },
                    success: function(response) {
                        var charges = response;
                        $('#make_payments_form .total_ibft').val(charges);
                    }
                });

                // $('#make_payments_datatable').on('click', '.details-row td.select-checkbox', function(event) {
                //     var $checkbox = $(this);
                //     var isChecked = $checkbox.hasClass('selected');
                //     var $row = $checkbox.closest('tr');
                //     $checkbox.toggleClass('selected', !isChecked);

                //     $row.find('td').toggleClass('bg-primary bg-lighten-5 primary', !isChecked);
                //     $row.toggleClass('selected', !isChecked);

                //     if (!isChecked) {
                //         var make_payments_table = $('#make_payments_datatable').DataTable();
                //         var parent = $row;
                //         var selected_id = $row.attr('id');
                //         var con_id = parseInt($row.attr('consolidation_id'));
                //         if (con_id) {
                //             var total_payable_amt = 0;
                //             var shipper_limit = 0;
                //             make_payments_table.rows().nodes().each(function(index) {
                //                 var row = make_payments_table.row(index);
                //                 var consolidation_id = $(row.node()).attr('consolidation_id');
                //                 if (con_id === consolidation_id && $(row.node()).find('.select-checkbox').hasClass('selected')) {
                //                     total_payable_amt += parseFloat($(row.node()).find('.payable').text());
                //                 }
                //             });
                //             if (total_payable_amt > shipper_limit) {
                //                 scan_sound(2);
                //                 toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {
                //                     positionClass: 'toast-top-center',
                //                     containerId: 'toast-top-center'
                //                 });
                //                 $('#make_payments #make_payments_form button.make').prop('disabled', true);
                //                 $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
                //             }
                //             if (total_payable_amt < 0) {
                //                 $('#make_payments #make_payments_form button.make_invoice').prop('disabled', false);
                //             }
                //         }
                //         check_reimbursement();
                //     }
                // });

                // // Click event for 'View Details' button
                // $('#make_payments_datatable').on('click', '.view-details', function(event) {
                //     var shipper = $(this).closest('tr').find('.shipper').text();
                //     var api = make_payments_table;
                //     var $clickedRow = $(this).closest('tr');

                //     // Remove any existing details rows if they exist
                //     if ($(api.row($clickedRow).node()).next().hasClass('details-row')) {
                //         $(api.row($clickedRow).node()).nextUntil(':not(.details-row)').remove();
                //         return;
                //     }

                //     $.ajax({
                //         url: '{{ route('admin.finance.make_payments.payment_list_remaining') }}',
                //         method: 'GET',
                //         data: { ids: ids },
                //         success: function(response) {
                //             var detailsHtml = '';
                //             // Flag to skip the first iteration
                //             var firstItemSkipped = false;

                //             response.data.forEach(function(item, index) {
                //                 if (index === 0) {
                //                     // Skip the first item
                //                     return;
                //                 }
                //                 if (item.shipper == shipper) {
                //                     detailsHtml += '<tr consolidation_id class="details-row" id="' + item.id + '">';
                //                     detailsHtml += '<td class="select-checkbox"></td>';
                //                     detailsHtml += '<td></td>';
                //                     detailsHtml += '<td class="account_type">' + item.account_type + '</td>';
                //                     detailsHtml += '<td class="shipper">' + item.shipper + '</td>';
                //                     detailsHtml += '<td class="shipment">' + item.shipment + '</td>';
                //                     detailsHtml += '<td class="origin">' + item.origin + '</td>';
                //                     detailsHtml += '<td class="type_text">' + item.type_text + '</td>';
                //                     detailsHtml += '<td class="status">' + item.status + '</td>';
                //                     detailsHtml += '<td class="created_at">' + item.created_at + '</td>';
                //                     detailsHtml += '<td class="aging">' + item.aging + '</td>';
                //                     detailsHtml += '<td class="amount">' + item.amount + '</td>';
                //                     detailsHtml += '<td class="charges">' + item.charges + '</td>';
                //                     detailsHtml += '<td class="gst">' + item.gst + '</td>';
                //                     detailsHtml += '<td class="wht">' + item.wht + '</td>';
                //                     detailsHtml += '<td class="fintech_charges">' + item.fintech_charges + '</td>';
                //                     detailsHtml += '<td class="packaging_charges">' + item.packaging_charges + '</td>';
                //                     detailsHtml += '<td class="deductable">' + item.deductable + '</td>';
                //                     detailsHtml += '<td class="payable">' + item.payable + '</td>';
                //                     detailsHtml += '<td class="arrival_date">' + item.arrival_date + '</td>';
                //                     detailsHtml += '<td></td>';
                //                     detailsHtml += '<td class="d-none shipper_id">' + item.shipper_id + '</td>';
                //                     detailsHtml += '</tr>';
                //                 }
                //             });
                //             $(api.row($clickedRow).node()).after(detailsHtml);

                //             $('#make_payments_datatable').on('click', '.select-checkbox', function() {
                //                 var $checkboxes = $('#make_payments_datatable').find('.select-checkbox');
                //                 var anyChecked = $checkboxes.hasClass('selected');
                //                 if (anyChecked) {
                //                     $('.select_none').removeClass('disabled');
                //                 } else {
                //                     $('.select_none').addClass('disabled');
                //                 }
                //             });
                //         },
                //         error: function(xhr, status, error) {
                //             console.error('Error fetching details:', error);
                //         }
                //     });
                // });

                var payable_list = [];
                let shipperTotal = {};
                // Make Payments Modal Calculation
                function calculation(parent) {
                    total_payable_amt = 0;
                    var id = parseInt(parent.attr('id'));

                    var payable = parent.children('td.payable').html();
                    var shipper_id = parent.children('td.shipper_id').html();
                    var index = $.inArray(id, selected_rows_shipments);

                    var total_amount_selector = $('#make_payments_form .total_amount');
                    var total_charges_selector = $('#make_payments_form .total_charges');
                    var total_gst_selector = $('#make_payments_form .total_gst');
                    var total_deductable_selector = $('#make_payments_form .total_deductable');
                    var total_payable_selector = $('#make_payments_form .total_payable');
                    var total_hold_selector = $('#make_payments_form .total_hold');
                    var total_wht_selector = $('#make_payments_form .wht');

                    // Row Selected
                    if (index === -1) {
                        selected_rows_shipments.push(id);
                        // payable_list.push(parseFloat(payable),shipper_id); //adding payable in to array payable_list
                        payable_list.push({
                            payable,
                            shipper_id
                        }); //adding payable in to array payable_list
                        calculateShipperTotal(parent);
                        var total_amount = ((total_amount_selector.val() != '') ? parseInt(
                            total_amount_selector
                            .val()) : 0) + ((parent.children('td.amount').html() != '') ? parseInt(
                            parent.children(
                                'td.amount').html().replace(/,/g, '')) : 0);
                        var total_charges = ((total_charges_selector.val() != '') ? parseFloat(
                            total_charges_selector
                            .val()) : 0) + ((parent.children('td.charges').html() != '') ? parseFloat(
                            parent
                            .children('td.charges').html().replace(/,/g, '')) : 0);
                        var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector
                                .val()) : 0) +
                            ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst')
                                .html()
                                .replace(/,/g, '')) : 0);
                        var total_deductable = ((total_deductable_selector.val() != '') ? parseFloat(
                            total_deductable_selector.val()) : 0) + ((parent.children('td.deductable')
                            .html() !=
                            '') ? parseFloat(parent.children('td.deductable').html().replace(/,/g,
                            '')) : 0);
                        var total_wht = ((total_wht_selector.val() != '') ? parseFloat(total_wht_selector
                                .val()) : 0) +
                            ((parent.children('td.wht').html() != '') ? parseFloat(parent.children('td.wht')
                                .html()
                                .replace(/,/g, '')) : 0);
                        var total_payable = ((total_payable_selector.val() != '') ? parseFloat(
                            total_payable_selector
                            .val()) : 0) + ((parent.children('td.payable').html() != '') ? parseFloat(
                            parent
                            .children('td.payable').html().replace(/,/g, '')) : 0);
                        var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector
                                .val()) :
                            0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent
                            .children(
                                'td.payable').html().replace(/,/g, '')) : 0);

                        // if selected row oayblae is less then 0 dipslay error
                        // var payable = $(this).closest('tr').find('td.payable').text();
                        if (payable < 0) {
                            var html = 'Negative payable detected for the shipper(s).<br/>';
                            content = document.createElement('div');
                            content.innerHTML = html;
                            swal({
                                content: content,
                                icon: 'error',
                                buttons: {
                                    cancel: {
                                        text: 'Close',
                                        value: null,
                                        visible: true,
                                        closeModal: true,
                                    }
                                },
                                closeOnClickOutside: false,
                                closeOnEsc: false,
                                dangerMode: true
                            });
                        }
                    }

                    //Row UnSelected
                    else {

                        selected_rows_shipments.splice(index, 1);

                        const removedEntry = payable_list[index]; // Get the entry being removed
                        const shipperId = removedEntry.shipper_id;
                        const payable = parseFloat(removedEntry.payable.replace(/,/g, ''));
                        // Remove the unselected entry from payable_list using splice
                        payable_list.splice(index, 1);
                        // Recalculate shipperTotal after removing the entry
                        shipperTotal = {}; // Reset shipperTotal object
                        calculateShipperTotal(parent);

                        var total_amount = ((total_amount_selector.val() != '') ? parseInt(
                            total_amount_selector
                            .val()) : 0) - ((parent.children('td.amount').html() != '') ? parseInt(
                            parent.children(
                                'td.amount').html().replace(/,/g, '')) : 0);
                        var total_charges = ((total_charges_selector.val() != '') ? parseFloat(
                            total_charges_selector
                            .val()) : 0) - ((parent.children('td.charges').html() != '') ? parseFloat(
                            parent
                            .children('td.charges').html().replace(/,/g, '')) : 0);
                        var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector
                                .val()) : 0) -
                            ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst')
                                .html()
                                .replace(/,/g, '')) : 0);
                        var total_deductable = ((total_deductable_selector.val() != '') ? parseFloat(
                            total_deductable_selector.val()) : 0) - ((parent.children('td.deductable')
                            .html() !=
                            '') ? parseFloat(parent.children('td.deductable').html().replace(/,/g,
                            '')) : 0);
                        var total_payable = ((total_payable_selector.val() != '') ? parseFloat(
                            total_payable_selector
                            .val()) : 0) - ((parent.children('td.payable').html() != '') ? parseFloat(
                            parent
                            .children('td.payable').html().replace(/,/g, '')) : 0);
                        var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector
                                .val()) :
                            0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent
                            .children(
                                'td.payable').html().replace(/,/g, '')) : 0);
                    }

                    if (selected_rows_shipments.length > 0) {

                        total_amount_selector.val(parseInt(total_amount));
                        total_charges_selector.val(parseFloat(total_charges).toFixed(2));
                        total_gst_selector.val(parseFloat(total_gst).toFixed(2));
                        total_deductable_selector.val(parseFloat(total_deductable).toFixed(2));
                        total_wht_selector.val(parseFloat(total_wht).toFixed(2));
                        total_payable_selector.val(parseFloat(total_payable).toFixed(2));
                        total_hold_selector.val(parseFloat(total_hold).toFixed(2));

                        $('#make_payments_form button.make').prop('disabled', false);
                        $('#make_payments_form button.make_invoice').prop('disabled', false);
                        $('#make_payments_form button.export_bank_order').prop('disabled',
                            false);

                        //if total payable is grate than 0 it enable make and export bank order button
                        if ($('#make_payments_form .total_payable').val() > 0) {
                            const isAnyNegative = Object.values(shipperTotal).some(total => total < 0);
                            // Disable make and export bank order button if any shipper's total payable is negative

                            if (isAnyNegative) {
                                $('#make_payments_form button.make').prop('disabled', true);
                                $('#make_payments_form button.export_bank_order').prop('disabled', true);
                                $('#make_payments_form button.make_invoice').prop('disabled', false);
                            } else {
                                $('#make_payments_form button.make').prop('disabled', false);
                                $('#make_payments_form button.export_bank_order').prop('disabled', false);
                            }
                            total_payable_amt += total_payable;
                        } else {
                            $('#make_payments_form button.make').prop('disabled', true);
                            $('#make_payments_form button.export_bank_order').prop('disabled', true);
                        }

                    } else {

                        total_amount_selector.val(0);
                        total_charges_selector.val(0);
                        total_gst_selector.val(0);
                        total_deductable_selector.val(0);
                        total_wht_selector.val(0);
                        total_payable_selector.val(0);
                        total_hold_selector.val(parseFloat(initial_total_hold).toFixed(2));

                        $('#make_payments_form button.make').prop('disabled', true);
                        $('#make_payments_form button.make_invoice').prop('disabled', true);
                        $('#make_payments_form button.export_bank_order').prop('disabled', true);
                    }

                    $('#make_payments_form .pending_payment_shipment_ids').val(
                        selected_rows_shipments);
                }

                $(document).on('click', '.toggle-details', function() {
                    var parentRow = $(this).closest('tr');
                    var userId = parentRow.attr('id').split('-')[1];
                    $('.row-' + userId).toggle();
                    $(this).text($(this).text() === 'Show Details' ? 'Hide Details' : 'Show Details');
                });

                $('#make_payments_datatable tbody').on('click', 'tr td.select-checkbox', function() {
                    var parent = $(this).parent('tr');
                    var selected_id = $(this).parent('tr').attr('id');
                    var con_id = parseInt($(this).parent('tr').attr('consolidation_id'));
                    if (con_id) {
                        var count = 0;
                        make_payments_table.rows().nodes().each(function(index) {
                            var row = make_payments_table.row(index);
                            var consolidation_id = $(row.node()).attr('consolidation_id');
                            var row_id = $(row.node()).attr('id');
                            if (con_id == consolidation_id) {
                                if ($(row.node().firstChild).hasClass('select-checkbox') &&
                                    !$(row
                                        .node()).hasClass('selected')) {
                                    var parent = $(row.node());

                                    calculation(parent);
                                    if (selected_id != row_id) {
                                        row.select();
                                    }
                                    count++;
                                } else {
                                    var parent = $(row.node());

                                    calculation(parent);
                                    if (selected_id != row_id) {
                                        row.deselect();
                                    }
                                    count++;
                                }
                            }

                        });
                        if (total_payable_amt > shipper_limit) {
                            scan_sound(2);
                            toastr.error("Payable amount should be less than shipper Cap!",
                                'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            $('#make_payments #make_payments_form button.make').prop('disabled',
                                true);
                            $('#make_payments #make_payments_form button.export_bank_order').prop(
                                'disabled', true);
                        }
                    }
                    else{
                        calculation(parent);
                        if (total_payable_amt > shipper_limit) {
                            scan_sound(2);
                            toastr.error("Payable amount should be less than shipper Cap!",
                                'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            $('#make_payments #make_payments_form button.make').prop('disabled',
                                true);
                            $('#make_payments #make_payments_form button.export_bank_order').prop(
                                'disabled', true);
                        }
                    }
                    check_reimbursement();
                });

                function check_reimbursement(){
                        setTimeout(function() {
                            var reimbursement_check = false;
                            var make_invoice = true;
                            var rows_selected = false;
                            make_payments_table.rows().nodes().each(function(index) {
                                var row2 = make_payments_table.row(index);

                                if ($(row2.node().firstChild).hasClass('select-checkbox')) {
                                    if($(row2.node()).hasClass('selected')) {
                                        rows_selected = true;
                                        var parent = $(row2.node());
                                        if (parent.children('td.account_type_id').html() == 'Reimbursement') {
                                            reimbursement_check = true;
                                        }
                                        if (parseFloat(parent.children('td.payable').html()) >= 0) {
                                            make_invoice = false;
                                        }
                                    }
                                }

                            });
                            if(!rows_selected){
                                make_invoice = false;
                            }
                            if(reimbursement_check || !make_invoice){
                                $('#make_payments_form button.make_invoice').prop('disabled', true);
                            }else{
                                $('#make_payments_form button.make_invoice').prop('disabled', false);
                            }

                        }, 200);

                    }

                function calculateShipperTotal() {
                    shipperTotal = {}; // Reset shipperTotal object
                    // total_payable_amt = 0; // Reset total_payable_amt
                    payable_list.forEach(entry => {
                        const shipperId = entry.user_id;
                        const payable = parseFloat(entry.payable.replace(/,/g, ''));
                        // total_payable_amt +=payable;
                        if (shipperTotal[shipperId]) {
                            shipperTotal[shipperId] += payable;
                        } else {
                            shipperTotal[shipperId] = payable;
                        }
                    });
                    // if(total_payable_amt > shipper_limit)
                    // {
                    //     calculation(tr);
                    //      var tr_index=$(tr).index();
                    //       setTimeout(() => {
                    //           $("#make_payments_datatable tbody tr").eq(tr_index).removeClass('selected bg-primary bg-lighten-5 primary');
                    //       }, 150);
                    // }
                }

                // Accordion
                function formatAccordion(data) {
                    var html = '<div class="accordion" id="accordionExample">';                    
                    // Iterate through each row of data and display them in a table format
                    html += '<table class="table table-bordered">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<th>ID</th>';
                    html += '<th>Serial Number</th>';
                    html += '<th>Account Type ID</th>';
                    html += '<th>Shipper</th>';
                    html += '<th>Shipment</th>';
                    html += '<th>Origin</th>';
                    html += '<th>Type</th>';
                    html += '<th>Status</th>';
                    html += '<th>Created At</th>';
                    html += '<th>Aging</th>';
                    html += '<th>Amount</th>';
                    html += '<th>Charges</th>';
                    html += '<th>GST</th>';
                    html += '<th>WHT</th>';
                    html += '<th>Fintech Charges</th>';
                    html += '<th>Packaging Charges</th>';
                    html += '<th>Deductable</th>';
                    html += '<th>Payable</th>';
                    html += '<th>Arrival Date</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';
                    
                    // Display data for each row
                    html += '<tr>';
                    html += '<td>' + data.id + '</td>';
                    html += '<td>' + data.serial_number + '</td>';
                    html += '<td>' + data.account_type_id + '</td>';
                    html += '<td>' + data.shipper + '</td>';
                    html += '<td>' + data.shipment + '</td>';
                    html += '<td>' + data.origin + '</td>';
                    html += '<td>' + data.type + '</td>';
                    html += '<td>' + data.status + '</td>';
                    html += '<td>' + data.created_at + '</td>';
                    html += '<td>' + data.aging + '</td>';
                    html += '<td>' + data.amount + '</td>';
                    html += '<td>' + data.charges + '</td>';
                    html += '<td>' + data.gst + '</td>';
                    html += '<td>' + data.wht + '</td>';
                    html += '<td>' + data.fintech_charges + '</td>';
                    html += '<td>' + data.packaging_charges + '</td>';
                    html += '<td>' + data.deductable + '</td>';
                    html += '<td>' + data.payable + '</td>';
                    html += '<td>' + data.arrival_date + '</td>';
                    html += '</tr>';
                    
                    html += '</tbody>';
                    html += '</table>';

                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                    html += '</div>';

                    return html;
                }
            });
        });

        $('#make_payments_form #company_bank').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select Company Bank',
            width: '100%',
            dropdownParent: $('#make_payments_form')
        });

        function verify_make_invoice_payments() {

            swal({
                title: 'Are You Sure?',
                content: content,
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: 'No',
                        value: null,
                        visible: true,
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Yes',
                        value: true,
                        visible: true,
                        closeModal: true
                    }
                },
                closeOnClickOutside: false,
                closeOnEsc: false,
                dangerMode: true
            }).then(function(confirm) {
                if (confirm) {
                    $.ajax({
                        url: '{!! route('admin.finance.make_payments.invoice') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'pending_payment_shipment_ids': $('#make_payments_form .pending_payment_shipment_ids').val()
                        }
                    })
                        .done(function(data) {
                            if(data.status == 1){
                                toastr.success("Invoice Generated", 'Success!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }else{
                                toastr.error("Error!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
            });


        }
    </script>



@endsection
