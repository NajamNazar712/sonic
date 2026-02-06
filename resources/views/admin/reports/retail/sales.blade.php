@extends('admin.layout.master')

@section('title', 'Retail Sales Report')

@section('content')
    <h1 class="mb-1">
        Retail Sales Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">

                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                        </fieldset>
                    </div>

                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                            <select name="search_retail_franchise" id="search_retail_franchise" class="form-control select2">
                                @foreach($retail_franchises as $franchise)
                                    <option value="{{$franchise->id}}">{{$franchise->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                            <select name="search_retail_center" id="search_retail_center" class="form-control select2">
                                @foreach($retail_centers as $center)
                                    <option value="{{$center->id}}">{{$center->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach($cities as $origin)
                                    <option value="{{$origin->id}}">{{$origin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $destination)
                                    <option value="{{$destination->id}}">{{$destination->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_rncc_no" id="search_rncc_no" placeholder="Search RNCC Number">
                        </fieldset>
                    </div>


                    <div class="col-4">

                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" title="Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" title="Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>

                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
{{--                        <th class="border-primary border-darken-1">Account No.</th>--}}
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Franchise / Trax Center</th>
                        <th class="border-primary border-darken-1">Franchise / Trax Center Name</th>
                        <th class="border-primary border-darken-1">Booking Staff Name</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Payment Status</th>
                        <th class="border-primary border-darken-1">Payment ID</th>
                        <th class="border-primary border-darken-1">RNCC Number</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Origin Zone</th>
                        <th class="border-primary border-darken-1">Destination Zone</th>
                        <th class="border-primary border-darken-1">Attempts</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">Weight Charges</th>
                        <th class="border-primary border-darken-1">Fuel Surcharge</th>
                        <th class="border-primary border-darken-1">Product Insurance Value</th>
                        <th class="border-primary border-darken-1">Insurance Charges</th>
                        <th class="border-primary border-darken-1">Discount Amount</th>
                        <th class="border-primary border-darken-1">Admin Discount Amount</th>
                        <th class="border-primary border-darken-1">Packaging Charges</th>
                        <th class="border-primary border-darken-1">Flyer Charges</th>
                        <th class="border-primary border-darken-1">GST</th>
                        <th class="border-primary border-darken-1">Fintech Charges</th>
                        <th class="border-primary border-darken-1">WHT Charges</th>
                        <th class="border-primary border-darken-1">SST Charges</th>
                        <th class="border-primary border-darken-1">Total Charges</th>
                        <th class="border-primary border-darken-1">Net Payable</th>
                        <th class="border-primary border-darken-1">Delivered Date</th>
                        <th class="border-primary border-darken-1">Booking Staff ID</th>
                        <th class="border-primary border-darken-1">Reference</th>
                        
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

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

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
             $('#search_retail_center').prepend('<option value="" selected="selected"></option>').select2({
                 placeholder:'Select Retail Center',
                 width:'100%',
                 allowClear:true
             });
             $('#search_retail_franchise').prepend('<option value="" selected="selected"></option>').select2({
                 placeholder:'Select Retail Franchise',
                 width:'100%',
                 allowClear:true
             });
            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin City',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination City',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });


            var from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            var to_date = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.retail_sales.list') }}',
                        method:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No.');
                            // head.push('Account No.');
                            head.push('Shipper Name');
                            head.push('Franchise/Trax center');
                            head.push('Franchise/Trax center Name');
                            head.push('Booking Staff Name');
                            head.push('Status');
                            head.push('Payment Status');
                            head.push('Payment ID');
                            head.push('RNCC Number');
                            head.push('Service Type');
                            head.push('Arrival Date');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Origin Zone');
                            head.push('Destination Zone');
                            head.push('Attempts');
                            head.push('Product Type');
                            head.push('Collection Amount');
                            head.push('Actual Weight');
                            head.push('Weight Charges');
                            head.push('Fuel Surcharge');
                            head.push('Product Insurance Value');
                            head.push('Insurance Charges');
                            head.push('Discount Amount');
                            head.push('Admin Discount Amount');
                            head.push('Packaging Charges');
                            head.push('Flyer Charges');
                            head.push('GST');
                            head.push('WHT');
                            head.push('SST');
                            head.push('Total Charges');
                            head.push('Net Payable');
                            head.push('Delivered Date');
                            head.push('Booking Staff ID');
                            head.push('Reference');
                            
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                // row.push(values.account_no);
                                row.push(values.shipper_name);
                                row.push(values.franchise_center);
                                row.push(values.franchise_center_name);
                                row.push(values.booked_by);
                                row.push(values.current_status);
                                row.push(values.payment_status);
                                row.push(values.payment_id);
                                row.push(values.pncc_id);
                                row.push(values.service_type);
                                row.push(values.arrival_date);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.origin_zone);
                                row.push(values.destination_zone);
                                row.push(values.attempts);
                                row.push(values.category);
                                row.push(values.collection_amount);
                                row.push(values.actual_weight);
                                row.push(values.weight_charges);
                                row.push(values.fuel_surcharge);
                                row.push(values.product_value);
                                row.push(values.insurance_charges);
                                row.push(values.discount_amount);
                                row.push(values.admin_discount);
                                row.push(values.packaging_charges);
                                row.push(values.flyer_charges);
                                row.push(values.gst);
                                row.push(values.wht);
                                row.push(values.sst);
                                row.push(values.total_charges);
                                row.push(values.p_net_payable);
                                row.push(values.delivered_or_returned);
                                row.push(values.booked_by_id);
                                row.push(values.retail_reference);
                                

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Retail Sales Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.retail_sales.list') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_tracking = $('#search_tracking_no').val();
                        d.search_sales_person =  $('#sales_person_select').val();
                        d.search_retail_center = $('#search_retail_center').val();
                        d.search_retail_franchise = $('#search_retail_franchise').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_status = $('#search_status').val();
                        d.search_rncc_no = $('#search_rncc_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[11, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number_link'},
                    { data:'shipper_name' ,name: 'rsi.shipper_name', class: 'align-middle shipper_name'},
                    { data:'franchise_center' ,name: 'franchise_center', class: 'align-middle franchise_center',orderable: false, searchable: false},
                    { data:'franchise_center_name' ,name: 'franchise_center_name', class: 'align-middle franchise_center_name',orderable: false, searchable: false},
                    { data:'booked_by' ,name: 'ru.name', class: 'align-middle booked_by'},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle current_status'},
                    { data:'payment_status' ,name: 'sps.name', class: 'align-middle payment_status'},
                    { data:'payment_id' ,name: 'dps.id', class: 'align-middle payment_status'},
                    { data:'pncc_id' ,name: 'rcds.cash_deposit_id', class: 'align-middle pncc_id'},
                    { data:'service_type' ,name: 'rsm.name', class: 'align-middle service_type'},
                    { data:'arrival_date' ,name: 'sj.created_at', class: 'align-middle arrival_date'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle destination'},
                    { data:'hub' ,name: 'h.name', class: 'align-middle hub'},
                    { data:'origin_zone' ,name: 'oz.name', class: 'align-middle origin_zone'},
                    { data:'destination_zone' ,name: 'dz.name', class: 'align-middle destination_zone'},
                    { data:'attempts' ,name: 'attempts', class: 'align-middle attempts',sortable:false},
                    { data:'category' ,name: 'p.product_name', class: 'align-middle category'},
                    { data:'collection_amount' ,name: 'pps.amount', class: 'align-middle collection_amount'},
                    { data:'actual_weight' ,name: 'shipments.actual_weight', class: 'align-middle actual_weight'},
                    { data:'weight_charges' ,name: 'shipments.weight_charges', class: 'align-middle weight_charges'},
                    { data:'fuel_surcharge' ,name: 'shipments.fuel_surcharge', class: 'align-middle fuel_surcharge'},
                    { data:'product_value' ,name: 'si.price', class: 'align-middle product_value'},
                    { data:'insurance_charges' ,name: 'rs.insurance_charges', class: 'align-middle insurance_charges'},
                    { data:'discount_amount' ,name: 'rs.charges_with_discount', class: 'align-middle discount_amount'},
                    { data:'admin_discount' ,name: 'rs.admin_discount', class: 'align-middle admin_discount'},
                    { data:'packaging_charges' ,name: 'rs.packaging_charges', class: 'align-middle packaging_charges'},
                    { data:'flyer_charges' ,name: 'rs.flyer_charges', class: 'align-middle flyer_charges'},
                    { data:'gst' ,name: 'rs.gst', class: 'align-middle gst'},
                    { data:'fintech_charges' ,name: 'shipments.fintech_charges', class: 'align-middle fintech_charges'},
                    { data:'wht' ,name: 'rs.wht', class: 'align-middle wht'},
                    { data:'sst' ,name: 'rs.cod_sst', class: 'align-middle sst'},
                    { data:'total_charges' ,name: 'rs.total_charges', class: 'align-middle total_charges'},
                    { data: 'p_net_payable' ,name: 'pps.payable', class: 'align-middle net_payable'},
                    { data: 'delivered_or_returned' ,name: 'dr.created_at', class: 'align-middle delivered_or_returned'},
                    { data: 'booked_by_id' ,name: 'ru.id', class: 'align-middle booked_by_id'},
                    { data: 'retail_reference' ,name: 'rref.ref', class: 'align-middle retail_reference'},
                    
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            var select_rncc = $('#search_rncc_no').selectize({
                placeholder: 'RNCC Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
                onType: function (str) {
                    var regex = /^[0-9,]+$/;
                    if (!regex.test(str)) {
                        select_rncc[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (input.length >= 2 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });


        });
    </script>
@endsection