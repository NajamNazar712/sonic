@extends('admin.layout.master')

@section('title', 'Quality of Service Report')

@section('content')
    <h1 class="mb-1">
        Quality of Service Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-start">

                    <div class="col-4">
                            <fieldset class="form-group">
                                <select name="search_shipper[]" id="search_shippers" class="form-control select2" multiple required data-rule-required="true" data-msg-required="This field is required">
                                </select>
                            </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
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
                            <select name="search_qsr" id="search_qsr" class="form-control select2">
                                <option value="1">Delivery</option>
                                <option value="2">Return</option>
                                <option value="3">All</option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipment_status" id="search_shipment_status" class="form-control select2">
                                @foreach($shipment_status as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shippimg_modes" id="search_shippimg_modes" class="form-control select2">
                                @foreach($shippimg_modes as $shippimg_mode)
                                    <option value="{{$shippimg_mode->id}}">{{$shippimg_mode->mode}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_types" id="search_types" class="form-control select2">
                                @foreach($types as $id => $type)
                                    <option value="{{ $id }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <select name="sub_segment_select" id="sub_segment_select" class="select2">
                            @foreach($sub_segments as $sub_segment)
                                <option value="{{$sub_segment->id}}">{{$sub_segment->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-4">
                        <select name="service_type_select" id="service_type_select" class="select2">
                            @foreach($service_types as $service_type)
                                <option value="{{$service_type->id}}">{{$service_type->booking_type}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_kam" id="search_kam" class="form-control select2">
                                @foreach($kam_sales as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_sale_person" id="search_sale_person" class="form-control select2">
                                @foreach($kam_sales as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <select name="search_area" id="search_area" class="select2">
                            @foreach($areas as $area)
                                <option value="{{$area->id}}">{{$area->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-4 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Current Status From</span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Current Status To</span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    {{--todo new--}}
                    <div class="col-4 ">
                        {{--                        <label>Arrival Date From</label>--}}
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Arrival Date From</span>
                            </span>
                            </div>
                            <input type="text" name="from_date1" class="form-control bg-primary border-primary white rounded-right" id="from_date1" placeholder="Arrival Date From">
                        </div>
                    </div>
                    <div class="col-4">
                        {{--                        <label>Arrival Date To</label>--}}
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Arrival Date To</span>
                            </span>
                            </div>
                            <input type="text" name="to_date1" class="form-control bg-primary border-primary white rounded-right" id="to_date1" placeholder="Arrival Date To">
                        </div>
                    </div>
                    {{--todo new end--}}

                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <div class="row justify-content-end">
                <div class="col-4">
                <h4 for="export" class="font">Excel Column(s):</h4>

                            <fieldset class="form-group">
                                <select name="export[]" id="export" class="form-control select2" multiple="multiple">
                                    <option value="selectAll">Select All</option>
                                </select>
                            </fieldset>
                        </div>
                </div>
                
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Sales Person</th>
                        <th class="border-primary border-darken-1">KAM</th>
                        <th class="border-primary border-darken-1">Sub Segment</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">First Attempt Date</th>
                        <th class="border-primary border-darken-1">Rider Picked Status Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        {{-- <th class="border-primary border-darken-1">Location Status Area</th>
                        <th class="border-primary border-darken-1">Location Status</th> --}}

                        <th class="border-primary border-darken-1">Last Location Screen Name</th>
                        <th class="border-primary border-darken-1">Sub Hub</th>
                        <th class="border-primary border-darken-1">Last Location Updated At</th>
                        <th class="border-primary border-darken-1">Entry Method</th>

                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Total Attempt</th>
                        <th class="border-primary border-darken-1">History Status</th>
                        {{-- <th class="border-primary border-darken-1">History Status Location</th> --}}
                        <th class="border-primary border-darken-1">Cargo Status</th>
                        <th class="border-primary border-darken-1">Bag Seal Number</th>
                        <th class="border-primary border-darken-1">Bag Status</th>
                        <th class="border-primary border-darken-1">Service</th>
                        <th class="border-primary border-darken-1">Origin Arrival Date</th>
                        <th class="border-primary border-darken-1">Destination Arrival Date</th>
                        <th class="border-primary border-darken-1">Last Status Date</th>
                        <th class="border-primary border-darken-1">Booked Status Date</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Consignee City Area</th>
                        <th class="border-primary border-darken-1">Concerned Hub</th>
                        <th class="border-primary border-darken-1">Return City</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        {{-- <th class="border-primary border-darken-1">Product Type</th> --}}
                        <th class="border-primary border-darken-1">Product Description</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Aging (Arrival)</th>
                        <th class="border-primary border-darken-1">Aging (Last Status)</th>
                        {{-- <th class="border-primary border-darken-1">Request #</th> --}}
                        {{-- <th class="border-primary border-darken-1">Request Status</th> --}}
                        {{-- <th class="border-primary border-darken-1">Case Nature</th> --}}
                        {{-- <th class="border-primary border-darken-1">Case Nature Type</th> --}}
                        {{-- <th class="border-primary border-darken-1">Adjusted amount</th> --}}
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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

        /* #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        } */

        #toast-top-full-width {
            position: fixed !important;
            width: 100% !important;
            top: 0 !important;
            left: 0 !important;
            text-align: center !important;
        }
        .toast-top-full-width .toast {
            width: 90rem !important;
        }
        .toast-top-full-width .toast-message {
            font-size: 24px !important;
        }
        .toast-title{
            display: none !important;
        }

    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_shipment_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });
            $('#search_shippers').select2({
                width:'100%',
                placeholder:"Select Shipper",
                allowClear:true,
                multiple: true,
                minimumInputLength: 2,
                ajax: {
                    dataType: 'json',
                    url:  '{!! route('admin.accounts.shipper_names.dropdown',['type'=>'active']) !!}',
                    data: function (params) {
                        return {
                            search: params.term,
                            sub_segment_select : $('#sub_segment_select').val()
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
            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin City',
                width:'100%',
                allowClear:true
            });
            $('#search_concerned_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Concerned Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination City',
                width:'100%',
                allowClear:true
            });
            $('#search_qsr').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select QSR',
                width:'100%',
                allowClear:true
            });
            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Zone',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_shippimg_modes').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
                width:'100%',
                allowClear:true
            });
            $('#search_types').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Type',
                width:'100%'
            });
            $('#sub_segment_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Sub Segment',
                allowClear: true
            });
            $('#service_type_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Search Service Type'
            });
            $('#search_kam').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Search KAM'
            });
            $('#search_sale_person').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Search Sales Person'
            });
            
            $('#search_area').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Area',
                width:'100%',
                allowClear:true
            });

            $('#export').select2({
                width:'100%',
                placeholder:"Excel Column(s)",
                allowClear:true,
            });
            $('#export').on('select2:select', function(e) {
                var selectAll = $('#export').find('option[value="selectAll"]');
                var firstOption = $('#export option').first();
                if (e.params.data.id === 'selectAll') {
                    firstOption.data().data.text = 'Un Select All';
                    $('#export').find('option').not(selectAll).prop('selected', true).trigger('change');
                    
                }
            });
            $('#export').on('select2:unselect', function(e) {
                var selectAll = $('#export').find('option[value="selectAll"]');
                var firstOption = $('#export option').first();
                if (e.params.data.id === 'selectAll') {
                    firstOption.data().data.text = 'Select All';
                    $('#export').find('option').not(selectAll).prop('selected', false).trigger('change');
                }
            });

            var from_max = '{{ Carbon\Carbon::now() }}';
            var to_max = '{{ Carbon\Carbon::now() }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: from_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    var old_date_formatted = $('input[name="from_date_formatted"]').val();
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: to_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    var current_date_formatted = $('input[name="to_date_formatted"]').val();
                    from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
                }
            });

            var booking_from_date = $('#from_date1').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#to_date1').pickadate('picker').set('min', $('#from_date1').pickadate('picker').get('select'));
                    }
                }
            });
            var booking_to_date = $('#to_date1').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#from_date1').pickadate('picker').set('max', $('#to_date1').pickadate('picker').get('select'));
                    }
                }
            });

            $('#from_date1').change(function() {
                var selectedOption = $(this).val();
                if (selectedOption != null)
                {
                    $('#from_date').val(null).trigger('change');
                    $('#to_date').val(null).trigger('change');
                    // console.log(selectedOption);
                }
            });
            $('#to_date1').change(function() {
                var selectedOption = $(this).val();
                if (selectedOption != null)
                {
                    $('#from_date').val(null).trigger('change');
                    $('#to_date').val(null).trigger('change');
                    // console.log(selectedOption);
                }
            });

            // $('#from_date').change(function() {
            //     console.log('jjj');
            //     var selectedOption = $(this).val();
            //     if (selectedOption != null)
            //     {
            //         $('#from_date1').val(null).trigger('change');
            //         $('#to_date1').val(null).trigger('change');
            //         // console.log(selectedOption);
            //     }
            // });
            // $('#to_date').change(function() {
            //     console.log('jjj');
            //     var selectedOption = $(this).val();
            //     if (selectedOption != null)
            //     {
            //         $('#from_date1').val(null).trigger('change');
            //         $('#to_date1').val(null).trigger('change');
            //         // console.log(selectedOption);
            //     }
            // });

            var index_column = [];
            var flag = false;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'QSR Report',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        action: function(e){
                                    if ($('#export').val().length === 0) {
                                        swal({
                                            text: 'Atleast 1 column should be selected for export.!',
                                            title: 'Please select excel column(s)',
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'OK',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                }
                                            },
                                            dangerMode: true
                                        })
                                        return;
                                    }
                                    $.ajax({
                                        url: "{{ route('admin.reports.qsr.list') }}",
                                        method: "POST",
                                        data: {
                                            excel: true,
                                            _token: $('meta[name="csrf-token"]').attr('content'),
                                            search_shipment_status: $('#search_shipment_status').val(),
                                            sub_segment: $('#sub_segment_select').val(),
                                            search_shippers: $('#search_shippers').val(),
                                            search_origin: $('#search_origin').val(),
                                            search_destination: $('#search_destination').val(),
                                            search_qsr: $('#search_qsr').val(),
                                            search_zone: $('#search_zone').val(),
                                            search_hub: $('#search_hub').val(),
                                            search_shipping_mode: $('#search_shippimg_modes').val(),
                                            search_from: $('input[name="from_date_formatted"]').val(),
                                            search_to: $('input[name="to_date_formatted"]').val(),
                                            arrival_search_from: $('input[name="from_date1_formatted"]').val(),
                                            arrival_search_to: $('input[name="to_date1_formatted"]').val(),
                                            search_types: $('#search_types').val(),
                                            selectedValue: $('#export').val(),
                                            selectedTexts: $('#export option:selected').map(function() {
                                                return $(this).text()
                                            }).get(),
                                            service_type_select: $('#service_type_select').val(),
                                            search_kam: $('#search_kam').val(),
                                            search_sale_person: $('#search_sale_person').val(),
                                            search_area :  $("#search_area").val()
                                        },
                                        beforeSend: function() {
                                            swal({
                                                title: 'Please Wait!',
                                                text: 'Downloading is in progress',
                                                icon: 'info',
                                                buttons: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false
                                            });
                                        },
                                        complete: function() {
                                            // Hide loader
                                            swal.close();
                                        },
                                        success: function(response) {
                                            var blob = new Blob([response], {
                                                type: 'text/csv'
                                            });
                                            var url = window.URL.createObjectURL(blob);
                                            var a = document.createElement('a');
                                            a.href = url;
                                            a.download = 'QSR Report.csv';
                                            document.body.appendChild(a);
                                            a.click();
                                            window.URL.revokeObjectURL(url);
                                            document.body.removeChild(a);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Failed to fetch CSV data:', status, error);
                                        }
                                    });
                                }
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                ajax: {
                    url: '{{ route('admin.reports.qsr.list') }}',
                    method: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: function (d) {
                        d.search_shipment_status = $('#search_shipment_status').val();
                        d.sub_segment = $('#sub_segment_select').val();
                        d.search_shippers = $('#search_shippers').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_qsr = $('#search_qsr').val();
                        d.search_zone = $('#search_zone').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_shipping_mode = $('#search_shippimg_modes').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                        d.arrival_search_from = $('input[name="from_date1_formatted"]').val();
                        d.arrival_search_to = $('input[name="to_date1_formatted"]').val();
                        d.search_types = $('#search_types').val();
                        d.selectedValue = $('#export').val();
                        d.search_area = $("#search_area").val();

                        d.selectedTexts = $('#export option:selected').map(function () {
                            return $(this).text()
                        }).get();
                        d.service_type_select = $('#service_type_select').val();
                        d.search_kam = $('#search_kam').val();
                        d.search_sale_person = $('#search_sale_person').val();
                    }
                },
                rowId: 'shId',
                order: [[22, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link', text:'Tracking Number', value:'tracking_number',download:true},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id' ,text:'Order ID', value:'order_id',download:true},
                    {data: 'account_no', name: 'u.id', class: 'align-middle account_no', text:'Account No.', value:'account_no',download:true},
                    {data: 'shipper', name: 'u.name', as:'shipper', class: 'align-middle shipper', text:'Shipper',value:'shipper',download:true},
                    {data: 'sales_person_name', name: 'sales_person.name', as:'sales_person_name', class: 'align-middle sales_person_name', text:'Sales Person',value:'sales_person_name',download:true},
                    {data: 'kam', name: 'kam', as:'kam', class: 'align-middle kam', text:'KAM',value:'kam',download:true},
                    {data: 'sub_segment', name: 'scs.name', as:'sub_segment', class: 'align-middle sub_segment',text:'Sub Segment', value:'sub_segment',download:true},
                    {data: 'name', name: 'shipments.consignee_name', class: 'align-middle name',text:'Consignee Name', value:'name',download:true},
                    {data: 'first_attempt_date', name: 'sjfa.created_at', as:'first_attempt_date', class: 'align-middle first_attempt_date',text:'First Attempt Date',value:'first_attempt_date',download:true},
                    {data: 'rider_picked_status_date', name: 'sjrp.created_at', as:'rider_picked_status_date', class: 'align-middle rider_picked_status_date',text:'Rider Picked Status Date',value:'rider_picked_status_date',download:true},
                    {data: 'status', name: 'ss.name', as:'status', class: 'align-middle status', text:'Status', value:'status',download:true},
                    // {data: 'scanning_city_area_name', name: 'ca_scanning.name', as:'scanning_city_area_name', class: 'align-middle scanning_city_area_name',text:'Location Status Area',value:'scanning_city_area_name',download:true},
                    // {data: 'location_status', name: 'ssjal.location_status', as:'location_status', class: 'align-middle location_status',text:'Location Status',value:'location_status',download:true},
                    
                    {data: 'last_location_screen_location_name', name: 'last_screen_location.name', as:'last_location_screen_location_name', class: 'align-middle last_location_screen_location_name',text:'Last Location Screen Location Name',value:'last_location_screen_location_name',download:true},
                    {data: 'ca_scanning_last_location_name', name: 'ca_scanning_last_location_name.name', as:'ca_scanning_last_location_name', class: 'align-middle ca_scanning_last_location_name',text:'Sub Hub',value:'ca_scanning_last_location_name',download:true},
                    {data: 'last_location_updated_at', name: 'ssjal_last_location.updated_at', as:'last_location_updated_at', class: 'align-middle last_location_updated_at',text:'Last Location Updated At',value:'last_location_updated_at',download:true},
                    {data: 'entry_method', name: 'ssj_last_location.entry_method', as:'entry_method', class: 'align-middle entry_method',text:'Entry Method',value:'entry_method',download:true},

                    {data: 'reason', name: 'ssr.name', as:'reason', class: 'align-middle reason',text:'Reason',value:'reason',download:true},
                    {data: 'remarks', name: 'sjr.remarks', class: 'align-middle remarks',text:'Remarks',value:'remarks',download:true},
                    {data: 'total_attempt' ,name: 'total_attempt', class: 'align-middle total_attempt',text:'Total Attempt',value:'total_attempt',download:true},
                    {data: 'history_status', name: 'hss.name', as:'history_status', class: 'align-middle history_status',text:'History Status',value:'history_status',download:true},
                    // {data: 'location_status_hss', name: 'ssjal_hss.location_status', as:'location_status_hss', class: 'align-middle location_status_hss',text:'History Location Status',value:'location_status_hss',download:true},
                    {data: 'cargo_status', name: 'cargo_status.name', as:'cargo_status', class: 'align-middle history_status',text:'Cargo Status',value:'cargo_status',download:true},
                    {data: 'seal_number', name: 'cmb.seal_number', class: 'align-middle history_status',text:'Bag Seal Number',value:'seal_number',download:true},
                    {data: 'bag_status', name: 'bs.name', as:'bag_status', class: 'align-middle history_status',text:'Bag Status',value:'bag_status',download:true},
                    {data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type',text:'Service Type',value:'service_type',download:true},
                    {data: 'arrival', name: 'sj.created_at', as:'arrival', class: 'align-middle arrival',text:'Origin Arrival Date',value:'arrival',download:true},
                    // New column start
                    {data: 'destination_arrival_date', as: 'destination_arrival_date', name:'destination_arrival_date', class: 'align-middle destination_arrival_date', text:'Destination Arrival Date', value:'destination_arrival_date', download:true},
                    // New column end
                    {data: 'last_status_date', name: 'journey.created_at', as:'last_status_date', class: 'align-middle last_status_date',text:'Last Status Date',value:'last_status_date',download:true},
                    {data: 'created_at', name: 'shipments.created_at', as:'created_at', class: 'align-middle created_at',text:'Booked Status Date',value:'created_at',download:true},
                    {data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode',text:'Shipping Mode',value:'shipping_mode',download:true},
                    {data: 'origin', name: 'oc.name', as:'origin', class: 'align-middle origin',text:'Origin',value:'origin',download:true},
                    {data: 'destination', name: 'dc.name', as:'destination', class: 'align-middle destination',text:'Destination',value:'destination',download:true},
                    {data: 'hub', name: 'h.name', as:'hub', class: 'align-middle hub',text:'Hub',value:'hub',download:true},
                    {data: 'area', name: 'ca.name', as:'area', class: 'align-middle area',text:'Consignee City Area',value:'area',download:true},
                    {data: 'current_hub', name: 'cmbh.name', as:'current_hub_name', class: 'align-middle current_hub',text:'Concerned Hub',value:'current_hub_name',download:true},
                    {data: 'return_city', name: 'rc.name',as:'return_city', class: 'align-middle return_city',text:'Return City',value:'return_city',download:true},
                    {data: 'zone', name: 'z.name',as:'zone',class: 'align-middle zone',text:'Zone',value:'zone',download:true},
                    // {data: 'product_type', name: 'p.product_name', class: 'align-middle product_type',text:'Product Type',value:'product_type',download:true},
                    {data: 'description', name: 'si.description', class: 'align-middle description',text:'Product Description',value:'description',download:true},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount',text:'Amount',value:'amount',download:true},
                    {data: 'aging', name: 'aging', class: 'align-middle aging',orderable: false, searchable: false,text:'Aging (Arrival)',value:'aging',download:true},
                    {data: 'aging_last_status', name: 'aging_last_status', class: 'align-middle aging',orderable: false, searchable: false,text:'Aging (Last Status)',value:'aging_last_status',download:true},
                    // {data: 'crm_id_padded_link', name: 'cr.id', as:'crm_request_id', class: 'align-middle crm_id_padded',text:'Request #',value:'crm_id_padded',download:true},
                    // {data: 'crm_request_status', name: 'crs.name', as:'crm_request_status', class: 'align-middle crm_request_status',text:'Request Status',value:'crm_request_status',download:true},
                    // {data: 'crm_request_case_nature', name: 'crcn.name',as:'crm_request_case_nature', class: 'align-middle crm_request_case_nature',text:'Case Nature',value:'crm_request_case_nature',download:true},
                    // {data: 'crm_request_case_nature_type', name: 'crcnt.type', class: 'align-middle crm_request_case_nature_type',text:'Case Nature Type',value:'crm_request_case_nature_type',download:true},
                    // {data: 'adjusted_amount', name: 'adjustment.adjustment_amount', class: 'align-middle adjusted_amount',text:'Adjusted amount',value:'adjusted_amount',download:true},
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
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var arrival_from_date = $('#from_date1').val();
                var arrival_to_date = $('#to_date1').val();

                var search_qsr = $('#search_qsr').val();
                var search_types = $('#search_types').val();
                var search_shippimg_modes = $('#search_shippimg_modes').val();
                var search_shipment_status = $('#search_shipment_status').val();

                // Check if any dropdown has a selected value
                var isDropdownSelected = search_qsr || search_types || search_shippimg_modes || search_shipment_status;

                // Check if either pair of dates is filled
                var isFromDateFilled = from_date && to_date;
                var isArrivalDateFilled = arrival_from_date && arrival_to_date;

                if (isDropdownSelected) {
                    if (!isFromDateFilled && !isArrivalDateFilled) {
                        toastr.error('Please fill in either the "From Date" and "To Date" pair or the "Arrival From Date" and "Arrival To Date" pair.', 'Error!', {
                            positionClass: 'toast-top-full-width',
                            containerId: 'toast-top-full-width'
                        });
                        return false;
                    } else {
                        table.draw();
                    }
                } else {
                    table.draw();
                }

                // table.draw();
            });

            let option = '';
            var columnNames2 = table.settings().init().columns.map(function (column) {
                if(column.download){
                    let col_name = column.value;
                    let col_text = column.text;
                
                    // if(column.as){
                    //     col_name+= ' as ' +column.as;
                    // }
                    if (col_name && col_text) {
                        option += `<option value="${col_name}">${col_text}</option>`;
                    }
                }
            });
            $('#export').append(option).select2({
                columns: 1,
                placeholder: 'Excel Column(s)',
                search: true,
                selectAll: true
            });

            var sub_segment_select = $('#sub_segment_select');
            var shippers_select = $('#search_shippers');
            {{--sub_segment_select.on('change', function(){--}}
            {{--    var sub_segment_value = sub_segment_select.val();--}}
            {{--    $.ajax({--}}
            {{--        url: "{{ route('admin.reports.qsr.updated_shippers_list') }}",--}}
            {{--        data: {--}}
            {{--            sub_segment_value--}}
            {{--        },--}}
            {{--        success: function (response) {--}}
            {{--            var shippers = response.data;--}}
            {{--            shippers_select.empty();--}}
            {{--            shippers.forEach(function(shipper) {--}}
            {{--                var newOption = new Option(shipper.name, shipper.id, false, false);--}}
            {{--                shippers_select.append(newOption);--}}
            {{--            });--}}
            {{--            shippers_select.trigger('change');--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}
        });

    </script>
@endsection