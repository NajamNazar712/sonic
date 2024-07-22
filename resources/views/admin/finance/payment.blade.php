@extends('admin.layout.master')

@section('title', 'Make Payments')

@section('content')

    <div class="content-body">
        <h1 class="mb-1">
            Make Payments
        </h1>
        <div class="card">
            <div class="card-content" aria-expanded="true">
                <div class="card-body" id="make_payments">
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
                                <div class="form-group input-group" style="margin-top: -20px ">
                                    <button type="button" id="search_filter_btn"
                                            class="float-right mb-1 mt-2 btn btn-outline-primary btn-min-width"><i
                                                class="la la-search" style="margin-right: 10px"></i>
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered datatable" id="make_payments_datatable"
                               style="z-index: 3;">
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
                                <th class="border-primary border-darken-1">Delivery / Return
                                    Datetime</th>
                                <th class="border-primary border-darken-1">Aging</th>
                                <th class="border-primary border-darken-1">Amount</th>
                                <th class="border-primary border-darken-1">Charges</th>
                                <th class="border-primary border-darken-1">GST</th>
                                <th class="border-primary border-darken-1">WHT</th>
                                <th class="border-primary border-darken-1">SMS Charges</th>
                                <th class="border-primary border-darken-1">Fintech Charges</th>
                                <th class="border-primary border-darken-1">Packing Charges</th>
                                <th class="border-primary border-darken-1">Deductible</th>
                                <th class="border-primary border-darken-1">Payable</th>
                                <th class="border-primary border-darken-1">Faf Charges</th>
                                <th class="border-primary border-darken-1">Arrival Date</th>
                                <th class="d-none">Shipper</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <form id="make_payments_form"
                          class="form-inline mt-1 mb-1 justify-content-center"
                          novalidate="novalidate" method="POST"
                          action="{{ route('admin.finance.make_payments.store') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="pending_payment_shipment_ids"
                               class="pending_payment_shipment_ids">

                        <div class="col-2">
                            <div class="form-group">
                                <label class="mx-auto">Total Amount</label>
                                <input type="text" name="total_amount"
                                       class="form-control text-center total_amount"
                                       placeholder="Total Amount" readonly="readonly">
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="form-group">
                                <label class="mx-auto">Total Charges</label>
                                <input type="text" name="total_charges"
                                       class="form-control text-center total_charges"
                                       placeholder="Total Charges" readonly="readonly">
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="form-group">
                                <label class="mx-auto">Total GST</label>
                                <input type="text" name="total_gst"
                                       class="form-control text-center total_gst"
                                       placeholder="Total GST" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label class="mx-auto">Total IBFT Charges</label>
                                <input type="text" name="total_ibft"
                                       class="form-control text-center total_ibft"
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

                        <div class="w-100 mt-2"></div>

                        <div class="col-2">
                            <div class="form-group">
                                <label class="mx-auto">WHT(3%)</label>
                                <input type="text" name="wht"
                                       class="form-control text-center wht"
                                       placeholder="With Holding Tax" readonly="readonly">
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="form-group">
                                <label class="mx-auto">Total Deductible</label>
                                <input type="text" name="total_deductable"
                                       class="form-control text-center total_deductable"
                                       placeholder="Total Deductable" readonly="readonly">
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="form-group">
                                <label class="mx-auto">Total Payable</label>
                                <input type="text" name="total_payable"
                                       class="form-control text-center total_payable"
                                       placeholder="Total Payable" readonly="readonly">
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="form-group">
                                <label class="mx-auto">Total Hold</label>
                                <input type="text" name="total_hold"
                                       class="form-control text-center total_hold"
                                       placeholder="Total Hold" readonly="readonly">
                            </div>
                        </div>

                        <div class="w-100"></div>
                        <hr>

                        <button type="button" class="mr-auto btn btn-secondary"
                                data-dismiss="modal">Close</button>
                        <button type="button" name="make_invoice" onclick="verify_make_invoice_payments()"
                                class="mr-1 btn btn-primary make_invoice">Corporate Invoice for Negative Payable</button>
                        <button type="submit" name="make"
                                class="mr-1 btn btn-primary make">Make & Export Bank Order</button>
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
            var selected_rows = [];
            var selected_shippers_id = [];


            function run_ibft(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{!! route('admin.finance.make_payments.fetch_shipper_ibft_charges') !!}',
                    method: 'POST',
                    data: {
                        selected_shippers_id: selected_shippers_id
                    },
                    success: function(response) {
                        // Assuming the response contains the charges data
                        var charges = response;
                        // Update the total_ibft input field value with the received charges
                        $('#make_payments #make_payments_form .total_ibft').val(charges);
                    },
                    error: function(error) {
                        console.error('Error fetching charges:', error);
                    }
                });
            }

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

            @if (session('print'))
            window.open('{!! route('admin.finance.make_payments.export_bank_order') !!}?done_payment_ids=' + '{{ implode(',', session('print')) }}',
                '_blank');
            @endif

            function stats_calculate() {
                positive_negative_filter = $('#positive_negative_filter_form select.positive_negative_filter')
                    .val();

                $.ajax({
                    url: '{!! route('admin.finance.make_payments.stats_calculate') !!}',
                    data: {
                        'positive_negative_filter': positive_negative_filter
                    }
                })
                    .done(function(data) {
                        $('#stats_total_amount').html(data.total_amount);
                        $('#stats_total_charges').html(data.total_charges);
                        $('#stats_total_payable').html(data.total_payable);
                    });
            }



            var selected_rows_shipments = [];

            var initial_total_hold = 0;
            var initial_ibft_charges = 0;

            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Shipper',
                width: '100%',
                allowClear: true
            }).bind('change', function() {
                table.draw(false);
            });;
            $('#positive_negative_filter_form select.positive_negative_filter').prepend(
                '<option value="" selected></option>').select2({
                placeholder: 'Select Positive/Negative Filter',
                width: '100%',
                allowClear: true
            }).bind('change', function() {
                stats_calculate();
                table.draw(false);
            });

            $('#shipper_status_form select.shipper_status').prepend(
                '<option value="" selected="selected"></option>').select2({
                placeholder: 'Shipper Status',
                width: '100%',
                allowClear: true
            }).bind('change', function() {
                table.draw(false);
            });

            $('#payment_cycles_form select.payment_cycles').prepend(
                '<option value="" selected="selected"></option>').select2({
                placeholder: 'Payment Cycle',
                width: '100%',
                allowClear: true
            }).bind('change', function() {
                table.draw(false);
            });

            $('#payment_cycle_days_form select.payment_cycle_days').prepend(
                '<option value="" selected="selected"></option>').select2({
                placeholder: 'Payment Cycle Date',
                width: '100%',
                allowClear: true
            }).bind('change', function() {
                table.draw(false);
            });

            $('#shipper_document_status_form select.shipper_document_status').prepend(
                '<option value="" selected="selected"></option>').select2({
                placeholder: 'Shipper Document Status',
                width: '100%',
                allowClear: true
            }).bind('change', function() {
                table.draw(false);
            });

            $('#payment_cycle_filter_form select.payment_cycle_filter').select2({
                placeholder: 'Payment Cycle Filter',
                width: '100%',
            }).bind('change', function() {
                table.draw();
            });
            $('#make_payments_form #company_bank').prepend('<option value="" selected="selected"></option>')
                .select2({
                    placeholder: 'Select Company Bank',
                    width: '100%',
                    dropdownParent: $('#make_payments_form')
                });


            $('#tracking_number_search_form').bind('submit', function(e) {
                e.preventDefault();

                length = $('#tracking_number_search_form #tracking_number').val().length;

                if (length == 0 || length >= 12) {
                    table.draw();
                }
            });

            $('#tracking_number_search_form #tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                if (this.value.length == 0 || this.value.length >= 6) {
                    table.draw();
                }
            });


            var total_payable_amt=0;
            var shipper_cap = @json($shipper_cap);
            var shipper_limit=parseFloat(shipper_cap.setting_value).toFixed(2);
            //Make Payment Modal Datatable
            var make_payments_table = $('#make_payments #make_payments_datatable').DataTable({
                dom: '<"pull-right"B>tr',
                buttons: [
                    {
                        text: 'Export Selected',
                        className: 'export_selected',
                        action: function(e) {
                            e.preventDefault();

                            if (selected_rows_shipments.length != 0) {
                                window.open('{!! route('admin.finance.make_payments.shipment_export_selected') !!}?ids=' +
                                    selected_rows_shipments, '_blank');
                            }
                        }
                    },

                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function(e) {
                            e.preventDefault();
                            make_payments_table.rows().nodes().each(function(index) {
                                var row = make_payments_table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') &&
                                    !$(row.node()).hasClass('selected')) {
                                    row.select();
                                    var parent = $(row.node());
                                    calculation(parent);
                                }

                            });

                            shipper_limit = parseFloat(shipper_limit).toFixed(2);
                            shipper_limit = parseFloat(shipper_limit);
                            total_payable_amt = total_payable_amt.toFixed(2);

                            if(total_payable_amt > shipper_limit)
                            {
                                scan_sound(2);
                                toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                $('#make_payments #make_payments_form button.make').prop('disabled', true);
                                $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
                            }
                            check_reimbursement();

                        }
                    },

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
                    url: '{{ route('admin.finance.make_payments.shipment_list') }}',
                    data: function(d) {
                        d.ids = selected_rows;
                        d.requested_from_date = $('input[name="requested_from_date_formatted"]').val();
                        d.requested_to_date = $('input[name="requested_to_date_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [
                    [3, 'desc']
                ],
                columns: [{
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
                        class: 'align-middle shipper'
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
                        data: 'sms_charges',
                        name: 'pending_payment_shipments.sms_charges',
                        class: 'align-middle sms_charges'
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
                    },
                    {data: 'shipper_id', name: 'u.id', class: 'align-middle shipper_id d-none', searchable: false,},
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
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
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

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(
                            header).is('.aging') ) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.type')) {
                            $(drop_select).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                },
                drawCallback: function() {
                    if (selected_rows_shipments.length == 0) {
                        initial_total_hold = this.api().column('.payable').data().reduce(function(a,
                                                                                                  b) {
                            return parseFloat(a.toString().replace(/,/g, '')) + parseFloat(b
                                .toString().replace(/,/g, ''));
                        }, 0);

                        $('#make_payments #make_payments_form .total_hold').val(parseFloat(
                            initial_total_hold).toFixed(2));

                    }
                }
            });
            //End Make Payment Modal Datatable

            $('#search_filter_btn').on('click', function() {
                run_ibft();
                make_payments_table.draw(true);
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                } else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.make_payment').enable();
                } else {
                    table.button('.make_payment').disable();
                }
            });

            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click', 'tr td.delivered_shipments button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#delivered_shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.finance.make_payments.delivered_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var tracking_numbers = '';

                            $.each(data, function(index, tracking_number) {
                                tracking_numbers += '<u><a href=' + route +
                                    '?tracking_number=' + tracking_number +
                                    ' target="_blank">' + tracking_number + '</a></u><br>';
                            });

                            $('#delivered_shipments .modal-body').html(tracking_numbers);

                            $('#delivered_shipments').modal('show');
                        }
                    });
            });
            $('#make_payments_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var zero_charges = false;
                    make_payments_table.rows().nodes().each(function(index) {
                        var row = make_payments_table.row(index);
                        if ($(row.node().firstChild).hasClass('select-checkbox') && $(row
                            .node()).hasClass('selected')) {

                            // var shipments = parseInt($(row.node()).find('td.shipment').text());
                            // var charges = parseInt($(row.node()).find('td.charges').text());
                            // if(charges == 0){
                            // 	console.log(shipments);
                            // }


                            var shipments = parseInt($(row.node()).find('td.shipment').text());
                            var account_type = parseInt($(row.node()).attr('account_type'));
                            if (account_type == 1) {
                                var type_id = parseInt($(row.node()).attr('type_id'));
                                var row_id = $(row.node()).attr('id');
                                if (type_id != 2) {
                                    var amount = parseInt($(row.node()).find('td.deductable')
                                        .text());
                                    if (amount == 0) {
                                        zero_charges = true;
                                        shipments_array.push(shipments);
                                    }
                                }
                            }

                        }
                    });
                    if (zero_charges) {
                        var html = '';

                        html += 'Charges are zero for the following Shipments<br/>';
                        $.each(shipments_array, function(index, tracking_number) {
                            html += tracking_number + '<br/>';
                        });

                        html += '<br/>Select yes to pay!';

                        content = document.createElement('div');
                        content.innerHTML = html;

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
                                verify_make_payments(form);
                            }
                        });
                    } else {
                        verify_make_payments(form);
                    }
                }
            });

            $('#datatable tbody').on('click', 'tr td.returned_shipments button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#returned_shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.finance.make_payments.returned_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var tracking_numbers = '';

                            $.each(data, function(index, tracking_number) {
                                tracking_numbers += '<u><a href=' + route +
                                    '?tracking_number=' + tracking_number +
                                    ' target="_blank">' + tracking_number + '</a></u><br>';
                            });

                            $('#returned_shipments .modal-body').html(tracking_numbers);

                            $('#returned_shipments').modal('show');
                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.adjusted_shipments button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#adjusted_shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.finance.make_payments.adjusted_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var tracking_numbers = '';

                            $.each(data, function(index, tracking_number) {
                                tracking_numbers += '<u><a href=' + route +
                                    '?tracking_number=' + tracking_number +
                                    ' target="_blank">' + tracking_number + '</a></u><br>';
                            });

                            $('#adjusted_shipments .modal-body').html(tracking_numbers);

                            $('#adjusted_shipments').modal('show');
                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('view_details')) {
                    $('#view_details .modal-body').html('');

                    $.ajax({
                        url: '{!! route('admin.finance.make_payments.shipment_details') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id
                        }
                    })
                        .done(function(data) {
                            var details =
                                '<table class="table table-sm table-bordered"><thead><tr role="row" class="bg-primary white"><th class="border-primary border-darken-1 align-middle text-center">Shipment</th><th class="border-primary border-darken-1 align-middle text-center">Type</th><th class="border-primary border-darken-1 align-middle text-center">Amount</th><th class="border-primary border-darken-1 align-middle text-center">Charges</th><th class="border-primary border-darken-1 align-middle text-center">GST</th><th class="border-primary border-darken-1 align-middle text-center">SMS Charges</th><th class="border-primary border-darken-1 align-middle text-center">Fintech Charges</th> <th class="border-primary border-darken-1 align-middle text-center">Packing Charges</th> <th class="border-primary border-darken-1 align-middle text-center">Deductable</th><th class="border-primary border-darken-1 align-middle text-center">Payable</th><th class="border-primary border-darken-1 align-middle text-center">Faf Charges</th></tr></thead><tbody>';

                            $.each(data, function(index, detail) {
                                details += '<tr>';
                                details += '<td class="align-middle text-center">' + detail
                                    .tracking_number + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .type + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .amount + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .charges + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .gst + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .sms_charges + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .fintech_charges + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .packaging_charges + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .deductable + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .payable + '</td>';
                                details += '<td class="align-middle text-center">' + detail
                                    .faf_charges + '</td>';
                                details += '</tr>';
                            });

                            details += '</tbody></table>';

                            $('#view_details .modal-body').html(details);

                            $('#view_details').modal('show');
                        });
                } else if ($(this).hasClass('make_payment')) {
                    selected_rows = [];

                    table.rows().deselect();

                    table.button('.make_payment').disable();

                    selected_rows.push(id);

                    $('#make_payments #make_payments_form .total_amount').val(0);
                    $('#make_payments #make_payments_form .total_charges').val(0);
                    $('#make_payments #make_payments_form .total_gst').val(0);
                    $('#make_payments #make_payments_form .total_deductable').val(0);
                    $('#make_payments #make_payments_form .total_payable').val(0);
                    $('#make_payments #make_payments_form .total_hold').val(0);
                    $('#make_payments #make_payments_form .wht').val(0);

                    $('#make_payments #make_payments_form button.make').prop('disabled', true);
                    $('#make_payments #make_payments_form button.make_invoice').prop('disabled', true);
                    $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);

                    $('#make_payments #make_payments_form .pending_payment_shipment_ids').val('');


                    selected_rows_shipments = [];

                    make_payments_table.clear().draw();

                    $('#make_payments').modal('show');
                }
            });

            $('#make_payments').on('hide.bs.modal', function() {
                selected_rows = [];
                table.rows().deselect();
                table.button('.make_payment').disable();
                payable_list = [];
                selected_rows_shipments = [];
                shipperTotal = {};
                selected_shippers_id = [];


            });

            var payable_list = [];
            let shipperTotal = {};

            // Make Payments Modal Calculation
            function calculation(parent) {
                total_payable_amt=0;
                var id = parseInt(parent.attr('id'));

                var payable = parent.children('td.payable').html();
                var shipper_id = parent.children('td.shipper_id').html();
                var index = $.inArray(id, selected_rows_shipments);

                var total_amount_selector = $('#make_payments #make_payments_form .total_amount');
                var total_charges_selector = $('#make_payments #make_payments_form .total_charges');
                var total_gst_selector = $('#make_payments #make_payments_form .total_gst');
                var total_deductable_selector = $('#make_payments #make_payments_form .total_deductable');
                var total_payable_selector = $('#make_payments #make_payments_form .total_payable');
                var total_hold_selector = $('#make_payments #make_payments_form .total_hold');
                var total_wht_selector = $('#make_payments #make_payments_form .wht');

                // Row Selected
                if (index === -1) {
                    selected_rows_shipments.push(id);
                    // payable_list.push(parseFloat(payable),shipper_id); //adding payable in to array payable_list
                    payable_list.push({ payable, shipper_id }); //adding payable in to array payable_list
                    // console.log(payable_list);
                    calculateShipperTotal(parent);

                    var total_amount = ((total_amount_selector.val() != '') ? parseInt(total_amount_selector
                        .val()) : 0) + ((parent.children('td.amount').html() != '') ? parseInt(parent.children(
                        'td.amount').html().replace(/,/g, '')) : 0);
                    var total_charges = ((total_charges_selector.val() != '') ? parseFloat(total_charges_selector
                        .val()) : 0) + ((parent.children('td.charges').html() != '') ? parseFloat(parent
                        .children('td.charges').html().replace(/,/g, '')) : 0);
                    var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector.val()) : 0) +
                        ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst').html()
                            .replace(/,/g, '')) : 0);
                    var total_deductable = ((total_deductable_selector.val() != '') ? parseFloat(
                        total_deductable_selector.val()) : 0) + ((parent.children('td.deductable').html() !=
                        '') ? parseFloat(parent.children('td.deductable').html().replace(/,/g, '')) : 0);
                    var total_wht = ((total_wht_selector.val() != '') ? parseFloat(total_wht_selector.val()) : 0) +
                        ((parent.children('td.wht').html() != '') ? parseFloat(parent.children('td.wht').html()
                            .replace(/,/g, '')) : 0);
                    var total_payable = ((total_payable_selector.val() != '') ? parseFloat(total_payable_selector
                        .val()) : 0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent
                        .children('td.payable').html().replace(/,/g, '')) : 0);
                    var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector.val()) :
                        0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent.children(
                        'td.payable').html().replace(/,/g, '')) : 0);

                    // if selected row oayblae is less then 0 dipslay error
                    // var payable = $(this).closest('tr').find('td.payable').text();
                    if(payable < 0){
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

                    // console.log("when unselect", shipperTotal);
                    const removedEntry = payable_list[index]; // Get the entry being removed
                    // console.log(removedEntry);
                    const shipperId = removedEntry.shipper_id;
                    const payable = parseFloat(removedEntry.payable.replace(/,/g, ''));
                    // console.log(removedEntry);
                    // console.log("when unselect and before calculate shipper total", shipperTotal);
                    // Remove the unselected entry from payable_list using splice
                    payable_list.splice(index, 1);
                    // Recalculate shipperTotal after removing the entry
                    shipperTotal = {}; // Reset shipperTotal object
                    calculateShipperTotal(parent);
                    // console.log("when unselect and after calculate shipper total", shipperTotal);

                    var total_amount = ((total_amount_selector.val() != '') ? parseInt(total_amount_selector
                        .val()) : 0) - ((parent.children('td.amount').html() != '') ? parseInt(parent.children(
                        'td.amount').html().replace(/,/g, '')) : 0);
                    var total_charges = ((total_charges_selector.val() != '') ? parseFloat(total_charges_selector
                        .val()) : 0) - ((parent.children('td.charges').html() != '') ? parseFloat(parent
                        .children('td.charges').html().replace(/,/g, '')) : 0);
                    var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector.val()) : 0) -
                        ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst').html()
                            .replace(/,/g, '')) : 0);
                    var total_deductable = ((total_deductable_selector.val() != '') ? parseFloat(
                        total_deductable_selector.val()) : 0) - ((parent.children('td.deductable').html() !=
                        '') ? parseFloat(parent.children('td.deductable').html().replace(/,/g, '')) : 0);
                    var total_payable = ((total_payable_selector.val() != '') ? parseFloat(total_payable_selector
                        .val()) : 0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent
                        .children('td.payable').html().replace(/,/g, '')) : 0);
                    var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector.val()) :
                        0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent.children(
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

                    $('#make_payments #make_payments_form button.make').prop('disabled', false);
                    $('#make_payments #make_payments_form button.make_invoice').prop('disabled', false);
                    $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', false);

                    //if total payable is grate than 0 it enable make and export bank order button
                    if($('#make_payments #make_payments_form .total_payable').val() > 0){
                        const isAnyNegative = Object.values(shipperTotal).some(total => total < 0);
                        // console.log(x);
                        // Disable make and export bank order button if any shipper's total payable is negative

                        if (isAnyNegative) {
                            $('#make_payments #make_payments_form button.make').prop('disabled', true);
                            $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
                            $('#make_payments #make_payments_form button.make_invoice').prop('disabled', false);
                        } else {
                            $('#make_payments #make_payments_form button.make').prop('disabled', false);
                            $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', false);
                        }
                        total_payable_amt+=total_payable;
                        // console.log(total_payable_amt);
                    }
                    else{
                        $('#make_payments #make_payments_form button.make').prop('disabled', true);
                        $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
                    }

                } else {

                    total_amount_selector.val(0);
                    total_charges_selector.val(0);
                    total_gst_selector.val(0);
                    total_deductable_selector.val(0);
                    total_wht_selector.val(0);
                    total_payable_selector.val(0);
                    total_hold_selector.val(parseFloat(initial_total_hold).toFixed(2));

                    $('#make_payments #make_payments_form button.make').prop('disabled', true);
                    $('#make_payments #make_payments_form button.make_invoice').prop('disabled', true);
                    $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);

                }

                $('#make_payments #make_payments_form .pending_payment_shipment_ids').val(selected_rows_shipments);
            }

            // Function to calculate shipper totals and disable buttons if any shipper's total payable is negative

            // var shipper_cap = @json($shipper_cap);
            // var shipper_limit=parseFloat(shipper_cap.setting_value).toFixed(2);
            // var total_payable_amt=0;
            function calculateShipperTotal() {

                // console.log("oknhai");
                shipperTotal = {}; // Reset shipperTotal object
                // total_payable_amt = 0; // Reset total_payable_amt

                payable_list.forEach(entry => {
                    const shipperId = entry.shipper_id;
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
                //     // alert("nikal ba");
                //     calculation(tr);
                //      var tr_index=$(tr).index();
                //       setTimeout(() => {
                //           $("#make_payments #make_payments_datatable tbody tr").eq(tr_index).removeClass('selected bg-primary bg-lighten-5 primary');
                //       }, 150);

                // }


            }

            $('#make_payments #make_payments_datatable tbody').on('click', 'tr td.select-checkbox', function() {
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
                            if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row
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
                    if(total_payable_amt > shipper_limit)
                    {
                        scan_sound(2);
                        toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $('#make_payments #make_payments_form button.make').prop('disabled', true);
                        $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
                    }

                } else {
                    calculation(parent);
                    if(total_payable_amt > shipper_limit)
                    {
                        scan_sound(2);
                        toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $('#make_payments #make_payments_form button.make').prop('disabled', true);
                        $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
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
                        $('#make_payments #make_payments_form button.make_invoice').prop('disabled', true);
                    }else{
                        $('#make_payments #make_payments_form button.make_invoice').prop('disabled', false);
                    }

                }, 200);

            }

            var shipments_array = [];

            function verify_make_payments(form) {
                $.ajax({
                    url: '{!! route('admin.finance.make_payments.verify') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pending_payment_shipment_ids': $(
                            '#make_payments #make_payments_form .pending_payment_shipment_ids').val()
                    }
                })
                    .done(function(data) {
                        if (data.status == 0) {
                            if (data.duplicate_shipments && data.over_payments) {
                                var html = 'The following Shipment(s) have Duplicate Same Type Payments:<br/>';

                                $.each(data.duplicate_shipments, function(index, duplicate_shipment) {
                                    html += duplicate_shipment + '<br/>';
                                });

                                html += 'The following Shipment(s) have Payments above the Limit:<br/>';

                                $.each(data.over_payments, function(index, over_payment) {
                                    html += over_payment.shipper + ' ' + '<b>' + over_payment.payable +
                                        '</b>' + '<br/>';
                                });

                                html += '<br/>Are you sure, you want to make the Payments?';

                                content = document.createElement('div');
                                content.innerHTML = html;

                                swal({
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
                                        if(total_payable_amt > shipper_limit)
                                        {
                                            scan_sound(2);
                                            toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }else{
                                            $('#make_payments #make_payments_form button').remove();
                                            form.submit();
                                        }
                                    }
                                });
                            } else if (data.duplicate_shipments) {
                                var html = 'The following Shipment(s) have Duplicate Same Type Payments:<br/>';

                                $.each(data.duplicate_shipments, function(index, duplicate_shipment) {
                                    html += duplicate_shipment + '<br/>';
                                });

                                html += '<br/>Are you sure, you want to make the Payments?';

                                content = document.createElement('div');
                                content.innerHTML = html;

                                swal({
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
                                        if(total_payable_amt > shipper_limit)
                                        {
                                            scan_sound(2);
                                            toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }else{
                                            $('#make_payments #make_payments_form button').remove();
                                            form.submit();
                                        }
                                    }
                                });
                            } else if (data.over_payments) {
                                var html = 'The following Shipment(s) have Payments above the Limit:<br/>';

                                $.each(data.over_payments, function(index, over_payment) {
                                    html += over_payment.shipper + ': ' + '<b>' + over_payment.payable +
                                        '</b>' + '<br/>';
                                });

                                html += '<br/>Are you sure, you want to make the Payments?';

                                content = document.createElement('div');
                                content.innerHTML = html;

                                swal({
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
                                        if(total_payable_amt > shipper_limit)
                                        {
                                            scan_sound(2);
                                            toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }else{
                                            $('#make_payments #make_payments_form button').remove();
                                            form.submit();
                                        }
                                    }
                                });
                            } else {
                                swal({
                                    text: 'Are you sure, you want to make the Payments?',
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

                                        if(total_payable_amt > shipper_limit)
                                        {
                                            scan_sound(2);
                                            toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }else{
                                            $('#make_payments #make_payments_form button').remove();
                                            form.submit();
                                        }
                                    }
                                });
                            }
                        } else if (data.status == 1) {
                            var html =
                                'Cannot proceed since following Shipper(s) have Overall Negative Payment(s) Selected:<br/>';

                            $.each(data.negative_payments, function(index, shipper) {
                                html += shipper + '<br/>';
                            });

                            content = document.createElement('div');
                            content.innerHTML = html;

                            swal({
                                content: content,
                                icon: 'warning',
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
                        } else {
                            var html = 'Shipper: <b>' + data.merged_account_negative.shipper +
                                '</b> Sister Account(s) have Overall Negative Payable:<br/>';

                            $.each(data.merged_account_negative.merged_account, function(index, account) {
                                html += account + ': ' + data.merged_account_negative.payable[index];
                            });

                            html += '<br/>Are you sure, you want to make the Payments?';

                            content = document.createElement('div');
                            content.innerHTML = html;

                            swal({
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
                                    $('#make_payments #make_payments_form button').remove();
                                    if(total_payable_amt > shipper_limit)
                                    {
                                        scan_sound(2);
                                        toastr.error("Payable amount should be less than shipper Cap!", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }else{
                                        form.submit();
                                    }
                                }
                            });
                        }
                    });
            }


            $('#star_shippers_filter').on('click', function() {
                $('#star_shippers_filter').val(1);
                table.draw(true);
                $('#star_shippers_filter').val(0);
            });


            $('#payment_cycles_form_val').on('change', function() {
                var id = $(this).val();
                var select = document.getElementById("payment_cycle_days_val");
                var length = select.options.length;

                // Clear all existing options in the select element
                select.innerHTML = "";

                // Create a placeholder option
                var placeholderOption = document.createElement("option");
                placeholderOption.text = "Select payment date";
                placeholderOption.value = "";
                select.add(placeholderOption);

                if (id == 2 || id == 4 || id == 5) {
                    var daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
                    for (var i = 0; i < daysOfWeek.length; i++) {
                        var option = document.createElement("option");
                        option.text = daysOfWeek[i];
                        option.value = i + 1;
                        select.add(option);
                    }
                } else {
                    // Append the default options to the select element
                    for (var i = 1; i < 29; i++) {
                        var option = document.createElement("option");
                        option.value = i;
                        option.text = i + ' date';
                        select.add(option);
                    }
                }
            });

            window.addEventListener('message', function(event) {

                selected_rows = [];

                $('#make_payments #make_payments_form .total_amount').val(0);
                $('#make_payments #make_payments_form .total_charges').val(0);
                $('#make_payments #make_payments_form .total_gst').val(0);
                $('#make_payments #make_payments_form .total_deductable').val(0);
                $('#make_payments #make_payments_form .total_payable').val(0);
                $('#make_payments #make_payments_form .total_hold').val(0);
                $('#make_payments #make_payments_form .wht').val(0);

                $('#make_payments #make_payments_form button.make').prop('disabled', true);
                $('#make_payments #make_payments_form button.make_invoice').prop('disabled', true);
                $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);

                $('#make_payments #make_payments_form .pending_payment_shipment_ids').val('');

                selected_rows_shipments = [];
                make_payments_table.clear().draw();

                $.each(event.data.id, function(index, val) {
                    selected_rows.push(val);
                });
                $.each(event.data.selected_shipper_id, function(index, val) {
                    selected_shippers_id.push(val);
                });
                run_ibft();
                make_payments_table.draw(true);

            });

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
                            'pending_payment_shipment_ids': $('#make_payments #make_payments_form .pending_payment_shipment_ids').val()
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
