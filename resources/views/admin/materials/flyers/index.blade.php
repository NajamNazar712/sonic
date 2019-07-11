@extends('admin.layout.master')

@section('title', 'Packaging Material Stock Requests')

@section('content')
    <h1 class="mb-1">
        Packaging Material Stock Requests
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Request Date/Time</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">Requested By</th>
                        <th class="border-primary border-darken-1">Warehouse</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{--Add Stock Modal--}}
    <div class="modal fade text-left" id="AddStockModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddStockModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Stock</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add_stock_form" action="{{route('admin.packaging.add.submit')}}" method="post">
                    @method('POST')
                    @csrf

                <div class="modal-body">

                        <div class="row justify-content-md-center">
                            <div class="col-12">
                                <div class="form-body">

                                    <input type="hidden" id="packaging_type_ids" name="packaging_type_ids">
                                    <input type="hidden" id="packaging_size_ids" name="packaging_size_ids">
                                    <input type="hidden" id="packaging_quantities" name="packaging_quantities">
                                    <div class="row justify-content-center">
                                        <div class="col-4 form-group">
                                            <input type="text" name="invoice_number" id="add_stock_invoice" class="form-control invoice" placeholder="Invoice Number *" data-rule-required="true" data-msg-required="This field is required">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                {{--<label for="sm_flyer">Packaging Material Type</label>--}}
                                                <select name="packaging_material_type" class="select2" id="packaging_material_type">
                                                    @foreach($packaging_types as $packaging_type)
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
                                                <input name="packaging_material_quantity" class="form-control numeric quantity" id="packaging_material_quantity" placeholder="Quantity"/>
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

                </div>
            </form>


            </div>
        </div>
    </div>
    {{--Add Stock Modal--}}

    <div class="modal fade text-left" id="RequestStockModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RequestStockModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Request Stock</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="request_stock_form" action="{{route('admin.packaging.requests.submit')}}" method="post">
                    @method('POST')
                    @csrf

                    <div class="modal-body">

                        <div class="row justify-content-md-center">
                            <div class="col-12">
                                <div class="form-body">

                                    <input type="hidden" id="request_type_ids" name="request_type_ids">
                                    <input type="hidden" id="request_size_ids" name="request_size_ids">
                                    <input type="hidden" id="request_quantities" name="request_quantities">
                                    <div class="row justify-content-center">
                                        <div class="col-4 form-group">
                                            <select name="request_from" class="select2" id="request_from">
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->master_type == 1? 'Master Warehouse':$warehouse->city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4 form-group">
                                            <select name="request_for" class="select2" id="request_for" data-rule-required="true" data-msg-required="This field is required">
                                                @foreach($warehouses as $warehouse)
                                                    @if($warehouse->master_type != 1)
                                                    <option value="{{ $warehouse->id }}">{{$warehouse->city->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                <div id="request_div" class="d-none">


                                    <div class="row">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                {{--<label for="sm_flyer">Packaging Material Type</label>--}}
                                                <select name="request_material_type" class="select2" id="request_material_type">
                                                    @foreach($packaging_types as $packaging_type)
                                                        <option value="{{ $packaging_type->id }}">{{ $packaging_type->type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <select name="request_material_size" class="select2" id="request_material_size"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-2">
                                            <div class="form-group">
                                                <input name="request_material_quantity" class="form-control numeric quantity" id="request_material_quantity" placeholder="Quantity"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-2">
                                            <div class="form-group">
                                                <button class="btn btn-primary btn-block" type="button" id="request_packaging_material_btn"> Add</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <table class="table table-bordered request_type_datatable" id="request_type_datatable" style="z-index: 3;">
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
                                            <button id="RequestPackagingMaterialBtn" type="submit" class="btn btn-primary btn-block" disabled> Submit</button>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>


            </div>
        </div>
    </div>

    {{--Send Stock Modal--}}
    <div class="modal fade text-left" id="SendStockModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SendStockModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Send Stock</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="send_stock_form" action="{{route('admin.packaging.stock_send.submit')}}" method="post">
                    @method('POST')
                    @csrf

                    <div class="modal-body">

                        <div class="row justify-content-md-center">
                            <div class="col-12">
                                <div class="form-body">

                                    <input type="hidden" id="send_type_ids" name="send_type_ids">
                                    <input type="hidden" id="send_size_ids" name="send_size_ids">
                                    <input type="hidden" id="send_quantities" name="send_quantities">
                                    <div class="row justify-content-center">
                                        <div class="col-4 form-group">
                                            <select name="send_from" class="select2" id="send_from">
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->master_type == 1? 'Master Warehouse':$warehouse->city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4 form-group">
                                            <select name="send_for" class="select2" id="send_for" data-rule-required="true" data-msg-required="This field is required">
                                                @foreach($warehouses as $warehouse)
                                                    @if($warehouse->master_type != 1)
                                                        <option value="{{ $warehouse->id }}">{{$warehouse->city->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div id="send_div" class="d-none">


                                        <div class="row">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    {{--<label for="sm_flyer">Packaging Material Type</label>--}}
                                                    <select name="send_material_type" class="select2" id="send_material_type">
                                                        @foreach($packaging_types as $packaging_type)
                                                            <option value="{{ $packaging_type->id }}">{{ $packaging_type->type }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <select name="send_material_size" class="select2" id="send_material_size"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-2">
                                                <div class="form-group">
                                                    <input name="send_material_quantity" class="form-control numeric quantity" id="send_material_quantity" placeholder="Quantity"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-2">
                                                <div class="form-group">
                                                    <button class="btn btn-primary btn-block" type="button" id="send_packaging_material_btn"> Add</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <table class="table table-bordered send_type_datatable" id="send_type_datatable" style="z-index: 3;">
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
                                                <button id="SendPackagingMaterialBtn" type="submit" class="btn btn-primary btn-block" disabled> Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>


            </div>
        </div>
    </div>
    {{--Send Stock Modal--}}

    <div class="modal fade text-left" id="DetailsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DetailsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Details</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
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
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var already_selected_size = [];
            var ptable;
            $('#packaging_material_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Packaging Material Type',
                dropdownParent: $("#AddStockModal")
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
                                    var newOption = new Option(value.size, value.id, false, false);
                                    $('#packaging_material_size').append(newOption).trigger('change');
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

            $('#request_material_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Packaging Material Type',
                dropdownParent: $("#RequestStockModal")
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
                            $('#request_material_size').empty();

                            $.each(data.sizes,function (key,value) {
                                var type_size = parseInt(type_id+value.id);

                                var index = $.inArray(type_size, already_selected_size);

                                if(index === -1){
                                    var newOption = new Option(value.size, value.id, false, false);
                                    $('#request_material_size').append(newOption).trigger('change');
                                    $('#request_material_size').val('').trigger('change');
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

            $('#send_material_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Packaging Material Type',
                dropdownParent: $("#SendStockModal")
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
                            $('#send_material_size').empty();

                            $.each(data.sizes,function (key,value) {
                                var type_size = parseInt(type_id+value.id);

                                var index = $.inArray(type_size, already_selected_size);

                                if(index === -1){
                                    var newOption = new Option(value.size, value.id, false, false);
                                    $('#send_material_size').append(newOption).trigger('change');
                                    $('#send_material_size').val('').trigger('change');
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
                dropdownParent: $("#AddStockModal")
            });

            $('#request_material_size').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Packaging Material Size',
                dropdownParent: $("#RequestStockModal")
            });

            $('#send_material_size').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Packaging Material Size',
                dropdownParent: $("#SendStockModal")
            });

            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 1000000
            });

            $('#AddStockModal').on('change','#add_stock_form input.invoice',function() {
                $(this).val($(this).val().trim());
            });



            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Requested Date/Time');
                            head.push('Created By');
                            head.push('Requested By');
                            head.push('Send By');
                            head.push('Tracking Number');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                row.push(values.requested_by);
                                row.push(values.send_by);
                                row.push(values.tracking_number);
                                row.push(values.status);

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
                @if (session('role_id') == 1 || count(array_intersect([77, 78], session('permissions'))) !== 0)

                    buttons: [
                        @if (session('role_id') == 1 || in_array(221, session('permissions')))
                    {
                        text: '<i class="la la-align-justify"></i> View Inventory',
                        className: 'btn btn-primary view_inventory',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            var url = '{{route('admin.packaging.inventory.index')}}';
                            var win = window.open(url, '_blank');
                            win.focus();
                        }
                    },
                        @endif
                    @if (session('role_id') == 1 || in_array(77, session('permissions')))
                        {
                            text: '<i class="la la-plus"></i> Add Stock',
                            className: 'btn btn-primary add_stock',
                            enabled: true,
                            action: function (e, dt, node, config) {
                                $('#AddStockModal').modal('show');

                            }
                        },
                    @endif

                    @if (session('role_id') == 1 || in_array(78, session('permissions')))
                        {
                            text: '<i class="la la-send"></i> Send Stock',
                            className: 'btn btn-primary send_stock',
                            enabled:true,
                            action: function(e, dt, node, config){
                                $('#SendStockModal').modal('show');

                            }
                        },
                    @endif
                    @if (session('role_id') == 1 || in_array(222, session('permissions')))
                        {
                            text: '<i class="la la-send"></i> Request Stock',
                            className: 'btn btn-primary request_stock',
                            enabled:true,
                            action: function(e, dt, node, config){
                                $('#RequestStockModal').modal('show');

                            }
                        },
                    @endif
                        {
                            extend: 'excel',
                            title: 'Packaging Material Stock',
                            className:'btn btn-primary',
                            text: '<i class="la la-file-excel-o"></i> Excel',
                        },'reset'],

                @else
                    buttons:[{
                    extend: 'excel',
                    title: 'Packaging Material Requests',
                    className:'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                @endif
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.packaging.list') }}',
                rowId: 'stock_request_id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'created_at', name: 'warehouse_stock_requests.created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'cb.name', class: 'align-middle created_by'},
                    {data: 'requested_by', name: 'rb.name', class: 'align-middle requested_by'},
                    {data: 'send_by', name: 'sb.name', class: 'align-middle send_by'},
                    {data: 'tracking_number_link', name: 'warehouse_stock_requests.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'align-middle action'}

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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
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
                    var data = $.map({!! $packaging_material_status !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });




            $('#request_for').prepend('<option value="" selected></option>').select2({
                placeholder: "Requesting Warehouse",
                width:'100%',
            }).bind('select2:select', function () {
                if($('#request_from').val() == $('#request_for').val()){
                    toastr.error('Request Warehouse can not be the same', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    $(this).val(null).trigger('change');
                }
            });

            $('#send_for').prepend('<option value="" selected></option>').select2({
                placeholder: "Receiving Warehouse",
                width:'100%',
            }).bind('select2:select', function () {
                if($('#send_from').val() == $('#send_for').val()){
                    toastr.error('Receiving Warehouse can not be the same as sender', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    $(this).val(null).trigger('change');
                }
            });



            $("#AddStockModal").on('shown.bs.modal', function(){
                initDatatable('packaging_type_datatable');
            });
            $("#AddStockModal").on('hidden.bs.modal', function(){
                destroyDatatable();
                $('#RequestMaterialBtn').attr('disabled', false);
            });

            $("#RequestStockModal").on('shown.bs.modal', function(){
                initDatatable('request_type_datatable');
            });
            $("#RequestStockModal").on('hidden.bs.modal', function(){
                destroyDatatable();
                $('#RequestMaterialBtn').attr('disabled', false);
            });

            $("#SendStockModal").on('shown.bs.modal', function(){
                initDatatable('send_type_datatable');
            });
            $("#SendStockModal").on('hidden.bs.modal', function(){
                destroyDatatable();
                $('#RequestMaterialBtn').attr('disabled', false);
            });
            function destroyDatatable() {
                ptable.clear();
                ptable.destroy();
            }

            function initDatatable(name) {
                ptable = $('#'+name).DataTable({
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
                if(type == ''){
                    flag = true;
                    var error = "<p id='type_error' class='danger'>Type is required</p>";
                    if($('#packaging_material_type').parent('div').find('p#type_error').length == 0){
                        $('#packaging_material_type').parent('div').append(error);
                    }
                }else{
                    flag = false;
                    $('#type_error').remove();
                }

                if(size == ''){
                    flag = true;
                    var error = "<p id='size_error' class='danger'>Size is required</p>";
                    if($('#packaging_material_size').parent('div').find('p#size_error').length == 0){
                        $('#packaging_material_size').parent('div').append(error);
                    }
                }else{
                    $('#size_error').remove();
                }

                if(quantity == ''){
                    flag = true;
                    var error = "<p id='quantity_error' class='danger'>Quantity is required</p>";
                    if($('#packaging_material_quantity').parent('div').find('p#quantity_error').length == 0){
                        $('#packaging_material_quantity').parent('div').append(error);
                    }
                }else{
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

            $('#add_stock_form').validate({
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
                            text: 'Stock is being added!',
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

            var request_types_array = [];
            var request_size_array = [];
            var request_quantity_array = [];
            var request_index_array = [];
            var rid = 100;
            $('#request_packaging_material_btn').on('click', function () {
                var type = $('#request_material_type').val();
                var type_name = $('#request_material_type option:selected').text();
                var size = $('#request_material_size').val();
                var size_name = $('#request_material_size option:selected').text();
                var quantity = $('#request_material_quantity').val();
                var warehouse_id = $('#request_from').val();
                var flag = false;

                if(warehouse_id == ''){
                    flag = true;
                    var error = "<p id='request_hub_type_error' class='danger'>Requested Warehouse</p>";
                    if($('#request_from').parent('div').find('p#request_hub_type_error').length == 0){
                        $('#request_material_type').parent('div').append(error);
                    }
                }else{
                    flag = false;
                    $('#request_hub_type_error').remove();
                }


                if(type == ''){
                    flag = true;
                    var error = "<p id='request_type_error' class='danger'>Type is required</p>";
                    if($('#request_material_type').parent('div').find('p#request_type_error').length == 0){
                        $('#request_material_type').parent('div').append(error);
                    }
                }else{
                    flag = false;
                    $('#request_type_error').remove();
                }

                if(size == ''){
                    flag = true;
                    var error = "<p id='request_size_error' class='danger'>Size is required</p>";
                    if($('#request_material_size').parent('div').find('p#request_size_error').length == 0){
                        $('#request_material_size').parent('div').append(error);
                    }
                }else{
                    $('#request_size_error').remove();
                }

                if(quantity == ''){
                    flag = true;
                    var error = "<p id='request_quantity_error' class='danger'>Quantity is required</p>";
                    if($('#request_material_quantity').parent('div').find('p#request_quantity_error').length == 0){
                        $('#request_material_quantity').parent('div').append(error);
                    }
                }else{
                    $('#request_quantity_error').remove();
                }


                if(flag == false){
                    $.ajax({
                        url: '{!! route('admin.packaging.requests.check_quantity') !!}',
                        method: 'POST',
                        data: {
                            'warehouse_id':warehouse_id,
                            'type_id': type,
                            'size_id': size,
                            'quantity': quantity,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if(data.status == 0){
                            var rowNo = ptable.rows().count();

                            var remove = '<a href="javascript:void(0);" class="btn btn-sm btn-danger remove"><i class="la la-close"></i></a>';
                            ptable.row.add([rowNo+1,type_name,size_name,quantity, remove]).node().id = rid;
                            ptable.draw(false);
                            already_selected_size.push(parseInt(type+size));
                            request_index_array.push(rid);
                            rid++;
                            request_types_array.push(type);
                            request_size_array.push(size);
                            request_quantity_array.push(quantity);
                            $('#RequestPackagingMaterialBtn').attr('disabled', false);
                            $('#request_material_type').val('').trigger('change');
                            $('#request_material_size').empty();
                            $('#request_material_quantity').val('');
                        }else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }

            });

            $('#request_type_datatable').on('click', 'a.remove', function(){
                var rowId = parseInt($(this).parents('tr').attr('id'));

                var index = $.inArray(rowId, request_index_array);

                if (index !== -1) {
                    already_selected_size.splice(index, 1);
                    request_index_array.splice(index, 1);
                    request_types_array.splice(index, 1);
                    request_size_array.splice(index, 1);
                    request_quantity_array.splice(index, 1);
                }
                ptable.row( $(this).parents('tr') ).remove().draw();
                if(ptable.rows().count() == 0){
                    $('#RequestPackagingMaterialBtn').attr('disabled', true);
                }
            });

            $('#request_stock_form').validate({
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
                        $('#request_type_ids').val(request_types_array);
                        $('#request_size_ids').val(request_size_array);
                        $('#request_quantities').val(request_quantity_array);
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Stock is being requested!',
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


            function resetRequestArrays() {
                already_selected_size = [];
                request_index_array = [];
                request_types_array = [];
                request_size_array = [];
                request_quantity_array = [];
                $('#RequestMaterialBtn').attr('disabled', true);
            }

            $('#request_from').prepend('<option value="" selected></option>').select2({
                placeholder: "Request From",
                width:'100%',
            }).bind('select2:select', function () {
                if($('#request_div').is(':hidden')){
                    $('#request_div').removeClass('d-none');
                }

                destroyDatatable();
                initDatatable('request_type_datatable');
                resetRequestArrays();
                if($('#request_from').val() == $('#request_for').val()){
                    toastr.error('Request Warehouse can not be the same', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    $(this).val(null).trigger('change');
                }

            });

            $('body #datatable').on('click', 'button.cancel', function () {
               var stock_request_id = $(this).parents('tr').attr('id');
               if(stock_request_id){
                   $.ajax({
                       url: '{!! route('admin.packaging.stock_request.cancel') !!}',
                       method: 'POST',
                       data: {
                           'stock_request_id':stock_request_id,
                           '_token': '{{ csrf_token() }}'
                       }
                   }).done(function (data) {
                        if(data.status){
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                   });
               }
            });

            $('body #datatable').on('click', 'button.confirm', function () {
                var stock_request_id = $(this).parents('tr').attr('id');
                if(stock_request_id){
                    $.ajax({
                        url: '{!! route('admin.packaging.stock_request.confirm') !!}',
                        method: 'POST',
                        data: {
                            'stock_request_id':stock_request_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            table.draw();
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });

            $('body').on('click', 'button.details', function () {
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $.ajax({
                        url: '{!! route('admin.packaging.stock_request.details') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            var html = '';

                            html += '<table class="table table-sm datatable text-center">';
                            html += '<thead>';
                            html += '<tr role="row">';
                            html += '<th><strong>Type</strong></th>';
                            html += '<th><strong>Size</strong></th>';
                            html += '<th><strong>Quantity</strong></th>';

                            html += '</tr>';
                            html += '</thead>';
                            html += '<tbody>';

                            $.each(data.details, function(index, value){
                                html += '<tr>';
                                html += '<td>' + value.packaging_type.type + '</td>';
                                html += '<td>' + value.packaging_size.size + '</td>';
                                html += '<td>' + value.quantity + '</td>';
                                html += '</tr>';
                            });


                            html += '</tbody>';
                            html += '</table>';

                            $('#DetailsModal').modal('show');
                            $('#DetailsModal .modal-body').html(html);
                        }else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });

                }
            });

            $('body #datatable').on('click', 'button.dispatch', function () {
                var stock_request_id = $(this).parents('tr').attr('id');
                if(stock_request_id){
                    $.ajax({
                        url: '{!! route('admin.packaging.stock_request.dispatch') !!}',
                        method: 'POST',
                        data: {
                            'stock_request_id':stock_request_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            table.draw();
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });


            $('#send_from').prepend('<option value="" selected></option>').select2({
                placeholder: "Send From",
                width:'100%',
            }).bind('select2:select', function () {
                if($('#send_div').is(':hidden')){
                    $('#send_div').removeClass('d-none');
                }

                destroyDatatable();
                initDatatable('send_type_datatable');
                resetRequestArrays();
                if($('#send_from').val() == $('#send_for').val()){
                    toastr.error('Send Warehouse can not be the same as receiver', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    $(this).val(null).trigger('change');
                }

            });

            var send_types_array = [];
            var send_size_array = [];
            var send_quantity_array = [];
            var send_index_array = [];
            var rid = 100;
            $('#send_packaging_material_btn').on('click', function () {
                var type = $('#send_material_type').val();
                var type_name = $('#send_material_type option:selected').text();
                var size = $('#send_material_size').val();
                var size_name = $('#send_material_size option:selected').text();
                var quantity = $('#send_material_quantity').val();
                var warehouse_id = $('#send_from').val();
                var flag = false;

                if(warehouse_id == ''){
                    flag = true;
                    var error = "<p id='send_hub_type_error' class='danger'>Select Sender Warehouse</p>";
                    if($('#send_from').parent('div').find('p#send_hub_type_error').length == 0){
                        $('#send_material_type').parent('div').append(error);
                    }
                }else{
                    flag = false;
                    $('#send_hub_type_error').remove();
                }


                if(type == ''){
                    flag = true;
                    var error = "<p id='send_type_error' class='danger'>Type is required</p>";
                    if($('#send_material_type').parent('div').find('p#send_type_error').length == 0){
                        $('#send_material_type').parent('div').append(error);
                    }
                }else{
                    flag = false;
                    $('#send_type_error').remove();
                }

                if(size == ''){
                    flag = true;
                    var error = "<p id='send_size_error' class='danger'>Size is required</p>";
                    if($('#send_material_size').parent('div').find('p#send_size_error').length == 0){
                        $('#send_material_size').parent('div').append(error);
                    }
                }else{
                    $('#send_size_error').remove();
                }

                if(quantity == ''){
                    flag = true;
                    var error = "<p id='send_quantity_error' class='danger'>Quantity is required</p>";
                    if($('#send_material_quantity').parent('div').find('p#send_quantity_error').length == 0){
                        $('#send_material_quantity').parent('div').append(error);
                    }
                }else{
                    $('#send_quantity_error').remove();
                }


                if(flag == false){
                    $.ajax({
                        url: '{!! route('admin.packaging.requests.check_quantity') !!}',
                        method: 'POST',
                        data: {
                            'warehouse_id':warehouse_id,
                            'type_id': type,
                            'size_id': size,
                            'quantity': quantity,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if(data.status == 0){
                            var rowNo = ptable.rows().count();

                            var remove = '<a href="javascript:void(0);" class="btn btn-sm btn-danger remove"><i class="la la-close"></i></a>';
                            ptable.row.add([rowNo+1,type_name,size_name,quantity, remove]).node().id = rid;
                            ptable.draw(false);
                            already_selected_size.push(parseInt(type+size));
                            send_index_array.push(rid);
                            rid++;
                            send_types_array.push(type);
                            send_size_array.push(size);
                            send_quantity_array.push(quantity);
                            $('#SendPackagingMaterialBtn').attr('disabled', false);
                            $('#send_material_type').val('').trigger('change');
                            $('#send_material_size').empty();
                            $('#send_material_quantity').val('');
                        }else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }

            });

            $('#send_stock_form').validate({
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
                        $('#send_type_ids').val(send_types_array);
                        $('#send_size_ids').val(send_size_array);
                        $('#send_quantities').val(send_quantity_array);
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Stock is being send!',
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


        });

    </script>
@endsection