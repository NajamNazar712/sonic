@extends('admin.layout.master')

@section('title', 'Same-Day Delivery')

@section('content')
    <h1 class="mb-1">
        Same-Day Delivery
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Phone</th>
                        <th class="border-primary border-darken-1">Consignee Address</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Same-day Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Booked Date</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Dispatched Time</th>
                        <th class="border-primary border-darken-1">Delivered Time</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">TAT</th>
                        <th class="border-primary border-darken-1">Time Remaining</th>
                        <th class="border-primary border-darken-1">Instructions</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style type="text/css">
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.sameday.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking .No');
                            head.push('Shipper');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Consignee Name');
                            head.push('Consignee Phone');
                            head.push('Consignee Address');
                            head.push('Product Type');
                            head.push('Same-day Type');
                            head.push('Status');
                            head.push('Booked Date');
                            head.push('Arrival Date');
                            head.push('Dispatched Time');
                            head.push('Delivered Time');
                            head.push('Updated By');
                            head.push('TAT');
                            head.push('Time Remaining');
                            head.push('Instructions');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_no);
                                row.push(values.shipper);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
                                row.push(values.consignee_address);
                                row.push(values.product_name);
                                row.push(values.timing);
                                row.push(values.current_status);
                                row.push(values.booked_date);
                                row.push(values.arrival);
                                row.push(values.dispatched_time);
                                row.push(values.delivered_status);
                                row.push(values.updated_by);
                                row.push(values.tat);
                                row.push(values.remaining_time);
                                row.push(values.instructions);

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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Sameday Deliveries',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.sameday.list') }}',
                rowId: 'shId',
                order: [[12, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
                    {data: 'timing', name: 'sms.id', class: 'align-middle timing'},
                    {data: 'current_status', name: 'current_status', class: 'align-middle current_status'},
                    {data: 'booked_date', name: 'shipments.created_at', class: 'align-middle booked_date'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'dispatched_time', name: 'dispatched.created_at', class: 'align-middle dispatched_time'},
                    {data: 'delivered_status', name: 'delivered_status', class: 'align-middle delivered_status',orderable: false, searchable: false},
                    {data: 'updated_by', name: 'updater.name', class: 'align-middle updated_by'},
                    {data: 'tat', name: 'tat', class: 'align-middle tat', orderable: false, searchable: false},
                    {data: 'remaining_time', name: 'remaining_time', class: 'align-middle remaining_time', orderable: false, searchable: false},
                    {data: 'instructions', name: 'shipments.special_instructions', class: 'align-middle instructions'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var product_select = '<select name="product_select" id="product_select" class="select2 form-control"></select>';
                    var timing_select = '<select name="timing_select" id="timing_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.delivered_status') || $(header).is('.tat') || $(header).is('.remaining_time')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.product_type')){
                            $(product_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.current_status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.timing')){
                            $(timing_select).appendTo($(search))
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
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $products !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $products !!}, function (obj) {
                        obj.text = obj.product_name;

                        return obj;
                    });

                    $("#product_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Product",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $timings !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data2 = $.map({!! $timings !!}, function (obj) {
                        obj.text = obj.timing; // replace name with the property used for the text

                        return obj;
                    });

                    $("#timing_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Timing",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            function print(id) {
                $.ajax({
                    url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                    method: 'POST',
                    data: {
                        'ids[]': id,
                        'admin': '{!! Auth::id() !!}',
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
            $('body').on('click','.airwaybill',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                // console.log(id);
                print(id);
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

        });

    </script>
@endsection