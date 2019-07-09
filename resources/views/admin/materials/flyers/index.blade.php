@extends('admin.layout.master')

@section('title', 'Packaging Material Stock')

@section('content')
    <h1 class="mb-1">
        Packaging Material Stock
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="container justify-content-center pb-2 text-center">
                    <div class="row">
                        <div class="col-3"><h4>Small Flyers: <u id="sm_flyers_title">{{number_format($packaging->small_flyers)}}</u></h4></div>
                        <div class="col-3"><h4>Medium Flyers: <u id="md_flyers_title">{{number_format($packaging->medium_flyers)}}</u></h4></div>
                        <div class="col-3"><h4>Large Flyers: <u id="lg_flyers_title">{{number_format($packaging->large_flyers)}}</u></h4></div>
                        <div class="col-3"><h4>Boxes: <u id="box_title">{{number_format($packaging->boxes)}}</u></h4></div>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Invoice No./Cargo ID</th>
                        <th class="border-primary border-darken-1">Entry Type</th>
                        <th class="border-primary border-darken-1">Entered Date/Time</th>
                        <th class="border-primary border-darken-1">Entered By</th>
                        <th class="border-primary border-darken-1">Small Flyers</th>
                        <th class="border-primary border-darken-1">Medium Flyers</th>
                        <th class="border-primary border-darken-1">Large Flyers</th>
                        <th class="border-primary border-darken-1">Boxes</th>
                        <th class="border-primary border-darken-1">Hub</th>
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
                                                <input name="packaging_material_quantity" class="form-control numeric quantity" id="packaging_material_quantity" placeholder="Quantity here"/>
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
                <form id="request_stock_form" action="{{route('admin.packaging.request.submit')}}" method="post">
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
                                                <input name="packaging_material_quantity" class="form-control numeric quantity" id="packaging_material_quantity" placeholder="Quantity here"/>
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
                <div class="modal-body text-center">
                    <form id="send_stock_form" action="{{route('admin.packaging.send.submit')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-4 form-group">
                                    <select name="city_select" id="city_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                    </select>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-4 form-group">
                                    <input type="text" name="invoice_number" id="send_stock_invoice" class="form-control numeric" placeholder="Cargo Number *" data-rule-required="true" data-msg-required="This field is required">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <input type="text" name="send_stock_smflyer" id="send_stock_smflyer" class="form-control small_flyer flyer" placeholder="Small Flyers">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="send_stock_mdflyer" id="send_stock_mdflyer" class="form-control medium_flyer flyer" placeholder="Medium Flyers">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="send_stock_lgflyer" id="send_stock_lgflyer" class="form-control large_flyer flyer" placeholder="Large Flyers">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="send_stock_boxes" id="send_stock_boxes" class="form-control box_flyer flyer" placeholder="Boxes">
                                </div>

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="SendNewStock" type="submit" class="btn btn-primary btn-block">Send Stock</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Send Stock Modal--}}

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
            $('#packaging_material_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Packaging Material Type',
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

            $('#packaging_material_size').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Packaging Material Size',
                dropdownParent: $("#AddStockModal")
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
                            head.push('Invoice No./Cargo ID');
                            head.push('Entry Type');
                            head.push('Entered Date/Time');
                            head.push('Entered By');
                            head.push('Small Flyers');
                            head.push('Medium Flyers');
                            head.push('Large Flyers');
                            head.push('Boxes');
                            head.push('Hub');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.reference_number);
                                row.push(values.entry_type);
                                row.push(values.created_at);
                                row.push(values.admin);
                                row.push(values.small_flyers);
                                row.push(values.medium_flyers);
                                row.push(values.large_flyers);
                                row.push(values.boxes);
                                row.push(values.hub);

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
                            window.location.href = '{{route('admin.packaging.inventory.index')}}';

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
                        {
                            extend: 'excel',
                            title: 'Packaging Material Stock',
                            className:'btn btn-primary',
                            text: '<i class="la la-file-excel-o"></i> Excel',
                        },'reset'],

                @else
                    buttons:[{
                    extend: 'excel',
                    title: 'Packaging Material Stock',
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
                rowId: 'psh_id',
                order: [[3, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'reference_number', name: 'packaging_stock_histories.reference_number', class: 'align-middle reference_number'},
                    {data: 'entry_type', name: 'packaging_stock_histories.entry_type', class: 'align-middle entry_type'},
                    {data: 'created_at', name: 'packaging_stock_histories.created_at', class: 'align-middle created_at'},
                    {data: 'admin', name: 'ad.name', class: 'align-middle admin'},
                    {data: 'small_flyers', name: 'packaging_stock_histories.small_flyers', class: 'align-middle small_flyers'},
                    {data: 'medium_flyers', name: 'packaging_stock_histories.medium_flyers', class: 'align-middle medium_flyers'},
                    {data: 'large_flyers', name: 'packaging_stock_histories.large_flyers', class: 'align-middle large_flyers'},
                    {data: 'boxes', name: 'packaging_stock_histories.boxes', class: 'align-middle boxes'},
                    {data: 'hub', name: 'cities.name', class: 'align-middle hub'}

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
                    var entry_select = '<select name="entry_select" id="entry_select" class="select2 form-control">' +
                        '<option value="0">Inbound</option>' +
                        '<option value="1">Outbound</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.entry_type')){
                            $(entry_select).appendTo($(search))
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
                    $("#entry_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            // $('body').on('change','#add_stock_form input',function() {
            //     $(this).val($(this).val().trim());
            // });
            $('body').on('change','#send_stock_form input',function() {
                $(this).val($(this).val().trim());
            });
            // $('#AddStockModal').on('hidden.bs.modal',function () {
            //     $('#add_stock_form')[0].reset();
            // });
            $('#SendStockModal').on('shown.bs.modal',function () {
                if(!$('#city_select').hasClass('select2-hidden-accessible')){
                    $('#city_select').select2({
                        placeholder:'Send To',
                        dropdownParent:$('#send_stock_form')
                    });
                }

                $.ajax({
                    url: '{!! route('admin.packaging.fetch.cities') !!}',
                    method: 'GET'
                }).done(function (data) {
                    if(data.status === 1){
                        var newOption = new Option('', '', false, false);
                        $('#city_select').append(newOption).trigger('select');
                        $.each(data.cities,function(key,value){
                            var newOption = new Option(value.name, value.id, false, false);
                            $('#city_select').append(newOption).trigger('select');
                        });
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    }
                });
            });
            $('#SendStockModal').on('hidden.bs.modal',function () {
                $('#send_stock_form')[0].reset();
                $('#city_select').empty().trigger('change');
                $('#city_select').val('').trigger('change');
            });
            // $( "#add_stock_form" ).validate({
            //     rules: {
            //         add_stock_sm_flyers: {
            //             require_from_group: [1, ".flyer"]
            //         },
            //         add_stock_md_flyers: {
            //             require_from_group: [1, ".flyer"]
            //         },
            //         add_stock_lg_flyers: {
            //             require_from_group: [1, ".flyer"]
            //         },
            //         add_stock_boxes: {
            //             require_from_group: [1, ".flyer"]
            //         }
            //     },
            //     errorClass:"danger",
            //     errorPlacement: function(error, element) {
            //         error.addClass('w-100').appendTo(element.parents('.form-group'));
            //     },
            //     submitHandler: function(form) {
            //
            //         $(form).find('button[type=submit]').attr('disabled', 'disabled');
            //
            //         form.submit();
            //
            //     }
            //
            //
            // });

            $( "#send_stock_form" ).validate({
                rules: {
                    send_stock_sm_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    send_stock_md_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    send_stock_lg_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    send_stock_boxes: {
                        require_from_group: [1, ".flyer"]
                    }
                },
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    form.submit();

                }


            });

            var ptable;
            $("#AddStockModal").on('shown.bs.modal', function(){
                initDatatable();
            });
            $("#AddStockModal").on('hidden.bs.modal', function(){
                destroyDatatable();
                $('#RequestMaterialBtn').attr('disabled', false);
            });

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

        });

    </script>
@endsection