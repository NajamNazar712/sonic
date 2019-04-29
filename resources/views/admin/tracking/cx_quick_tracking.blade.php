
@extends('admin.layout.master')
@section('title','Quick Tracking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    CX Quick Tracking
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                                <div class="row justify-content-center mb-2">
                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Search Tracking Number" id="search_tracking_number">
                                        </fieldset>
                                    </div>
                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Search Consignee Phone Number" id="search_consignee_phone_number">
                                        </fieldset>
                                    </div>
                                    <div class="col-3">
                                        <fieldset class="form-group">
                                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                                @foreach($shippers as $shipper)
                                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Search Order ID" id="search_order_id">
                                        </fieldset>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                    </div>
                                </div>
                            </div>
                            <div id="tracking_info" class="d-none">
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Tracking Number</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">COD Amount</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Shipper Name</th>
                                        <th class="border-primary border-darken-1">Consignee Name</th>
                                        <th class="border-primary border-darken-1">Consignee Phone Number</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <style>
        table.dataTable {
            font-size: 12px;
        }
        #single_div p.status{
            font-weight: bold;
        }
        #single_div{
            font-size: 20px;
        }
        #single_div h4{
            font-weight: bolder;
            font-size: 18px;
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

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
        .datatable tbody tr{
            /*font-size: 18px;*/
            /*font-weight: bold;*/
        }

        .goldClass{
            background-color: gold;
        }
        .yellowClass{
            background-color: #86cd7c;
        }
        .greenClass{
            background-color: springgreen;
        }
        .redClass{
            background-color: red;
            color:#fff;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#consignee_phone_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_order_id').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Shipper',
                width: '100%',
                allowClear: true
            });
            var table;
            function init(){
                jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                    if (this.context.length) {
                        body = [];

                        var jsonResult = $.ajax({
                            url: '{{ route('admin.cx_quick_tracking.cx_list') }}',
                            data: {
                                'page': 'all',
                                'search_tracking': $('#search_tracking_number').val(),
                                'search_shipper': $('#search_shipper').val(),
                                'search_phone_no': $('#search_consignee_phone_number').val(),
                                'search_order_id': $('#search_order_id').val(),
                            },
                            success: function (result) {
                                head = [];

                                head.push('S. No');
                                head.push('Tracking Number');
                                head.push('Origin');
                                head.push('Destination');
                                head.push('Address');
                                head.push('COD Amount.');
                                head.push('Status');
                                head.push('Shipper Name');
                                head.push('Consignee Name');
                                head.push('Consignee Phone Number');
                                $.each(result.data, function (index, values) {
                                    row = [];

                                    row.push(index + 1);
                                    row.push(values.tracking_number);
                                    row.push(values.origin);
                                    row.push(values.destination);
                                    row.push(values.address);
                                    row.push(values.cod_amount);
                                    row.push(values.status);
                                    row.push(values.shipper_name);
                                    row.push(values.consignee_name);
                                    row.push(values.consignee_phone_no);

                                    body.push(row);
                                });
                            },
                            async: false
                        });

                        return {body: body, header: head};
                    }
                });
                table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [{
                        extend: 'excel',
                        title: 'CX Quick Tracking',
                        className: 'btn btn-primary mb-1',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                    lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                    pageLength: 50,
                    pagingType: 'full_numbers',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.cx_quick_tracking.cx_list') }}',
                        data: function (d) {
                            d.search_tracking = $('#search_tracking_number').val();
                            d.search_shipper = $('#search_shipper').val();
                            d.search_phone_no = $('#search_consignee_phone_number').val();
                            d.search_order_id = $('#search_order_id').val();
                        }
                    },
                    order: [1, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_numbers'},
                        {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                        {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                        {data: 'address', name: 'shipments.consignee_address', class: 'align-middle address'},
                        {data: 'cod_amount', name: 'shipments.amount', class: 'align-middle cod_amount'},
                        {data: 'status', name: 'ss.name', class: 'align-middle status'},
                        {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                        {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                        {data: 'consignee_phone_no', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone_no'
                        }
                    ],
                    rowCallback: function (row, data, index) {
                        var info = table.page.info();
                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    },
                    initComplete: function () {
                        this.api().table().columns.adjust();
                    }
                });
            }
            $('#search_filter_btn').on('click', function () {
                if($('#tracking_info').hasClass('d-none')) {
                    $('#tracking_info').removeClass('d-none');
                    init();
                }
                else {
                    table.draw();
                }
            });
        });
    </script>
@endsection