
@extends('admin.layout.master')
@section('title','Pending Deliveries')

@section('content')
    <h1 class="mb-1">
        Pending Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center">
                    <div class="col-3">
                        <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                            @foreach($shipping_mode as $mode)
                                <option value="{{$mode->id}}">{{$mode->mode}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <select name="search_hub" id="search_hub" class="form-control select2">
                            @foreach($hubs as $hub)
                                <option value="{{$hub->id}}">{{$hub->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Reattempt By</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Sub Stations</th>
                        <th class="border-primary border-darken-1">Weight</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Product Description</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Origin Arrival Date</th>
                        <th class="border-primary border-darken-1">Destination Arrival Date</th>
                        <th class="border-primary border-darken-1">Status Date</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">


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
        .selectize-control {
            width: 100%;
        }

        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Shipping Mode',
            allowClear:true
        }).bind('change', function() {
            table.draw();
        });
        $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Hub',
            allowClear:true
        }).bind('change', function() {
            table.draw();
        });
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.delivery.pending.list') }}',
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Tracking .No');
                        head.push('Shipper');
                        head.push('Origin');
                        head.push('Destination');
                        head.push('Hub');
                        head.push('Consignee Name');
                        head.push('Reattempted By');
                        head.push('Address');
                        head.push('Sub Station');
                        head.push('Weight');
                        head.push('Collection Amount');
                        head.push('Product Type');
                        head.push('Product Description');
                        head.push('Shipping Mode');
                        head.push('Service Type');
                        head.push('Status');
                        head.push('Reason');
                        head.push('Remarks');
                        head.push('Origin Arrival Date');
                        head.push('Destination Arrival Date');
                        head.push('Status Date');
                        $.each(result.data, function(index, values) {
                            row = [];

                            row.push(index + 1);
                            row.push(values.tracking_number);
                            row.push(values.shipper);
                            row.push(values.origin);
                            row.push(values.destination);
                            row.push(values.hub);
                            row.push(values.consignee_name);
                            row.push(values.agent);
                            row.push(values.consignee_address);
                            row.push(values.sub_station);
                            row.push(values.weight);
                            row.push(values.amount);
                            row.push(values.product_type);
                            row.push(values.shipment_description);
                            row.push(values.shipping_mode);
                            row.push(values.service_type);
                            row.push(values.status);
                            row.push(values.reason);
                            row.push(values.remarks);
                            row.push(values.arrival);
                            row.push(values.destination_arrival);
                            row.push(values.current_status_date);

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
                    title: 'Pending Deliveries',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'
            ],
            scrollX: true, scrollY: '500px',
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax:{
                url: '{{ route('admin.delivery.pending.list') }}',
                data: function (d) {
                    d.search_shipping_mode = $('#search_shipping_mode').val();
                    d.search_hub = $('#search_hub').val();
                }
            },
            rowId: 'shId',
            order: [[19, 'desc']],
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                {data: 'agent', name: 'agent.name', class: 'align-middle agent'},
                {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                {data: 'sub_station', name: 'dlm.area_name', class: 'align-middle sub_station', searchable:false},
                {data: 'weight', name: 'shipments.actual_weight', class: 'align-middle weight'},
                {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                {data: 'product_type', name: 'prod.product_name', class: 'align-middle product_type'},
                {data: 'shipment_description', name: 'si.description', class: 'align-middle shipment_description',orderable: false, searchable: false},
                {data: 'shipping_mode', name: 'shipping_mode', class: 'align-middle shipping_mode'},
                {data: 'service_type', name: 'service_type', class: 'align-middle service_type'},
                {data: 'status', name: 'status', class: 'align-middle status'},
                {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                {data: 'remarks', name: 'shipments_journey.remarks', class: 'align-middle remarks'},
                {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                {data: 'destination_arrival', name: 'sjd.created_at ', class: 'align-middle destination_arrival', orderable: false, searchable: false},
                {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
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
                var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                    '</select>';
                var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();


                    if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.destination_arrival') || $(header).is('.shipment_description') || $(header).is('.sub_station')) {
                        $(td).appendTo($(search));
                    }else if($(header).is('.status')){
                        $(drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.shipping_mode')){
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
                var data1 = $.map({!! $shipping_mode !!}, function (obj) {
                    obj.id = obj.id

                    return obj;
                });
                var data1 = $.map({!! $shipping_mode !!}, function (obj) {
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
                    obj.id = obj.id

                    return obj;
                });
                var data2 = $.map({!! $service_type !!}, function (obj) {
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
        $('body').on('click','.dispute_modal',function(){
            var shipment_id = parseInt($(this).parents('tr').attr('id'));
            $('#UniversalDisputeModal').modal('show');
            $('#universal_dispute_id').val(shipment_id);
        });
        var select;
        $('#UniversalDisputeModal').on('shown.bs.modal',function () {
            var id = $('#universal_dispute_id').val();

            if(id){
                $.ajax({
                    url: '{!! route('admin.dispute.data') !!}',
                    method: 'POST',
                    data:{
                        '_token': '{{ csrf_token() }}',
                        'shipment_id':id
                    }
                }).done(function (data) {
                    if(data.success == 1){
                        $('#universal_city_select').prepend('<option value="" selected="selected"></option>').select2({
                            placeholder:'Select a city',
                            dropdownParent:$('#universal_dispute_form')
                        });
                        $.each(data.cities,function(key,value){
                            var newOption = new Option(value.name, value.id, false, false);
                            $('#universal_city_select').append(newOption).trigger('select');
                        });
                        $.each(data.dispute_types,function(key,value) {
                            var dispute = new Option(value.type, value.id, false, false);
                            $('#universal_dispute_type_select').append(dispute).trigger('select');
                        });
                        $('#universal_dispute_type_select').prepend('<option value="" selected="selected"></option>').select2({
                            placeholder:'Select a Dispute type',
                            dropdownParent:$('#universal_dispute_form')
                        });
                        $('#universal_tracking_number').val(data.tracking);
                        select = $('#universal_tracking_number').selectize({
                            placeholder: 'Tracking Number(s)*',
                            delimiter: ',',
                            createOnBlur: true,
                            persist: false,
                            plugins: ['remove_button'],
                            onDropdownOpen: function(dropdown) {
                                dropdown.remove();
                            },
                            onType: function(str) {
                                var regex = /^[0-9,]+$/;

                                if (!regex.test(str)) {
                                    select[0].selectize.setTextboxValue('');
                                }
                            },
                            create: function(input) {
                                if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
                                    return {
                                        value: input,
                                        text: input
                                    }
                                }
                                else {
                                    return false;
                                }
                            }
                        });

                    }
                });
            }
        });
        var max_char = 190;
        $('#universal_description').on('keypress copy paste',function (e) {
            // var comment = $(this).val();
            // console.log(comment)
            if ($(this).val().length == max_char) {
                e.preventDefault();
            } else if ($(this).val().length > max_char) {
                // Maximum exceeded
                this.value = this.value.substring(0, max_char);
            }
        });
        $('#UniversalDisputeModal').on('hidden.bs.modal',function () {
            $('#universal_dispute_form')[0].reset();
            $('#UniversalDisputeCreate').removeAttr('disabled');
            select[0].selectize.destroy();
            $("#universal_dispute_form").validate().resetForm();
            $('#universal_city_select').empty().trigger('change');
            $('#universal_dispute_type_select').empty().trigger('change');
        });
        $('#universal_dispute_form').validate({
            ignore: [],
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function(form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');

                var city_select = $('#universal_city_select').val();
                var dispute_type_select = $('#universal_dispute_type_select').val();
                var tracking_number = $('#universal_tracking_number').val();
                var description = $('#universal_description').val();
                $.ajax({
                    url: '{!! route('admin.dispute.create.universal') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'city_select': city_select,
                        'dispute_type_select':dispute_type_select,
                        'tracking_number':tracking_number,
                        'description':description
                    }
                }).done(function(data){
                    $('#UniversalDisputeModal').modal('hide');
                    if (data.invalid !== undefined) {

                        var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                        toastr.error(message, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    if(data.success != undefined){
                        // table.ajax.reload();
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                });

            }


        });

    </script>
@endsection