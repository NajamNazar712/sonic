@extends('admin.layout.master')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
      </div>
      <div class="content-body">
        
        
       
        <!-- Active Orders -->
       <h1>Welcome To Trax Logistics,
       <span class="user-name text-bold-700 ">{{Auth::user()->name}}</span>
     </h1>
        <!-- Active Orders -->
          <div class="row">
              <div class="col">
                  <div class="card">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-book-open font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-right">
                                      <h3 class="">{{$booked}}</h3>
                                      <span>Booked</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card bg-gradient-directional-info">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-basket-loaded text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$received}}</h3>
                                      <span>Received</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div><div class="col">
                  <div class="card bg-gradient-directional-success">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-emoticon-smile text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$delivered}}</h3>
                                      <span>Delivered</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div><div class="col">
                  <div class="card bg-gradient-directional-warning">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-refresh text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$return}}</h3>
                                      <span>Returns</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="card bg-gradient-directional-danger">
                      <div class="card-content">
                          <div class="card-body">
                              <div class="media d-flex">
                                  <div class="align-self-center">
                                      <i class="icon-shield text-white font-large-2 float-left"></i>
                                  </div>
                                  <div class="media-body text-white text-right">
                                      <h3 class="text-white">{{$pending}}</h3>
                                      <span>Pendings</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <h2>Order Details</h2>
              <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                  <thead>
                  <tr role="row" class="bg-primary white">

                      <th class="border-primary border-darken-1"></th>
                      <th class="border-primary border-darken-1">SN No.</th>
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
                      <th class="border-primary border-darken-1">Action</th>
                  </tr>
                  </thead>
              </table>

          </div>
      </div>
    </div>
  </div>
  <!-- ////////////////////////////////////////////////////////////////////////////-->

  @endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/fixedHeader.dataTables.min.css')}}">

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
            border-color: #666EE8;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
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

    <script src="{{asset('app-assets/vendors/js/charts/jquery.sparkline.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.fixedHeader.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                // "scrollX": true,
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: 'Print',
                    className: 'btn btn-primary dispute_modal',
                    enabled: true,
                    action: function (e, dt, node, config) {

                    }
                }],
                fixedHeader: {
                    header: true,
                    headerOffset: $('.header-navbar').height()
                },
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.orders.list') }}',
                rowId: 'shipment_id',
                order: [[1, 'asc']],
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
                    {data: 'booking_date', name: 'booking_date', class: 'align-middle booking_date'},
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
                }
            });
            // var dom = document.getElementById("stacked-line");
            // var myChart = echarts.init(dom);
            // var app = {};
            // option = null;
            // option = {
            //     title: {
            //         text: '折线图堆叠'
            //     },
            //     tooltip: {
            //         trigger: 'axis'
            //     },
            //     legend: {
            //         data:['邮件营销','联盟广告','视频广告','直接访问','搜索引擎']
            //     },
            //     grid: {
            //         left: '3%',
            //         right: '4%',
            //         bottom: '3%',
            //         containLabel: true
            //     },
            //     toolbox: {
            //         feature: {
            //             saveAsImage: {}
            //         }
            //     },
            //     xAxis: {
            //         type: 'category',
            //         boundaryGap: false,
            //         data: ['周一','周二','周三','周四','周五','周六','周日']
            //     },
            //     yAxis: {
            //         type: 'value'
            //     },
            //     series: [
            //         {
            //             name:'邮件营销',
            //             type:'line',
            //             stack: '总量',
            //             data:[120, 132, 101, 134, 90, 230, 210]
            //         },
            //         {
            //             name:'联盟广告',
            //             type:'line',
            //             stack: '总量',
            //             data:[220, 182, 191, 234, 290, 330, 310]
            //         },
            //         {
            //             name:'视频广告',
            //             type:'line',
            //             stack: '总量',
            //             data:[150, 232, 201, 154, 190, 330, 410]
            //         },
            //         {
            //             name:'直接访问',
            //             type:'line',
            //             stack: '总量',
            //             data:[320, 332, 301, 334, 390, 330, 320]
            //         },
            //         {
            //             name:'搜索引擎',
            //             type:'line',
            //             stack: '总量',
            //             data:[820, 932, 901, 934, 1290, 1330, 1320]
            //         }
            //     ]
            // };
            // ;
            // if (option && typeof option === "object") {
            //     myChart.setOption(option, true);
            // }

        });
    </script>
@endsection