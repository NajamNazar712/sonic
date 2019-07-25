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
                      <div class="card-body">
                          <div class="row mb-1">
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-0 mt-sm-1 mt-xs-1">
                                  <input type="text" name="from_date_operations" class="form-control chart_date bg-primary border-primary white rounded-right" id="from_date_operations" placeholder="Date From" data-value="{{$operation_dates['from']}}">
                              </div>
                              <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-0 mt-sm-1 mt-xs-1">
                                  <input type="text" name="to_date_operations" class="form-control chart_date bg-primary border-primary white rounded-right" id="to_date_operations" placeholder="Date To" data-value="{{$operation_dates['to']}}">
                              </div>
                              <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1">
                                  <select name="search_hub" id="search_hub" class="select2 form-control">
                                      @foreach($cities as $city)
                                          @if($city->id == $default_hub_id)
                                              <option value="{{$city->id}}" selected="selected">{{$city->name}}</option>
                                          @else
                                              <option value="{{$city->id}}">{{$city->name}}</option>
                                          @endif
                                      @endforeach
                                  </select>
                              </div>
                              <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1">
                                  <select name="search_service_type" id="search_service_type" class="select2 form-control">
                                      @foreach($service_types as $service_type)
                                          @if($city->id == 1)
                                              <option value="{{$service_type->id}}" selected="selected">{{$service_type->booking_type}}</option>
                                          @else
                                              <option value="{{$service_type->id}}">{{$service_type->booking_type}}</option>
                                          @endif
                                      @endforeach
                                  </select>
                              </div>
                              {{--<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1">--}}
                                  {{--<select name="graph_shipper" id="graph_shipper" class="select2 form-control">--}}
                                      {{--@foreach($shippers as $shipper)--}}
                                          {{--<option value="{{$shipper->id}}">{{$shipper->name}}</option>--}}
                                      {{--@endforeach--}}
                                  {{--</select>--}}
                              {{--</div>--}}
                              <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12 mt-lg-0 mt-md-1 mt-sm-1 mt-xs-1 text-right">
                                  <button type="button" class="btn round btn-primary btn-glow operations_forecast_search">Search <i class="ft-bar-chart"></i></button>
                              </div>
                          </div>
                          <div class="text-right">
                              <h5>
                                  <b>
                                      Last updated at:
                                  </b>
                                  <u>
                                      @if(!empty($last_update_at))
                                        {{$last_updated_at->updated_at}}
                                      @else
                                        0000-00-00 00:00:00
                                      @endif
                                  </u>
                              </h5>
                          </div>
                          <div class="row">
                              <div class="div_border" style="width: 50%">
                                  <div class="text-center">
                                      <h3>Operations Incoming Loads Forecast</h3>
                                  </div>
                                  <div class="row ml-1 mt-3" style="width: 95%">
                                      <div id="incoming_table">
                                          <table class="table table-sm table-bordered datatable" id="datatable_incoming" style="z-index: 3;">
                                              <thead>
                                              <tr role="row" class="black">
                                                  <th class="black border-darken-1">Description</th>
                                                  <th class="black border-darken-1">Shipments</th>
                                              </tr>
                                              </thead>
                                          </table>
                                      </div>
                                      <div id="incoming_chart" class="width-150 mt-1" style="margin-left: 4px">
                                          <canvas id="myChartincoming" width="10%" height="10%"></canvas>
                                      </div>
                                  </div>
                                  <div class="ml-1 mt-4 row height-200">
                                      <div class="width-300 mt-1">
                                          <table class="table table-sm table-bordered datatable" id="datatable_incoming_weight_range" style="z-index: 3;">
                                              <thead style='display:none;'>
                                              </thead>
                                          </table>
                                      </div>
                                      <div id="incoming_bar_chart" class="width-200">
                                          <canvas id="mybarchartincoming" height="230px"></canvas>
                                      </div>
                                  </div>

                                  <div class="row ml-1 mr-1">
                                      <div class="col-6">
                                          <div class="card bg-gradient-directional-info">
                                              <div class="card-content">
                                                  <div class="card-body justify-content-center">
                                                      <div class="text-white text-center">
                                                          <h6 class="text-white">Per Rider Loads</h6>
                                                      </div>
                                                      <div id="incoming_per_rider_loads_div">
                                                          <div id="incoming_per_rider_loads" class="text-white text-center">
                                                            <h4 class="text-white">{{$operation_incoming['per_rider_loads']}}</h4>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-6">
                                          <div class="card bg-gradient-directional-info">
                                              <div class="card-content">
                                                  <div class="card-body justify-content-center">
                                                      <div class="text-white text-center">
                                                          <h6 class="text-white">Day Wise Growth</h6>
                                                      </div>
                                                      <div id="incoming_day_wise_growth_div">
                                                          <div id="incoming_day_wise_growth" class="text-white text-center">
                                                            <h4 class="text-white">{{$operation_incoming['day_wise_growth']}}</h4>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="row ml-1 mr-1">
                                      <div class="col-6">
                                          <div class="card bg-gradient-directional-info">
                                              <div class="card-content">
                                                  <div class="card-body justify-content-center">
                                                      <div class="text-white text-center">
                                                          <h6 class="text-white">Heavy Shipments</h6>
                                                      </div>
                                                      <div id="incoming_heavy_deliveries_div">
                                                          <div id="incoming_heavy_deliveries" class="text-white text-center">
                                                              <h4 class="text-white">{{$operation_incoming['heavy_deliveries']}}</h4>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-6">
                                          <div class="card bg-gradient-directional-info">
                                              <div class="card-content">
                                                  <div class="card-body justify-content-center">
                                                      <div class="text-white text-center">
                                                          <h6 class="text-white">Light Shipments</h6>
                                                      </div>
                                                      <div id="incoming_light_deliveries_div">
                                                          <div id="incoming_light_deliveries" class="text-white text-center">
                                                              <h4 class="text-white">{{$operation_incoming['light_deliveries']}}</h4>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="float-right div_border" style="width: 49%; margin-left: 1px">
                                  <div class="text-center">
                                      <h3>Operations Outgoing Loads Forecast</h3>
                                  </div>

                                  <div class="ml-1 row">
                                      <div class="mt-1 width-300">
                                          <table class="table table-sm table-bordered datatable" style="z-index: 3;"><thead>
                                              <tr role="row">
                                                  <th>Description</th>
                                                  <th>Nos</th>
                                              </tr>
                                              </thead>
                                              <tbody id="outgoing_pickups_tbody">
                                                  <tr id="pickup_request" role="row">
                                                      <td>Pickup Request</td>
                                                      <td class="text-center">{{$operation_outgoing_pickups['pickups']}}</td>
                                                  </tr>
                                                  <tr id="no_of_shipments" role="row">
                                                      <td># of Shipments</td>
                                                      <td class="text-center">{{$operation_outgoing_pickups['no_of_shipments']}}</td>
                                                  </tr>
                                              </tbody>
                                          </table>
                                      </div>
                                  </div>

                                  <div class="row mt-1">
                                      <div id="outgoing_table" class="width-60-per ml-1">
                                          <table class="table table-sm table-bordered datatable" id="datatable_top_five" style="z-index: 3;">
                                              <thead>
                                              <tr role="row" class="black">
                                                  <th class="black border-darken-1">Top Five Customers</th>
                                                  <th class="black border-darken-1">Shipments</th>
                                              </tr>
                                              </thead>
                                          </table>
                                      </div>
                                      <div id="outgoing_chart" class="width-35-per">
                                          <canvas id="myChartoutgoing" height="230px"></canvas>
                                      </div>
                                  </div>

                                  <div class="row height-200">
                                      <div class="ml-1 width-300 mt-1">
                                          <table class="table table-sm table-bordered datatable" id="datatable_outgoing_weight_range" style="z-index: 3;">
                                              <thead style='display:none;'>
                                              </thead>
                                          </table>
                                      </div>
                                      <div id="outgoing_bar_chart" class="width-200">
                                          <canvas id="mybarchartoutgoing" height="230px"></canvas>
                                      </div>
                                  </div>
                                  <div class="row ml-1 mr-1">
                                      <div class="col-6">
                                          <div class="card bg-gradient-directional-info">
                                              <div class="card-content">
                                                  <div class="card-body justify-content-center">
                                                      <div class="text-white text-center">
                                                          <h6 class="text-white">Per Rider Loads</h6>
                                                      </div>
                                                      <div id="outgoing_per_rider_loads_div">
                                                          <div id="outgoing_per_rider_loads" class="text-white text-center">
                                                              <h4 class="text-white">{{$operation_outgoing['per_rider_loads']}}</h4>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-6">
                                          <div class="card bg-gradient-directional-info">
                                              <div class="card-content">
                                                  <div class="card-body justify-content-center">
                                                      <div class="text-white text-center">
                                                          <h6 class="text-white">Day Wise Growth</h6>
                                                      </div>
                                                      <div id="outgoing_day_wise_growth_div">
                                                          <div id="outgoing_day_wise_growth" class="text-white text-center">
                                                              <h4 class="text-white">{{$operation_outgoing['day_wise_growth']}}</h4>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="row ml-1 mr-1">
                                      <div class="col-6">
                                          <div class="card bg-gradient-directional-info">
                                              <div class="card-content">
                                                  <div class="card-body justify-content-center">
                                                      <div class="text-white text-center">
                                                          <h6 class="text-white">Heavy Shipments</h6>
                                                      </div>
                                                      <div id="outgoing_heavy_deliveries_div">
                                                          <div id="outgoing_heavy_deliveries" class="text-white text-center">
                                                              <h4 class="text-white">{{$operation_outgoing['heavy_deliveries']}}</h4>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-6">
                                          <div class="card bg-gradient-directional-info">
                                              <div class="card-content">
                                                  <div class="card-body justify-content-center">
                                                      <div class="text-white text-center">
                                                          <h6 class="text-white">Light Shipments</h6>
                                                      </div>
                                                      <div id="outgoing_light_deliveries_div">
                                                          <div id="outgoing_light_deliveries" class="text-white text-center">
                                                              <h4 class="text-white">{{$operation_outgoing['light_deliveries']}}</h4>
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

        .statusBooked{
            background-color: #5DADE2;
        }
        .statusOrigin{
            background-color: #E67E22;
        }
        .statusIntransit{
            background-color: #7F8C8D;
        }
        .statusDestination{
            background-color: #F1C40F;
        }
        .statusNotattempted{
            background-color: #1F618D;
        }
        .statusDeliveryunsuccessful{
            background-color: #28B463;
        }
        .statusOnhold{
            background-color: #154360;
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

            var from_date_operations = $('#from_date_operations').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_operations_root').css('top', '-350px');
                },
                onSet: function(context) {
                    // console.log($('input[name="search_hub"]').val());
                    // var old_date_formatted = $('input[name="from_date_operations_formatted"]').val();
                    // var current_date_formatted = $('input[name="to_date_operations_formatted"]').val();
                    // var contractMoment = moment(old_date_formatted);
                    // var current = moment(contractMoment).add(29, 'days');
                    // if(contractMoment._i > current_date_formatted || current_date_formatted > current._i){
                    //     to_date_operations.pickadate('picker').set({'select': current.toDate()},{muted: true});
                    // }
                }
            });

            var to_date_operations = $('#to_date_operations').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_operations_root').css('top', '-350px');
                },
                onSet: function(context) {
                    // var current_date_formatted = $('input[name="to_date_operations_formatted"]').val();
                    // var old_date_formatted = $('input[name="from_date_operations_formatted"]').val();
                    // var currentMoment = moment(current_date_formatted);
                    // var currentDate = moment(currentMoment).subtract(29, 'days');
                    // if(old_date_formatted > currentMoment._i || currentDate._i > old_date_formatted) {
                    //     from_date_operations.pickadate('picker').set({'select': currentDate.toDate()}, {muted: true});
                    // }
                }
            });




            $('#graph_destination').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select a Destination",
                allowClear:true
            });

            $('#search_hub').prepend('<option value="""></option>').select2({
                width:'100%',
                placeholder:"Select Hub",
                allowClear:true
            });

            $('#search_service_type').prepend('<option value="""></option>').select2({
                width:'100%',
                placeholder:"Select Service Type",
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

            //incoming
            var table_incoming = $('#datatable_incoming').DataTable({
                searching: false,
                paging: false,
                info: false,
                scrollX: false, scrollY: false,
                autoWidth: false,
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.dashboard.incoming_list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="from_date_operations_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_operations_formatted"]').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_service_type = $('#search_service_type').val();
                    }
                },
                rowId: 'opfs_id',
                columns: [
                    {data: 'status', name: 'ss.name', class: 'white align-middle status',orderable: false, searchable: false},
                    {data: 'count_link', name: 'count_link', class: 'text-center white align-middle count', orderable: false, searchable: false },

                ]
            });
            var table_bar_incoming = $('#datatable_incoming_weight_range').DataTable({
                searching: false,
                paging: false,
                info: false,
                scrollX: false, scrollY: false,
                autoWidth: false,
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.dashboard.incoming_weight_range_list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="from_date_operations_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_operations_formatted"]').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_service_type = $('#search_service_type').val();
                    }
                },
                columns: [
                    {data: 'range', name: 'operation_forecast_weight_ranges.name', class: 'align-middle status',orderable: false, searchable: false},
                    {data: 'count', name: 'count', class: 'text-center align-middle count',orderable: false, searchable: false },
                ],
            });

            var ctx = document.getElementById('myChartincoming').getContext('2d');

            var doughnut_chart_shipments = @json($doughnut_chart_shipments_count);
            var incoming_bar_chart_shipments = @json($incoming_bar_chart_shipments);
            var piedata = {
                datasets: [{
                    data: [((doughnut_chart_shipments.booked/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.arrived_at_origin/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.in_transit/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.arrived_at_destination/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.not_attempted/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.delivery_unsuccessful/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.on_hold/doughnut_chart_shipments.total)*100).toFixed(2)],
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
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var currentValue = dataset.data[tooltipItem.index];
                                return currentValue + "%";
                            }
                        },
                    }
                }
            });
            Chart.pluginService.register({
                beforeDraw: function(chart) {
                    var width = myDoughnutChart.width,
                        height = myDoughnutChart.height,
                        ctx = myDoughnutChart.ctx;

                    ctx.restore();
                    var fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";

                    var text = doughnut_chart_shipments.total,
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 2;

                    if(text != 0) {
                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                }
            });

            ctxbarchart = document.getElementById('mybarchartincoming').getContext('2d');

            var bardata = {
                    datasets: [
                        {
                            label: "0.5 KG",
                            data: [incoming_bar_chart_shipments.one],
                            backgroundColor: ["#669911", "#119966" ],
                            hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                        },
                        {
                            label: "Upto 2 KG",
                            data: [incoming_bar_chart_shipments.two],
                            backgroundColor: ["#669911", "#119966" ],
                            hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                        },
                        {
                            label: "Upto 5 KG",
                            data: [incoming_bar_chart_shipments.three],
                            backgroundColor: ["#669911", "#119966" ],
                            hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                        },
                        {
                            label: "Above 5 KG",
                            data: [incoming_bar_chart_shipments.four],
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

            //outgoing

            var table_outgoing = $('#datatable_top_five').DataTable({
                searching: false,
                paging: false,
                info: false,
                scrollX: false, scrollY: false,
                autoWidth: false,
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.dashboard.outgoing_top_customers_list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="from_date_operations_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_operations_formatted"]').val();
                    }
                },
                columns: [
                    {data: 'name', name: 'ss.name', class: 'align-middle white status',orderable: false, searchable: false},
                    {data: 'count', name: 'count', class: 'text-center align-middle white count', orderable: false, searchable: false },

                ],
                'rowCallback': function(row, data, index){
                    if(index == 0){
                        $('td', row).css('background-color', '#5DADE2');
                    }
                    else if(index == 1){
                        $('td', row).css('background-color', '#E67E22');
                    }
                    else if(index == 2){
                        $('td', row).css('background-color', '#7F8C8D');
                    }
                    else if(index == 3){
                        $('td', row).css('background-color', '#F1C40F');
                    }
                    else if(index == 4){
                        $('td', row).css('background-color', '#1F618D');
                    }
                }
            });
            var table_bar_outgoing = $('#datatable_outgoing_weight_range').DataTable({
                searching: false,
                paging: false,
                info: false,
                scrollX: false, scrollY: false,
                autoWidth: false,
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.dashboard.outgoing_weight_range_list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="from_date_operations_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_operations_formatted"]').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_service_type = $('#search_service_type').val();
                    }
                },
                columns: [
                    {data: 'range', name: 'operation_forecast_weight_ranges.name', class: 'align-middle status',orderable: false, searchable: false},
                    {data: 'count', name: 'count', class: 'text-center align-middle count',orderable: false, searchable: false },
                ],
            });
            var outgoing_bar_chart_shipments = @json($outgoing_bar_chart_shipments);
            var outgoing_doughnut_top_five_customers = @json($outgoing_doughnut_top_five_customers);
            var ctx_2 = document.getElementById('myChartoutgoing').getContext('2d');

            var piedata_2 = {
                datasets: [{
                    data: [((outgoing_doughnut_top_five_customers.first.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2), ((outgoing_doughnut_top_five_customers.second.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2), ((outgoing_doughnut_top_five_customers.third.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2), ((outgoing_doughnut_top_five_customers.fourth.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2), ((outgoing_doughnut_top_five_customers.fifth.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2)],
                    backgroundColor: [
                        '#5DADE2',
                        '#E67E22',
                        '#7F8C8D',
                        '#F1C40F',
                        '#1F618D',
                    ],
                }],

                // These labels appear in the legend and in the tooltips when hovering different arcs

                labels: [
                    outgoing_doughnut_top_five_customers.first.name,
                    outgoing_doughnut_top_five_customers.second.name,
                    outgoing_doughnut_top_five_customers.third.name,
                    outgoing_doughnut_top_five_customers.fourth.name,
                    outgoing_doughnut_top_five_customers.fifth.name
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
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var currentValue = dataset.data[tooltipItem.index];
                                return currentValue + "%";
                            }
                        },
                    }
                },
            });

            Chart.pluginService.register({
                beforeDraw: function(chart) {
                    var width = myDoughnutChart_2.width,
                        height = myDoughnutChart_2.height,
                        ctx = myDoughnutChart_2.ctx;

                    ctx.restore();
                    var fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";

                    var text = outgoing_doughnut_top_five_customers.total,
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 2;

                    if(text != 0) {
                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                }
            });

            ctxbarchart_2 = document.getElementById('mybarchartoutgoing').getContext('2d');


            var bardata_2 = {
                datasets: [
                    {
                        label: "0.5 KG",
                        data: [outgoing_bar_chart_shipments.one],
                        backgroundColor: ["#669911", "#119966" ],
                        hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                    },
                    {
                        label: "Upto 2 KG",
                        data: [outgoing_bar_chart_shipments.two],
                        backgroundColor: ["#669911", "#119966" ],
                        hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                    },
                    {
                        label: "Upto 5 KG",
                        data: [outgoing_bar_chart_shipments.three],
                        backgroundColor: ["#669911", "#119966" ],
                        hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                    },
                    {
                        label: "Above 5 KG",
                        data: [outgoing_bar_chart_shipments.four],
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
            $('.operations_forecast_search').on('click',function() {
                var current_date = $('input[name="to_date_operations_formatted"]').val();
                var old_date = $('input[name="from_date_operations_formatted"]').val();
                var hub = $('#search_hub').val();
                var service_type = $('#search_service_type').val();
                table_incoming.draw();
                table_bar_incoming.draw();
                table_outgoing.draw();
                table_bar_outgoing.draw();
                $.ajax({
                    url: '{!! route('admin.dashboard.operation_forecast_search') !!}',
                    data: {
                        // 'destination': destination,
                        // 'shipper': shipper,
                        'search_date_to': current_date,
                        'search_date_from': old_date,
                        'search_hub': hub,
                        'search_service_type': service_type,
                    }
                }).done(function (data) {
                    //incoming
                    $('#myChartincoming').remove(); // this is my <canvas> elementmybarchartincoming
                    $('#incoming_chart').append('<canvas id="myChartincoming" width="10%" height="10%"><canvas>');

                    $('#incoming_per_rider_loads').remove();
                    $('#incoming_day_wise_growth').remove();
                    $('#incoming_heavy_deliveries').remove();
                    $('#incoming_light_deliveries').remove();

                    var incoming_per_rider_loads = '<div id="incoming_per_rider_loads" class="text-white text-center">' +
                                                        '<h4 class="text-white">' + data.operation_incoming.per_rider_loads +'</h4>' +
                                                    '</div>';
                    var incoming_day_wise_growth = '<div id="incoming_day_wise_growth" class="text-white text-center">' +
                                                        '<h4 class="text-white">' + data.operation_incoming.day_wise_growth +'</h4>' +
                                                    '</div>';
                    var incoming_heavy_deliveries = '<div id="incoming_heavy_deliveries" class="text-white text-center">' +
                                                        '<h4 class="text-white">' + data.operation_incoming.heavy_deliveries +'</h4>' +
                                                    '</div>';
                    var incoming_light_deliveries = '<div id="incoming_light_deliveries" class="text-white text-center">' +
                                                        '<h4 class="text-white">' + data.operation_incoming.light_deliveries +'</h4>' +
                                                    '</div>';

                    $('#incoming_per_rider_loads_div').append(incoming_per_rider_loads);
                    $('#incoming_day_wise_growth_div').append(incoming_day_wise_growth);
                    $('#incoming_heavy_deliveries_div').append(incoming_heavy_deliveries);
                    $('#incoming_light_deliveries_div').append(incoming_light_deliveries);

                    var ctx = document.getElementById('myChartincoming').getContext('2d');

                    var doughnut_chart_shipments = data.doughnut_chart_shipments_count;
                    var incoming_bar_chart_shipments = data.incoming_bar_chart_shipments;
                    var outgoing_bar_chart_shipments = data.outgoing_bar_chart_shipments;
                    var outgoing_doughnut_top_five_customers = data.outgoing_doughnut_top_five_customers;
                    var piedata = {
                        datasets: [{
                            data: [((doughnut_chart_shipments.booked/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.arrived_at_origin/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.in_transit/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.arrived_at_destination/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.not_attempted/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.delivery_unsuccessful/doughnut_chart_shipments.total)*100).toFixed(2), ((doughnut_chart_shipments.on_hold/doughnut_chart_shipments.total)*100).toFixed(2)],
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
                            //             tooltips: {
                            //                 //     label: [Math.round((doughnut_chart_shipments.booked/doughnut_chart_shipments.total)*100) + '%', 20, 30, 40, 50, 60, 70],
                            //                 // },
                            //                 callbacks: {
                            //                     return dataset.data + "%";
                            // }
                            //             }
                            tooltips: {
                                callbacks: {
                                    label: function (tooltipItem, data) {
                                        var dataset = data.datasets[tooltipItem.datasetIndex];
                                        var currentValue = dataset.data[tooltipItem.index];
                                        return currentValue + "%";
                                    }
                                },
                            }
                        }
                    });

                    Chart.pluginService.register({
                        beforeDraw: function(chart) {
                            var width = myDoughnutChart.width,
                                height = myDoughnutChart.height,
                                ctx = myDoughnutChart.ctx;

                            ctx.restore();
                            var fontSize = (height / 114).toFixed(2);
                            ctx.font = fontSize + "em sans-serif";
                            ctx.textBaseline = "middle";

                            var text = doughnut_chart_shipments.total,
                                textX = Math.round((width - ctx.measureText(text).width) / 2),
                                textY = height / 2;
                            if(text != 0){
                                ctx.fillText(text, textX, textY);
                                ctx.save();
                            }
                        }
                    });


                    $('#mybarchartincoming').remove(); // this is my <canvas> element
                    $('#incoming_bar_chart').append('<canvas id="mybarchartincoming" height="230px"><canvas>');

                    ctxbarchart = document.getElementById('mybarchartincoming').getContext('2d');

                    var bardata = {
                        datasets: [
                            {
                                label: "0.5 KG",
                                data: [incoming_bar_chart_shipments.one],
                                backgroundColor: ["#669911", "#119966" ],
                                hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                            },
                            {
                                label: "Upto 2 KG",
                                data: [incoming_bar_chart_shipments.two],
                                backgroundColor: ["#669911", "#119966" ],
                                hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                            },
                            {
                                label: "Upto 5 KG",
                                data: [incoming_bar_chart_shipments.three],
                                backgroundColor: ["#669911", "#119966" ],
                                hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                            },
                            {
                                label: "Above 5 KG",
                                data: [incoming_bar_chart_shipments.four],
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

                    //outtgoing

                    $('#outgoing_per_rider_loads').remove();
                    $('#outgoing_day_wise_growth').remove();
                    $('#outgoing_heavy_deliveries').remove();
                    $('#outgoing_light_deliveries').remove();

                    var outgoing_per_rider_loads = '<div id="outgoing_per_rider_loads" class="text-white text-center">' +
                        '<h4 class="text-white">' + data.operation_outgoing.per_rider_loads +'</h4>' +
                        '</div>';
                    var outgoing_day_wise_growth = '<div id="outgoing_day_wise_growth" class="text-white text-center">' +
                        '<h4 class="text-white">' + data.operation_outgoing.day_wise_growth +'</h4>' +
                        '</div>';
                    var outgoing_heavy_deliveries = '<div id="outgoing_heavy_deliveries" class="text-white text-center">' +
                        '<h4 class="text-white">' + data.operation_outgoing.heavy_deliveries +'</h4>' +
                        '</div>';
                    var outgoing_light_deliveries = '<div id="outgoing_light_deliveries" class="text-white text-center">' +
                        '<h4 class="text-white">' + data.operation_outgoing.light_deliveries +'</h4>' +
                        '</div>';

                    $('#outgoing_per_rider_loads_div').append(outgoing_per_rider_loads);
                    $('#outgoing_day_wise_growth_div').append(outgoing_day_wise_growth);
                    $('#outgoing_heavy_deliveries_div').append(outgoing_heavy_deliveries);
                    $('#outgoing_light_deliveries_div').append(outgoing_light_deliveries);


                    $('#pickup_request').remove(); // this is my <canvas> element
                    $('#no_of_shipments').remove(); // this is my <canvas> element
                    $('#outgoing_pickups_tbody').append('<tr id="pickup_request" role="row">' +
                        '<td>Pickup Request</td>' +
                        '<td class="text-center">' + data.operation_outgoing_pickups.pickups + '</td>' +
                        '</tr>' +
                        '<tr id="no_of_shipments" role="row">\n' +
                        '<td># of Shipments</td>\n' +
                        '<td class="text-center">' + data.operation_outgoing_pickups.no_of_shipments + '</td>' +
                        '</tr>');


                    $('#myChartoutgoing').remove(); // this is my <canvas> elementmybarchartincoming
                    $('#outgoing_chart').append('<canvas id="myChartoutgoing" height="230px"><canvas>');

                    var ctx_2 = document.getElementById('myChartoutgoing').getContext('2d');

                    var piedata_2 = {
                        datasets: [{
                            data: [((outgoing_doughnut_top_five_customers.first.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2), ((outgoing_doughnut_top_five_customers.second.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2), ((outgoing_doughnut_top_five_customers.third.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2), ((outgoing_doughnut_top_five_customers.fourth.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2), ((outgoing_doughnut_top_five_customers.fifth.count/outgoing_doughnut_top_five_customers.total)*100).toFixed(2)],
                            backgroundColor: [
                                '#5DADE2',
                                '#E67E22',
                                '#7F8C8D',
                                '#F1C40F',
                                '#1F618D',
                            ],
                        }],

                        // These labels appear in the legend and in the tooltips when hovering different arcs

                        labels: [
                            outgoing_doughnut_top_five_customers.first.name,
                            outgoing_doughnut_top_five_customers.second.name,
                            outgoing_doughnut_top_five_customers.third.name,
                            outgoing_doughnut_top_five_customers.fourth.name,
                            outgoing_doughnut_top_five_customers.fifth.name
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
                                callbacks: {
                                    label: function (tooltipItem, data) {
                                        var dataset = data.datasets[tooltipItem.datasetIndex];
                                        var currentValue = dataset.data[tooltipItem.index];
                                        return currentValue + "%";
                                    }
                                },
                            }
                        },
                    });


                    Chart.pluginService.register({
                        beforeDraw: function(chart) {
                            var width = myDoughnutChart_2.width,
                                height = myDoughnutChart_2.height,
                                ctx = myDoughnutChart_2.ctx;

                            ctx.restore();
                            var fontSize = (height / 114).toFixed(2);
                            ctx.font = fontSize + "em sans-serif";
                            ctx.textBaseline = "middle";

                            var text = outgoing_doughnut_top_five_customers.total,
                                textX = Math.round((width - ctx.measureText(text).width) / 2),
                                textY = height / 2;

                            if(text != 0) {
                                ctx.fillText(text, textX, textY);
                                ctx.save();
                            }
                        }
                    });

                    $('#mybarchartoutgoing').remove(); // this is my <canvas> element
                    $('#outgoing_bar_chart').append('<canvas id="mybarchartoutgoing" height="230px"><canvas>');

                    ctxbarchart_2 = document.getElementById('mybarchartoutgoing').getContext('2d');

                    var bardata_2 = {
                        datasets: [
                            {
                                label: "0.5 KG",
                                data: [outgoing_bar_chart_shipments.one],
                                backgroundColor: ["#669911", "#119966" ],
                                hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                            },
                            {
                                label: "Upto 2 KG",
                                data: [outgoing_bar_chart_shipments.two],
                                backgroundColor: ["#669911", "#119966" ],
                                hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                            },
                            {
                                label: "Upto 5 KG",
                                data: [outgoing_bar_chart_shipments.three],
                                backgroundColor: ["#669911", "#119966" ],
                                hoverBackgroundColor: ["#66A2EB", "#FCCE56"]
                            },
                            {
                                label: "Above 5 KG",
                                data: [outgoing_bar_chart_shipments.four],
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

            });
        });
    </script>
@endsection