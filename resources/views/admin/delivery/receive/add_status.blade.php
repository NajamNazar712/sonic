
@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Receive Deliveries(Delivery Note: {{$delivery_note_id}})
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="status_update_form" action="{{route('admin.delivery.receive.add.status')}}" method="post">
                    @csrf
                <input type="hidden" value="{{$delivery_note_id}}" id="delivery_note" name="delivery_note_id">
                <input type="hidden" value="{{$shipments_count}}" id="shipments_count" name="shipments_count">
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Consignee</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Current Status</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Clear</th>
                    </tr>
                    </thead>
                </table>
                <div class="row justify-content-center">
                    <div class="col-2">
                        <button id="statusSubmit" type="submit" disabled class="btn btn-primary btn-block">Update Status</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>


    </div>

    <!--Replacement Modal -->
    <div class="modal fade text-left" id="ReplacementModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ReplacementModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Replacement Shipment Weight</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <form id="replacement_form" action="{{route('admin.delivery.receive.replacements.submit')}}" method="post">
                    <table class="table table-bordered datatable" id="replacementtable" style="z-index: 3;">
                        <thead>
                        @csrf
                        @method('PUT')
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Weight Of Shipment</th>

                        </tr>
                        </thead>
                    </table>
                        <input type="hidden" name="shipment_id_list" id="shipment_id_list">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button id="ReplacementUpdate" type="submit" class="btn btn-primary btn-block">Update</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Replacement Modal -->
    <!--Try&Buy Modal -->
    <div class="modal fade text-left" id="TryBuyModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="TryBuyModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Try &amp; Buy Delivery</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <form id="trybuy_form" action="{{route('admin.delivery.receive.trybuys.submit')}}" method="post">
                        <table class="table table-bordered datatable" id="trybuytable" style="z-index: 3;">
                            <thead>
                            @csrf
                            @method('PUT')
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Product Type</th>
                                <th class="border-primary border-darken-1">Product Description</th>
                                <th class="border-primary border-darken-1">Item Price</th>
                                <th class="border-primary border-darken-1">Receiving</th>

                            </tr>
                            </thead>
                        </table>
                        <div class="row justify-content-center mb-2">
                            <div class="col">
                                <h4><U>Total Cod Amount:</U> Rs: <span id="cod"></span></h4>
                            </div>
                        </div>
                        <input type="hidden" name="trybuy_id_list" id="trybuy_id_list">
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="TrybuyUpdate" type="submit" class="btn btn-primary btn-block">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Try&Buy Modal -->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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
            border-color: #666EE8;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        table.dataTable tbody tr td.status,
        table.dataTable tbody tr td.reason,
        table.dataTable tbody tr td.remarks {
            min-width: 110px !important;
            max-width: 150px !important;
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
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var selected_rows = [];
            var note_id = $('#delivery_note').val();
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: 'Delivered',
                    className: 'btn btn-primary delivered',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        if(selected_rows != ''){
                            $.ajax({
                                url: '{!! route('admin.delivery.receive.delivered') !!}',
                                method: 'POST',
                                data: {
                                    'shipment_ids': selected_rows,
                                    'delivery_note_id': note_id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if(data.status == 0){

                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    checkShipmentStatuses();
                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                }
                                $.each(selected_rows, function(index, id) {
                                    table.row($('#datatable tbody tr#' + id)).deselect();
                                });
                                selected_rows = [];
                                table.button(0).disable();
                                table.ajax.reload();
                                $('.reasonDrop','.statusDrop').select2('destroy');
                                setTimeout(function () {
                                    $(".reasonDrop").select2({
                                        placeholder: "Select a Reason",
                                        width:'100%'
                                    });
                                    $(".statusDrop").select2({
                                        placeholder: "Select a Status",
                                        width:'100%'
                                    });
                                },2000);

                            });
                        }else{
                            var error = "Something went wrong please refresh page and try again!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }
                    }

                }],
                fixedHeader: {
                    header: true,
                    headerOffset: $('.header-navbar').height()
                },
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.delivery.receive.add.list',['id'=>$delivery_note_id]) }}',
                rowId: 'shId',
                order: [[2, 'asc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'tracking_number',name: 'tracking_number', class: 'align-middle tracking_number'},
                    {data:'consignee_name',name: 'consignee_name', class: 'align-middle consignee_name'},
                    {data:'amount',name: 'amount', class: 'align-middle amount'},
                    {data:'status',name: 'status', class: 'align-middle status statusOnChange'},
                    {data:'reason',name: 'reason', class: 'align-middle reason reasonSelect'},
                    {data:'remarks',name: 'remarks', class: 'align-middle remarks'},
                    {data:'address',name: 'address', class: 'align-middle address'},
                    {data:'destination',name: 'destination', class: 'align-middle destination'},
                    {data:'shipper',name: 'shipper', class: 'align-middle shipper'},
                    {data:'current_status',name: 'current_status', class: 'align-middle current_status'},
                    {data:'service_type',name: 'service_type', class: 'align-middle service_type'},
                    {data:'action',name: 'action', class: 'align-middle action'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    $(".reasonDrop").select2({
                        placeholder: "Select a Reason",
                        width:'100%'
                    });
                    $(".statusDrop").select2({
                        placeholder: "Select a Status",
                        width:'100%'
                    });
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.reason') || $(header).is('.remarks') || $(header).is('.action')) {
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
                    table.button(0).enable();
                    // table.button(1).enable();
                }
                else {
                    table.button(0).disable();
                    // table.button(1).disable();
                }
            });

            $('body').on('select2:select','.statusOnChange .statusDrop',function (e) {
                $('#statusSubmit').removeAttr('disabled');
                var statusSelection = $(this).find(':selected');
                var status = statusSelection.val();
                var reason = statusSelection.closest('td').next('td').find('.reasonDrop');

                $.ajax({
                    url:'{!! route('admin.delivery.receive.reason') !!}',
                    type:'POST',
                    dataType:'json',
                    data: {
                        'status':status,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 0){
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            reason.append(newOption).trigger('change');
                        });
                    }else{
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });
            $('body').on('click','.clear',function () {
                // console.log();
               var status = $(this).parents().closest('tr').find('.statusDrop');
               var reason = $(this).parents().closest('tr').find('.reasonDrop');
               status.val('').trigger("change");
               reason.val('').trigger("change");
               $('.remarks input').val('');
                // $('.reasonDrop').val('').trigger("change");
            });
            var shipments = [];
            $('#status_update_form').bind('submit', function(event) {
                var shipment = $('#shipment_ids');
                event.preventDefault();
                var id = '';
                var count = table.data().count();
                for(var i = 0;i<count;i++){
                    id = table.row( i ).id();
                    shipments.push(id);
                }
                shipment.val(shipments);
                this.submit();
            });
            //on page load ajax
            var shipment_id_list = [];
            var shipments_count = $('#shipments_count').val();
            function checkShipmentStatuses(){
                var delivery_note = $('#delivery_note').val();
                $.ajax({
                    url: '{!! route('admin.delivery.receive.shipmentstatuscheck') !!}',
                    method: 'POST',
                    data: {
                        'delivery_note_id': delivery_note,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {

                    shipments_count = shipments_count-1;
                    if(shipments_count>0) {
                        if (data.status == 1) {

                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                            checkShipmentStatuses();
                            console.log(shipments_count);

                        } else if (data.status == 2) {

                            $('#ReplacementModal').modal('show');

                            var repl = $('#replacementtable').DataTable({
                                dom: 'ltipr',
                                fixedHeader: {
                                    header: true,
                                    headerOffset: $('.header-navbar').height()
                                },
                                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                                pageLength: 25,
                                stateSave: true,
                                pagingType: 'full_numbers',
                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                                    {name: 'tracking_number', class: 'align-middle tracking_number'},
                                    {name: 'service_type', class: 'align-middle service_type'},
                                    {name: 'weight', class: 'align-middle weight'},

                                ],
                                rowCallback: function(row, data, index) {
                                    var info = repl.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                                    if ($.inArray(data.id, selected_rows) !== -1) {
                                        repl.row(row).select();
                                    }
                                },
                                initComplete: function() {

                                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                                    this.api().columns().every(function(column_id) {
                                        var column = this;
                                        var header = column.header();

                                        if ($(header).is('.serial_number') || $(header).is('.weight')) {
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
                                }
                            });

                            $.ajax({
                                url:'{!! route('admin.delivery.receive.replacements') !!}',
                                type:'POST',
                                dataType:'json',
                                data: {
                                    'replacements':data.replacement,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if(data.status == 0){
                                    var rowNo = repl.rows().count();
                                    $.each(data.data,function (key,value) {
                                        var inp = "<input class='form-control' name='weight["+value.id+"]' placeholder='Enter Weight'>";
                                        // console.log(value.tracking_number)
                                        repl.row.add([rowNo+1,value.tracking_number,value.booking_type_id,inp]).node().id = value.id;
                                        repl.draw(false);
                                        shipment_id_list.push(value.id);
                                    });

                                }else{
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });

                        } else if (data.status == 3) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                            $('#TryBuyModal').modal('show');
                            // checkShipmentStatuses();
                            console.log(data);

                            var trybuy = $('#trybuytable').DataTable({
                                dom: 'ltipr',
                                fixedHeader: {
                                    header: true,
                                    headerOffset: $('.header-navbar').height()
                                },
                                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                                pageLength: 25,
                                stateSave: true,
                                pagingType: 'full_numbers',

                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                    {name: 'product_type', class: 'align-middle product_type'},
                                    {name: 'product_description', class: 'align-middle product_description'},
                                    {name: 'item_price', class: 'align-middle item_price'},
                                    {name: 'receiving', class: 'align-middle receiving'},

                                ],
                                rowCallback: function(row, data, index) {
                                    var info = trybuy.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                                    if ($.inArray(data.id, selected_rows) !== -1) {
                                        trybuy.row(row).select();
                                    }
                                }
                            });
                            $.ajax({
                                url:'{!! route('admin.delivery.receive.trybuys') !!}',
                                type:'POST',
                                dataType:'json',
                                data: {
                                    'trybuy':data.try,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {

                                if(data.status == 0){
                                    var rowNo = trybuy.rows().count();
                                    $.each(data.data,function (key,value) {
                                        var inp = "<input type='checkbox' checked class='form-control' name='bought["+value.pid+"]' id='bought_"+value.pid+"'>";
                                        // console.log(value.tracking_number)
                                        trybuy.row.add([rowNo+1,value.type,value.description,value.price,inp]).node().id = value.pid;
                                        trybuy.draw(false);
                                        // shipment_id_list.push(value.id);
                                        $('#cod').text(data.total_cod);
                                    });

                                }else{
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });


                        } else if (data.status == 0) {
                            console.log(data.error);
                            console.log(shipments_count);

                        }
                    }
                });
            }
            checkShipmentStatuses();

            $('body').on('click','.receiving input:checkbox',function () {
                var check = $(this);
                var price = $(this).parents('tr').find('td.item_price').text();
                if($.isNumeric(price)){

                }
            });
            //replacement modal bind
            $('#replacement_form').bind('submit',function (e) {
                e.preventDefault();
                $('#shipment_id_list').val(shipment_id_list);
                this.submit();
            });
        });
    </script>
@endsection