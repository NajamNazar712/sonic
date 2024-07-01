@extends('client.layout.master')
@section('title','Service Ledger')


@section('content')
    <h1 class="mb-1">
        Service Ledger
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form action="{{ route('admin.finance.shipment_ledger.list') }}" method="GET" id="search_form">
                    <div class="row">
                        <div class="col-5">
                            <label for="">Date (From)</label>
                            <div class="form-group input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type="text" name="from_date" id="from_date" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Date (From)">
                                </div>
                            <span class="text-danger d-none" id="from_date_error">This field is required</span>
                        </div>
    
                        <div class="col-5">
                            <label for="">Date (To)</label>
                            <div class="form-group input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type="text" name="to_date" id="to_date" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Date (To)">
                            </div>
                            <span class="text-danger d-none" id="to_date_error">This field is required</span>
                        </div>
                        <div class="ml-2" id="search_btn_div">
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
                                        <th class="border-primary border-darken-1">Particulars</th>
                                        <th class="border-primary border-darken-1">Debit</th>
                                        <th class="border-primary border-darken-1">Credit</th>
                                        <th class="border-primary border-darken-1">Balance</th>
                                        <th class="border-primary border-darken-1">Referenece</th>
                                        <th class="border-primary border-darken-1">Number of shipments</th>
                                    </tr>
                                </thead>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

    <style>
        .info_modal{
            max-width: 110rem;
        }

        #search_btn_div{
            margin: 29px 0px 0px 0px;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
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

        $('#search_btn').on('click', function () {
            
            if ($('#from_date').val() == '' || $('#to_date').val() == '' || ($('#from_date').val() == '' && $('#to_date').val() == '')){
                $('#from_date_error, #to_date_error').removeClass('d-none');
            }

            else {
                $('#from_date_error, #to_date_error').addClass('d-none');
                var formData = {
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val()
                };

                $.ajax({
                    url: "{{ route('cod.finance.shipment_ledger.list') }}",
                    data: { formData },
                    success: function (response) {
                        if ($.fn.DataTable.isDataTable('#datatable')) {
                            $('#datatable').DataTable().destroy();
                        }
                        var table = $('#datatable').DataTable({
                            data: response.data,
                            dom: 'ltipr',
                            searching: true,
                            columns: [
                                { data: 'shipment_book_date' },
                                { data: 'particulars' },
                                { data: 'debit' },
                                { data: 'credit' },
                                { data: 'balance' },
                                { data: 'reference_id' },
                                {
                                    data: 'number_of_shipments',
                                    render: function (data, type, row) {
                                        return '<a href="#" class="shipment-link" data-id="' + row.id + '">' + data + '</a>';
                                    }
                                }
                            ],
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

                                // Add click event for shipment links
                                $('#datatable').on('click', 'a.shipment-link', function (e) {
                                    e.preventDefault();
                                    var shipmentId = $(this).data('id');
                                    var shipmentData = response.data.find(item => item.id == shipmentId);

                                    $('#tracking-number').text(shipmentData.tracking_number);
                                    $('#origin').text(shipmentData.origin_city_name);
                                    $('#destination').text(shipmentData.destination_city_name);
                                    $('#cod-amount').text(shipmentData.cod_amount);
                                    $('#type-of-charges').text(shipmentData.type_of_charges);
                                    $('#weight-charges').text(shipmentData.weight_charges);
                                    $('#fuel-surcharge').text(shipmentData.fuel_surcharge);
                                    $('#gst').text(shipmentData.gst);
                                    $('#net-payable').text(shipmentData.net_payable);

                                    $('#shipmentModal').modal('show');
                                });

                            }
                        });
                    }
                });
            }
        });



    }); 
    </script>

@endsection