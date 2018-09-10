@extends('client.layout.master')

@section('title', 'Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
      </div>
      <div class="content-body">

        <!-- Active Orders -->
       <h1 class="pb-2">Welcome To Trax Logistics,
       <span class="user-name text-bold-700 ">{{Auth::user()->name}}</span>
     </h1>
        <!-- Active Orders -->
          <div class="row">
              <div class="col">
                  <div class="card pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-book-open font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-right">
                                      <h3 class="">{{$stats['booked']}}</h3>
                                      <span>Booked</span>
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
                                      <i class="icon-basket-loaded text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['received']}}</h3>
                                      <span>Received</span>
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
                                      <i class="icon-emoticon-smile text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['delivered']}}</h3>
                                      <span>Delivered</span>
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
                                      <i class="icon-refresh text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['return']}}</h3>
                                      <span>Returns</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card bg-gradient-directional-danger pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-shield text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['pending']}}</h3>
                                      <span>Pendings</span>
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
                          <div id="shipment_statistics_chart" class="height-400 echart-container"></div>
                          <div class="row">
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-0 mt-sm-1 mt-xs-1">
                                  <input type="text" name="from_date" class="form-control graph_date bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{$dates['old_date']}}">
                              </div>
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-0 mt-sm-1 mt-xs-1">
                                  <input type="text" name="to_date" class="form-control graph_date bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{$dates['current']}}">
                              </div>
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1">
                                  <select name="graph_destination" class="select2" id="graph_destination">
                                      {{--<option value="">All</option>--}}
                                @foreach($cities as $city)
                                      <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                                  </select>
                              </div>
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1 text-right">
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
                                <th class="border-primary border-darken-1">Service Type</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Origin</th>
                                <th class="border-primary border-darken-1">Destination</th>
                                <th class="border-primary border-darken-1">Consignee Name</th>
                                <th class="border-primary border-darken-1">Consignee Contact</th>
                                <th class="border-primary border-darken-1">Consignee Address</th>
                                <th class="border-primary border-darken-1">COD Amount</th>
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
<!--Shipment Charges Modal -->
<div class="modal fade text-left" id="ShipmentChargesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShipmentChargesModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="shipment_charges_modal_heading">Shipment Charges of #<span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="hidden" id="shipment_charges_modal_id">
            <div class="modal-body shipment_charges_body text-center" id="shipment_charges_body">
            </div>
        </div>
    </div>
</div>
<!--Shipment Charges Modal -->
<!--Dispute Modal -->
<div class="modal fade text-left" id="DisputeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DisputeModal"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Launch Dispute</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body  text-center">
                <form id="dispute_form" action="" method="post">
                    <input type="hidden" id="dispute_shipment_id" name="dispute_shipment_id">
                    <div class="row mb-2">
                        <div class="col-12 form-group">
                            <select name="city_select" id="city_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                <option></option>
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <select name="dispute_type_select" id="dispute_type_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                <option></option>
                                @foreach($dispute_types as $dispute)
                                    <option value="{{$dispute->id}}">{{$dispute->type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <input name="tracking_number" id="tracking_number" class="tracking_number" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <textarea name="description" id="description" class="form-control" cols="30" rows="3" placeholder="Enter Description" data-rule-required="true" data-msg-required="This field is required"></textarea>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="DisputeCreate" type="submit" class="btn btn-primary btn-block">Launch Dispute</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Dispute Modal -->
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


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
            function print(selected_rows) {
                $.ajax({
                    url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                    method: 'POST',
                    data: {
                        'ids[]': selected_rows,
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
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                scrollX: true,
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: 'Print',
                    className: 'btn btn-primary print',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        table.button(0).disable();
                        print(selected_rows);
                        $.each(selected_rows, function(index, id) {
                            table.row($('#datatable tbody tr#' + id)).deselect();
                        });
                        selected_rows = [];

                    }
                }],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('cod.orders.list') }}',
                rowId: 'shipment_id',
                order: [[13, 'asc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type'},
                    {data: 'status', name: 'ss.name', class: 'align-middle status'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'phone1', name: 'shipments.consignee_phone_number_1', class: 'align-middle phone1'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'product_type', name: 'p.product_name', class: 'align-middle product_type'},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
                    {data: 'instructions', name: 'shipments.special_instructions', class: 'align-middle instructions'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if (data.shipper_status_id == 1) {
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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select')) {
                            $(td).appendTo($(search));
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

                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click','.cancel_order',function () {
               var id = parseInt($(this).parents('tr').attr('id'));
               if(id){
                   $.ajax({
                       url: '{!! route('cod.orders.cancel') !!}',
                       method: 'POST',
                       data: {
                           'shipment_id': id,
                           '_token': '{{ csrf_token() }}'
                       }
                   }).done(function (data) {
                        if(data.status === 1){
                            table.draw('false');
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                   });
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
                    table.button(0).enable();
                }
                else {
                    table.button(0).disable();
                }
            });
           //echar
            var myChart = echarts.init(document.getElementById('shipment_statistics_chart'));

            chartOptions = {

            // Setup grid
                grid: {
                    x: 40,
                    x2: 20
                },

                // Add tooltip
                tooltip: {
                    trigger: 'axis'
                },

                // Add legend
                legend: {
                    data: ['Booked', 'Received', 'Delivered', 'Return', 'Pending']
                },

                // Add custom colors
                color: ['#cecece', '#62BCF6', '#69DEB4', '#FFB280', '#FF8090'],

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
                        name: 'Booked',
                        type: 'line',
                        data: @json($graph['booked'])
                    },
                    {
                        name: 'Received',
                        type: 'line',
                        data: @json($graph['received'])
                    },
                    {
                        name: 'Delivered',
                        type: 'line',
                        data: @json($graph['delivered'])
                    },
                    {
                        name: 'Return',
                        type: 'line',
                        data: @json($graph['return'])
                    },
                    {
                        name: 'Pending',
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
                var current_date = $('input[name="to_date_formatted"]').val();
                var old_date = $('input[name="from_date_formatted"]').val();
                console.log("Old Date: = "+old_date);
                console.log("New Date: = "+current_date);
                console.log("Destination: = "+destination);
                $.ajax({
                  url: '{!! route('cod.orders.search') !!}',
                        method: 'POST',
                        data: {
                            'destination': destination,
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
                                data: ['Booked', 'Received', 'Delivered', 'Return', 'Pending']
                            },
                            color: ['#cecece', '#62BCF6', '#69DEB4', '#FFB280', '#FF8090'],

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
                                    name: 'Booked',
                                    type: 'line',
                                    data: data.graph['booked']
                                },
                                {
                                    name: 'Received',
                                    type: 'line',
                                    data: data.graph['received']
                                },
                                {
                                    name: 'Delivered',
                                    type: 'line',
                                    data: data.graph['delivered']
                                },
                                {
                                    name: 'Return',
                                    type: 'line',
                                    data: data.graph['return']
                                },
                                {
                                    name: 'Pending',
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

            //Dispute
            $('#city_select').select2({
                placeholder:'Select a city',
                dropdownParent:$('#dispute_form')
            });
            $('#dispute_type_select').select2({
                placeholder:'Select a Dispute type',
                dropdownParent:$('#dispute_form')
            });

            $('body').on('click','.dispute_modal',function () {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                $('#DisputeModal').modal('show');
                $('#dispute_shipment_id').val(shipment_id);
            });
            $('#DisputeModal').on('shown.bs.modal',function(){
                var id = $('#dispute_shipment_id').val();
                if(id){
                    $.ajax({
                        url: '{!! route('cod.dispute.data') !!}',
                        method: 'POST',
                        data:{
                            '_token': '{{ csrf_token() }}',
                            'shipment_id':id
                        }
                    }).done(function (data) {
                        if(data.success == 1){
                            $('#tracking_number').val(data.tracking);
                            select = $('#tracking_number').selectize({
                                placeholder: 'Tracking Number(s)*',
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
                                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
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
                        } else{
                            toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }
                    });
                }
            });

            var max_char = 190;
            $('#description').on('keypress copy paste',function (e) {
                // var comment = $(this).val();
                // console.log(comment)
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('body').on('change','#DisputeModal input,#DisputeModal textarea',function() {
                $(this).val($(this).val().trim());
            });
            $( "#dispute_form" ).validate({
                ignore: [],
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    var city_select = $('#city_select').val();
                    var dispute_type_select = $('#dispute_type_select').val();
                    var tracking_number = $('#tracking_number').val();
                    var description = $('#description').val();
                    $.ajax({
                        url: '{!! route('cod.dispute.create') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'city_select': city_select,
                            'dispute_type_select':dispute_type_select,
                            'tracking_number':tracking_number,
                            'description':description
                        }
                    }).done(function (data) {
                        $('#DisputeModal').modal('hide');

                        if (data.invalid !== undefined) {
                            var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                            toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }

                        if (data.disallowed !== undefined) {
                            var message = 'Following Tracking Number(s) doesn\'t belong to you: ' + data.disallowed.join(', ');

                            toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        if(data.success != undefined){
                            table.draw('false');
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }
                    });

                }


            });
            $('#DisputeModal').on('hidden.bs.modal',function (e) {
                $('#dispute_form')[0].reset();
                $('#DisputeCreate').removeAttr('disabled');
                select[0].selectize.destroy();
                $('#dispute_form').validate().resetForm();
                $('#city_select').val('').trigger('change');
                $('#dispute_type_select').val('').trigger('change');
            });

            $('body').on('click','.view_charges',function () {
                var shipment_id = $(this).parents('tr').attr('id');
                $('#ShipmentChargesModal').modal('show');
                $('#shipment_charges_modal_id').val(shipment_id);
                $.ajax({
                    url:'{!! route("cod.orders.charges") !!}',
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