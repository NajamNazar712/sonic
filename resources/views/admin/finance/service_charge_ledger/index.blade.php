@extends('admin.layout.master')
@section('title','Live Shipper Ledger')


@section('content')
    <h1 class="mb-1">
        Live Shipper Ledger
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form action="{{ route('admin.finance.shipment_ledger.list') }}" method="GET" id="search_form">
                    <div class="row">

                        <div class="col-4">
                            <label for="">Select Shipper</label>
                            <fieldset class="form-group">
                                <select name="search_shipper[]" id="search_shipper" class="form-control select2"  data-rule-required="true" data-msg-required="Shipper is required">
                                </select>
                                <span class="text-danger d-none" id="shipper_error">This field is required</span>
                            </fieldset>
                        </div>

                        <div class="col-4">
                            <label for="">Date (From)</label>
                            <div class="form-group input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type="text" name="from_date" data-value="{{ Carbon\Carbon::today()->subMonth(1) }}" id="from_date" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Date (From)" data-rule-required="true" data-msg-required="From date is required">
                            </div>
                            <span class="text-danger d-none" id="from_date_error">This field is required</span>
                        </div>

                        <div class="col-4">
                            <label for="">Date (To)</label>
                            <div class="form-group input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type="text" data-value="{{ Carbon\Carbon::today() }}" name="to_date" id="to_date" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Date (To)" data-rule-required="true" data-msg-required="To date is required">
                            </div>
                            <span class="text-danger d-none" id="to_date_error">This field is required</span>
                        </div>
                        <div class="ml-2 mt-2" id="search_btn_div">
                            <button type="button" class="btn btn-primary" id="search_btn">
                                Search
                            </button>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">Date</th>
                                        <th class="border-primary border-darken-1">Tracking Number</th>
                                        <th class="border-primary border-darken-1">Payment Type</th>
                                        <th class="border-primary border-darken-1">Shipper</th>
                                        <th class="border-primary border-darken-1">Debit</th>
                                        <th class="border-primary border-darken-1">Credit</th>
                                        <th class="border-primary border-darken-1">Balance</th>
                                        <th class="border-primary border-darken-1">Reference</th>
                                        <th class="border-primary border-darken-1">Payment Status</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th colspan="4">Total</th>
                                    <th class="total-debit"></th>
                                    <th class="total-credit"></th>
                                    <th class="total-balance"></th>
                                    <th colspan="2"></th>
                                </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>

                    {{-- Modal --}}
                    <div class="modal fade" id="shipmentModal" tabindex="-1" role="dialog" aria-labelledby="shipmentModalLabel" aria-hidden="true">
                        <div class="modal-dialog info_modal" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="shipmentModalLabel">Shipment Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="modal-content">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th>Tracking Number</th>
                                                    <td id="tracking-number"></td>
                                                </tr>
                                                <tr>
                                                    <th>Origin</th>
                                                    <td id="origin"></td>
                                                </tr>
                                                <tr>
                                                    <th>Destination</th>
                                                    <td id="destination"></td>
                                                </tr>
                                                <tr>
                                                    <th>COD Amount</th>
                                                    <td id="cod-amount"></td>
                                                </tr>
                                                <tr>
                                                    <th>Type of Charges</th>
                                                    <td id="type-of-charges"></td>
                                                </tr>
                                                <tr>
                                                    <th>Weight Charges</th>
                                                    <td id="weight-charges"></td>
                                                </tr>
                                                <tr>
                                                    <th>Fuel Surcharge</th>
                                                    <td id="fuel-surcharge"></td>
                                                </tr>
                                                <tr>
                                                    <th>GST</th>
                                                    <td id="gst"></td>
                                                </tr>
                                                <tr>
                                                    <th>Net Payable</th>
                                                    <td id="net-payable"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

    <style>
        .info_modal{
            max-width: 110rem;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
    $(document).ready(function() {

        $("body").delegate('.payment_print','click', function() {
            var id = $(this).attr('data-id');
            var shipment_type = $(this).attr('data-shipment_type');

            if (shipment_type == 1) {
                var url = '{!! route('admin.finance.done_payments.details_print') !!}';
            } else {
                var url = '{!! route('admin.finance.retail.done_payments.details_print') !!}';
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': id
                }
            })
                .done(function(data) {
                    var tab = window.open('', '_blank');

                    if (!tab) {
                        swal({
                            title: 'Popup Blocker Enabled!',
                            text: 'Please add this site to your exception list.',
                            icon: 'error',
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                    } else {
                        tab.document.write(data);
                        tab.document.close();
                        tab.focus();
                    }
                });
        });
        // from date
        $('#search_form #from_date').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function(context) {
                if (context.select) {
                    $('#search_form #from_date').pickadate('picker').set('min', $('#search_from #from_date').pickadate('picker').get('select'));
                }
            }
        });

        // to date
        $('#search_form #to_date').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function(context) {
                if (context.select) {
                    $('#search_form #to_date').pickadate('picker').set('min', $('#search_from #to_date').pickadate('picker').get('select'));
                }
            }
        });

        $('#search_shipper').select2({
            width:'100%',
            placeholder:"Select Shipper",
            allowClear:true,
            minimumInputLength: 2,
            ajax: {
                dataType: 'json',
                url:  '{!! route('admin.accounts.shipper_names.dropdown',['type'=>'active']) !!}',
                data: function (params) {
                    return {
                        search: params.term,
                        account_type_id: [1],
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                delay: 700,
            }
        });

        $('#search_btn').on('click', function () {
            var shippers = $('#search_shipper').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            if (shippers == '' || from_date == '' || to_date == '') {
                $('#shipper_error, #from_date_error, #to_date_error').removeClass('d-none');
            } else {
                var formData = {
                    shippers: $('#search_shipper').val(),
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val()
                };

                $.ajax({
                    url: "{{ route('admin.finance.shipment_ledger.list') }}",
                    data: { formData },
                    success: function (response) {
                        if ($.fn.DataTable.isDataTable('#datatable')) {
                            $('#datatable').DataTable().destroy();
                        }

                        // Initialize DataTable with response data
                        var table = $('#datatable').DataTable({
                            data: response.data,
                            dom: 'Bltipr', // Add 'B' for Buttons
                            searching: true,
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: 'Export to Excel',
                                    title: 'Data Export'
                                }
                            ],
                            lengthMenu: [[5000, 10000, 20000, 30000, -1], [5000, 10000, 20000, 30000, 'All']],
                            columns: [
                                { data: 'created_at' },
                                { data: 'tracking_number' },
                                { data: 'type' },
                                { data: 'user_name' },
                                { data: 'debit' },
                                { data: 'credit' },
                                { data: 'balance' },
                                { data: 'reference_id' },
                                { data: 'payment_status_journey' },
                            ],
                            footerCallback: function (row, data, start, end, display) {
                                var api = this.api();

                                // Helper function to format numbers as needed
                                function numberFormat(num, decimals = 2) {
                                    return num.toLocaleString(undefined, {
                                        minimumFractionDigits: decimals,
                                        maximumFractionDigits: decimals
                                    });
                                }
                                // Calculate total debit and credit
                                var totalDebit = api.column(4).data().reduce(function (a, b) {
                                    console.log(parseFloat(b.replace(/,/g, '')));
                                    return a + parseFloat(b.replace(/,/g, '') || 0);
                                }, 0);

                                var totalCredit = api.column(5).data().reduce(function (a, b) {
                                    return a + parseFloat(b.replace(/,/g, '') || 0);
                                }, 0);

                                var totalBalance = parseFloat(api.column(6).data().toArray().slice(-1)[0].replace(/,/g, '') || 0);


                                // Update footer with totals
                                $(api.column(4).footer()).html(numberFormat(totalDebit));
                                $(api.column(5).footer()).html(numberFormat(totalCredit));
                                $(api.column(6).footer()).html(numberFormat(totalBalance));

                            },
                            initComplete: function () {
                                var api = this.api();
                                $('#datatable thead tr.search').remove();
                                var searchRow = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(api.table().header());
                                api.columns().every(function (index) {
                                    var column = this;
                                    var inputHTML = `
                                <td style="padding:5px;" class="border-primary border-lighten-2">
                                    <fieldset class="form-group m-0 position-relative has-icon-right">
                                        <input type="text" class="form-control form-control-sm input-sm primary" placeholder="Search">
                                        <div class="form-control-position primary">
                                            <i class="la la-search"></i>
                                        </div>
                                    </fieldset>
                                </td>
                            `;
                                    var input = $(inputHTML).appendTo(searchRow);
                                    $('input', input).on('keyup change clear', function () {
                                        if (column.search() !== this.value) {
                                            column.search(this.value).draw();
                                        }
                                    });
                                });
                            }
                        });


                    }
                });
            }
        });

// Helper function to format numbers with 2 decimal places
        function number_format(number, decimals) {
            return Number(number).toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        }





    });
    </script>

@endsection