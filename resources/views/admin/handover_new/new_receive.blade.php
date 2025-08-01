@extends('admin.layout.master')

@section('title', 'Receive Handover Note')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Receive Handover Bags
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <!-- <div id="camera_scan" class="d-none">
                                <div id="camera_view" class="camera_view"></div>
                            </div> -->

                            <div class="form-inline mb-1 justify-content-center">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <select name="bag_number" id="bag_number" class="form-control select2">
                                                @foreach ($handover_bag_numbers as $bag_number)
                                                    <option value="{{ $bag_number->id }}">{{ $bag_number->bag_number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
    
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control" id="shipment_type" placeholder="Shipment Type">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="my-4 d-none" id="shipment_type_datatable_container">
                                <div class="text-center" id="shipment_text">
                                    
                                </div>
                                <table class="table table-bordered datatable" id="shipment_type_datatable">
                                    <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">Tracking Number</th>
                                            <th class="border-primary border-darken-1">Shipper</th>
                                            <th class="border-primary border-darken-1">Phone No</th>
                                            <th class="border-primary border-darken-1">Pickup Date</th>
                                            <th class="border-primary border-darken-1">Special Instruction</th>
                                        </tr>
                                        </thead>
                                </table>
                            </div>

                            <form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="form-group d-none" id="scan_tracking_number_div">
                                    <input type="text" id="scan_tracking_number" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                                    <!-- <div class="d-inline-block ml-1">
                                        <a href="#" id="camera_scan_initiate" tabindex="-1">
                                            <i class="ft-camera h1"></i>
                                        </a>
                                    </div> -->
                                </div>
                                <div class="form-group ml-1 d-none" id="scan_button">
                                    <button type="submit" name="add" class="btn btn-primary add" value="Scan">Scan</button>
                                </div>
                            </form>

                            <div class="d-none" id="scan_tracking_numebr_table">
                                <table class="table table-bordered datatable" id="datatable" style="width:100%; z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1" id="shipment_verified_cn">Shipment Verified CN</th>
                                        <th class="border-primary border-darken-1">Tracking Number</th>
                                        <th class="border-primary border-darken-1">Shipper</th>
                                        <th class="border-primary border-darken-1">Phone No</th>
                                        <th class="border-primary border-darken-1">Pickup Date</th>
                                        <th class="border-primary border-darken-1">Special Instruction</th>
                                        {{-- <th class="border-primary border-darken-1"></th> --}}
                                    </tr>
                                    </thead>
                                </table>
                            </div>


                            <form id="arrival_of_shipments_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.handover.receive.store_new') }}" novalidate="novalidate">
                                {{ csrf_field() }}
    
                                <input type="hidden" name="shipment_ids" class="shipment_ids">
                                <input type="hidden" name="handover_id" class="handover_id">
                                
                                <div class="form-group ml-1 d-none" id="confirm_button">
                                    <button type="submit" name="confirm" class="btn btn-primary confirm" value="Confirm" disabled="disabled">Confirm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- Shipment Pieces Modal --}}
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

    <style>
        #shipment_type_datatable th
        /* #shipment_type_datatable td */
        {
            text-align: left;
            vertical-align: middle;
            width: 14.28%;
        }

        #shipment_type_datatable th {
            font-weight: bold;
        }

        #shipment_type_datatable_wrapper {
            width: 100%;
        }

        #shipment_type_datatable {
            table-layout: fixed;
            width: 100% !important;
        }

        #datatable th
        /* #datatable td */
        {
            text-align: left;
            vertical-align: middle;
            width: 12.5%;
        }

        #datatable th {
            font-weight: bold;
        }

        #datatable_wrapper {
            width: 100%;
        }

        #datatable {
            table-layout: fixed;
            width: 100% !important;
        }

        .dataTables_empty {
            text-align: center;
            vertical-align: middle;
        }

        #datatable td .dataTables_empty{
            text-align: center !important;
        }




    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/detectActions.js')}}" type="text/javascript"></script>

    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var bag_number = $('#bag_number');
            var scan_tracking_number = $('#scan_tracking_number');
            var shipment_type_datatable = $('#shipment_type_datatable');
            var shipment_type_datatable_container = $('#shipment_type_datatable_container');
            var scan_tracking_numebr_table = $('#scan_tracking_numebr_table');
            var scan_tracking_number_div = $('#scan_tracking_number_div');
            var scan_button = $('#scan_button');
            var confirm_button = $('#confirm_button');
            // scan_tracking_number.prop('disabled', true);

            bag_number
            .prepend('<option value="" selected="selected"></option>')
            .select2({
                placeholder:'Select Bag Number',
                width: '200px',
                allowClear:true,
                // ajax: {
                //     dataType: 'json',
                //     url:  '{!! route('admin.handover.receive.bag_number_dropdown') !!}',
                //         data: function (params) {
                //             return {
                //                 search: params.term,
                //             }
                //         },
                //         processResults: function (data) {
                //             return {
                //                 results: data
                //             };
                //         },
                //     delay: 200,
                // }
            });

            var bag = null;

            var shipment_type_datatable = $('#shipment_type_datatable').DataTable({
                dom: 'ltipr',
                scrollX: false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                rowId: 'id',
                ajax: {
                    url: '{{ route('admin.handover.receive.handover_shipment_type') }}',
                    type: "GET",
                    data: function (d) {
                        d.bag_number = bag;
                    }
                },
                columns: [
                    { 
                        name: 'serial_number', 
                        class: 'align-middle serial_number', 
                        orderable: false, 
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { 
                        name: 'tracking_number', 
                        class: 'align-middle tracking_number', 
                        data: 'tracking_number',
                        orderable: false, 
                        searchable: false 
                    },
                    { 
                        name: 'shipper_name', 
                        class: 'align-middle shipper_name',
                        data: 'shipper_name',
                        orderable: false, 
                        searchable: false 
                    },
                    { 
                        name: 'consignee_phone_number_1', 
                        class: 'align-middle consignee_phone_number_1',
                        data: 'consignee_phone_number_1',
                        orderable: false, 
                        searchable: false 
                    },
                    { 
                        name: 'pickup_date', 
                        class: 'align-middle pickup_date',
                        data: 'pickup_date', 
                        orderable: false, 
                        searchable: false 
                    },
                    { 
                        name: 'special_instructions', 
                        class: 'align-middle special_instructions', 
                        data: 'special_instructions',
                        orderable: false, 
                        searchable: false 
                    }
                ],

                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },

                drawCallback: function(settings) {
                    var json = settings.json;
                    if (json && json.data.length > 0) {
                        var type = json.data[0].shipment_type_text;
                        var $container = $('#shipment_text');
                        var $h1 = $container.find('#shipmentType');
                        if ($h1.length === 0) {
                            $h1 = $('<h1>', { id: 'shipmentType' }).appendTo($container);
                            $h1.css({
                                'font-size': '24px',
                                'font-weight': 'bold'
                            });
                        }
                        $h1.text(type);
                        if (type === 'Shipment Type is Normal') {
                            $h1.css('color', '#6fed6f');
                        } else if (type === 'Shipment Type is Return') {
                            $h1.css('color', '#e96a6a');
                        } else {
                            $h1.css('color', 'black');
                        }
                        var trimmed_type = type.split(' ').pop();
                        $('#shipment_type').val(trimmed_type);
                        scan_tracking_number.prop('disabled', false);
                    } else {
                        scan_tracking_number.prop('disabled', true);
                    }
                }
            });

            let bagChangeCount = 0;
            bag_number.on('change', function() {
                bag = $(this).val();
                bagChangeCount++;
                table.clear().draw();
                if (bagChangeCount < 2){
                    $.ajax({
                        type: "GET",
                        url:  '{!! route('admin.handover.receive.check_full_bag') !!}',
                        data: {
                            bag_number: bag
                        },
                        success: function (response) {
                            if (response.status == 1 && response.error) {
                                scan_sound(2);
                                toastr.error(response.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            else {
                                if (bag) {
                                    shipment_type_datatable.draw();
                                    shipment_type_datatable_container.removeClass('d-none');
                                    scan_tracking_numebr_table.removeClass('d-none');
                                    scan_tracking_number_div.removeClass('d-none');
                                    scan_button.removeClass('d-none');
                                    confirm_button.removeClass('d-none');
                                } else {
                                    shipment_type_datatable_container.addClass('d-none');
                                    scan_tracking_numebr_table.addClass('d-none');
                                    scan_tracking_number_div.addClass('d-none');
                                    scan_button.addClass('d-none');
                                    confirm_button.addClass('d-none');
                                }
                            }
                        }
                    });
                } else if (bagChangeCount === 2) {
                    // Second bag change, reload the page
                    location.reload();
                }

            });

            var shipment_ids = [];
        
            $('#add_shipment_form input.tracking_number').focus();

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'shipper', class: 'align-middle shipper', orderable: false, searchable: false},
                    {name: 'phone_number', class: 'align-middle phone_number', orderable: false, searchable: false},
                    {name: 'pickup_date', class: 'align-middle pickup_date', orderable: false, searchable: false},
                    {name: 'special_instructions', class: 'align-middle special_instructions', orderable: false, searchable: false},
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
                    $('#scan_tracking_number').on('keydown', function(){
                        $('#add_shipment_form button.add').prop('disabled', false);
                    });
                    var tracking_number = $(form).find('input.tracking_number').val();
                    var handover = $('#bag_number').val();
                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        $.ajax({
                            url: '{!! route('admin.handover.receive.shipment_details_new') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                'action' : window.lastAction,
                                'handover': handover,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                form.reset();
                                $('#add_shipment_form input.tracking_number').val('').focus();
                                remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
                                if (data.status == 0) {
                                    id = data.details.id;
                                    var index = $.inArray(id, shipment_ids);
                                    if (index === -1) {
                                        var rowNo = table.rows().count();
                                        var verificationStatus = data.details.verification_status;
                                        var backgroundColor = '';
                                        if (verificationStatus === 'Verified') {
                                            backgroundColor = 'background-color: rgb(111, 237, 111);; color: white;';
                                        } else if (verificationStatus === 'Excess') {
                                            backgroundColor = 'background-color: rgb(233, 106, 106); color: white;';
                                        }

                                        // table.row.add([rowNo + 1, data.details.verification_status, data.details.tracking_number, data.details.shipper, data.details.phone_number, data.details.pickup_date,data.details.special_instructions, remove_button]).node().id = data.details.id;
                                        var rowNode = table.row.add([
                                            rowNo + 1,
                                            data.details.verification_status,
                                            data.details.tracking_number,
                                            data.details.shipper,
                                            data.details.phone_number,
                                            data.details.pickup_date,
                                            data.details.special_instructions,
                                            remove_button
                                        ]).node();
                                        
                                        // Set the background color for the verification_status cell
                                        $(rowNode).find('td:eq(1)').attr('style', backgroundColor);

                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                        shipment_ids.push(data.details.id);
                                        $('#add_shipment_form button.add').prop('disabled', false);
                                        $('#arrival_of_shipments_form button.confirm').prop('disabled', false);
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }

                                //Open Modal if Shipment Receive Pieces are greater than 1 
                                else if(data.status == 3){
                                    $('#scan_piece_tracking_number').prop('disabled', true);
                                    $('#piece_confirm').prop('disabled', true);

                                    $('#add_shipment_form button.add').prop('disabled', false);
                                    $('#arrival_of_shipments_form button.confirm').prop('disabled', false);


                                    $('#piece_shipment_id').val(data.details.id);
                                    $('#piece_tracking_number').val(data.details.tracking_number);
                                    $('#piece_shipment_count').val(data.details.pieces_count);
                                    $('#total_piece_count').html('Total Shipment Pieces: ' + data.details.pieces_count);
                                    $('#ShipmentPiecesModal').modal('show');
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                    $('#piece_confirm').click(function() {
                                        id = data.details.id;
                                        var index = $.inArray(id, shipment_ids);
                                        if (index === -1) {
                                            var piece_remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
                                            var rowNo = table.rows().count();
                                            table.row.add([rowNo + 1, data.details.tracking_number, data.details.shipper, data.details.phone_number, data.details.pickup_date,data.details.special_instructions, piece_remove_button]).node().id = data.details.id;
                                            table.draw(false);
                                            table.order([0, 'desc']).draw();
                                            scan_sound(1);
                                            shipment_ids.push(data.details.id);
                                            $('#ShipmentPiecesModal').modal('hide');
                                            $('#add_shipment_form button.add').prop('disabled', false);
                                            $('#arrival_of_shipments_form button.confirm').prop('disabled', false);
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                        }
                                    });
                                }

                                else {
                                    $('#add_shipment_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                    else {
                        $('#add_shipment_form button.add').prop('disabled', false);
                        scan_sound(2);
                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            var shipment_piece_ids = [];
            var all_shipment_piece_ids = [];
            
            $('#ShipmentPiecesModal').on('hide.bs.modal', function (e) {
                $('#scan_piece_tracking_number').val('');
                $('#pieces_weight').val('');
                shipment_piece_ids = [];
                piece_table.clear().draw();
            });

            //Scanning Shipment Pieces
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
                            url: '{!! route('admin.v2_pickups.arrival.bulk.piece.piece_details') !!}',
                            method: 'POST',
                            data: {
                                'shipment_id': shipment_id,
                                'piece_id': item,
                                'action' : window.lastAction,
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 10000,
                            error: function (data) {
                                scan_sound(2);
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
                                        $('#pieces_weight').prop('disabled', false);
                                        $('#piece_confirm').prop('disabled', false);
                                    }
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else{
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        });
                    }
                    else{
                        scan_sound(2);
                        toastr.error('Shipment Item has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });

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
                        $('#pieces_weight').prop('disabled', true);
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
                submitHandler: function (form) { }
            });

            $('#arrival_of_shipments_form').bind('submit', function(e) {
                e.preventDefault(); 
                $('#arrival_of_shipments_form input.shipment_ids').val(shipment_ids);
                $('#arrival_of_shipments_form input.handover_id').val(bag);

                var form = this;
                swal({
                    text: 'Are you sure, Select Yes to receive the Handover?',
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
                            text: 'Handovers are being received!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        blockPagePermanently();
                        form.submit();
                    }
                });
            });

            $('#datatable tbody').on('click', 'tr td.remove button', function() {
                var parent = $(this).parents('tr');
                var id = parseInt(parent.attr('id'));

                $.ajax({
                    url: '{!! route('admin.pickups.receive.shipment_remove') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if (data.status == 0) {
                            table.row(parent).remove();
                            table.draw(false);

                            var index = $.inArray(id, shipment_ids);

                            if (index !== -1) {
                                shipment_ids.splice(index, 1);

                                if (shipment_ids.length == 0) {
                                    $('#arrival_of_shipments_form button.confirm').prop('disabled', true);
                                }
                            }

                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }

                        

                        else {
                            scan_sound(2);
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
            });
        });
    </script>
@endsection