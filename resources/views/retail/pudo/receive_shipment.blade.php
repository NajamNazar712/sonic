@extends('retail.layout.master')

@section('title', 'Receive Shipments')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Receive Shipments
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                                <div class="form-group">
                                    <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">

                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                                </div>
                            </form>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 4;width: 100%!important;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Destination Hub</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Collection Amount</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>

                            <form id="arrival_of_shipments_form" class="form-horizontal text-center" method="POST" action="{{ route('retail.receive.shipment.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="shipment_ids" class="shipment_ids">

                                <div class="form-group ml-1">
                                    <button type="submit" name="confirm" class="btn btn-primary confirm" value="Confirm" disabled="disabled">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tryAndbuyModal" data-backdrop="static" role="dialog" aria-labelledby="tryAndbuyModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delivered_shipments_modal_title">Try And Buy Items(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_try_and_buy_shipment_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                        <input type="hidden" name="try_and_buy_shipment_id" id="try_and_buy_shipment_id">
                        <input type="hidden" name="try_and_buy_tracking_number" id="try_and_buy_tracking_number">
                        <input type="hidden" name="try_and_buy_shipment_items_count" id="try_and_buy_shipment_items_count">
                        <div class="row justify-content-center">
                            <div class="form-group col-5">
                                <input type="text" name="scan_item" id="scan_item" class="form-control scan_item" placeholder="Scan Item">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="row">
                                <p id="total_item_count"></p>
                            </div>
                        </div>
                        <table class="table table-bordered datatable" id="try_and_buy_datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Item ID</th>
                                <th class="border-primary border-darken-1">Tracking Number</th>
                                <th class="border-primary border-darken-1"></th>
                            </tr>
                            </thead>
                        </table>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" id="try_and_buy_airwaybill" disabled="disabled">Print Air Waybill</button>
                        </div>

                        <div class="row justify-content-center">
                            <div class="form-group col-5">
                                <input type="text" name="scan_try_and_buy_tracking_number" id="scan_try_and_buy_tracking_number" class="form-control scan_try_and_buy_tracking_number" placeholder="Scan Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary try_and_buy_confirm" id="try_and_buy_confirm" disabled="disabled">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ShipmentPiecesModal" data-backdrop="static" role="dialog" aria-labelledby="ShipmentPiecesModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delivered_shipments_modal_title">Shipment Piece(s)</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_shipment_pieces_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                        <input type="hidden" name="piece_shipment_id" id="piece_shipment_id">
                        <input type="hidden" name="piece_tracking_number" id="piece_tracking_number">
                        <input type="hidden" name="piece_shipment_count" id="piece_shipment_count">
                        <div class="row justify-content-center">
                            <div class="form-group col-5">
                                <input type="text" name="scan_piece" id="scan_piece" class="form-control scan_piece" placeholder="Scan Piece">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="row">
                                <p id="total_item_count"></p>
                            </div>
                        </div>
                        <table class="table table-bordered datatable" id="piece_datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Piece ID</th>
                                <th class="border-primary border-darken-1">Tracking Number</th>
                                <th class="border-primary border-darken-1"></th>
                            </tr>
                            </thead>
                        </table>

                        <div class="row justify-content-center">
                            <div class="form-group col-5">
                                <input type="text" name="scan_piece_tracking_number" id="scan_piece_tracking_number" class="form-control scan_piece_tracking_number" placeholder="Scan Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary piece_confirm" id="piece_confirm" disabled="disabled">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            @if (session('print_shipment_ids'))
            var url = '{!! route('cod.shipment.book.print_air_waybill') !!}';

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    'ids': @json(session('print_shipment_ids')),
                    'admin': true,
                    '_token': '{{ csrf_token() }}'
                }
            })
                .done(function (data) {
                    var tab = window.open('', '_blank');

                    if (!tab) {
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
            @endif

            function print(id) {
                var url = '{!! route('cod.shipment.book.print_air_waybill') !!}';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        'ids[]': id,
                        'admin': true,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        var tab = window.open('', '_blank');

                        if (!tab) {
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

            var shipment_ids = [];
            var shipment_item_ids = [];
            var all_shipment_item_ids = [];
            var shipment_piece_ids = [];
            var all_shipment_piece_ids = [];
            $('#add_shipment_form input.tracking_number').focus();

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'city', class: 'align-middle city', orderable: false, searchable: false},
                    {name: 'hub', class: 'align-middle hub', orderable: false, searchable: false},
                    {name: 'shipper', class: 'align-middle shipper', orderable: false, searchable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false, searchable: false},
                    {name: 'rider', class: 'align-middle rider', orderable: false, searchable: false},
                    {name: 'remove', class: 'align-middle remove', sortable: false, orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();
                    //
                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#add_try_and_buy_shipment_form input.scan_item').focus();

            var try_and_buy_table = $('#try_and_buy_datatable').DataTable({
                dom: 'ltipr',
                paging:false,
                autoWidth: false,
                columns: [
                    {orderable: false, searchable: false, name: 'try_serial_number', class: 'align-middle serial_number'},
                    {name: 'try_item_id', class: 'align-middle item_id', orderable: false, searchable: false},
                    {name: 'try_tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'try_remove', class: 'align-middle remove', sortable: false, orderable: false, searchable: false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#add_shipment_weight_form input.volumetric_weight').checkboxpicker().bind('change', function() {
                var parent = $(this).parent('.form-group').prev('.form-group');

                if (this.checked) {
                    $('#add_shipment_weight_form input.weight').val('').prop('disabled', true);

                    $('#add_shipment_weight_form .volumetric_weights input').val('').prop('disabled', false);
                }
                else {
                    $('#add_shipment_weight_form input.weight').val('').prop('disabled', false);

                    $('#add_shipment_weight_form .volumetric_weights input').val('').prop('disabled', true);
                }
            });

            $('#add_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#add_try_and_buy_shipment_form input.scan_item').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#add_shipment_pieces_form input.scan_piece').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#scan_try_and_buy_tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#add_shipment_weight_form input.weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('#add_shipment_weight_form .volumetric_weights input.length').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('#add_shipment_weight_form .volumetric_weights input.breadth').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('#add_shipment_weight_form .volumetric_weights input.height').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('#add_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#add_shipment_form button.add').prop('disabled', true);

                    var tracking_number = $(form).find('input.tracking_number').val();
                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        $.ajax({
                            url: '{!! route('retail.receive.shipment.shipment_details') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 10000,
                            error: function (data) {
                                form.reset();

                                $('#add_shipment_form input.tracking_number').val('').focus();

                                $('#add_shipment_form button.add').prop('disabled', false);
                                scan_sound(2);
                                toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            },
                            success: function(data) {
                                form.reset();

                                $('#add_shipment_form input.tracking_number').val('').focus();

                                remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';

                                if (data.status == 0) {
                                    id = data.details.id;

                                    var index = $.inArray(id, shipment_ids);

                                    if (index === -1) {
                                        var rowNo = table.rows().count();
                                        table.row.add([rowNo + 1, data.details.tracking_number,data.details.city ,data.details.hub, data.details.shipper, data.details.amount,data.details.rider,remove_button]).node().id = data.details.id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                        shipment_ids.push(data.details.id);

                                        $('#add_shipment_form button.add').prop('disabled', false);

                                        $('#arrival_of_shipments_form button.confirm').prop('disabled', false);

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else if(data.status == 2){
                                    $('#scan_try_and_buy_tracking_number').prop('disabled', true);
                                    $('#try_and_buy_confirm').prop('disabled', true);
                                    if(data.details.scanned_shipment_item){
                                        var item_index = $.inArray(parseInt(data.details.scanned_shipment_item), all_shipment_item_ids);
                                        if (item_index === -1) {
                                            var try_remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
                                            var try_rowNo = try_and_buy_table.rows().count();
                                            try_and_buy_table.row.add([try_rowNo + 1, data.details.scanned_shipment_item, data.details.tracking_number, try_remove_button]).node().id = data.details.scanned_shipment_item;
                                            try_and_buy_table.draw(false);
                                            try_and_buy_table.columns.adjust().draw();
                                            scan_sound(1);
                                            shipment_item_ids.push(data.details.scanned_shipment_item);
                                            $('#try_and_buy_shipment_id').val(data.details.id);
                                            $('#try_and_buy_tracking_number').val(data.details.tracking_number);
                                            $('#try_and_buy_shipment_items_count').val(data.details.shipment_items_count);
                                            $('#total_item_count').html('Total Shipment Items: ' + data.details.shipment_items_count);
                                            // all_shipment_item_ids.push(data.scanned_shipment_item);
                                            var check = parseInt(try_rowNo) + 1;
                                            if(parseInt(data.details.shipment_items_count) === parseInt(check)){
                                                $('#try_and_buy_airwaybill').prop('disabled', false);
                                            }
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            $('#tryAndbuyModal').modal('show');
                                        }
                                        else{
                                            toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }
                                    }
                                    else{
                                        $('#try_and_buy_shipment_id').val(data.details.id);
                                        $('#try_and_buy_tracking_number').val(data.details.tracking_number);
                                        $('#try_and_buy_shipment_items_count').val(data.details.shipment_items_count);
                                        $('#total_item_count').html('Total Shipment Items: ' + data.details.shipment_items_count);
                                        $('#tryAndbuyModal').modal('show');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                    $('#add_shipment_form button.add').prop('disabled', false);

                                    $('#arrival_of_shipments_form button.confirm').prop('disabled', false);
                                }
                                else if(data.status == 3){
                                    $('#scan_piece_tracking_number').prop('disabled', true);
                                    $('#piece_confirm').prop('disabled', true);
                                    if(data.details.scanned_shipment_piece){
                                        var piece_index = $.inArray(parseInt(data.details.scanned_shipment_piece), all_shipment_piece_ids);
                                        if (piece_index === -1) {
                                            var piece_remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
                                            var piece_rowNo = piece_table.rows().count();
                                            piece_table.row.add([piece_rowNo + 1, data.details.scanned_shipment_piece, data.details.tracking_number, piece_remove_button]).node().id = data.details.scanned_shipment_piece;
                                            piece_table.draw(false);
                                            piece_table.columns.adjust().draw();
                                            scan_sound(1);
                                            shipment_piece_ids.push(data.details.scanned_shipment_piece);
                                            $('#piece_shipment_id').val(data.details.id);
                                            $('#piece_tracking_number').val(data.details.tracking_number);
                                            $('#piece_shipment_count').val(data.details.pieces);
                                            $('#total_piece_count').html('Total Shipment Piece(s): ' + data.details.pieces);
                                            // all_shipment_item_ids.push(data.scanned_shipment_item);
                                            var check = parseInt(piece_rowNo) + 1;
                                            if(parseInt(data.details.piece) === parseInt(check)){
                                                $('#scan_piece_tracking_number').prop('disabled', false);
                                                $('#piece_confirm').prop('disabled', false);
                                            }
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            $('#ShipmentPiecesModal').modal('show');
                                        }
                                        else{
                                            toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }
                                    }
                                    else{
                                        $('#piece_shipment_id').val(data.details.id);
                                        $('#piece_tracking_number').val(data.details.tracking_number);
                                        $('#piece_shipment_count').val(data.details.pieces_count);
                                        $('#total_piece_count').html('Total Shipment Pieces: ' + data.details.pieces_count);
                                        $('#ShipmentPiecesModal').modal('show');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                    $('#add_shipment_form button.add').prop('disabled', false);

                                    $('#arrival_of_shipments_form button.confirm').prop('disabled', false);
                                }
                                else {
                                    $('#add_shipment_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        });
                    }
                    else {
                        $('#add_shipment_form button.add').prop('disabled', false);

                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            $('#scan_item').on('change', function () {
                var item = parseInt($(this).val());
                $('#scan_item').val('').focus();
                if(item){
                    var new_item_index = $.inArray(item, shipment_item_ids);
                    if (new_item_index === -1) {
                        var shipment_id = $('#try_and_buy_shipment_id').val();
                        var shipment_tracking_number = $('#try_and_buy_tracking_number').val();
                        var shipment_items_count = $('#try_and_buy_shipment_items_count').val();
                        $.ajax({
                            url: '{!! route('admin.pickups.receive.try_and_buy.item_details') !!}',
                            method: 'POST',
                            data: {
                                'shipment_id': shipment_id,
                                'item_id': item,
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 10000,
                            error: function (data) {
                                toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            },
                            success: function (data) {
                                if(data.status == 0){
                                    var try_remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
                                    var try_rowNo = try_and_buy_table.rows().count();
                                    try_and_buy_table.row.add([try_rowNo + 1, data.scanned_shipment_item, shipment_tracking_number, try_remove_button]).node().id = data.scanned_shipment_item;
                                    try_and_buy_table.draw(false);
                                    try_and_buy_table.columns.adjust().draw();
                                    scan_sound(1);
                                    shipment_item_ids.push(data.scanned_shipment_item);
                                    var check = parseInt(try_rowNo) + 1;
                                    if(parseInt(shipment_items_count) === parseInt(check)){
                                        $('#try_and_buy_airwaybill').prop('disabled', false);
                                    }
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        });
                    }
                    else{
                        toastr.error('Shipment Item has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });


            $('#try_and_buy_datatable tbody').on('click', 'tr td.remove button', function() {
                var parent = $(this).parents('tr');
                var id = parseInt(parent.attr('id'));
                var index = $.inArray(id, shipment_item_ids);
                if (index !== -1) {
                    try_and_buy_table.row(parent).remove();
                    try_and_buy_table.draw(false);
                    var try_rowNo = try_and_buy_table.rows().count();
                    shipment_item_ids.splice(index, 1);
                    var shipment_items_count = $('#try_and_buy_shipment_items_count').val();
                    var check = parseInt(try_rowNo);
                    if(parseInt(shipment_items_count) !== parseInt(check)){
                        $('#try_and_buy_airwaybill').prop('disabled', true);
                        $('#scan_try_and_buy_tracking_number').prop('disabled', true);
                        $('#try_and_buy_confirm').prop('disabled', true);
                    }
                }
            });


            $('#try_and_buy_airwaybill').on('click', function () {
                id = $('#try_and_buy_shipment_id').val();
                $('#scan_try_and_buy_tracking_number').prop('disabled', false);
                $('#try_and_buy_confirm').prop('disabled', false);
                print(id);
            });

            $('#add_try_and_buy_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    var shipment_id = $('#try_and_buy_shipment_id').val();
                    var tracking_number = $(form).find('input.scan_try_and_buy_tracking_number').val();
                    $.ajax({
                        url: '{!! route('retail.receive.shipment.try_and_buy.shipment_details') !!}',
                        method: 'POST',
                        data: {
                            'tracking_number': tracking_number,
                            '_token': '{{ csrf_token() }}'
                        },
                        timeout: 10000,
                        error: function (data) {
                            toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        },
                        success: function (data) {
                            form.reset();
                            remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';

                            if (data.status == 0) {
                                id = data.details.id;

                                var index = $.inArray(id, shipment_ids);

                                if (index === -1) {
                                    var rowNo = table.rows().count();
                                    var new_row = table.row.add([rowNo + 1, data.details.tracking_number,data.details.city,data.details.hub, data.details.shipper, data.details.amount,data.details.rider, remove_button]).draw().node();
                                    $(new_row).css('color', 'white');
                                    $(new_row).css('background-color', 'orange');
                                    new_row.id = data.details.id;
                                    table.draw(false);
                                    table.order([0, 'desc']).draw();
                                    scan_sound(1);
                                    shipment_ids.push(data.details.id);
                                    if(all_shipment_item_ids.length == 0){
                                        all_shipment_item_ids = shipment_item_ids;
                                    }
                                    else{
                                        all_shipment_item_ids.concat(shipment_item_ids);
                                    }
                                    $('#add_shipment_form button.add').prop('disabled', false);

                                    $('#arrival_of_shipments_form button.confirm').prop('disabled', false);

                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                }
                                $('#tryAndbuyModal').modal('hide');
                                // shipment_item_ids = [];
                                // try_and_buy_table.clear().draw();
                            }
                            else{
                                $('#add_shipment_form button.add').prop('disabled', false);
                                scan_sound(2);
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    });
                }
            });


            $('#tryAndbuyModal').on('hide.bs.modal', function (e) {
                $('#scan_try_and_buy_tracking_number').val('');
                shipment_item_ids = [];
                try_and_buy_table.clear().draw();
            });

            $('#arrival_of_shipments_form').bind('submit', function(e) {
                e.preventDefault();

                $('#add_shipment_weight_form input.shipment_ids').val(shipment_ids);

                // $('#ShipmentWeightModal').modal('show');
            });

            $('#arrival_of_shipments_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    $('#arrival_of_shipments_form input.shipment_ids').val(shipment_ids);
                    swal({
                        text: 'Are you sure, you want to Receive these Shipments?',
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
                    }).then(function(confirm) {
                        if (confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Shipments are being marked arrived!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            blockPagePermanently();
                            form.submit();
                        }
                    });
                }
            });


            $('#datatable tbody').on('click', 'tr td.remove button', function() {
                var parent = $(this).parents('tr');
                var id = parseInt(parent.attr('id'));

                table.row(parent).remove();
                table.draw(false);

                var index = $.inArray(id, shipment_ids);

                if (index !== -1) {
                    shipment_ids.splice(index, 1);

                    if (shipment_ids.length == 0) {
                        $('#arrival_of_shipments_form button.confirm').prop('disabled', true);
                    }
                }

                toastr.success('Shipment has been removed', 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                {{--$.ajax({--}}
                {{--    url: '{!! route('admin.pickups.receive.shipment_remove') !!}',--}}
                {{--    method: 'POST',--}}
                {{--    data: {--}}
                {{--        'id': id,--}}
                {{--        '_token': '{{ csrf_token() }}'--}}
                {{--    },--}}
                {{--    timeout: 10000,--}}
                {{--    error: function (data) {--}}
                {{--        toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
                {{--    },--}}
                {{--    success: function (data) {--}}
                {{--        if (data.status == 0) {--}}
                {{--            table.row(parent).remove();--}}
                {{--            table.draw(false);--}}

                {{--            var index = $.inArray(id, shipment_ids);--}}

                {{--            if (index !== -1) {--}}
                {{--                shipment_ids.splice(index, 1);--}}

                {{--                if (shipment_ids.length == 0) {--}}
                {{--                    $('#arrival_of_shipments_form button.confirm').prop('disabled', true);--}}
                {{--                }--}}
                {{--            }--}}

                {{--            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
                {{--        }--}}
                {{--        else {--}}
                {{--            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
                {{--        }--}}
                {{--    }--}}
                {{--});--}}
            });

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

            $('#add_shipment_pieces_form input.scan_piece').focus();

            var piece_table = $('#piece_datatable').DataTable({
                dom: 'ltipr',
                paging:false,
                autoWidth: false,
                columns: [
                    {orderable: false, searchable: false, name: 'piece_serial_number', class: 'align-middle serial_number'},
                    {name: 'piece_id', class: 'align-middle piece_id', orderable: false, searchable: false},
                    {name: 'piece_tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'piece_remove', class: 'align-middle remove', sortable: false, orderable: false, searchable: false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            $('#scan_piece').on('change', function () {
                var item = parseInt($(this).val());
                $('#scan_piece').val('').focus();
                if(item){
                    var new_item_index = $.inArray(item, shipment_piece_ids);
                    if (new_item_index === -1) {
                        var shipment_id = $('#piece_shipment_id').val();
                        var shipment_tracking_number = $('#piece_tracking_number').val();
                        var shipment_piece_count = $('#piece_shipment_count').val();
                        $.ajax({
                            url: '{!! route('retail.receive.shipment.piece.piece_details') !!}',
                            method: 'POST',
                            data: {
                                'shipment_id': shipment_id,
                                'piece_id': item,
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 10000,
                            error: function (data) {
                                toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            },
                            success: function (data) {
                                if(data.status == 0){
                                    var piece_remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
                                    var piece_rowNo = piece_table.rows().count();
                                    piece_table.row.add([piece_rowNo + 1, data.scanned_shipment_piece, shipment_tracking_number, piece_remove_button]).node().id = data.scanned_shipment_piece;
                                    piece_table.draw(false);
                                    piece_table.columns.adjust().draw();
                                    scan_sound(1);
                                    shipment_piece_ids.push(data.scanned_shipment_piece);
                                    var check = parseInt(piece_rowNo) + 1;
                                    if(parseInt(shipment_piece_count) === parseInt(check)){
                                        $('#scan_piece_tracking_number').prop('disabled', false);
                                        $('#piece_confirm').prop('disabled', false);
                                    }
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        });
                    }
                    else{
                        toastr.error('Shipment Item has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });
            $('#piece_datatable tbody').on('click', 'tr td.remove button', function() {
                var parent = $(this).parents('tr');
                var id = parseInt(parent.attr('id'));
                var index = $.inArray(id, shipment_piece_ids);
                if (index !== -1) {
                    piece_table.row(parent).remove();
                    piece_table.draw(false);
                    var piece_rowNo = piece_table.rows().count();
                    shipment_piece_ids.splice(index, 1);
                    var shipment_piece_count = $('#piece_shipment_count').val();
                    var check = parseInt(piece_rowNo);
                    if(parseInt(shipment_piece_count) !== parseInt(check)){
                        $('#scan_piece_tracking_number').prop('disabled', true);
                        $('#piece_confirm').prop('disabled', true);
                    }
                }
            });


            $('#add_shipment_pieces_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    var shipment_id = $('#piece_shipment_id').val();
                    var tracking_number = $(form).find('input.scan_piece_tracking_number').val();
                    $.ajax({
                        url: '{!! route('retail.receive.shipment.piece.shipment_details') !!}',
                        method: 'POST',
                        data: {
                            'tracking_number': tracking_number,
                            '_token': '{{ csrf_token() }}'
                        },
                        timeout: 10000,
                        error: function (data) {
                            form.reset();
                            $('#add_shipment_form button.add').prop('disabled', false);
                            scan_sound(2);
                            toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        },
                        success: function (data) {
                            form.reset();
                            remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';

                            if (data.status == 0) {
                                var id = data.details.id;

                                var index = $.inArray(id, shipment_ids);

                                if (index === -1) {
                                    var rowNo = table.rows().count();
                                    var new_row = table.row.add([rowNo + 1, data.details.tracking_number,data.details.city ,data.details.hub, data.details.shipper, data.details.amount,data.details.rider,remove_button]).draw().node();
                                    new_row.id = data.details.id;
                                    table.draw(false);
                                    table.order([0, 'desc']).draw();

                                    scan_sound(1);
                                    shipment_ids.push(data.details.id);
                                    if(all_shipment_item_ids.length == 0){
                                        all_shipment_piece_ids = shipment_piece_ids;
                                    }
                                    else{
                                        all_shipment_piece_ids.concat(shipment_piece_ids);
                                    }
                                    $('#add_shipment_form button.add').prop('disabled', false);

                                    $('#arrival_of_shipments_form button.confirm').prop('disabled', false);

                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                }
                                $('#ShipmentPiecesModal').modal('hide');
                            }
                            else{
                                $('#add_shipment_form button.add').prop('disabled', false);
                                scan_sound(2);
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    });
                }
            });

            $('#ShipmentPiecesModal').on('hide.bs.modal', function (e) {
                $('#scan_piece_tracking_number').val('');
                shipment_piece_ids = [];
                piece_table.clear().draw();
            });
        });

        function camera_scan_detected(tracking_number) {
            $('#add_shipment_form input.tracking_number').val(tracking_number);

            if (!$('#add_shipment_form input.volumetric_weight').is(':checked')) {
                $('#add_shipment_form input.weight').focus();
            }
            else {
                $('#add_shipment_form input.length').focus();
            }
        }
    </script>
@endsection