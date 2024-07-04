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
        // $(document).ready(function() {
        //     window.addEventListener('message', function(event) {
        //         var selectedData = event.data;
        //         $.ajax({
        //             url: "{{ route('admin.finance.make_payments.payment_list') }}",
        //             data: { ids: selectedData },
        //             success: function(response) {
        //                 // Initialize DataTable
        //                 var table = $('#make_payments_datatable').DataTable({
        //                     dom: '<"pull-right"B>tr',
        //                     data: response.data,
        //                     select: {
        //                         info: false,
        //                         style: 'multi',
        //                         selector: 'td.select-checkbox',
        //                         className: 'selected bg-primary bg-lighten-5 primary'
        //                     },
        //                     columns: [
        //                         {
        //                             data: null,
        //                             orderable: false,
        //                             className: 'select-checkbox',
        //                             render: function(data, type, row) {
        //                                 return '<input type="checkbox" class="row-select-checkbox d-none" id="table_checkbox">';
        //                             }
        //                         },
        //                         { 
        //                             data: null,
        //                             render: function (data, type, row, meta) {
        //                                 return meta.row + 1;
        //                             }
        //                         },
        //                         { data: 'account_type_text' },
        //                         { data: 'shipper' },
        //                         { data: 'shipment' },
        //                         { data: 'origin' },
        //                         { data: 'type' },
        //                         { data: 'status' },
        //                         { data: 'arrival_date' },
        //                         { 
        //                             data: null,
        //                             render: function(data, type, row) {
        //                                 return (parseFloat(row.charges) + parseFloat(row.gst)).toFixed(2);
        //                             }
        //                         },
        //                         { data: 'amount' },
        //                         { data: 'charges' },
        //                         { data: 'gst' },
        //                         { data: 'wht' },
        //                         { data: 'fintech_charges' },
        //                         { data: 'packaging_charges' },
        //                         { 
        //                             data: null,
        //                             render: function(data, type, row) {
        //                                 return (parseFloat(row.payable) - parseFloat(row.fintech_charges)).toFixed(2);
        //                             }
        //                         },
        //                         { data: 'payable' },
        //                         { data: 'arrival_date' }
        //                     ],

                            // buttons: [
                            //     {
                            //         text: 'Select All',
                            //         className: 'btn btn-primary select_all',
                            //         action: function () {
                            //             table.rows().select();
                            //             updateSelectNoneButtonState();
                            //         }
                            //     },
                            //     {
                            //         text: 'Select None',
                            //         className: 'btn btn-primary select_none disabled',
                            //         action: function () {
                            //             table.rows().deselect();
                            //             updateSelectNoneButtonState();
                            //         }
                            //     }
                            // ],

        //                     // Handle row selection via checkbox
        //                     rowCallback: function(row, data, index) {
        //                         var checkbox = $('input.row-select-checkbox', row);
        //                         checkbox.prop('checked', $(row).hasClass('selected'));
        //                     }
        //                 });

        //                 // checkbox
        //                 $(document).ready(function() {
        //                     $('#make_payments_datatable').on('click', '.select-checkbox', function () {
        //                         var $row = $(this).closest('tr');
        //                         var $checkbox = $row.find('.row-select-checkbox');
                                
        //                         var isChecked = $checkbox.prop('checked');
        //                         $checkbox.prop('checked', !isChecked);
        //                         console.log(!isChecked);
                                
        //                         if (!isChecked) {
        //                             $('.select_none').removeClass('disabled');
        //                         } else {
        //                             $('.select_none').addClass('disabled');
        //                         }
        //                     });
        //                 });

                        // function updateSelectNoneButtonState() {
                        //     var selectedRows = table.rows({ selected: true }).count();
                        //     var selectNoneButton = $('.select_none');
                        //     if (selectedRows > 0) {
                        //         selectNoneButton.removeClass('disabled');
                        //     } else {
                        //         selectNoneButton.addClass('disabled');
                        //     }
                        // }

                        // $('#make_payments_datatable tbody').on('change', '.row-select-checkbox', function() {
                        //     var $row = $(this).closest('tr');
                        //     var rowIndex = table.row($row).index();
                        //     if (this.checked) {
                        //         table.row($row).select();
                        //     } else {
                        //         table.row($row).deselect();  
                        //     }
                        //     updateSelectNoneButtonState();
                        // });

                        // $('#make_payments_datatable thead').on('click', 'input[type="checkbox"]', function(e) {
                        //     if (this.checked) {
                        //         $('#make_payments_datatable tbody input.row-select-checkbox:not(:checked)').prop('checked', true).trigger('change');
                        //     } else {
                        //         $('#make_payments_datatable tbody input.row-select-checkbox:checked').prop('checked', false).trigger('change');
                        //     }
                        //     updateSelectNoneButtonState();
                        // });
        //             }
        //         });

        //         function calculation(parent) {
        //             total_payable_amt=0;
        //             var id = parseInt(parent.attr('id'));
        //             var payable = parent.children('td.payable').html();
        //             var shipper_id = parent.children('td.shipper_id').html();
        //             var index = $.inArray(id, selected_rows_shipments);

        //             var total_amount_selector = $(' #make_payments_form .total_amount');
        //             var total_charges_selector = $(' #make_payments_form .total_charges');
        //             var total_gst_selector = $(' #make_payments_form .total_gst');
        //             var total_deductable_selector = $(' #make_payments_form .total_deductable');
        //             var total_payable_selector = $(' #make_payments_form .total_payable');
        //             var total_hold_selector = $(' #make_payments_form .total_hold');
        //             var total_wht_selector = $(' #make_payments_form .wht');

        //             // Row Selected
        //             if (index === -1) {
        //                 selected_rows_shipments.push(id);
        //                 // payable_list.push(parseFloat(payable),shipper_id); //adding payable in to array payable_list 
        //                 payable_list.push({ payable, shipper_id }); //adding payable in to array payable_list 
        //                 // console.log(payable_list);
        //                 calculateShipperTotal(parent);

        //                 var total_amount = ((total_amount_selector.val() != '') 
        //                     ? parseInt(total_amount_selector.val()) : 0)
        //                     + ((parent.children('td.amount').html() != '') 
        //                     ? parseInt(parent.children('td.amount').html().replace(/,/g, '')) : 0);
                        
        //                 var total_charges = ((total_charges_selector.val() != '') ? parseFloat(total_charges_selector
        //                     .val()) : 0) + ((parent.children('td.charges').html() != '') ? parseFloat(parent
        //                     .children('td.charges').html().replace(/,/g, '')) : 0);
                        
        //                 var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector.val()) : 0) +
        //                     ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst').html()
        //                         .replace(/,/g, '')) : 0);
                        
        //                 var total_deductable = ((total_deductable_selector.val() != '') ? parseFloat(
        //                     total_deductable_selector.val()) : 0) + ((parent.children('td.deductable').html() !=
        //                     '') ? parseFloat(parent.children('td.deductable').html().replace(/,/g, '')) : 0);
                        

        //                     console.log(total_deductable);


        //                 var total_wht = ((total_wht_selector.val() != '') ? parseFloat(total_wht_selector.val()) : 0) +
        //                     ((parent.children('td.wht').html() != '') ? parseFloat(parent.children('td.wht').html()
        //                         .replace(/,/g, '')) : 0);
                        
        //                 var total_payable = ((total_payable_selector.val() != '') ? parseFloat(total_payable_selector
        //                     .val()) : 0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent
        //                     .children('td.payable').html().replace(/,/g, '')) : 0);
                        
        //                 var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector.val()) :
        //                     0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent.children(
        //                         'td.payable').html().replace(/,/g, '')) : 0);

        //                 // if selected row oayblae is less then 0 dipslay error
        //                     // var payable = $(this).closest('tr').find('td.payable').text();
        //                     if(payable < 0){
        //                         var html = 'Negative payable detected for the shipper(s).<br/>';
        //                             content = document.createElement('div');
        //                             content.innerHTML = html;
        //                             swal({
        //                                 content: content,
        //                                 icon: 'error',
        //                                 buttons: {
        //                                     cancel: {
        //                                         text: 'Close',
        //                                         value: null,
        //                                         visible: true,
        //                                         closeModal: true,
        //                                     }
        //                                 },
        //                                 closeOnClickOutside: false,
        //                                 closeOnEsc: false,
        //                                 dangerMode: true
        //                             });
        //                     }        
        //             } 

        //             //Row UnSelected
        //             else {
        //                 selected_rows_shipments.splice(index, 1);
        //                 const removedEntry = payable_list[index]; // Get the entry being removed
        //                 const shipperId = removedEntry.shipper_id;
        //                 const payable = parseFloat(removedEntry.payable.replace(/,/g, ''));
        //                 // Remove the unselected entry from payable_list using splice
        //                 payable_list.splice(index, 1);
        //                 // Recalculate shipperTotal after removing the entry
        //                 shipperTotal = {}; // Reset shipperTotal object
        //                 calculateShipperTotal(parent);

        //                 var total_amount = ((total_amount_selector.val() != '') ? parseInt(total_amount_selector
        //                 .val()) : 0) - ((parent.children('td.amount').html() != '') ? parseInt(parent.children(
        //                     'td.amount').html().replace(/,/g, '')) : 0);
        //                 var total_charges = ((total_charges_selector.val() != '') ? parseFloat(total_charges_selector
        //                     .val()) : 0) - ((parent.children('td.charges').html() != '') ? parseFloat(parent
        //                     .children('td.charges').html().replace(/,/g, '')) : 0);
        //                 var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector.val()) : 0) -
        //                     ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst').html()
        //                         .replace(/,/g, '')) : 0);
        //                 var total_deductable = ((total_deductable_selector.val() != '') ? parseFloat(
        //                     total_deductable_selector.val()) : 0) - ((parent.children('td.deductable').html() !=
        //                     '') ? parseFloat(parent.children('td.deductable').html().replace(/,/g, '')) : 0);
        //                 var total_payable = ((total_payable_selector.val() != '') ? parseFloat(total_payable_selector
        //                     .val()) : 0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent
        //                     .children('td.payable').html().replace(/,/g, '')) : 0);
        //                 var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector.val()) :
        //                     0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent.children(
        //                         'td.payable').html().replace(/,/g, '')) : 0);
        //             }
        //             if (selected_rows_shipments.length > 0) {
        //                 total_amount_selector.val(parseInt(total_amount));
        //                 total_charges_selector.val(parseFloat(total_charges).toFixed(2));
        //                 total_gst_selector.val(parseFloat(total_gst).toFixed(2));
        //                 total_deductable_selector.val(parseFloat(total_deductable).toFixed(2));
        //                 total_wht_selector.val(parseFloat(total_wht).toFixed(2));
        //                 total_payable_selector.val(parseFloat(total_payable).toFixed(2));
        //                 total_hold_selector.val(parseFloat(total_hold).toFixed(2));

        //                 $('#make_payments #make_payments_form button.make').prop('disabled', false);
        //                 $('#make_payments #make_payments_form button.make_invoice').prop('disabled', false);
        //                 $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', false);

        //                 //if total payable is grate than 0 it enable make and export bank order button
        //                 if($('#make_payments #make_payments_form .total_payable').val() > 0){
        //                         const isAnyNegative = Object.values(shipperTotal).some(total => total < 0);
        //                         // Disable make and export bank order button if any shipper's total payable is negative
        //                         if (isAnyNegative) {
        //                             $('#make_payments #make_payments_form button.make').prop('disabled', true);
        //                             $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
        //                             $('#make_payments #make_payments_form button.make_invoice').prop('disabled', false);
        //                         } else {
        //                             $('#make_payments #make_payments_form button.make').prop('disabled', false);
        //                             $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', false);
        //                             $('#make_payments #make_payments_form button.make_invoice').prop('disabled', true);
        //                         }
        //                         total_payable_amt+=total_payable;
        //                 }
        //                 else{
        //                     $('#make_payments #make_payments_form button.make').prop('disabled', true);
        //                     $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
        //                     $('#make_payments #make_payments_form button.make_invoice').prop('disabled', false);
        //                 }

        //             } else {
                    
        //                 total_amount_selector.val(0);
        //                 total_charges_selector.val(0);
        //                 total_gst_selector.val(0);
        //                 total_deductable_selector.val(0);
        //                 total_wht_selector.val(0);
        //                 total_payable_selector.val(0);
        //                 total_hold_selector.val(parseFloat(initial_total_hold).toFixed(2));

        //                 $('#make_payments #make_payments_form button.make').prop('disabled', true);
        //                 $('#make_payments #make_payments_form button.make_invoice').prop('disabled', true);
        //                 $('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
        //             }

        //             $('#make_payments #make_payments_form .pending_payment_shipment_ids').val(selected_rows_shipments);
        //         }
        //     });
        // });



        // $(document).ready(function() {
        //     window.addEventListener('message', function(event) {
        //         var selectedData = event.data;
        //         var ids = selectedData;
        //         $.ajax({
        //             url: '{{ route('admin.finance.make_payments.payment_list') }}',
        //             method: 'GET',
        //             data: {
        //                 ids: ids,
        //                 pickup_address_id: selectedData.pickup_address_id,
        //                 _token: $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function(data) {
        //                 $('#make_payments_datatable').DataTable({
        //                     dom: '<"pull-right"B>tr',
        //                     data: data.data,
        //                     select: {
        //                         info: false,
        //                         style: 'multi',
        //                         selector: 'td.select-checkbox',
        //                         className: 'selected bg-primary bg-lighten-5 primary'
        //                     },

        //                     buttons: [
        //                         {
        //                             text: 'Select All',
        //                             className: 'btn btn-primary select_all',
        //                             action: function () {
        //                                 table.rows().select();
        //                                 updateSelectNoneButtonState();
        //                             }
        //                         },
        //                         {
        //                             text: 'Select None',
        //                             className: 'btn btn-primary select_none disabled',
        //                             action: function () {
        //                                 table.rows().deselect();
        //                                 updateSelectNoneButtonState();
        //                             }
        //                         }
        //                     ],

        //                     columns: [
        //                         { 
        //                             data: null, 
        //                             defaultContent: '', 
        //                             orderable: false,
        //                             className: 'select-checkbox', 
        //                             render: function() {
        //                                 return '<input type="checkbox" class="row-select-checkbox d-none" id="table_checkbox">';
        //                             }
        //                         },
        //                         { data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
        //                         { data: 'account_type_id' },
        //                         { data: 'shipper' },
        //                         { data: 'shipment' },
        //                         { data: 'origin' },
        //                         { data: 'type' },
        //                         { data: 'status' },
        //                         { data: 'created_at' },
        //                         { data: 'aging' },
        //                         { data: 'amount' },
        //                         { data: 'charges' },
        //                         { data: 'gst' },
        //                         { data: 'wht' },
        //                         { data: 'fintech_charges' },
        //                         { data: 'packaging_charges' },
        //                         { data: 'deductable' },
        //                         { data: 'payable' },
        //                         { data: 'arrival_date' }
        //                     ],
        //                     rowCallback: function(row, data, index) {
        //                         $(row).attr('consolidation_id', data.consolidation_id || '');
        //                         $(row).attr('type_id', data.type);
        //                         $(row).attr('account_type', data.account_type_id);

        //                         var checkbox = $('input.row-select-checkbox', row);
        //                         checkbox.prop('checked', $(row).hasClass('selected'));
        //                     }
        //                 });

        //                 // checkbox
        //                 $(document).ready(function() {
        //                     $('#make_payments_datatable').on('click', '.select-checkbox', function () {
        //                         var $row = $(this).closest('tr');
        //                         var $checkbox = $row.find('.row-select-checkbox');
                                
        //                         var isChecked = $checkbox.prop('checked');
        //                         $checkbox.prop('checked', !isChecked);
        //                         console.log(!isChecked);
                                
        //                         if (!isChecked) {
        //                             $('.select_none').removeClass('disabled');
        //                         } else {
        //                             $('.select_none').addClass('disabled');
        //                         }
        //                     });
        //                 });

        //                 function updateSelectNoneButtonState() {
        //                     var selectedRows = table.rows({ selected: true }).count();
        //                     var selectNoneButton = $('.select_none');
        //                     if (selectedRows > 0) {
        //                         selectNoneButton.removeClass('disabled');
        //                     } else {
        //                         selectNoneButton.addClass('disabled');
        //                     }
        //                 }

        //                 $('#make_payments_datatable tbody').on('change', '.row-select-checkbox', function() {
        //                     var $row = $(this).closest('tr');
        //                     var rowIndex = table.row($row).index();
        //                     if (this.checked) {
        //                         table.row($row).select();
        //                     } else {
        //                         table.row($row).deselect();
        //                     }
        //                     updateSelectNoneButtonState();
        //                 });

        //                 $('#make_payments_datatable thead').on('click', 'input[type="checkbox"]', function(e) {
        //                     if (this.checked) {
        //                         $('#make_payments_datatable tbody input.row-select-checkbox:not(:checked)').prop('checked', true).trigger('change');
        //                     } else {
        //                         $('#make_payments_datatable tbody input.row-select-checkbox:checked').prop('checked', false).trigger('change');
        //                     }
        //                     updateSelectNoneButtonState();
        //                 });
        //             }
        //         });
        //     });
        // });


        $(document).ready(function() {
            var table; // Declare table variable outside to make it accessible

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
                            buttons: [
                                {
                                    text: 'Select All',
                                    className: 'btn btn-primary select_all',
                                    action: function () {
                                        table.rows().select();
                                        updateSelectNoneButtonState();
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
                            columns: [
                                { 
                                    data: null, 
                                    defaultContent: '', 
                                    orderable: false,
                                    className: 'select-checkbox', 
                                    render: function() {
                                        return '<input type="checkbox" class="row-select-checkbox d-none">';
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
                            rowCallback: function(row, data, index) {
                                $(row).attr('consolidation_id', data.consolidation_id || '');
                                $(row).attr('type_id', data.type);
                                $(row).attr('account_type', data.account_type_id);

                                var checkbox = $('input.row-select-checkbox', row);
                                checkbox.prop('checked', $(row).hasClass('selected'));
                            }
                        });

                        $('#make_payments_datatable').on('click', '.select-checkbox', function () {
                                var $row = $(this).closest('tr');
                                var $checkbox = $row.find('.row-select-checkbox');
                                
                                var isChecked = $checkbox.prop('checked');
                                $checkbox.prop('checked', !isChecked);
                                console.log(!isChecked);
                                
                                if (!isChecked) {
                                    $('.select_none').removeClass('disabled');
                                } else {
                                    $('.select_none').addClass('disabled');
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
                    }
                });
            });
        });




    </script>
    


@endsection