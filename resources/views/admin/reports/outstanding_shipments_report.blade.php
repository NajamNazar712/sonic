@extends('admin.layout.master')

@section('title', 'Outstanding Shipments Report')

@section('content')
    <h1 class="mb-1">
        Outstanding Shipments Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="form-group">
                            <select name="hub" class="select2" id="hub">


                                @foreach($hubs as $hub)
                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ml-1">
                            <select name="shipment_status" class="select2" id="shipment_status">
                                <option value="1">Outstanding Shipments</option>
                                <option value="2">Resolved</option>
                                <option value="3">Adjust In Payment</option>

                            </select>
                        </div>
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                            </div>

                            <input type="text" name="delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="delivery_date_from" placeholder="Delivery Date (From)">
                        </div>

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                            </div>

                            <input type="text" name="delivery_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="delivery_date_to" placeholder="Delivery Date (To)">
                        </div>

                        <div class="form-group ml-1">
                            <button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
                        </div>
                    </form>

                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Consignee</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Recovery Status</th>
                        <th class="border-primary border-darken-1">Current Status</th>
                        <th class="border-primary border-darken-1">Status Updated Datetime</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">DNCC</th>
                        <th class="border-primary border-darken-1">SDN</th>
                        <th class="border-primary border-darken-1">Aging</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '150px',
                placeholder: 'Select Hub',
                allowClear:true,
            }).bind('change', function() {
                table.draw();
            });
            $('#search_form #shipment_status').prepend('<option value="" selected="selected"></option>').select2({
                width: '150px',
                placeholder: 'Select Status',
                allowClear:true,
            }).bind('change', function() {
                table.draw();
            });

            $('#search_form #delivery_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #delivery_date_to').pickadate('picker').set('min', $('#search_form #delivery_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #delivery_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #delivery_date_from').pickadate('picker').set('max', $('#search_form #delivery_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form').on('submit',function (e) {
                e.preventDefault();
                table.draw();
            });
            // $('#search_form').validate({
            //     errorClass: 'danger',
            //     successClass: 'success',
            //     errorPlacement: function(error, element) {
            //         error.addClass('w-100').appendTo(element.parents('form'));
            //     },
            //     submitHandler: function(form) {
            //         table.draw();
            //
            //         return false;
            //     }
            // });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.outstanding_shipments.list') }}',
                        data:{
                            'page': 'all',
                            'hub': $('#search_form #hub').val(),
                            'shipment_status': $('#search_form #shipment_status').val(),
                            'delivery_date_from': $('#search_form input[name="delivery_date_from_formatted"]').val(),
                            'delivery_date_to': $('#search_form input[name="delivery_date_to_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Tracking Number');
                            head.push('Consignee');
                            head.push('Address');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Shipper');
                            head.push('Service Type');
                            head.push('Amount');
                            head.push('Recovery Status');
                            head.push('Current Status');
                            head.push('Status Updated at');
                            head.push('Remarks');
                            head.push('DNCC');
                            head.push('SDN');
                            head.push('Aging');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.consignee);
                                row.push(values.address);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.shipper);
                                row.push(values.service_type);
                                row.push(values.amount);
                                row.push(values.recovery_status);
                                row.push(values.current_status);
                                row.push(values.status_updated_at);
                                row.push(values.remarks);
                                row.push(values.dncc);
                                row.push(values.sdn);
                                row.push(values.aging);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '300px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Outstanding Shipments Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.outstanding_shipments.list') }}',
                    data: function (d) {
                        d.hub = $('#search_form #hub').val();
                        d.shipment_status = $('#search_form #shipment_status').val();
                        d.delivery_date_from = $('#search_form input[name="delivery_date_from_formatted"]').val();
                        d.delivery_date_to = $('#search_form input[name="delivery_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[1, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'tracking_number', name: 's.tracking_number', class: 'align-middle text-center tracking_number'},
                    {data:'consignee', name: 's.consignee_name', class: 'align-middle text-center consignee'},
                    {data:'address', name: 's.consignee_address', class: 'align-middle text-center address'},
                    {data:'destination', name: 'dc.name', class: 'align-middle text-center destination'},
                    {data:'hub', name: 'hc.name', class: 'align-middle text-center hub'},
                    {data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
                    {data:'service_type', name: 'bt.booking_type', class: 'align-middle text-center service_type'},
                    {data:'amount', name: 's.amount', class: 'align-middle text-center amount'},
                    {data:'recovery_status', name: 'ss.name as status', class: 'align-middle text-center recovery_status'},
                    {data:'current_status', name: 'ss.name as status', class: 'align-middle text-center current_status'},
                    {data:'status_updated_at', name: 'sj.updated_at', class: 'align-middle text-center status_updated_at'},
                    {data:'remarks', name: 'sj.remarks', class: 'align-middle text-center remarks'},
                    {data:'dncc', name: 'delivery_note_shipments.delivery_note_id', class: 'align-middle text-center dncc'},
                    {data:'sdn', name: 'dnsdn.station_deposit_note_id', class: 'align-middle text-center sdn'},
                    {data:'aging', name: 'aging', class: 'align-middle text-center aging'}

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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.recovery_status')) {
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

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });

    </script>
@endsection