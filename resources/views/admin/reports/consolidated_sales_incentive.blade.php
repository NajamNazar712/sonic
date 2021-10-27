@extends('admin.layout.master')

@section('title', 'Consolidated Sales Incentive Report')

@section('content')
    <h1 class="mb-1">
        Consolidated Sales Incentive Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12 ">

                        <div id="search_form" class="row mb-2 justify-content-center">
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="admin" id="admin" class="form-control select2">
                                        @foreach($admins as $admin)
                                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="filter_date" id="filter_date" class="form-control select2">
                                        @foreach($filter_dates as $filter_date)
                                            <option value="{{$filter_date->id}}">{{$filter_date->date}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-2">
                                <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="" id="report_data">

                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Employee ID</th>
                            <th class="border-primary border-darken-1">Employee Name</th>
                            <th class="border-primary border-darken-1">Shippers</th>
                            <th class="border-primary border-darken-1">Shipments</th>
                            <th class="border-primary border-darken-1">Revenue</th>
                            <th class="border-primary border-darken-1">Commission</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        span.font-13{
            font-size: 13px;
        }
    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            $('#filter_date').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'From - To*',
                width:'100%',
                allowClear:true
            });

            $('#admin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Employee',
                width:'100%',
                allowClear:true
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
                        url: '{{ route('admin.reports.sales_incentive.consolidated_list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Shippers');
                            head.push('Shipments');
                            head.push('Revenue');
                            head.push('Commission');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.admin);
                                row.push(values.shipper_count);
                                row.push(values.shipment_count);
                                row.push(values.revenue);
                                row.push(values.commission);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Consolidated Sales Incentive Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                "autoWidth": false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.sales_incentive.consolidated_list') }}',
                    data:function (d){
                        d.filter_date = $('#filter_date').val();
                        d.admin_id = $('#admin').val();
                    }
                },
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'trax_id' ,name: 'a.trax_id', class: 'align-middle trax_id'},
                    { data:'admin' ,name: 'a.name', class: 'align-middle admin'},
                    { data:'shipper_count' ,name: 'sales_incentives.shipper_count', class: 'align-middle shipper_count'},
                    { data:'shipment_count' ,name: 'sales_incentives.shipment_count', class: 'align-middle shipment_count'},
                    { data:'revenue' ,name: 'sales_incentives.revenue', class: 'align-middle revenue'},
                    { data:'commission' ,name: 'sales_incentives.commission', class: 'align-middle commission'}
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
                table.draw(true);
            });
        });
    </script>
@endsection
