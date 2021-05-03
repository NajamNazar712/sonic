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

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('input.incentive_value').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.hr.rider_incentive.list') }}',
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
                ajax: '{{ route('admin.human_resource.rider_incentive.list') }}',
                rowId: 'rider_id',
                order: [[6, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'employee_id', name: 'riders.employee_id', class: 'align-middle employee_id'},
                    {data: 'rider_name', name: 'riders.name', class: 'align-middle rider_name'},
                    {data: 'rider_phone', name: 'riders.phone', class: 'align-middle rider_phone'},
                    {data: 'cnic', name: 'riders.cnic', class: 'align-middle cnic'},
                    {data: 'rider_city', name: 'cities.name', class: 'align-middle rider_city'},
                    {data: 'rider_type', name: 'rt.name', class: 'align-middle rider_type'},
                    {data: 'pickup_shipments', name: 'riders_incentives.pickup_shipments', class: 'align-middle pickup_shipments'},
                    {data: 'pickup_incentive', name: 'riders_incentives.pickup_incentive', class: 'align-middle pickup_incentive'},
                    {data: 'delivery_shipments', name: 'riders_incentives.delivery_shipments', class: 'align-middle delivery_shipments'},
                    {data: 'delivery_incentive', name: 'riders_incentives.delivery_incentive', class: 'align-middle delivery_incentive'},
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
                    /*var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Disable</option>' +
                        '<option value="1">Enable</option>' +
                        '</select>';*/
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        /*else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }*/
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    /*$("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });*/
                    this.api().table().columns.adjust();
                }
            });


        });


    </script>
@endsection