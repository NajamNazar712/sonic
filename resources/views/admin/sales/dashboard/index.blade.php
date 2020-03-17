@extends('admin.layout.master')

@section('title', 'Sales Dashboard')

@section('content')
    <h1>Sales Dashboard</h1>

    <section>
        <div class="row">
            <div class="col-3">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-stripped table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Current Average Shipments Per Day</td><td>{{ $business_accounts_total->average_shipments }}</td>
                                    </tr>
                                    <tr>
                                        <td>Projected Shipments Per Day</td><td>{{ $business_accounts_total->projected_shipments }}</td>
                                    </tr>
                                    <tr>
                                        <td>Overall Projection achieved per Day</td><td>{{ $business_accounts_total->last_day_numbers }}</td>
                                    </tr>
                                    <tr>
                                        <td>Overall Projection achieved %</td><td>{{ $business_accounts_total->achieved }}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-stripped table-bordered">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Average Shipment / Day</th>
                                    <th class="border-primary border-darken-1">Projected Shipment / Day</th>
                                    <th class="border-primary border-darken-1">Last Day Numbers</th>
                                    <th class="border-primary border-darken-1">Achieved %</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($hubs_data) > 0)
                                    @foreach($hubs_data as $hub)
                                        <tr>
                                            <td>{{ $hub->city->name }}</td><td>{{ $hub->average_shipment }}</td><td>{{ $hub->projected_shipment }}</td><td>{{ $hub->last_day_number }}</td><td>{{ $hub->achieved }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="5">No Data Found</td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">

                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Account ID</th>
                                    <th class="border-primary border-darken-1">Company Name</th>
                                    <th class="border-primary border-darken-1">City Name</th>
                                    <th class="border-primary border-darken-1">Contact Person</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">Email Address</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                    <th class="border-primary border-darken-1">Average Shipment / Day</th>
                                    <th class="border-primary border-darken-1">Projected Shipment / Day</th>
                                    <th class="border-primary border-darken-1">Last Day Number</th>
                                    <th class="border-primary border-darken-1">Achieved %</th>
                                    <th class="border-primary border-darken-1">Reason</th>
                                    <th class="border-primary border-darken-1">Remarks</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{{--    <div class="modal fade text-left" id="SalesTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal"--}}
{{--         aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-md" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h4 class="modal-title" id="">Tag Sales Person</h4>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <input type="hidden" id="shipper_id">--}}
{{--                    <select name="Sale_person" id="saletag" class="form-control select2">--}}
{{--                        @foreach($sale_name as $sn)--}}
{{--                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--                <div class="modal-footer">--}}
{{--                    <button type="button" class="btn btn-success" id="salesTagSubmit">Submit</button>--}}
{{--                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">



@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.dashboard.sales.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Account ID');
                            head.push('Company Name');
                            head.push('City Name');
                            head.push('Contact Person');
                            head.push('Phone No.');
                            head.push('Address');
                            head.push('Email Address');
                            head.push('Status');
                            head.push('Sales Person Tagged');
                            head.push('Average Shipment / Day');
                            head.push('Projected Shipment / Day');
                            head.push('Last Day Number');
                            head.push('Achieved %');
                            head.push('Reason');
                            head.push('Remarks');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.account_id);
                                row.push(values.shipper);
                                row.push(values.city);
                                row.push(values.poc);
                                row.push(values.phone);
                                row.push(values.address);
                                row.push(values.email);
                                row.push(values.shipper_status);
                                row.push(values.sales_person);
                                row.push(values.average_shipment);
                                row.push(values.projected_shipment);
                                row.push(values.last_day_number);
                                row.push(values.achieved);
                                row.push(values.reason);
                                row.push(values.remarks);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Sales Dashboard',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset',
                ],
                scrollX: true,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                rowId: 'id',
                ajax: '{{ route('admin.dashboard.sales.list') }}',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'account_id', name:'u.id', class: 'align-middle account_id'},
                    {data: 'shipper', name:'u.name', class: 'align-middle shipper'},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'poc', name: 'u.poc', class: 'align-middle poc'},
                    {data: 'phone', name: 'u.phone', class: 'align-middle phone'},
                    {data: 'address', name: 'u.address', class: 'align-middle address'},
                    {data: 'email', name: 'u.email', class: 'align-middle email'},
                    {data: 'shipper_status', name: 'shipper_status', class: 'align-middle shipper_status'},
                    {data: 'sales_person', name: 'sp.name', class: 'align-middle sales_person'},
                    {data: 'average_shipment', name: 'business_projection_accounts.average_shipment', class: 'align-middle average_shipment'},
                    {data: 'projected_shipment', name: 'business_projection_accounts.projected_shipment', class: 'align-middle projected_shipment'},
                    {data: 'last_day_number', name: 'business_projection_accounts.last_day_number', class: 'align-middle last_day_number'},
                    {data: 'achieved', name: 'business_projection_accounts.achieved', class: 'align-middle achieved'},
                    {data: 'reason', name: 'bpr.name', class: 'align-middle reason'},
                    {data: 'remarks', name: 'business_projection_accounts.remarks', class: 'align-middle remarks'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="3">Enable</option>' +
                        '<option value="4">Disable</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.shipper_status')){
                            $(drop_select).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

        });

    </script>

@endsection

