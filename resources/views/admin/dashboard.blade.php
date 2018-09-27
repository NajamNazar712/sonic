@extends('admin.layout.master')

@section('title', 'Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
      </div>
      <div class="content-body">

        <!-- Active Orders -->
          <div class="row">
              <div class="col">
                  <div class="card pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-grid font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-right">
                                      <h3 class="">{{$stats['total']}}</h3>
                                      <span>Total Booked Shipment(s)</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card bg-gradient-directional-primary pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-hourglass text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['booked']}}</h3>
                                      <span>Pending Shipment(s)</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card bg-gradient-directional-info pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-layers text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['received']}}</h3>
                                      <span>Received Shipment(s)</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div><div class="col">
                  <div class="card bg-gradient-directional-success pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-check text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['delivered']}}</h3>
                                      <span>Delivered Shipment(s)</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div><div class="col">
                  <div class="card bg-gradient-directional-warning pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-loop text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['return']}}</h3>
                                      <span>Returned Shipment(s)</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card bg-gradient-directional-inprocess pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-shuffle text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['pending']}}</h3>
                                      <span>In Process Shipment(s)</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="card col-12">
                  <div class="card-content collapse show">
                      <div class="card-body">
                          <div id="shipment_statistics_chart" class="height-300 echart-container"></div>
                          <div class="row">
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-0 mt-sm-1 mt-xs-1">
                                  <input type="text" name="from_date" class="form-control graph_date bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{$dates['old_date']}}">
                              </div>
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-0 mt-sm-1 mt-xs-1">
                                  <input type="text" name="to_date" class="form-control graph_date bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{$dates['current']}}">
                              </div>
                              <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1">
                                  <select name="graph_destination" id="graph_destination" class="select2 form-control">
                                      {{--<option value="">All</option>--}}
                                      @foreach($cities as $city)
                                          <option value="{{$city->id}}">{{$city->name}}</option>
                                      @endforeach
                                  </select>
                              </div>
                              <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1">
                                  <select name="graph_shipper" id="graph_shipper" class="select2 form-control">
                                      @foreach($shippers as $shipper)
                                          <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                      @endforeach
                                  </select>
                              </div>
                              <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1 text-right">
                                  <button type="button" class="btn round btn-primary mr-1 btn-glow statistics_search">Search <i class="ft-bar-chart"></i></button>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>
          </div>

          <div class="row">
              <div class="card">
              <div class="card-content">
                  <div class="card-body">

              <h2>Order Details</h2>
              <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                  <thead>
                  <tr role="row" class="bg-primary white">
                      <th class="border-primary border-darken-1"></th>
                      <th class="border-primary border-darken-1">S No.</th>
                      <th class="border-primary border-darken-1">Tracking No.</th>
                      <th class="border-primary border-darken-1">Order ID</th>
                      <th class="border-primary border-darken-1">Account No.</th>
                      <th class="border-primary border-darken-1">Shipper</th>
                      <th class="border-primary border-darken-1">Service Type</th>
                      <th class="border-primary border-darken-1">Status</th>
                      <th class="border-primary border-darken-1">Payment Status</th>
                      <th class="border-primary border-darken-1">Origin</th>
                      <th class="border-primary border-darken-1">Destination</th>
                      <th class="border-primary border-darken-1">Consignee Name</th>
                      <th class="border-primary border-darken-1">Consignee Contact</th>
                      <th class="border-primary border-darken-1">Consignee Address</th>
                      <th class="border-primary border-darken-1">Collection Amount</th>
                      <th class="border-primary border-darken-1">Product Type</th>
                      <th class="border-primary border-darken-1">Booking Date</th>
                      <th class="border-primary border-darken-1">Instructions</th>
                      <th class="border-primary border-darken-1"></th>
                  </tr>
                  </thead>
              </table>

                  </div>
              </div>
              </div>
          </div>
      </div>
    </div>
  </div>
  <!-- ////////////////////////////////////////////////////////////////////////////-->

  @endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style type="text/css">

        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var old_date_limit = '{{ Carbon\Carbon::now()->subDays(29)->toDateString() }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: new Date(old_date_limit),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top', '-350px');
                },
                onSet: function(context) {
                    var old_date_formatted = $('input[name="from_date_formatted"]').val();
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(29, 'days');
                    to_date.pickadate('picker').set({'select': current.toDate()},{muted: true});
                }
            });

            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '-350px');
                },
                onSet: function(context) {
                    var current_date_formatted = $('input[name="to_date_formatted"]').val();
                    var currentMoment = moment(current_date_formatted);
                    var currentDate = moment(currentMoment).subtract(29, 'days');
                    from_date.pickadate('picker').set({'select': currentDate.toDate()},{muted: true});
                }
            });
            $('#graph_destination').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select a Destination",
                allowClear:true
            });
            $('#graph_shipper').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select a Shipper",
                allowClear:true
            });
            function print(selected_rows) {
                $.ajax({
                    url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                    method: 'POST',
                    data: {
                        'ids[]': selected_rows,
                        'admin': {!! Auth::id() !!},
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.orders.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Account No.');
                            head.push('Shipper');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Payment Status');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Consignee Name');
                            head.push('Consignee Contact');
                            head.push('Consignee Address');
                            head.push('Collection Amount');
                            head.push('Product Type');
                            head.push('Booking Date');
                            head.push('Instructions');



                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.order_id);
                                row.push(values.account_no);
                                row.push(values.shipper);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.payment_status);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.consignee_name);
                                row.push(values.phone1 + " | " + values.phone2);
                                row.push(values.consignee_address);
                                row.push(values.amount);
                                row.push(values.product_type);
                                row.push(values.booking_date);
                                row.push(values.instructions);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: '<i class="la la-print"></i> Print',
                    className: 'btn btn-primary print',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        table.button('.print').disable();
                        print(selected_rows);
                        table.rows().deselect();
                        selected_rows = [];

                    }
                },
                    {
                        extend: 'excel',
                        title: 'Order Details',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.orders.list') }}',
                rowId: 'shipment_id',
                order: [[16, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'account_no', name: 'u.id', class: 'align-middle account_no'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'service_type', name: 'service_type', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'payment_status', name: 'payment_status', class: 'align-middle payment_status'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'phone', name: 'phone', class: 'align-middle phone',orderable:false,searchable:false},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'product_type', name: 'product', class: 'align-middle product_type'},
                    {data: 'booking_date', name: 'booking_date', class: 'align-middle booking_date'},
                    {data: 'instructions', name: 'shipments.special_instructions', class: 'align-middle instructions'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if (data.shipper_status_id === 1) {
                        $('td:eq(0)', row).addClass('select-checkbox');

                        if ($.inArray(data.id, selected_rows) !== -1) {
                            table.row(row).select();
                        }
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control">' +
                        '</select>';
                    var product_select = '<select name="product_select" id="product_select" class="select2 form-control"></select>';
                    var payment_select = '<select name="payment_select" id="payment_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.product_type')){
                            $(product_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.payment_status')){
                            $(payment_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id

                        return obj;
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.text = obj.booking_type;

                        return obj;
                    });

                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data3 = $.map({!! $products !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data3 = $.map({!! $products !!}, function (obj) {
                        obj.text = obj.product_name; // replace name with the property used for the text

                        return obj;
                    });

                    $("#product_select").prepend('<option value="" selected></option>').select2({
                        data:data3,
                        placeholder: "Select Product",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#payment_select").prepend('<option value="" selected></option>').select2({
                        data:data4,
                        placeholder: "Select Payment",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            $('.datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.print').enable();
                }
                else {
                    table.button('.print').disable();
                }
            });

            var myChart = echarts.init(document.getElementById('shipment_statistics_chart'));

            chartOptions = {

                // Setup grid
                grid: {
                    x: 60,
                    x2: 40
                },

                // Add tooltip
                tooltip: {
                    trigger: 'axis'
                },

                // Add legend
                legend: {
                    data: ['Pending Shipment(s)', 'Received Shipment(s)', 'Delivered Shipment(s)', 'Returned Shipment(s)', 'In Process Shipment(s)']
                },

                // Add custom colors
                color: ['#535BE2', '#168DEE', '#69DEB4', '#FF7E39', '#d6a42a'],

                // Hirozontal axis
                xAxis: [{
                    type: 'category',
                    boundaryGap: false,
                    axisLabel: {
                        rotate: 45
                    },
                    data: @json($graph['dates'])
                }
                ],
                // Vertical axis

                yAxis: [{
                    type: 'value'
                }],
                // Add series
                series: [
                    {
                        name: 'Pending Shipment(s)',
                        type: 'line',
                        data: @json($graph['booked'])
                    },
                    {
                        name: 'Received Shipment(s)',
                        type: 'line',
                        data: @json($graph['received'])
                    },
                    {
                        name: 'Delivered Shipment(s)',
                        type: 'line',
                        data: @json($graph['delivered'])
                    },
                    {
                        name: 'Returned Shipment(s)',
                        type: 'line',
                        data: @json($graph['return'])
                    },
                    {
                        name: 'In Process Shipment(s)',
                        type: 'line',
                        data: @json($graph['pending'])
                    }
                ]
            };


            myChart.setOption(chartOptions);
            $('.statistics_search').on('click',function(){
                var search_btn = $(this);
                search_btn.prop('disabled',true);
                var destination = $('#graph_destination').val();
                var shipper = $('#graph_shipper').val();
                var current_date = $('input[name="to_date_formatted"]').val();
                var old_date = $('input[name="from_date_formatted"]').val();
                console.log("Old Date: = "+old_date);
                console.log("New Date: = "+current_date);
                console.log("Destination: = "+destination);
                console.log("Shipper: = "+shipper);
                $.ajax({
                    url: '{!! route('admin.orders.search') !!}',
                    method: 'POST',
                    data: {
                        'destination': destination,
                        'shipper': shipper,
                        'current_date': current_date,
                        'old_date': old_date,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function(data){
                    if(data.status == 1){
                        myChart.clear();
                        updateChartOptions = {


                            grid: {
                                x: 40,
                                x2: 20
                            },


                            tooltip: {
                                trigger: 'axis'
                            },
                            legend: {
                                data: ['Pending Shipment(s)', 'Received Shipment(s)', 'Delivered Shipment(s)', 'Returned Shipment(s)', 'In Process Shipment(s)']
                            },
                            color: ['#535BE2', '#168DEE', '#69DEB4', '#FF7E39', '#d6a42a'],

                            xAxis: [{
                                type: 'category',
                                boundaryGap: false,
                                axisLabel: {
                                    rotate: 45
                                },
                                data: data.graph['dates']

                            }],
                            yAxis: [{
                                type: 'value'
                            }],
                            series: [
                                {
                                    name: 'Pending Shipment(s)',
                                    type: 'line',
                                    data: data.graph['booked']
                                },
                                {
                                    name: 'Received Shipment(s)',
                                    type: 'line',
                                    data: data.graph['received']
                                },
                                {
                                    name: 'Delivered Shipment(s)',
                                    type: 'line',
                                    data: data.graph['delivered']
                                },
                                {
                                    name: 'Returned Shipment(s)',
                                    type: 'line',
                                    data: data.graph['return']
                                },
                                {
                                    name: 'In Process Shipment(s)',
                                    type: 'line',
                                    data: data.graph['pending']
                                }
                            ]
                        };
                        myChart.setOption(updateChartOptions);
                        // setTimeout(function () {
                            search_btn.removeAttr('disabled');
                        // },3000);

                    }
                });
            });

            $('body').on('click','.view_charges',function () {
                var shipment_id = $(this).parents('tr').attr('id');
                $('#ShipmentChargesModal').modal('show');
                $('#shipment_charges_modal_id').val(shipment_id);
                $.ajax({
                    url:'{!! route("admin.orders.charges") !!}',
                    method: 'POST',
                    data: {
                        'shipment_id': shipment_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#shipment_charges_body').html(data);
                    $('#shipment_charges_modal_heading span').text(shipment_id);
                })
            });

            window.onresize = function() {
                $(".echart-container").each(function(){
                    var id = $(this).attr('_echarts_instance_');
                    window.echarts.getInstanceById(id).resize();
                });
            };
        });
    </script>
@endsection