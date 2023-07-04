@extends('admin.layout.master')
@section('title','Shipper Return Receiving History')

@section('content')
    <h1 class="mb-1">
        Shipper Return Receiving History
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">
                    <div class="col-12">
                        <form id="search_form" class="form-inline mb-1 justify-content-center " novalidate="novalidate">
                            <div class="col-3 mt-1">
                                <fieldset class="form-group input-group">
                                    <select name="rider_id" id="rider_id" class="select2 form-control " style="width: 100%">
                                        @foreach($riders as $rider)
                                            <option value="{{$rider->id}}">{{$rider->name}} - {{$rider->trax_id}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-3 mt-1">
                                <fieldset class="form-group input-group">
                                    <select name="shipper_id" id="shipper_id" class="select2 form-control " style="width: 100%" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($shippers as $shipper)
                                            <option value="{{$shipper->id}}">{{$shipper->name}} - {{$shipper->id}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-3 mt-1">
                                <fieldset class="form-group input-group">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                    <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)" data-rule-required="true" data-msg-required="Search Date (From) is required">
                                </fieldset>
                            </div>

                            <div class="col-3 mt-1">
                                <fieldset class="form-group input-group">

                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)" data-rule-required="true" data-msg-required="Search Date (To) is required">
                                </fieldset>
                            </div>

                            <div class="col-2 mt-1">
                                <div class="form-group">
                                    <button type="submit" id="search_filter_btn"
                                            class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                
                
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Return Note No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Rider TraxID</th>
                        <th class="border-primary border-darken-1">Rider City</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Shipper Receiving time</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $("#search_form #shipper_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Shipper",
                width: '100%',
                allowClear:true,
            });
            
            $("#search_form #rider_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Rider",
                width: '100%',
                allowClear:true,
            });

            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.shipper_return_receiving.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Return Note No.');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Shipper');
                            head.push('Rider Name');
                            head.push('Rider TraxID');
                            head.push('Rider City');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Consignee Name');
                            head.push('Phone');
                            head.push('Address');
                            head.push('Collection Amount');
                            head.push('Shipping Mode');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Remarks');
                            head.push('Shipper Receiving time');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.return_note_id_excel);
                                row.push(values.tracking);
                                row.push(values.order_id);
                                row.push(values.shipper);
                                row.push(values.rider_name);
                                row.push(values.rider_trax_id);
                                row.push(values.rider_city);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
                                row.push(values.consignee_address);
                                row.push(values.amount);
                                row.push(values.mode);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.received_remarks);
                                row.push(values.received_date);


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
                        title: 'Shipper Return Receiving History',
                        className: 'btn btn-primary datatable_excel_btn d-none',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                ajax:{
                    url:'{{ route('admin.return.shipper_return_receiving.history.list') }}',
                    data: function (d) {
                        d.rider_id = $('#rider_id').val();
                        d.shipper_id = $('#shipper_id').val();
                        d.search_date_from = $('input[name="search_date_from_formatted').val();
                        d.search_date_to = $('input[name="search_date_to_formatted').val();
                    }
                },
                rowId: 'shId',
                order: [[1, 'desc']],
                columns: [
                    {data: 'id',defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'return_note_id', name: 'return_sheets.return_note_id', class: 'align-middle return_note_id'},
                    {data: 'tracking_number', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 's.order_id', class: 'align-middle order_id'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'rider_name', name: 'rider.name', class: 'align-middle rider_name'},
                    {data: 'rider_trax_id', name: 'rider.trax_id', class: 'align-middle rider_trax_id'},
                    {data: 'rider_city', name: 'rc.name', class: 'align-middle rider_city'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'consignee_name', name: 's.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 's.consignee_phone_number_1', class: 'align-middle consignee_phone'},
                    {data: 'consignee_address', name: 's.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 's.amount', class: 'align-middle amount'},
                    {data: 'mode', name: 'sm.id', class: 'align-middle mode'},
                    {data: 'service_type', name: 'bt.id', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'received_remarks', name: 'return_sheets.remarks', class: 'align-middle received_remarks', orderable: false, searchable: false},
                    {data: 'received_date', name: 'return_sheets.received_at', class: 'align-middle received_date'},
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.remarks')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.mode')){
                            $(mode_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
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
                    var data1 = $.map({!! $shipping_mode !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.mode;
                        return obj;
                    });

                    $("#mode_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.booking_type;
                        return obj;
                    });

                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            function print(id) {
                $.ajax({
                    url: '{!! route('admin.return.receive.rn.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
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

            $('body').on('click','.printreturnnote',function () {
                var returnnote = $(this).parents('tr').find('td:eq(3)').text();
                // var returnnote = rowdata.find('td:eq(3)').text();
                print(returnnote);
            });

            $( "#search_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    table.draw(true);
                    $('.datatable_excel_btn').removeClass('d-none');
                }
            });

        });
    </script>
@endsection