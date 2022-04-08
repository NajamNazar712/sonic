@extends('admin.layout.master')

@section('title', 'Packaging Material Requests')

@section('content')
    <h1 class="mb-1">
        Packaging Material Requests
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                        </div>
                        <input type="text" name="requested_from_date"
                               class="form-control bg-primary border-primary white rounded-right"
                               id="requested_from_date" placeholder="Requested Date From">
                    </div>
                </div>
                        <div class="col-md-4">
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                        </div>
                        <input type="text" name="requested_to_date"
                               class="form-control bg-primary border-primary white rounded-right"
                               id="requested_to_date" placeholder="Requested Date To">
                    </div>
                </div>
                        <div class="col-md-2">
                            <div class="form-group input-group" style="margin-top: -20px ">
                                <button type="button" id="search_filter_btn"
                                        class="float-right mb-1 mt-2 btn btn-outline-primary btn-min-width"><i
                                            class="la la-search" style="margin-right: 10px"></i> Search
                                </button>
                            </div>
                    </div>
                    </div>
                </div>
                
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Shipper Cell Number</th>
                        <th class="border-primary border-darken-1">Requested Date/Time</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Total Quantity</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Payment Mode</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Requested By</th>
                        <th class="border-primary border-darken-1">Request Date Aging</th>
                        <th class="border-primary border-darken-1">Confirmed Date Aging</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    {{--View Modal--}}
    <div class="modal fade text-left" id="ViewTypeSizeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewTypeSizeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">View Type and Size</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="EditTypeSizeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditTypeSizeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="update_type_size_button">Update</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddRemarks" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRemarks"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add/Update Remarks</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="#" id="update_remarks_form" class="form">
                        <input type="hidden" name="remarks_shipment_id" id="remarks_shipment_id">
                        <textarea type="text" name="packaging_remarks" class="form-group form-control" id="packaging_remarks" placeholder="Remarks" rows="7"></textarea>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="update_remarks_button">Update</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Request -->
    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Request Packaging Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.packaging.requests.submit')}}" id="material_request_form" method="post">
                        @csrf
                        <div class="row justify-content-md-center">
                            <div class="col-12">
                                <div class="form-body">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12 col-lg-6">
                                            <div class="form-group">
                                                <select name="shippers_select" id="shippers_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="Pickup address is required">
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                    @endforeach
                                                   
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6">
                                            <div class="form-group">
                                                <select name="address_select" id="address_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="Pickup address is required">
                                                    </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-md-12 col-lg-6">
                                            <div id="new_pickup_address" class="d-none">
                                                <div class="form-group">
                                                    <textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required">
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" name="new_pickup_phone_number" id="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                                </div>
                                                <div class="form-group">
                                                    <select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">
                                                        @foreach($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="packaging_type_ids" name="packaging_type_ids">
                                    <input type="hidden" id="packaging_size_ids" name="packaging_size_ids">
                                    <input type="hidden" id="packaging_quantities" name="packaging_quantities">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12 col-lg-6">
                                            <div class="form-group">
                                                <select name="mode_of_payment" class="select2" id="mode_of_payment" data-rule-required="true" data-msg-required="Payment mode is required">
                                                    <option></option>
                                                    @foreach($payment_mode as $mode)
                                                        <option value="{{$mode->id}}">{{$mode->mode}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row packaging_div">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                {{--<label for="sm_flyer">Packaging Material Type</label>--}}
                                                <select name="packaging_material_type" class="select2" id="packaging_material_type">
                                                    @foreach($packaging_material_types as $packaging_type)
                                                        <option value="{{ $packaging_type->id }}">{{ $packaging_type->type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <select name="packaging_material_size" class="select2" id="packaging_material_size"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-2">
                                            <div class="form-group">
                                                <input name="packaging_material_quantity" class="form-control quantity" id="packaging_material_quantity" placeholder="Quantity"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-2">
                                            <div class="form-group">
                                                <button class="btn btn-primary btn-block" type="button" id="add_packaging_material_btn"> Add</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <table class="table table-bordered packaging_type_datatable" id="packaging_type_datatable" style="z-index: 3;">
                                            <thead>
                                            <tr role="row" class="bg-primary white">

                                                <th class="border-primary border-darken-1">S. No.</th>
                                                <th class="border-primary border-darken-1">Packaging Type</th>
                                                <th class="border-primary border-darken-1">Size</th>
                                                <th class="border-primary border-darken-1">Quantity</th>
                                                <th class="border-primary border-darken-1"></th>

                                            </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-12 col-lg-6">
                                            <button id="RequestMaterialBtn" type="submit" class="btn btn-primary btn-block" disabled>Request Material</button>
                                        </div>
                                    </div>

                            </div>
                        </div>
                        </div>
                    </form>
                </div>
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" class="btn btn-info" data-dismiss="modal">Close</button>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>

    <!-- End Add Request -->



@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style type="text/css">

        .vertical-align-middle{
            vertical-align: middle !important;
        }
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    {{--    todo for datepicker--}}
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    {{--    todo for datepicker end--}}

    {{--    todo date filter field--}}
    <script>
        var booking_from_date = $('#requested_from_date').pickadate({
            firstDay: 1,
            clear: '',
            max: '{{ Carbon\Carbon::now() }}',
            // format: 'dd mmmm, yyyy',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function (context) {
                if (context.select) {
                    $('#requested_to_date').pickadate('picker').set('min', $('#requested_from_date').pickadate('picker').get('select'));
                }
            }
        });
        var booking_to_date = $('#requested_to_date').pickadate({
            firstDay: 1,
            clear: '',
            max: '{{ Carbon\Carbon::now() }}',
            // format: 'dd mmmm, yyyy',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 23:59:59',
            hiddenSuffix: '_formatted',
            onSet: function (context) {
                if (context.select) {
                    $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
                }
            }
        });

    </script>

    <script>
        $(document).ready(function () {
            $('#search_filter').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search',
                width:'100%',
                allowClear:false
            });

    </script>

    <script type="text/javascript">
        $(document).ready(function () {

            function print(id, booking_type_id) {
                if (booking_type_id != 4) {
                    var url = '{!! route('cod.shipment.book.print_air_waybill') !!}';
                }
                else {
                    var url = '{!! route('admin.shipment.book.print_air_waybill') !!}';
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        'ids[]': id,
                        'admin': true,
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

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.requests.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Shipper');
                            head.push('Shipper Cell Number');
                            head.push('Requested Date/Time');
                            head.push('City');
                            head.push('Total Quantity');
                            head.push('Amount');
                            head.push('Address');
                            head.push('Payment Mode');
                            head.push('Status');
                            head.push('Remarks');
                            head.push('Requested By');
                            head.push('Requested Date Aging');
                            head.push('Confirmed Date Aging');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.shipper_phone);
                                row.push(values.created_at);
                                row.push(values.city);
                                row.push(values.total_quantity);
                                row.push(values.amount);
                                row.push(values.address);
                                row.push(values.mode);
                                row.push(values.status);
                                row.push(values.remarks);
                                row.push(values.requested_by);
                                row.push(values.aging);
                                row.push(values.confirmed_aging);

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
                    {{--    {--}}
                    {{--        text: '<i class="la la-plus"></i> Add Request',--}}
                    {{--        className: 'btn btn-primary add_request',--}}
                    {{--        enabled: true,--}}
                    {{--        action: function (e, dt, node, config) {--}}
                    {{--            $('#AddRequestModal').modal('show');--}}
                    {{--        }--}}
                    {{--    },--}}
                    {{--    @if (session('role_id') == 1 || in_array(221, session('permissions')))--}}
                    {{--{--}}
                    {{--    text: '<i class="la la-align-justify"></i> View Inventory',--}}
                    {{--    className: 'btn btn-primary view_inventory',--}}
                    {{--    enabled: true,--}}
                    {{--    action: function (e, dt, node, config) {--}}
                    {{--        var url = '{{route('admin.packaging.inventory.index')}}';--}}
                    {{--        var win = window.open(url, '_blank');--}}
                    {{--        win.focus();--}}
                    {{--    }--}}
                    {{--},--}}
                    {{--    @endif--}}
                    {
                        extend: 'excel',
                        className: 'btn btn-primary',
                        title: 'Packaging Material Requests',
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
                ajax:{
                    url: '{{  route('admin.packaging.requests.list') }}',
                    data: function (d) {
                        d.requested_from_date = $('#requested_from_date').val();
                        d.requested_to_date = $('#requested_to_date').val();
                    }
                },
                rowId: 'request_id',
                order: [[4, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'packaging_material_requests.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'shipper_phone',name:'u.phone',class: 'align-middle shipper_phone'},
                    {data: 'created_at', name: 'packaging_material_requests.created_at', class: 'align-middle created_at'},
                    {data: 'city', name: 'ct.name', class: 'align-middle city'},
                    {data: 'total_quantity_button', class: 'align-middle total_quantity_button',orderable: false, searchable: false},
                    {data: 'amount', name: 'packaging_material_requests.amount', class: 'align-middle amount'},
                    {data: 'address', name: 'packaging_material_requests.address', class: 'align-middle address'},
                    {data: 'mode', name: 'ppm.id', class: 'align-middle mode'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'remarks', name: 'sj.remarks', class: 'align-middle remarks_view'},
                    {data: 'requested_by', name:'rb.name', class: 'align-middle requested_by'},
                    {data: 'aging', class: 'align-middle aging', orderable: false, searchable: false},
                    {data: 'confirmed_aging', class: 'align-middle confirmed_aging', orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'align-middle action',orderable: false, searchable: false}
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
                    var payment_mode_select = '<select name="payment_mode_select" id="payment_mode_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.total_quantity_button') || $(header).is('.aging') || $(header).is('.confirmed_aging')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.mode')){
                            $(payment_mode_select).appendTo($(search))
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
                    var data2 = $.map({!! $packaging_request_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data2 = $.map({!! $packaging_request_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data: data2,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $payment_mode !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $payment_mode !!}, function (obj) {
                        obj.text = obj.mode;

                        return obj;
                    });

                    $("#payment_mode_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw(true);
            });

            $('body').on('click','tr td .edit',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));

                $.ajax({
                    url: '{!! route('admin.packaging.requests.quantity_details') !!}',
                    method: 'POST',
                    data: {
                        'id': request_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        var html = '';
                        html += '<form id="update_type_size_form">';
                        html += '@csrf @method("put")';
                        html += '<input type="hidden" value="'+data.request_id+'" name="request_id">'
                        html += '<table class="table table-sm datatable text-center" id="quantity_table">';
                        html += '<thead><tr><th>S No.</th><th><strong>Type</strong></th><th><strong>Size</strong></th><th><strong>Quantity</strong></th><th></th></tr></thead>';
                        html += '<tbody>';
                        $.each(data.types, function(index, value) {

                            var ind = index + 1;
                            html += '<tr class=""><td class="vertical-align-middle">' + ind + '</td>';
                            html += '<td class="vertical-align-middle">' + value.type + '</td>';
                            html += '<td class="vertical-align-middle">' + value.size + '</td>';
                            html += '<td><input type="text" class="type_size_quantity form-control" name="quantity[' + value.index + ']" value="' + value.quantity + '"></td>';
                            if (data.types.length > 1)
                            {
                                html += '<td><button type="button" class="btn btn-danger quantity_delete_btn">Remove</button> </td></tr>'
                            }
                            else{
                                html += '<td></td>';
                            }
                        });
                        html += '</tbody></table></form>';


                        $('#EditTypeSizeModal .modal-body').html(html);

                        $('.type_size_quantity').inputmask({
                            'alias': 'integer',
                            'allowMinus': false,
                            'allowPlus': false,
                            'rightAlign': false,
                            'min': 1,
                            'max': 10000
                        });

                        $('#EditTypeSizeModal').modal('show');
                    }
                    // console.log(data.success);
                });
            });

            $('body').on('click','.quantity_delete_btn',function () {
                if($('#quantity_table tr').length > 2)
                {
                    $(this).parents('tr').remove();
                }
                if($('.quantity_delete_btn').length == 1)
                {
                    $('.quantity_delete_btn').remove();
                }
            })

            $('body').on('click','#datatable .quantity',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));

                $.ajax({
                    url: '{!! route('admin.packaging.requests.quantity_details') !!}',
                    method: 'POST',
                    data: {
                        'id': request_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        var html = '';
                        html += '<table class="table table-sm datatable text-center">';
                        html += '<thead><tr><th>S No.</th><th><strong>Type</strong></th><th><strong>Size</strong></th><th><strong>Quantity</strong></th></tr></thead>';
                        html += '<tbody>';
                        $.each(data.types, function(index, value) {
                            var ind = index+1;
                            html += '<tr class=""><td>' + ind + '</td>';
                            html += '<td>' + value.type + '</td>';
                            html += '<td>' + value.size + '</td>';
                            html += '<td>' + value.quantity + '</td></tr>';
                        });
                        html += '</tbody></table>';

                        $('#ViewTypeSizeModal .modal-body').html(html);
                        $('#ViewTypeSizeModal').modal('show');
                    }
                    // console.log(data.success);
                });
            });
            //grn
            $('body').on('click','.grn',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;

                    $.ajax({
                        url: '{!! route('admin.packaging.requests.good_receiving_note') !!}',
                        method: 'POST',
                        data: {
                        'id': request_id,
                        'shipment_id': shipment_id_data,
                        '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
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
                        // console.log(data.success);
                    });
            });
            //confirm
            $('body').on('click','.confirm',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                var booking_type_id = table.row($(this).parents('tr')).data().booking_type_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Confirm Packaging Material!',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.confirm') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {

                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                if(shipment_id_data != null && booking_type_id != null){
                                    print(shipment_id_data, booking_type_id);
                                }
                                table.draw();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        });
                    }
                });

            });
            //cancel
            $('body').on('click','.cancel',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Cancel Packaging Material!',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.cancel') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {

                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        });
                    }
                });

            });
            //dispatch
            $('body').on('click','.dispatch',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Dispatch Packaging Material!',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.dispatch') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {

                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        });
                    }
                });

            });
            //complete
            $('body').on('click','.completed',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Complete Packaging Material!',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.completed') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                });

            });
            //replenish
            $('body').on('click','.replenished',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Replenish Packaging Material!',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.replenish') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                });

            });
            //remarks
            $('body').on('click','.remarks',function(){
                var request_id = table.row($(this).parents('tr')).data().shipment_id;
                var remarks = table.row($(this).parents('tr')).data().remarks;
                $('#remarks_shipment_id').val(request_id);
                $('#packaging_remarks').val(remarks);

                $('#AddRemarks').modal('show');

            });
            $('#update_remarks_form').on('submit', function(e){
                e.preventDefault();
            });

            $('#update_type_size_form').on('submit', function(e){
                e.preventDefault();
            });

            $('#update_type_size_button').on('click', function(){
                $('.type_size_quantity').each(function (i,v) {
                    if($(v).val() == null || $(v).val() == '' || !$.isNumeric($(v).val()))
                    {
                        var error = 'Please enter valid quantity';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    else{
                        $('#update_type_size_button').attr('disabled', true);
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.update') !!}',
                            method: 'POST',
                            data: $("#update_type_size_form").serialize(),
                        }).done(function (data) {
                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                                $('#EditTypeSizeModal').modal('hide');
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#update_type_size_button').attr('disabled', false);
                        });
                    }
                });
            });
            var already_selected_size = [];
            $('#AssignAgentModal').on('hide.bs.modal', function (e) {
                $('#remarks_shipment_id').val('');
                $('#packaging_remarks').val('');
            });

            $("#AddRequestModal").on('shown.bs.modal', function(){
                initDatatable();
            });
            $("#AddRequestModal").on('hidden.bs.modal', function(){
                $('#shippers_select').val('').trigger('change');
                $('#address_select').empty().trigger('change');
                destroyDatatable();
                $('#RequestMaterialBtn').attr('disabled', false);
            });

            var ptable;
            function destroyDatatable() {
                ptable.clear();
                ptable.destroy();
            }
            function initDatatable() {
                ptable = $('#packaging_type_datatable').DataTable({
                    dom: 'ltipr',
                    paging:false,
                    ordering:[0, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                        {name: 'packaging_type', class: 'align-middle packaging_type', orderable: false},
                        {name: 'size', class: 'align-middle size', orderable: false},
                        {name: 'quantity', class: 'align-middle quantity', orderable: false},
                        {name: 'action', class: 'align-middle action', orderable: false}
                    ],
                    rowCallback: function(row, data, index) {

                    },
                    initComplete: function() {

                    }
                });
            }
            var packaging_types_array = [];
            var packaging_size_array = [];
            var packaging_quantity_array = [];
            var packaging_index_array = [];
            var rid = 100;
            $('#add_packaging_material_btn').on('click', function () {
                var type = $('#packaging_material_type').val();
                var type_name = $('#packaging_material_type option:selected').text();
                var size = $('#packaging_material_size').val();
                var size_name = $('#packaging_material_size option:selected').text();
                var quantity = $('#packaging_material_quantity').val();
                var flag = false;
                if(type == '' || type == null || size == '' || size == null || quantity == '' || quantity == null){
                    if(type == '' || type == null){
                        var error = "<p id='type_error' class='danger'>Type is required</p>";
                        if($('#packaging_material_type').parent('div').find('p#type_error').length == 0){
                            $('#packaging_material_type').parent('div').append(error);
                        }
                    }
                    else{
                        $('#type_error').remove();
                    }
                    if(size == '' || size == null){
                        var error = "<p id='size_error' class='danger'>Size is required</p>";
                        if($('#packaging_material_size').parent('div').find('p#size_error').length == 0){
                            $('#packaging_material_size').parent('div').append(error);
                        }
                    }
                    else{
                        $('#size_error').remove();
                    }
                    if(quantity == '' || quantity == null){
                        var error = "<p id='quantity_error' class='danger'>Quantity is required</p>";
                        if($('#packaging_material_quantity').parent('div').find('p#quantity_error').length == 0){
                            $('#packaging_material_quantity').parent('div').append(error);
                        }
                    }
                    else{
                        $('#quantity_error').remove();
                    }
                    flag = true;
                }
                else{
                    flag = false;
                    $('#type_error').remove();
                    $('#size_error').remove();
                    $('#quantity_error').remove();
                }

                if(flag == false){
                    var rowNo = ptable.rows().count();

                    var remove = '<a href="javascript:void(0);" class="btn btn-sm btn-danger remove"><i class="la la-close"></i></a>';
                    ptable.row.add([rowNo+1,type_name,size_name,quantity, remove]).node().id = rid;
                    ptable.draw(false);
                    already_selected_size.push(parseInt(type+size));
                    packaging_index_array.push(rid);
                    rid++;
                    packaging_types_array.push(type);
                    packaging_size_array.push(size);
                    packaging_quantity_array.push(quantity);
                    $('#RequestMaterialBtn').attr('disabled', false);
                    $('#packaging_material_type').val('').trigger('change');
                    $('#packaging_material_size').empty();
                    $('#packaging_material_quantity').val('');
                }

            });
            $('#packaging_type_datatable').on('click', 'a.remove', function(){
               var rowId = parseInt($(this).parents('tr').attr('id'));

                var index = $.inArray(rowId, packaging_index_array);

                if (index !== -1) {
                    already_selected_size.splice(index, 1);
                    packaging_index_array.splice(index, 1);
                    packaging_types_array.splice(index, 1);
                    packaging_size_array.splice(index, 1);
                    packaging_quantity_array.splice(index, 1);
                }
                ptable.row( $(this).parents('tr') ).remove().draw();
                if(ptable.rows().count() == 0){
                    $('#RequestMaterialBtn').attr('disabled', true);
                }
            });

            $('#material_request_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    if(ptable.rows().count() > 0){
                        $('#packaging_type_ids').val(packaging_types_array);
                        $('#packaging_size_ids').val(packaging_size_array);
                        $('#packaging_quantities').val(packaging_quantity_array);
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Your request is being submitted!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }else{

                        toastr.error("Please Select atleast one packaging type!", 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        return false;
                    }

                }
            });
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': 10000
            });
            $('input.quantity').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': 100000
            });
            $('#mode_of_payment').select2({
                width: '100%',
                placeholder: 'Payment Mode',
                dropdownParent: $("#AddRequestModal")
            });
            $('#packaging_material_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Packaging Material Type',
                dropdownParent: $("#AddRequestModal .packaging_div")
            }).bind('select2:select', function () {
                var type_id = $(this).val();
                if(type_id){
                    $.ajax({
                        url: '{!! route('admin.packaging.requests.sizes') !!}',
                        method: 'POST',
                        data: {
                            'id': type_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            $('#packaging_material_size').empty();

                            $.each(data.sizes,function (key,value) {
                                var type_size = parseInt(type_id+value.id);

                                var index = $.inArray(type_size, already_selected_size);

                                if(index === -1){
                                    var sizeOptions = new Option(value.size, value.id, false, false);
                                    $('#packaging_material_size').append(sizeOptions).trigger('change');
                                    $('#packaging_material_size').val('').trigger('change');
                                }

                            });

                        }else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });

                }
            });

            $('#packaging_material_size').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Packaging Material Size',
                dropdownParent: $("#AddRequestModal .packaging_div")
            });


            $('#shippers_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shipper*',
                dropdownParent: $("#AddRequestModal")
            }).bind('select2:select', function() {
                var shipper_id = $(this).val();
                $.ajax({
                        url: '{!! route('admin.packaging.requests.pickup_address') !!}',
                        data: {
                            'shipper_id': shipper_id
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                                $('#address_select').empty().trigger('change');
                                var newOption = new Option('New', 0, false, false);
                                $('#address_select').append(newOption).trigger('change');
                            $.each(data.pickup_data,function (key,value) {
                                var select_address = new Option(value.pickup_address, value.pickup_address_id, false, false);
                                $('#address_select').append(select_address).trigger('change');
                                $('#address_select').val('').trigger('change');

                            });
                        }
                    });

                
            });

            $('#address_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Send To*',
                dropdownParent: $("#AddRequestModal")
            }).bind('select2:select', function() {
                $(this).valid();

                if (this.value == 0) {
                    $('#new_pickup_address').removeClass('d-none');
                }
                else {
                    $('#new_pickup_address').addClass('d-none');
                }
            });
            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*',
                dropdownParent: $("#AddRequestModal")
            }).bind('change', function() {
                $(this).valid();
            });

        });

    </script>
@endsection