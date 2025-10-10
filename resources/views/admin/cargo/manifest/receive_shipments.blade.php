@extends('admin.layout.master')

@section('title', ' Quick Receive Bag Shipment(s)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Quick Receive Bag Shipment(s)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            @if(session('received_html'))
                                <div class="alert alert-success">
                                    {!! session('received_html') !!}
                                </div>
                            @endif
                            @if(session('misrouted_html'))
                                <div class="alert alert-danger">
                                    {!! session('misrouted_html') !!}
                                </div>
                            @endif
                            @if(session('sr_html'))
                                <div class="alert alert-danger">
                                    {!! session('sr_html') !!}
                                </div>
                            @endif
                            @if(session('went_wrong'))
                                <div class="alert alert-danger">
                                    {!! session('went_wrong') !!}
                                </div>
                            @endif
                            @if(session('already_received_shipments_html'))
                                <div class="alert alert-info">
                                    {!! session('already_received_shipments_html') !!}
                                </div>
                            @endif
                            @if(session('rm_html'))
                                <div class="alert alert-info">
                                    {!! session('rm_html') !!}
                                </div>
                            @endif
                            <div id="camera_scan" class="d-none">
                                <div id="camera_view" class="camera_view"></div>
                            </div>

                            <form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <input type="hidden" name="shipment_bag_type" id="shipment_bag_type">
                                <div class="form-group mr-2">
                                    <select name="bag_type" id="bag_type" class="form-control select2" data-rule-required="true">
                                       <option value="1">Normal</option>
                                       <option value="2">Return</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required" disabled>

                                    <div class="d-inline-block ml-1">
                                        <a href="#" id="camera_scan_initiate" tabindex="-1">
                                            <i class="ft-camera h1"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="add" class="btn btn-primary" value="Add">Add</button>
                                </div>
                            </form>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Bag Number</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">Consignee</th>
                                    <th class="border-primary border-darken-1">Amount</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Service Type</th>
                                    <th class="border-primary border-darken-1">Open Box</th>
                                </tr>
                                </thead>
                            </table>

                            <form id="receive_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.cargo_manifest.receive.bag.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input id="bag_type_for_receive" type="hidden" name="bag_type" class="bag_type">
                                <input type="hidden" name="shipment_ids" class="shipment_ids">
                                <input type="hidden" name="open_box_ids" class="open_box_ids" id="open_box_ids">

                                <div class="form-group ml-1">
                                    <button type="submit" name="receive" class="btn btn-primary receive" value="Confirm" disabled="disabled">Receive</button>
                                </div>
                            </form>
                        </div>
                    </div>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/detectActions.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            @if(session('errors'))
            scan_sound(2);
            @endif

            $("#bag_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Bag Type",
                width:'100%',
            }).bind('change', function() {
              $('#shipment_bag_type').val(this.value);
              $('.tracking_number').attr('disabled',false);
              $('#bag_type').attr('disabled',true);
        });



            var shipment_ids = [];
            var shipment_piece_ids = [];
            var all_shipment_piece_ids = [];

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                autoWidth: false,
                paging:false,
                columns: [
                    {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'bag_number', class: 'align-middle bag_number', orderable: false, searchable: false},
                    {name: 'origin', class: 'align-middle origin', orderable: false, searchable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false, searchable: false},
                    {name: 'hub', class: 'align-middle hub', orderable: false, searchable: false},
                    {name: 'consignee', class: 'align-middle consignee', orderable: false, searchable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false, searchable: false},
                    {name: 'shipping_mode', class: 'align-middle shipping_mode', orderable: false, searchable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false, searchable: false},
                    {name: 'open_box', class: 'align-middle open_box', orderable: false},
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();
                    //
                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    var misroute = data[11]; // misroute veriable
                    if(misroute == 1)
                    {
                        // $(row).addClass('alert-danger');
                    }
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#add_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
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
                    var shipment_bag_type = $(form).find('input#shipment_bag_type').val();
                    var shipment_bag_type_route = null;

                    $('#bag_type_for_receive').val(shipment_bag_type);

                    if(shipment_bag_type == 1)
                    {
                        shipment_bag_type_route = '{!! route('admin.cargo_manifest.receive.bag.details') !!}';
                    }
                    else
                    {
                        shipment_bag_type_route = '{!! route('admin.cargo_manifest.receive.bag.details.return') !!}';
                    }

                    form.reset();
                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        blockPagePermanently();

                        $.ajax({
                            {{--url: '{!! route('admin.cargo_manifest.receive.bag.details') !!}',--}}
                            url: shipment_bag_type_route,
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                'bag_type': shipment_bag_type,
                                'action': window.lastAction,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 0) {

                                    id = data.details.id;

                                    var index = $.inArray(id, shipment_ids);

                                    if(data.is_open_box==1){
                                        var open_box = '<input type="checkbox" checked="checked" class="form-control open_box" name="open_box['+ data.shId+']">';

                                    }else{

                                        var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';
                                    }

                                    if (index === -1) {
                                        var rowNo = table.rows().count();

                                        table.row.add([rowNo + 1, data.details.tracking_number, data.details.bag_number, data.details.origin, data.details.destination, data.details.hub, data.details.consignee, data.details.amount, data.details.shipping_mode, data.details.service_type
                                            ,open_box,data.details.misroute
                                        ])
                                            .node().id = data.details.id;
                                        // var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                        shipment_ids.push(data.details.id);

                                        $('#information .scanned').html(shipment_ids.length);

                                        $('#add_shipment_form button.add').prop('disabled', false);

                                        $('#receive_form .receive').prop('disabled', false);
                                        UnblockPagePermanently();
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else if(data.status == 2){
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
                                    UnblockPagePermanently();
                                }
                                else {
                                    UnblockPagePermanently();
                                    $('#add_shipment_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                    else {
                        UnblockPagePermanently();
                        $('#add_shipment_form button.add').prop('disabled', false);
                        scan_sound(2);
                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            $('#receive_form').bind('submit', function(e) {
                e.preventDefault();

                var form = this;
                open_box_ids = [];
                table.rows().every(function (index) {
                    var node = $(this.node());
                    if (node.find('td.open_box input').is(':checked')) {
                        open_box_ids.push(parseInt(node.attr('id')));
                    }
                });

                $('#receive_form input.shipment_ids').val(shipment_ids);
                $('#receive_form input#open_box_ids').val(open_box_ids);



                var html = 'Are you sure, you want to confirm Shipment(s) as received?';

                content = document.createElement('div');
                content.innerHTML = html;

                swal({
                    content: content,
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
                        form.submit();
                    }
                });
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
                            url: '{!! route('admin.cargo_manifest.bags.piece_details') !!}',
                            method: 'POST',
                            data: {
                                'shipment_id': shipment_id,
                                'piece_id': item,
                                'screen_location_id': 20,
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
                    shipment_ids.push(shipment_id);
                    var tracking_number = $(form).find('input.scan_piece_tracking_number').val();
                    $.ajax({
                        url: '{!! route('admin.cargo_manifest.receive.bag.details') !!}',
                        method: 'POST',
                        data: {
                            'tracking_number': tracking_number,
                            'pieces_confirm': 1,
                            'bag_type':  $('#shipment_bag_type').val(),
                            'action': window.lastAction,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {
                                id = data.details.id;

                                var index = $.inArray(id, shipment_ids);

                                if(data.is_open_box==1){
                                    var open_box = '<input type="checkbox" checked="checked" class="form-control open_box" name="open_box['+ data.shId+']">';

                                }else{

                                    var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';
                                }


                                if (index === -1) {
                                    var rowNo = table.rows().count();

                                    table.row.add([rowNo + 1, data.details.tracking_number, data.details.bag_number, data.details.origin, data.details.destination, data.details.hub, data.details.consignee, data.details.amount, data.details.shipping_mode, data.details.service_type,open_box]).node().id = data.details.id;
                                    table.draw(false);
                                    table.order([0, 'desc']).draw();
                                    scan_sound(1);
                                    shipment_ids.push(data.details.id);

                                    $('#information .scanned').html(shipment_ids.length);

                                    $('#add_shipment_form button.add').prop('disabled', false);

                                    $('#receive_form .receive').prop('disabled', false);
                                    UnblockPagePermanently();
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            }
                            else {
                                UnblockPagePermanently();
                                $('#add_shipment_form button.add').prop('disabled', false);
                                scan_sound(2);
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#ShipmentPiecesModal').modal('hide');
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

            $('#add_shipment_form').trigger('submit');
        }
    </script>
@endsection