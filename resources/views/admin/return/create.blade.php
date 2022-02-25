
@extends('admin.layout.master')
@section('title','Create Return Note')

@section('content')
    <h1 class="mb-1">
        Create Return Note
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="camera_scan" class="d-none">
                    <div id="camera_view" class="camera_view"></div>
                </div>

                <form action="#" id="return_note_form">
                <div class="row justify-content-center mb-2">
                    <input type="hidden" name="shipper_id" id="shipper_id">
                    <div class="col-3">
                        <fieldset>
                            <input type="text" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                        </fieldset>
                    </div>

                    <div class="col-1">
                        <a href="#" id="camera_scan_initiate" class="d-block text-right" tabindex="-1">
                            <i class="ft-camera h1"></i>
                        </a>
                    </div>
                </div>
                </form>
                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="hub_name" id="hub_name" class="form-control select2" required >
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                            <div class="danger" id="rider_error" style="display:none;">This field is required</div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="rider_name" id="rider_name" class="form-control select2" required >

                            </select>
                            <div class="danger" id="rider_error" style="display:none;">This field is required</div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="route" id="route" class="form-control select2" required>
                                @foreach($routes as $route)
                                    <option value="{{$route->id}}">{{$route->code}} ({{$route->start}} to {{$route->end}})</option>
                                @endforeach
                            </select>
                            <div class="danger" id="route_error" style="display:none;">This field is required</div>
                        </fieldset>
                    </div>

                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Open Box</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
                <form id="create_return_note_form" class="" method="post" action="{{ route('admin.return.create.note.submit') }}">
                    <div class="row justify-content-center">
                        @csrf
                        <input type="hidden" name="hub_id" id="hub_id">
                        <input type="hidden" name="shipment_ids" id="shipment_ids">
                        <input type="hidden" name="open_box_ids" id="open_box_ids">
                        <input type="hidden" name="rider_id" id="selected_rider_id">
                        <input type="hidden" name="route_id" id="selected_route_id">
                        <div class="col-3">
                            <button type="submit" name="submit_and_print" value="submit_and_print" class="btn btn-primary btn-block ">Submit &amp; Print</button>

                        </div>

                    </div>
                </form>
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
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            @if(session('print'))
            var pid = '{{ session('print') }}';
            print(pid);
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
            @endif

            $('#add_shipment_pieces_form input.scan_piece').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            var shipment_ids = [];
            var shipment_piece_ids = [];
            var all_shipment_piece_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                autoWidth : false,
                scrollX: true,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false},
                    {name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
                    {name: 'phone', class: 'align-middle phone', orderable: false},
                    {name: 'address', class: 'align-middle address', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'status', class: 'align-middle status', orderable: false},
                    {name: 'open_box', class: 'align-middle open_box', orderable: false},
                    {name: 'action', class: 'align-middle action',orderable: false,searchable:false}
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

                    this.api().table().columns.adjust();
                }
            });

            $('#hub_name').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub*',
            });
            $('#rider_name').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider*',
            });
            $('#route').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Route*',
            });
            $("#hub_name").on('change',function () {
                var hub_id = $(this).val();
                $.ajax({
                    url: '{!! route('admin.return.create.riders.hub') !!}',
                    method: 'GET',
                    data: {
                        'hub_id': hub_id,
                    }
                }).done(function(data) {
                    console.log(data);
                    html = "";
                    $.each(data,function (i,v) {
                        if(v.trax_id)
                        html +=  `<option value="${v.id}" data-id="${v.route_id}">${v.name} - ${v.trax_id}</option>`
                        else
                        html +=  `<option value="${v.id}" data-id="${v.route_id}">${v.name}</option>`
                    });
                    $('#rider_name').html(html);
                    $('#rider_name').val(null).trigger('change');

                });
            });

            $('#rider_name').on('change',function () {
                var route = $(this).find(":selected").data("id");
                $('#route').val(route).trigger('change');
            });
            $('input#scan_tracking').focus();
            $('#return_note_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = scan.val();
                var hub_id = $('#hub_id').val();
                var shipper_id = $('#shipper_id').val();

                if (tracking != '') {
                    scan.attr('disabled', true);
                    if(table.row().count() == 0) {
                        blockPagePermanently();
                        $.ajax({
                            url: '{{route('admin.return.create.shipment_details')}}',
                            type: 'POST',
                            data: {
                                'tracking': tracking,
                                'shipper_id': shipper_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {

                            if (data.status == 1) {
                                UnblockPagePermanently();
                                scan_sound(2);
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            else if(data.status == 2){
                                $('#scan_piece_tracking_number').prop('disabled', true);
                                $('#scan_piece_tracking_number').val(tracking);
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
                                $('#shipper_id').val(data.shipper_id);
                                UnblockPagePermanently();
                            } else {
                                var rowNo = table.rows().count();
                                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger returnnoterow"><i class="la la-close"></i></a>';
                                var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';

                                var row = table.row.add([rowNo + 1, data.tracking_number, data.destination, data.consignee_name, data.phone, data.address, data.amount, data.service_type, data.shipment_status, open_box, remove]).node().id = data.shId;
                                table.draw(false);
                                $('tr#'+row).attr('class',data.class);
                                scan_sound(1);
                                UnblockPagePermanently();
                                shipment_ids.push(data.shId);
                                $('#hub_id').val(data.hub);
                                $('#shipper_id').val(data.shipper_id);
                            }

                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        });
                    }
                    else {
                        if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking)) === -1) {
                            blockPagePermanently();
                            $.ajax({
                                url: '{{route('admin.return.create.shipment_details')}}',
                                type: 'POST',
                                data: {
                                    'tracking': tracking,
                                    'hub_id':hub_id,
                                    'shipper_id':shipper_id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                                .done(function (data) {
                                if (data.status == 1) {
                                    UnblockPagePermanently();
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                else if(data.status == 2){
                                    $('#scan_piece_tracking_number').prop('disabled', true);
                                    $('#scan_piece_tracking_number').val(tracking);
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
                                    $('#shipper_id').val(data.shipper_id);
                                    UnblockPagePermanently();
                                }
                                else {
                                    var rowNo = table.rows().count();
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger returnnoterow"><i class="la la-close"></i></a>';
                                    var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';

                                    var row = table.row.add([rowNo + 1, data.tracking_number, data.destination, data.consignee_name, data.phone, data.address, data.amount, data.service_type, data.shipment_status, open_box, remove]).node().id = data.shId;
                                    table.draw(false);
                                    $('tr#'+row).attr('class',data.class);
                                    scan_sound(1);
                                    UnblockPagePermanently();
                                    shipment_ids.push(data.shId);
                                    $('#shipper_id').val(data.shipper_id);
                                    table.order([0, 'desc']).draw();
                                }

                                scan.val('');
                                scan.attr('disabled', false);
                                scan.focus();
                            });
                        } else {
                            scan_sound(2);
                            var error = 'Tracking Number already scanned!';
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });

                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        }
                    }
                }
            });
            $('body').on('click','a.returnnoterow',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, shipment_ids);
                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                }
                console.log(shipment_ids.length);
                if(shipment_ids.length < 1){
                    $("#shipper_id").val('');
                }
                table.row( $(this).parents('tr') ).remove().draw();
            });


            $('#create_return_note_form').bind('submit', function(event) {
                var this_form = this;
                event.preventDefault();
                var count = table.rows().count();
                var errors = 0;
                var rider = $('#rider_name').val();
                var route = $('#route').val();
                if (rider !== '' && rider !== null) {

                    $('#rider_error').css('display', 'none');
                } else {
                    var error = "Rider not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#rider_error').css('display', 'block');
                }
                if (route !== '' && route !== null) {

                    $('#route_error').css('display', 'none');
                } else {
                    var error = "Route not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#route_error').css('display', 'block');
                }

                if(count > 0) {
                    if (errors == 0) {
                        $('#create_return_note_form button[type="submit"]').attr('disabled', 'disabled');
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to create the Return Note!',
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
                                open_box_ids = [];
                                table.rows().every(function(index) {
                                    var node = $(this.node());
                                    if(node.find('td.open_box input').is(':checked')){
                                        open_box_ids.push(parseInt(node.attr('id')));
                                    }
                                });
                                $('#create_return_note_form input#open_box_ids').val(open_box_ids);
                                $('#create_return_note_form input#shipment_ids').val(shipment_ids);
                                $('#create_return_note_form input#selected_rider_id').val(rider);
                                $('#create_return_note_form input#selected_route_id').val(route);

                                this_form.submit();

                            }
                        });

                    }
                }else{
                    var error = "Select at-least one shipment!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                }
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
                            url: '{!! route('admin.return.create.shipment.piece_details') !!}',
                            method: 'POST',
                            data: {
                                'shipment_id': shipment_id,
                                'piece_id': item,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function(data) {
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
                    var hub_id = $('#hub_id').val();
                    var tracking_number = $(form).find('input.scan_piece_tracking_number').val();
                    if(hub_id == null || hub_id == ''){
                        $.ajax({
                            url: '{!! route('admin.return.create.shipment_details') !!}',
                            method: 'POST',
                            data: {
                                'tracking': tracking_number,
                                'pieces_confirm': 1,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {

                                if (data.status == 1) {
                                    UnblockPagePermanently();
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                } else {
                                    var rowNo = table.rows().count();
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger returnnoterow"><i class="la la-close"></i></a>';
                                    var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';

                                    var row = table.row.add([rowNo + 1, data.tracking_number, data.destination, data.consignee_name, data.phone, data.address, data.amount, data.service_type, data.shipment_status, open_box, remove]).node().id = data.shId;
                                    table.draw(false);
                                    $('tr#'+row).attr('class',data.class);
                                    scan_sound(1);
                                    UnblockPagePermanently();
                                    shipment_ids.push(data.shId);
                                    $('#hub_id').val(data.hub);
                                }
                            });

                    }
                    else{
                        blockPagePermanently();
                        // $('#hub_id').val('');
                        $.ajax({
                            url:'{{route('admin.return.create.shipment_details')}}',
                            type:'POST',
                            data: {
                                'tracking':tracking_number,
                                'hub_id':hub_id,
                                'pieces_confirm':1,
                                '_token':'{!! csrf_token() !!}'
                            }
                        })
                            .done(function (data) {

                                if (data.status == 1) {
                                    UnblockPagePermanently();
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                else {
                                    var rowNo = table.rows().count();
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger returnnoterow"><i class="la la-close"></i></a>';
                                    var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';

                                    var row = table.row.add([rowNo + 1, data.tracking_number, data.destination, data.consignee_name, data.phone, data.address, data.amount, data.service_type, data.shipment_status, open_box, remove]).node().id = data.shId;
                                    table.draw(false);
                                    $('tr#'+row).attr('class',data.class);
                                    scan_sound(1);
                                    UnblockPagePermanently();
                                    shipment_ids.push(data.shId);
                                    table.order([0, 'desc']).draw();
                                }
                            });
                    }
                    $('#ShipmentPiecesModal').modal('hide');
                    return false;
                }
            });

            $('#ShipmentPiecesModal').on('hide.bs.modal', function (e) {
                $('#scan_piece_tracking_number').val('');
                shipment_piece_ids = [];
                piece_table.clear().draw();
            });
        });

        function camera_scan_detected(tracking_number) {
            $('#scan_tracking').val(tracking_number);

            $('#return_note_form').trigger('submit');
        }
    </script>
@endsection