
@extends('admin.layout.master')
@section('title','Update Receive Deliveries')

@section('content')
    <h1 class="mb-1">
        Update Receive Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="camera_scan" class="d-none">
                    <div id="camera_view" class="camera_view"></div>
                </div>

                <form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                    <div class="form-group">
                        <input type="text" name="tracking_number" id="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">

                        <div class="d-inline-block ml-1">
                            <a href="#" id="camera_scan_initiate" tabindex="-1">
                                <i class="ft-camera h1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="form-group ml-1">
                        <button type="submit" name="add" id="add_tracking_number" class="btn btn-primary add" value="Add">Add</button>
                    </div>
                </form>

                <input type="hidden" value="{{$delivery_note_id}}" id="delivery_note">


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Service Type</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">

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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var shipment_ids = [];
            var selected_rows = [];


            $('#camera_scan_initiate').bind('click', function() {
                if ($('#camera_scan').hasClass('d-none')) {
                    $('#camera_scan').removeClass('d-none');

                    camera_scanning_start('#camera_view');
                }
                else {
                    $('#camera_scan').addClass('d-none');

                    camera_scanning_stop();
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true,
                buttons: [{
                    text: '<i class="la la-cogs"></i> Remove',
                    className: 'btn btn-primary bulk_remove',
                    enabled:false,
                    action: function (e, dt, node, config) {
                        var delivery_note_id = $('#delivery_note').val();
                        //$('input:hidden[name=delivery_note]').val(selected_rows);

                        if(selected_rows.length === 0){
                            table.button('.bulk_remove').disable();
                            return false;
                        }
                        else if(selected_rows !== ''){
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes to bulk remove shipment!',
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
                                        url: '{!! route('admin.delivery.receive.update.remove.bulk') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'shipment_ids':selected_rows,
                                            'delivery_note_id':delivery_note_id,
                                        }
                                    }).done(function(data){
                                        if(data.status == 0){
                                            table.rows().deselect();
                                            selected_rows = [];
                                            table.button('.bulk_remove').disable();
                                            table.draw(true);
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            $('#add_tracking_number').prop('disabled', true);
                                            setTimeout(function() {
                                                window.location.href = '{{ route('admin.delivery.receive.index') }}';
                                            }, 2500);
                                        }
                                        else{
                                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            table.button('.bulk_remove').disable();
                                        }
                                    });
                                }
                            });

                        }else{
                            var error = 'Data Not Found, Please Try again!';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            table.button('.bulk_remove').disable();
                        }
                    }
                },
                    {
                    text: '<i class="la la-print"></i> Print',
                    className: 'btn btn-primary print',
                    enabled:true,
                    action: function (e, dt, node, config) {
                        var delivery_note_id = $('#delivery_note').val();

                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes to print!',
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
                                        url: '{!! route('admin.delivery.receive.print') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'id':delivery_note_id,
                                        }
                                    }).done(function(data){
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
                            });
                    }
                },{
                    extend: 'selectAll',
                    text: 'Select All',
                    className: 'select_all',
                    action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                            var row = table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                row.select();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index === -1) {
                                    selected_rows.push(id);
                                }

                                table.button('.bulk_remove').enable();
                            }
                        });
                    }
                }, {
                    extend: 'selectNone',
                    text: 'Select None',
                    className: 'select_none',
                    action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                            var row = table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                row.deselect();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index !== -1) {
                                    selected_rows.splice(index, 1);
                                }

                                if (selected_rows.length == 0) {
                                    table.button('.bulk_remove').disable();

                                }
                            }
                        });
                    }
                },'reset'],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.delivery.receive.update.list',['note'=>$delivery_note_id]) }}',
                rowId: 'shId',
                order: [[1, 'desc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data:'tracking_number',name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data:'destination',name: 'oc.name', class: 'align-middle destination'},
                    {data:'consignee_name',name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data:'phone',name: 'shipments.consignee_phone_number_1', class: 'align-middle phone'},
                    {data:'address',name: 'shipments.consignee_address', class: 'align-middle address'},
                    {data:'amount',name: 'shipments.amount', class: 'align-middle amount'},
                    {data:'service_type',name: 'service_type', class: 'align-middle service_type'},
                    {data:'action',name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    if(data.payment_mode_id != 2){
                        $('td:eq(0)', row).addClass('select-checkbox');
                    }
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.shipment_id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.action')) {
                            $(td).appendTo($(search));
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
            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.bulk_remove').enable();
                }
                else {
                    table.button('.bulk_remove').disable();
                }
            });

            $('body').on('click','a.deliverynoterow',function () {
                var shipment_id = $(this).parents('tr').attr('id');
                var delivery_note = $('#delivery_note').val();
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to remove the shipment!',
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
                        blockPagePermanently();
                        $.ajax({
                            url:'{{route('admin.delivery.receive.update.remove')}}',
                            type:'POST',
                            data: {
                                'shipment_id':shipment_id,
                                'delivery_note_id':delivery_note,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status == 0){
                                UnblockPagePermanently();
                                table.row( $(this).parents('tr') ).remove().draw();
                                var total_rows = table.rows().count();
                                if(total_rows <= 0){
                                    $('#add_tracking_number').prop('disabled', true);
                                    setTimeout(function() {
                                        window.location.href = '{{ route('admin.delivery.receive.index') }}';
                                    }, 2500);
                                }
                            }else{
                                UnblockPagePermanently();
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                });
            });
            function camera_scan_detected(tracking_number) {
                $('#add_shipment_form input.tracking_number').val(tracking_number);

                $('#add_shipment_form input.tracking_number').focus();

            }


            $( "#add_shipment_form" ).validate({

                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                        var tracking = $.trim($("#add_shipment_form #tracking_number").val());

                        if (tracking) {
                            $("#add_shipment_form #tracking_number").attr('disabled', true);

                            $.ajax({

                                url:'{!! route('admin.delivery.note.shipment.info') !!}',
                                method:'POST',
                                data:{
                                    'tracking':tracking,
                                    '_token': '{{ csrf_token() }}',
                                }
                            }).done(function(data) {
                                if(data.status == 0){
                                   var delivery_note_id = $('#delivery_note').val();
                                    $.ajax({

                                        url: '{!! route('admin.delivery.receive.add.shipments') !!}',
                                        method: 'POST',
                                        data: {
                                            'shipment_id': data.shId,
                                            'tracking_number':data.tracking_number,
                                            'delivery_note_id': delivery_note_id,
                                            '_token': '{{ csrf_token() }}',
                                        }
                                    })
                                        .done(function (value){
                                            if(value.status == 0){
                                                    toastr.success(value.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                                     table.draw();
                                                $("#add_shipment_form #tracking_number").attr('disabled', false);
                                            }
                                            else{
                                                toastr.error(value.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                                $("#add_shipment_form #tracking_number").attr('disabled', false);
                                            }
                                        }
                                    )

                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    $("#add_shipment_form #tracking_number").attr('disabled', false);
                                }
                                $('#tracking_number').val('');

                            });

                        }
                        else{
                            var error ='Not Found';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }

            // });
        }

        });
            $('#add_shipment_form input.tracking_number').focus();
            $('#add_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
        });
        function camera_scan_detected(tracking_number) {
            $('#add_shipment_form input.tracking_number').val(tracking_number);

            $('#add_shipment_form input.tracking_number').focus();

        }
    </script>
@endsection