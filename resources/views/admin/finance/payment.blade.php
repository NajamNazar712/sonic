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
        window.addEventListener('message', function(event) {
            var selectedData = event.data;
            console.log(selectedData);
            $.ajax({
                url: "{{ route('admin.finance.make_payments.payment_list') }}",
                data: { ids: selectedData },
                success: function(response) {
                    // populate datatable
                    var table = $('#make_payments_datatable').DataTable({
                        destroy: true, // Destroy existing table (if any)
                        data: response.data, // Set data from response
                        columns: [
                            { data: 'id' },
                            { data: 'account_type_id' },
                            { data: 'shipper' },
                            { data: 'shipment' },
                            { data: 'origin' },
                            { data: 'type' },
                            { data: 'status' },
                            { data: 'arrival_date' },
                            { 
                                data: null, // Example for computed column
                                render: function(data, type, row) {
                                    // Example computation
                                    return (parseFloat(row.charges) + parseFloat(row.gst)).toFixed(2);
                                }
                            },
                            { data: 'amount' },
                            { data: 'charges' },
                            { data: 'gst' },
                            { data: 'wht' },
                            { data: 'fintech_charges' },
                            { data: 'packaging_charges' },
                            { 
                                data: null, // Example for computed column
                                render: function(data, type, row) {
                                    // Example computation
                                    return (parseFloat(row.payable) - parseFloat(row.fintech_charges)).toFixed(2);
                                }
                            },
                            { data: 'payable' },
                            { data: 'arrival_date' }
                        ]
                    });
                }
            });
        });
    </script>
    
        

@endsection