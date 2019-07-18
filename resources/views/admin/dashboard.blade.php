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
              <div class="col-3">
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
              <div class="col-3">
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
              <div class="col-3">
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
              </div><div class="col-3">
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
              </div>
          </div>
              <div class="row justify-content-center">
                  <div class="col-3">
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
              <div class="col-3">
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
              <div class="col-3">
                  <div class="card bg-gradient-directional-red pull-up">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-close text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$stats['canceled']}}</h3>
                                      <span>Cancelled Shipment(s)</span>
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
                          <div id="shipment_statistics_chart" class="height-300 echart-container d-none"></div>
                          <div class="row">
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-0 mt-sm-1 mt-xs-1">
                                  <input type="text" name="from_date" class="form-control graph_date bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{$dates['old_date']}}">
                              </div>
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-0 mt-sm-1 mt-xs-1">
                                  <input type="text" name="to_date" class="form-control graph_date bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{$dates['current']}}">
                              </div>
                              <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1">
                                  <select name="graph_destination" id="graph_destination" class="select2 form-control">
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
                                  <button type="button" class="btn round btn-primary btn-glow statistics_search">Search <i class="ft-bar-chart"></i></button>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="card col-12">
                  <div class="card-content collapse show">
                      <div class="card-body row">
                          <div class="div_border" style="width: 50%">
                              <div class="text-center">
                                  <h2>Operations Incoming Loads Forecast</h2>
                              </div>
                              <div class="row mt-1 ml-1" style="width: 95%">
                                  <div id="incoming_table">
                                      <table class="table table-sm table-bordered datatable" id="datatable" style="z-index: 3;">
                                          <thead>
                                          <tr role="row" class="black">
                                              <th class="border-darken-1">Description</th>
                                              <th class="border-darken-1">Shipments</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <tr role="row" class="white" style="background-color: #5DADE2">
                                              <td>Shipment - Booked</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #E67E22">
                                              <td>Shipment - Arrived at Origin</td>
                                              <td>10</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #7F8C8D">
                                              <td>Shipment - In Transit</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #F1C40F">
                                              <td>Shipment - Arrived at Destination</td>
                                              <td>10</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #1F618D">
                                              <td>Shipment - Not Attempted</td>
                                              <td>10</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #28B463">
                                              <td>Shipment - Delivery Unsuccessful</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #154360">
                                              <td>Shipment - On Hold</td>
                                              <td>10</td>
                                          </tr>
                                          <tr role="row" class="black">
                                              <td>Delivery Forecast</td>
                                              <td>10</td>
                                          </tr>
                                          </tbody>
                                      </table>
                                  </div>
                                  <div id="incoming_chart" class="width-35-per mt-1" style="margin-left: 4px">
                                      <canvas id="myChartincoming" width="10%" height="10%"></canvas>
                                  </div>
                              </div>
                              <div class="ml-1 row height-200">
                                  <div class="width-300 mt-1">
                                      <table class="table table-sm table-bordered datatable" id="datatable" style="z-index: 3;">
                                          <tbody>
                                          <tr role="row">
                                              <td>0.5 KG</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row">
                                              <td>Upto 2 KG</td>
                                              <td>10</td>
                                          </tr>
                                          <tr role="row">
                                              <td>upto 5 KG</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row">
                                              <td>Above 5 KG</td>
                                              <td>10</td>
                                          </tr>
                                          </tbody>
                                      </table>
                                  </div>
                                  <div class="width-200">
                                      <canvas id="mybarchartincoming" height="230px"></canvas>
                                  </div>
                              </div>
                          </div>
                          <div class="float-right div_border" style="width: 49%; margin-left: 1px">
                              <div class="text-center">
                                  <h2>Operations Outgoing Loads Forecast</h2>
                              </div>

                              <div class="ml-1 row">
                                  <div class="mt-1 width-300">
                                      <table class="table table-sm table-bordered datatable" id="datatable" style="z-index: 3;"><thead>
                                          <tr role="row">
                                              <th>Description</th>
                                              <th>Nos</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <tr role="row">
                                              <td>0.5 KG</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row">
                                              <td>Upto 2 KG</td>
                                              <td>10</td>
                                          </tr>
                                          </tbody>
                                      </table>
                                  </div>
                              </div>

                              <div class="row mt-1 ml-1" style="width: 95%">
                                  <div id="outgoing_table" class="width-300">
                                      <table class="table table-sm table-bordered datatable" id="datatable" style="z-index: 3;">
                                          <thead>
                                          <tr role="row" class="black">
                                              <th class="border-darken-1">Top Five Customers</th>
                                              <th class="border-darken-1">Shipments</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <tr role="row" class="white" style="background-color: #5DADE2">
                                              <td>Mega Brands</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #E67E22">
                                              <td>Saloni</td>
                                              <td>10</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #7F8C8D">
                                              <td>Chinayere</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #F1C40F">
                                              <td>Shiza Hassan</td>
                                              <td>10</td>
                                          </tr>
                                          <tr role="row" class="white" style="background-color: #1F618D">
                                              <td>Domelic</td>
                                              <td>10</td>
                                          </tr>
                                          </tbody>
                                      </table>
                                  </div>
                                  <div id="outgoing_chart" class="width-35-per" style="margin-left: 4px">
                                      <canvas id="myChartoutgoing" width="10%" height="10%"></canvas>
                                  </div>
                              </div>

                              <div class="ml-1 row height-200">
                                  <div class="width-300 mt-1">
                                      <table class="table table-sm table-bordered datatable" id="datatable" style="z-index: 3;">
                                          <tbody>
                                          <tr role="row">
                                              <td>0.5 KG</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row">
                                              <td>Upto 2 KG</td>
                                              <td>10</td>
                                          </tr>
                                          <tr role="row">
                                              <td>upto 5 KG</td>
                                              <td>50</td>
                                          </tr>
                                          <tr role="row">
                                              <td>Above 5 KG</td>
                                              <td>10</td>
                                          </tr>
                                          </tbody>
                                      </table>
                                  </div>
                                  <div class="width-200">
                                      <canvas id="mybarchartoutgoing" height="230px"></canvas>
                                  </div>
                              </div>
                          </div>
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
        .small-calender-icon{
            font-size: 17px !important;
        }
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .selectize-control {
             width: 300px !important;
        }

        .div_border{
            border-style: solid;
        }


    </style>
@endsection

@section('js')
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/chartjs/chart.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
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

            var myChart;

            $('.statistics_search').on('click',function(){
                var search_btn = $(this);
                search_btn.prop('disabled',true);
                var destination = $('#graph_destination').val();
                var shipper = $('#graph_shipper').val();
                var current_date = $('input[name="to_date_formatted"]').val();
                var old_date = $('input[name="from_date_formatted"]').val();
                $.ajax({
                    url: '{!! route('admin.dashboard.search') !!}',
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
                        $('#shipment_statistics_chart').removeClass('d-none');

                        if (!myChart) {
                          myChart = echarts.init(document.getElementById('shipment_statistics_chart'));
                        }

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
                                data: ['Pending Shipment(s)', 'Received Shipment(s)', 'Delivered Shipment(s)', 'Returned Shipment(s)', 'In Process Shipment(s)', 'Cancelled Shipment(s)']
                            },
                            color: ['#535BE2', '#168DEE', '#69DEB4', '#FF7E39', '#d6a42a','#FF0000'],

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
                                },
                                {
                                    name: 'Cancelled Shipment(s)',
                                    type: 'line',
                                    data: data.graph['cancelled']
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

            window.onresize = function() {
                $(".echart-container").each(function(){
                    var id = $(this).attr('_echarts_instance_');
                    window.echarts.getInstanceById(id).resize();
                });
            };

            var ctx = document.getElementById('myChartincoming').getContext('2d');
            // var chart = new Chart(ctx, {
            //     // The type of chart we want to create
            //     type: 'line',
            //
            //     // The data for our dataset
            //     data: {
            //         labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            //         datasets: [{
            //             label: 'My First dataset',
            //             backgroundColor: 'rgb(255, 99, 132)',
            //             borderColor: 'rgb(255, 99, 132)',
            //             data: [0, 10, 5, 2, 20, 30, 45]
            //         }]
            //     },
            //
            //     // Configuration options go here
            //     options: {}
            // });
            // var piedata = {
            //     datasets: [{
            //         data: [10, 20, 30, 40, 50, 60, 70],
            //         backgroundColor: [
            //             '#5DADE2',
            //             '#E67E22',
            //             '#7F8C8D',
            //             '#F1C40F',
            //             '#1F618D',
            //             '#28B463',
            //             '#154360'
            //         ],
            //     }],
            //
            //     // These labels appear in the legend and in the tooltips when hovering different arcs
            //
            //
            // };
            var piedata = {
                datasets: [{
                    data: [10, 20, 30, 40, 50, 60, 70],
                    backgroundColor: [
                        '#5DADE2',
                        '#E67E22',
                        '#7F8C8D',
                        '#F1C40F',
                        '#1F618D',
                        '#28B463',
                        '#154360'
                    ],
                }],

                // These labels appear in the legend and in the tooltips when hovering different arcs

                labels: [
                    "Booked",
                    "Arrived at Origin",
                    "In Transit",
                    "Arrived at Destination",
                    "Not Attempted",
                    "Delivery Unsuccessful",
                    "On Hold",
                ]

            };
            console.log(piedata);
            var options = {
                options: {
                    legend: {
                        display: false,
                    },
                    showTooltips: false
                }
            };

            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: piedata,
                options: {
                    legend: {
                        display: false,
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltips: {
                        tittle: [10, 20, 30, 40, 50, 60, 70],
                    },
                },
                centerText: {
                    display: true,
                    text: "280"
                }
            });

            ctxbarchart = document.getElementById('mybarchartincoming').getContext('2d');


            var bardata = {
                    datasets: [
                        {
                            label: "0.5 KG",
                            data: [25],
                            backgroundColor: ["#669911", "#119966" ],
                            hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                        },
                        {
                            label: "Upto 2 KG",
                            data: [20],
                            backgroundColor: ["#669911", "#119966" ],
                            hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                        },
                        {
                            label: "Upto 5 KG",
                            data: [30],
                            backgroundColor: ["#669911", "#119966" ],
                            hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                        },
                        {
                            label: "Above 5 KG",
                            data: [40],
                            backgroundColor: ["#669911", "#119966" ],
                            hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                        }
                    ],

                // These labels appear in the legend and in the tooltips when hovering different arcs

            };

            var myBarChart = new Chart(ctxbarchart, {
                type: 'horizontalBar',
                data: bardata,
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.5,
                            barThickness: 6,
                            maxBarThickness: 8,
                            minBarLength: 2,
                            gridLines: {
                                offsetGridLines: true
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltips: {
                        callbacks: {
                            title: function() {}
                        }
                    }
                }
            });
            var ctx_2 = document.getElementById('myChartoutgoing').getContext('2d');
            // var chart = new Chart(ctx, {
            //     // The type of chart we want to create
            //     type: 'line',
            //
            //     // The data for our dataset
            //     data: {
            //         labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            //         datasets: [{
            //             label: 'My First dataset',
            //             backgroundColor: 'rgb(255, 99, 132)',
            //             borderColor: 'rgb(255, 99, 132)',
            //             data: [0, 10, 5, 2, 20, 30, 45]
            //         }]
            //     },
            //
            //     // Configuration options go here
            //     options: {}
            // });
            // var piedata = {
            //     datasets: [{
            //         data: [10, 20, 30, 40, 50, 60, 70],
            //         backgroundColor: [
            //             '#5DADE2',
            //             '#E67E22',
            //             '#7F8C8D',
            //             '#F1C40F',
            //             '#1F618D',
            //             '#28B463',
            //             '#154360'
            //         ],
            //     }],
            //
            //     // These labels appear in the legend and in the tooltips when hovering different arcs
            //
            //
            // };
            var piedata_2 = {
                datasets: [{
                    data: [10, 20, 30, 40, 50, 60, 70],
                    backgroundColor: [
                        '#5DADE2',
                        '#E67E22',
                        '#7F8C8D',
                        '#F1C40F',
                        '#1F618D',
                        '#28B463',
                        '#154360'
                    ],
                }],

                // These labels appear in the legend and in the tooltips when hovering different arcs

                labels: [
                    "Shipment - Booked",
                    "Shipment - Arrived at Origin",
                    "Shipment - In Transit",
                    "Shipment - Arrived at Destination",
                    "Shipment - Not Attempted",
                    "Shipment - Delivery Unsuccessful",
                    "Shipment - On Hold",
                ]

            };

            var myDoughnutChart_2 = new Chart(ctx_2, {
                type: 'doughnut',
                data: piedata_2,
                options: {
                    legend: {
                        display: false,
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltips: {
                        tittle: [10, 20, 30, 40, 50, 60, 70],
                    },
                },
                centerText: {
                    display: true,
                    text: "280"
                }
            });

            ctxbarchart_2 = document.getElementById('mybarchartoutgoing').getContext('2d');


            var bardata_2 = {
                datasets: [
                    {
                        label: "0.5 KG",
                        data: [25],
                        backgroundColor: ["#669911", "#119966" ],
                        hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                    },
                    {
                        label: "Upto 2 KG",
                        data: [20],
                        backgroundColor: ["#669911", "#119966" ],
                        hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                    },
                    {
                        label: "Upto 5 KG",
                        data: [30],
                        backgroundColor: ["#669911", "#119966" ],
                        hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                    },
                    {
                        label: "Above 5 KG",
                        data: [40],
                        backgroundColor: ["#669911", "#119966" ],
                        hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                    }
                ],

                // These labels appear in the legend and in the tooltips when hovering different arcs

            };

            var myBarChart_2 = new Chart(ctxbarchart_2, {
                type: 'horizontalBar',
                data: bardata_2,
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.5,
                            barThickness: 6,
                            maxBarThickness: 8,
                            minBarLength: 2,
                            gridLines: {
                                offsetGridLines: true
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltips: {
                        callbacks: {
                            title: function() {}
                        }
                    }
                }
            });


        });
    </script>
@endsection