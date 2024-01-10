@extends('admin.layout.master')

@section('title', 'Weight QC')

@section('content')
    <h1 class="mb-1">
        Weight QC
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="col-3 mb-1">
                        <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                                @foreach($shipping_modes as $mode)
                                    <option value="{{$mode->id}}">{{$mode->mode}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="search_user" id="search_user" class="form-control select2">
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="search_origin_hub" id="search_origin_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="search_destination_hub" id="search_destination_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="weighted_as" id="weighted_as" class="form-control select2">
                                <option value="1">Dense</option>
                                <option value="2">Volumetric</option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="weight_type_select" id="weight_type_select" class="select2">
                                @foreach($weight_types as $weight_type)
                                    <option value="{{$weight_type->id}}">{{$weight_type->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="sub_segment_select" id="sub_segment_select" class="select2">
                                @foreach($sub_segments as $sub_segment)
                                    <option value="{{$sub_segment->id}}">{{$sub_segment->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required">
                        </div>
                    </div>
                    <div class="col-3 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required">
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>
                </form>

                <div class="d-none" id="table">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Sub Segment</th>
                            <th class="border-primary border-darken-1">Shipping Mode</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Booking Date</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Weight Input by Shipper (A)</th>
                            <th class="border-primary border-darken-1">Arrival Weight (B)</th>
                            <th class="border-primary border-darken-1">Difference (B-A)</th>
                            <th class="border-primary border-darken-1">Chargeable Weight</th>
                            <th class="border-primary border-darken-1">Weighted As</th> 
                            <th class="border-primary border-darken-1">Weight Recorded As</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            var select = $('.tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
            });
            $('#search_form #search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shipping Mode*',
            });
            $('#search_form #search_user').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shipper',
            });
            $('#search_form #search_origin_hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Origin Hub',
            });
            $('#search_form #search_destination_hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Destination Hub',
            });
            $('#search_form #search_zone').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Zone',
            });
            $('#search_form #weighted_as').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Weighted As',
            });
            $('#search_form #sub_segment_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Sub Segment*'
            });
            $('#search_form #weight_type_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Weight Recorded As*'
            });

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var date_limit = '{{ Carbon\Carbon::now()->toDateString() }}';
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                max: new Date(date_limit),
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $('#table').removeClass('d-none');
                    table.draw(true);
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.weight_qc.list') }}',
                        data: params,
                        success: function (result) {

                            head = [];

                            head.push('S. No.');
                            head.push('Tracking Number');
                            head.push('Shipper Name');
                            head.push('Sub Segment');
                            head.push('Shipping Mode');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Booking Date');
                            head.push('Arrival Date');
                            head.push('Weight Input by Shipper (A)');
                            head.push('Arrival Weight (B)');
                            head.push('Difference (B-A)');
                            head.push('Chargeable Weight')
                            head.push('Weighted As');
                            head.push('Weight Recorded As');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.sub_segment);
                                row.push(values.shipping_mode);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.booking_date);
                                row.push(values.arrival_date);
                                row.push(values.estimated_weight);
                                row.push(values.actual_weight);
                                row.push(values.difference);
                                row.push(values.chargeable_weight);
                                row.push(values.weighted_as);
                                row.push(values.weight_type_name)
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Weight QC Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: [50, 0],
                ajax:{
                    url: '{{ route('admin.reports.weight_qc.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#search_form .tracking_numbers').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.search_user = $('#search_user').val();
                        d.search_origin_hub = $('#search_origin_hub').val();
                        d.search_destination_hub = $('#search_destination_hub').val();
                        d.search_zone = $('#search_zone').val();
                        d.weighted_as = $('#weighted_as').val();
                        d.sub_segment = $('#search_form #sub_segment_select').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                        d.weight_type = $('#search_form #weight_type_select').val();
                    }
                },
                order: [[8, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle text-center shipper'},
                    { data:'sub_segment' ,name: 'scs.name', class: 'align-middle text-center sub_segment'},
                    { data:'shipping_mode' ,name: 'sm.mode', class: 'align-middle text-center shipping_mode'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle text-center origin'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle text-center destination'},
                    { data:'booking_date' ,name: 'bkg_date.created_at', class: 'align-middle text-center booking_date'},
                    { data:'arrival_date' ,name: 'arv_date.created_at', class: 'align-middle text-center arrival_date'},
                    { data:'estimated_weight' ,name: 'shipments.estimated_weight', class: 'align-middle text-center estimated_weight'},
                    { data:'actual_weight' ,name: 'shipments.actual_weight', class: 'align-middle text-center actual_weight'},
                    { data:'difference' ,name: 'difference', class: 'align-middle text-center difference', orderable: false, searchable: false},
                    { data:'chargeable_weight' ,name: 'shipments.chargeable_weight', class: 'align-middle text-center chargeable_weight', orderable: false, searchable: false},
                    { data:'weighted_as' ,name: 'weighted_as', class: 'align-middle text-center weighted_as', orderable: false, searchable: false},
                    { data:'weight_type_name' ,name: 'weight_type', class: 'align-middle text-center weight_type', orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

        });

    </script>
@endsection