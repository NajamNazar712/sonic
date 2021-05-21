@extends('admin.layout.master')

@section('title', 'Riders Incentive')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Riders Incentive
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div id="search_form" class="row mb-2 justify-content-center">

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
                                        <select name="search_city" id="search_city" class="form-control select2">
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                            @endforeach
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

                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>

                                        <input type="text" name="search_date_from"
                                               class="form-control pickadate bg-primary border-primary white rounded-right"
                                               id="search_date_from" placeholder="Date (From)"
                                               data-value="{{Carbon\Carbon::now()->subDays(3)}}">
                                    </div>
                                </div>
                                <div class="col-4 ">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                        </div>

                                        <input type="text" name="search_date_to"
                                               class="form-control pickadate bg-primary border-primary white rounded-right"
                                               id="search_date_to" placeholder="Date (To)"
                                               data-value="{{ Carbon\Carbon::today() }}">
                                    </div>

                                </div>


                                <div class="col-4 mb-1">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="search_employee_name" id="search_employee_name" placeholder="Search Employee Name">
                                    </fieldset>
                                </div>
                                <div class="col-4 mb-1">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="search_employee_id" id="search_employee_id" placeholder="Search Employee ID">
                                    </fieldset>
                                </div>
                                <div class="col-2">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Employee ID</th>
                                        <th class="border-primary border-darken-1">Employee Name</th>
                                        <th class="border-primary border-darken-1">Phone</th>
                                        <th class="border-primary border-darken-1">CNIC</th>
                                        <th class="border-primary border-darken-1">City</th>
                                        <th class="border-primary border-darken-1">Rider Type</th>
                                        <th class="border-primary border-darken-1">Pickup Shipment Count</th>
                                        <th class="border-primary border-darken-1">Pickup Incentive</th>
                                        <th class="border-primary border-darken-1">Delivery Shipment Count</th>
                                        <th class="border-primary border-darken-1">Delivery Incentive</th>
                                        <th class="border-primary border-darken-1">Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('#search_city').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search City',
                width:'100%',
                allowClear:true
            });
            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Zone',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });

            $('input.incentive_value').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
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

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.rider_incentive.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Phone');
                            head.push('CNIC');
                            head.push('City');
                            head.push('Rider Type');
                            head.push('Pickup Shipment Count');
                            head.push('Pickup Incentive');
                            head.push('Delivery Shipment Count');
                            head.push('Delivery Incentive');
                            head.push('Date');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.employee_id);
                                row.push(values.rider_name);
                                row.push(values.rider_phone);
                                row.push(values.cnic);
                                row.push(values.rider_city);
                                row.push(values.rider_type);
                                row.push(values.pickup_shipments);
                                row.push(values.pickup_incentive);
                                row.push(values.delivery_shipments);
                                row.push(values.delivery_incentive);
                                row.push(values.date);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    extend: 'excel',
                    title: 'Rider Incentive',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.human_resource.rider_incentive.list') }}',
                    data: function (d) {
                        d.employee_id = $('#search_employee_id').val();
                        d.employee_name = $('#search_employee_name').val();
                        d.search_city = $('#search_city').val();
                        d.search_zone = $('#search_zone').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'rider_id',
                order: [[11, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'employee_id', name: 'riders.trax_id', class: 'align-middle employee_id'},
                    {data: 'rider_name', name: 'riders.name', class: 'align-middle rider_name'},
                    {data: 'rider_phone', name: 'riders.phone', class: 'align-middle rider_phone'},
                    {data: 'cnic', name: 'riders.cnic', class: 'align-middle cnic'},
                    {data: 'rider_city', name: 'cities.name', class: 'align-middle rider_city'},
                    {data: 'rider_type', name: 'rt.name', class: 'align-middle rider_type'},
                    {data: 'pickup_shipments', name: 'riders_incentives.pickup_shipments', class: 'align-middle pickup_shipments'},
                    {data: 'pickup_incentive', name: 'riders_incentives.pickup_incentive', class: 'align-middle pickup_incentive'},
                    {data: 'delivery_shipments', name: 'riders_incentives.delivery_shipments', class: 'align-middle delivery_shipments'},
                    {data: 'delivery_incentive', name: 'riders_incentives.delivery_incentive', class: 'align-middle delivery_incentive'},
                    {data: 'date', name: 'riders_incentives.date', class: 'align-middle date'},
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

        });


    </script>
@endsection