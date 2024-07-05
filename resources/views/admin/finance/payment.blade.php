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
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
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
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
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
                        <table class="table table-bordered datatable" id="make_payments_datatable">
                            <thead>
                                <tr class="bg-primary white">
                                    <th></th>
                                    <th>S. No.</th>
                                    <th>Account Type</th>
                                    <th>Shipper</th>
                                    <th>Shipment</th>
                                    <th>Origin</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Delivery / Return Datetime</th>
                                    <th>Aging</th>
                                    <th>Amount</th>
                                    <th>Charges</th>
                                    <th>GST</th>
                                    <th>WHT</th>
                                    <th>Fintech Charges</th>
                                    <th>Packing Charges</th>
                                    <th>Deductible</th>
                                    <th>Payable</th>
                                    <th>Arrival Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table rows will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>
        
                    <form id="make_payments_form" class="form-inline my-1 justify-content-center"
                        novalidate="novalidate" method="POST" action="{{ route('admin.finance.make_payments.store') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="pending_payment_shipment_ids" class="pending_payment_shipment_ids">
        
                        <div class="readonly_div">
                            <div class="row mt-3 readonly_section">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Amount</label>
                                        <input type="text" name="total_amount" class="form-control text-center total_amount"
                                            placeholder="Total Amount" readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Charges</label>
                                        <input type="text" name="total_charges" class="form-control text-center total_charges"
                                            placeholder="Total Charges" readonly="readonly">
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
                                            class="form-control text-center total_deductable" placeholder="Total Deductable"
                                            readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Payable</label>
                                        <input type="text" name="total_payable" class="form-control text-center total_payable"
                                            placeholder="Total Payable" readonly="readonly">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Hold</label>
                                        <input type="text" name="total_hold" class="form-control text-center total_hold"
                                            placeholder="Total Hold" readonly="readonly">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Close</button>
                            <button type="button" name="make_invoice" onclick="verify_make_invoice_payments()"
                                class="btn btn-primary mr-2 make_invoice">Corporate Invoice for Negative Payable</button>
                            <button type="submit" name="make" class="btn btn-primary make">Make & Export Bank Order</button>
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
            overflow-x: auto; /* Enable horizontal scrolling */
        }
        
        .table-responsive {
            overflow-x: auto; /* Ensure table is scrollable horizontally */
            max-width: 100%; /* Adjust max-width as needed */
        }

        .readonly_section{
            width: 110rem;
        }

        .readonly_div{
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
            var table;
            window.addEventListener('message', function(event) {
                var selectedData = event.data;
                var ids = selectedData;
                $.ajax({
                    url: '{{ route('admin.finance.make_payments.payment_list') }}',
                    method: 'GET',
                    data: {
                        ids: ids,
                        pickup_address_id: selectedData.pickup_address_id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        table = $('#make_payments_datatable').DataTable({
                            destroy: true,
                            dom: '<"pull-right"B>tr',
                            data: data.data,
                            select: {
                                style: 'multi',
                                selector: 'td.select-checkbox',
                                className: 'selected bg-primary bg-lighten-5 primary'
                            },

                            columns: [
                                { 
                                    data: 'id',
                                    defaultContent: '',
                                    orderable: false,
                                    className: 'select-checkbox',
                                    render: function() {
                                        return '';
                                    }
                                },
                                { data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
                                { data: 'account_type_id' },
                                { data: 'shipper' },
                                { data: 'shipment' },
                                { data: 'origin' },
                                { data: 'type' },
                                { data: 'status' },
                                { data: 'created_at' },
                                { data: 'aging' },
                                { data: 'amount' },
                                { data: 'charges' },
                                { data: 'gst' },
                                { data: 'wht' },
                                { data: 'fintech_charges' },
                                { data: 'packaging_charges' },
                                { data: 'deductable' },
                                { data: 'payable' },
                                { data: 'arrival_date' }
                            ],

                            buttons: [
                                {
                                    text: 'Select All',
                                    className: 'btn btn-primary select_all',
                                    action: function () {
                                        table.rows().select();
                                        updateSelectNoneButtonState();

                                        table.rows().nodes().each(function(index) {
                                            var row = table.row(index);

                                            row.select();
                                            calculation($(row.node()));

                                            if ($(row.node().firstChild).hasClass('select-checkbox') &&
                                                !$(row.node()).hasClass('selected')) {
                                                row.select();
                                                calculation($(row.node()));
                                            }
                                        });
                                    }
                                },
                                {
                                    text: 'Select None',
                                    className: 'btn btn-primary select_none disabled',
                                    action: function () {
                                        table.rows().deselect();
                                        updateSelectNoneButtonState();
                                    }
                                }
                            ],
                            rowCallback: function(row, data, index) {
                                $(row).attr('consolidation_id', data.consolidation_id || '');
                                $(row).attr('type_id', data.type);
                                $(row).attr('account_type', data.account_type_id);
                                var checkbox = $('input.row-select-checkbox', row);
                                checkbox.prop('checked', $(row).hasClass('selected'));
                            }
                        });

                        $('#make_payments_datatable').on('click', '.select-checkbox', function(){
                            if ($(this).is(':checked')) {
                                alert('ok');
                                var $row = $(this).closest('tr');
                                updateSelectNoneButtonState()
                                calculation($row);
                            }
                        });

                        $('#make_payments_datatable tbody').on('change', '.row-select-checkbox', function() {
                            var $row = $(this).closest('tr');
                            var rowIndex = table.row($row).index();
                            if (this.checked) {
                                table.row($row).select();
                            } else {
                                table.row($row).deselect();
                            }
                            updateSelectNoneButtonState();
                            calculation($row);
                        });

                        $('#make_payments_datatable thead').on('click', 'input[type="checkbox"]', function(e) {
                            if (this.checked) {
                                $('#make_payments_datatable tbody input.row-select-checkbox:not(:checked)').prop('checked', true).trigger('change');
                            } else {
                                $('#make_payments_datatable tbody input.row-select-checkbox:checked').prop('checked', false).trigger('change');
                            }
                            updateSelectNoneButtonState();
                        });

                        function updateSelectNoneButtonState() {
                            var selectedRows = table.rows({ selected: true }).count();
                            var selectNoneButton = $('.select_none');
                            if (selectedRows > 0) {
                                selectNoneButton.removeClass('disabled');
                            } else {
                                selectNoneButton.addClass('disabled');
                            }
                        }

                        // Make Payments Modal Calculation
                        function calculation(parent) {
                            var total_amount_selector = $('#make_payments_form .total_amount');
                            var total_charges_selector = $('#make_payments_form .total_charges');
                            var total_gst_selector = $('#make_payments_form .total_gst');
                            var total_deductable_selector = $('#make_payments_form .total_deductable');
                            var total_payable_selector = $('#make_payments_form .total_payable');
                            var total_hold_selector = $('#make_payments_form .total_hold');
                            var total_wht_selector = $('#make_payments_form .wht');

                            var total_amount = 0;
                            var total_charges = 0;
                            var total_gst = 0;
                            var total_deductable = 0;
                            var total_wht = 0;
                            var total_payable = 0;
                            var total_hold = 0;

                            // Iterate through selected rows
                            table.rows({ selected: true }).every(function () {
                                var row = this;
                                var rowData = row.data();
                                
                                var payable = parseFloat(rowData.payable.replace(/,/g, ''));
                                var amount = parseFloat(rowData.amount.replace(/,/g, ''));
                                var charges = parseFloat(rowData.charges.replace(/,/g, ''));
                                var gst = parseFloat(rowData.gst.replace(/,/g, ''));
                                var deductable = parseFloat(rowData.deductable.replace(/,/g, ''));
                                var wht = parseFloat(rowData.wht.replace(/,/g, ''));

                                // Calculate totals
                                total_amount += amount;
                                total_charges += charges;
                                total_gst += gst;
                                total_deductable += deductable;
                                total_wht += wht;
                                total_payable += payable;
                                total_hold += payable;

                                // Check for negative payable
                                if (payable < 0) {
                                    var html = 'Negative payable detected for the shipper(s).<br/>';
                                    var content = document.createElement('div');
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
                            });

                            // Update UI elements
                            total_amount_selector.val(total_amount.toFixed(2));
                            total_charges_selector.val(total_charges.toFixed(2));
                            total_gst_selector.val(total_gst.toFixed(2));
                            total_deductable_selector.val(total_deductable.toFixed(2));
                            total_wht_selector.val(total_wht.toFixed(2));
                            total_payable_selector.val(total_payable.toFixed(2));
                            total_hold_selector.val(total_hold.toFixed(2));

                            // Enable/disable buttons based on conditions
                            if (total_payable > 0) {
                                $('#make_payments_form button.make').prop('disabled', false);
                                $('#make_payments_form button.make_invoice').prop('disabled', false);
                                $('#make_payments_form button.export_bank_order').prop('disabled', false);
                            } else {
                                $('#make_payments_form button.make').prop('disabled', true);
                                $('#make_payments_form button.make_invoice').prop('disabled', true);
                                $('#make_payments_form button.export_bank_order').prop('disabled', true);
                            }

                            // Store selected rows
                            var selected_rows_shipments = table.rows({ selected: true }).ids().toArray();
                            $('#make_payments_form .pending_payment_shipment_ids').val(selected_rows_shipments);
                        }
                    }
                });
            });
        });
    </script>
    


@endsection