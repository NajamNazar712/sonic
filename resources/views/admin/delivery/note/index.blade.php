@extends('admin.layout.master')
@section('title','Create Delivery Note')

@section('content')
    <h1 class="mb-1">
        Create Delivery Note
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="camera_scan" class="d-none">
                    <div id="camera_view" class="camera_view"></div>
                </div>


                <div class="row mb-2 justify-content-center">
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="operation_rider_type" id="operation_rider_type" class="form-control select2" required>
                                @foreach($operation_rider_category as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                            <div class="danger" id="operation_error" style="display:none;">This field is required</div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="rider_name" id="rider_name" class="form-control select2" required>

                            </select>
                            <div class="danger" id="rider_error" style="display:none;">This field is required</div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="route" id="route" class="form-control select2" required>
                              {{--  @foreach($routes as $route)
                                    <option value="{{$route->id}}">{{$route->code}} ({{$route->start}} to {{$route->end}})</option>
                                @endforeach--}}
                            </select>
                            <div class="danger" id="route_error" style="display:none;">This field is required</div>
                        </fieldset>
                    </div>

                </div>
                <div id="camera_scan" class="d-none">
                    <div id="camera_view" class="camera_view"></div>
                </div>

                <form action="#" id="delivery_note_form">
                    <div class="row justify-content-center align-items-center mb-2">
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
                        <input type="hidden" id="rider_id" name="rider_id">
                        <input type="hidden" id="operation_rider_type_id" name="operation_rider_type_id">
                        <input type="hidden" id="route_id" name="route_id">
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Notification</th>
                        <th class="border-primary border-darken-1">Rider Information</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Last Rider Name</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Open Box</th>
                        <th class="border-primary border-darken-1">Consolidation</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
                <form id="create_delivery_note_form" class="" method="post" action="{{ route('admin.delivery.note.create') }}">
                    <div class="row justify-content-center">
                        <div class="col-2 text-center">
                            <label class="font-medium-2 font-weight-bold block">Delivery Note Shipment(s) Order</label>
                            <div class="form-group">
                                <label for="order_checkbox" class="font-medium-2 text-bold-600 mr-1">Default</label>
                                <input type="checkbox" name="order_checkbox" id="order_checkbox" class="switchery order_checkbox" data-color="info" data-size="sm" data-switchery="true">
                                <label for="order_checkbox" class="font-medium-2 text-bold-600 ml-1">Scanned</label>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        @csrf
                        <input type="hidden" name="hub_id" id="hub_id">
                        <input type="hidden" name="operation_rider_type_for_attendance" id="operation_rider_type_for_attendance">
                        <input type="hidden" name="shipment_ids" id="shipment_ids">
                        <input type="hidden" name="open_box_ids" id="open_box_ids">
                        <input type="hidden" name="notification_ids" id="notification_ids">
                        <input type="hidden" name="rider_info_ids" id="rider_info_ids">
                        <input type="hidden" name="selected_rider_id" id="selected_rider_id">
                        <input type="hidden" name="selected_route_id" id="selected_route_id">
                        <input type="hidden" name="special_rider_name" id="special_rider_name">
                        <input type="hidden" name="special_rider_phone" id="special_rider_phone">


                        <div class="col-3">
                            <button type="submit" id="deliveryNoteSubmitBtn" class="btn btn-primary btn-block ">Submit &amp; Print</button>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="SpecialRiderModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SpecialRiderModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Special Rider</h4>

                </div>
                <form id="special_rider_form" class="justify-content-center" novalidate="novalidate">
                    <div class="modal-body text-center">

                        <div class="form-group">
                            <input type="text" name="special_rider_name" id="special_rider_name_input" class="form-control" placeholder="Special Rider Name" data-rule-required="true" data-msg-required="Rider Nume is required">
                        </div>
                        <div class="form-group">
                            <input type="text" name="special_rider_phone" id="special_rider_phone_input" class="form-control phone" placeholder="Special Rider Phone" data-rule-required="true" data-msg-required="Phone Number is required">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary width-100" id="add_special_rider_button">Add</button>
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

    <div class="modal fade" id="OtpModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="OtpModal"
         aria-hidden="true" style="top:30%;">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content col">
                <div class="modal-header text-center">
                    <div class="row align-items-center">
                        <div class="col sonic_logo align-middle text-left">
                            <img src="{{asset('img/sonic_logo_new.png')}}" alt="Sonic" class="d-inline-block mx-auto w-50">
                        </div>

                        <div class="col trax_logo align-middle text-right">
                            <img src="{{asset('img/trax_logo_new.png')}}" alt="Trax" class="d-inline-block mx-auto w-50">
                        </div>
                    </div>
                </div>
                <div class="modal-body  text-center">
                    <div class="row justify-content-center">
                        <div class="form-group form-inline">
                            <input type="text" class="form-control otp" autofocus id="otp_input" placeholder="Enter Verification Code">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button tabindex="-1" type="button" class="btn btn-primary ml-1" id="otp_submit" disabled>Enter</button>
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
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var rider_dncc_check = false;

            $('.phone').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });
            $('#otp_input').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'mask': '999999'
            });
            $('body').on('keyup change','#otp_input',function() {
                if($(this).val().length === 6){
                    $('#otp_submit').attr('disabled', false);
                }
                else{
                    $('#otp_submit').attr('disabled', true);
                }
            });
                    @if(session('print'))
            var pid = '{{ session('print') }}';
            print(pid);
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
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
            var ccd_shipment_ids = [];
            var ccd_tracking_numbers = [];
            var tracking_ids = [];
            var consolidation_ids = [];
            var notification_ids = [];
            var rider_info_ids = [];
            var shipment_piece_ids = [];
            var all_shipment_piece_ids = [];
            var ccd_rider = null;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: 'Select All Notification(s)',
                        enabled: false,
                        className: 'select_all_notifications',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                var notification_box = $(row.node()).find('td.notification input');
                                if (!notification_box.is(':checked')) {
                                    notification_box.prop('checked',true);
                                    var id = parseInt($(notification_box).parents('tr').attr('id'));

                                    var index = $.inArray(id, shipment_ids);
                                    if(notification_ids[index] === 0){
                                        notification_ids[index] = 1;
                                    }
                                }
                            });
                            table.button('.select_none_notifications').enable();
                        }
                    }, {
                        text: 'Unselect All Notification(s)',
                        className: 'select_none_notifications',
                        enabled: false,
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                var notification_box = $(row.node()).find('td.notification input');
                                if (notification_box.is(':checked')) {
                                    notification_box.prop('checked',false);
                                    var id = parseInt($(notification_box).parents('tr').attr('id'));

                                    var index = $.inArray(id, shipment_ids);
                                    if(notification_ids[index] === 1){
                                        notification_ids[index] = 0;
                                    }
                                }

                            });
                            table.button('.select_all_notifications').enable();
                        }
                    },
                    {
                        text: 'Select All Rider Information(s)',
                        enabled: false,
                        className: 'select_all_rider_informations',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                var rider_information_box = $(row.node()).find('td.rider_information input');
                                if (!rider_information_box.is(':checked')) {
                                    rider_information_box.prop('checked',true);
                                    var id = parseInt($(rider_information_box).parents('tr').attr('id'));

                                    var index = $.inArray(id, shipment_ids);
                                    if(rider_info_ids[index] === 0){
                                        rider_info_ids[index] = 1;
                                    }
                                }
                            });
                            table.button('.select_none_rider_informations').enable();
                        }
                    }, {
                        text: 'Unselect Rider Information(s)',
                        className: 'select_none_rider_informations',
                        enabled: false,
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                var rider_information_box = $(row.node()).find('td.rider_information input');
                                if (rider_information_box.is(':checked')) {
                                    rider_information_box.prop('checked',false);
                                    var id = parseInt($(rider_information_box).parents('tr').attr('id'));

                                    var index = $.inArray(id, shipment_ids);
                                    if(rider_info_ids[index] === 1){
                                        rider_info_ids[index] = 0;
                                    }
                                }


                            });
                            table.button('.select_all_rider_informations').enable();
                        }
                    },
                ],
                scrollX: true,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false},
                    {name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
                    {name: 'phone', class: 'align-middle phone', orderable: false},
                    {name: 'notification', class: 'align-middle notification', orderable: false},
                    {name: 'rider_information', class: 'align-middle rider_information', orderable: false},
                    {name: 'address', class: 'align-middle address', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'status', class: 'align-middle status', orderable: false},
                    {name: 'rider_name', class: 'align-middle rider_name', orderable: false},
                    {name: 'remarks', class: 'align-middle remarks', orderable: false},
                    {name: 'open_box', class: 'align-middle open_box', orderable: false},
                    {name: 'consolidation', class: 'align-middle consolidation', orderable: false},
                    {name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });


            $('#rider_name').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider*',
            });

            $('#operation_rider_type').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Category*',
            }).bind('select2:select', function () {
                if(this.value){
                    var id = this.value;
                    $.ajax({
                        url: '{!! route('admin.delivery.note.operation_riders') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'operation_rider_type': this.value,
                        }
                    }).done(function(data){

                        if (data.status == 1) {
                            var html = "";
                            $.each(data.riders, function(key,v) {
                                if(v.trax_id)
                                    html +=  `<option value="${v.id}" data-id="${v.route_id}">${v.name} - ${v.trax_id} - ${v.hub_name}</option>`
                                else
                                    html +=  `<option value="${v.id}" data-id="${v.route_id}">${v.name}</option>`
                            });
                            $('#rider_name').html(html);
                            $('#rider_name').val('').trigger('change');
                            $('#operation_rider_type_id').val(id);
                            $('#operation_rider_type').attr('disabled',true);
                            console.log(id);
                        }
                        else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });
            $('#route').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Route*',
            });
            $('#rider_name').on('change',function () {
                //var route = $(this).find(":selected").data("id");
                var rider_id = $(this).val();
                rider_dncc_check = false;
                if(rider_id != null){
                    $.ajax({
                        url: '{!! route('admin.delivery.note.rider_dncc_status') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'rider_id': rider_id,
                        }
                    }).done(function(data){
                        if (data.status == 1) {
                            ccd_rider = parseInt(data.ccd_rider);
                            $("#rider_id").val(rider_id);
                            $("#rider_name").attr('disabled',true);

                            var html = "";
                            $.each(data.routes, function(key,v) {

                                html +=  `<option value="${v.id}" data-id="${v.route_id}">${v.code} - (${v.start}  to  ${v.end})</option>`

                            });
                            $('#route').html(html);
                            $('#route').val('').trigger('change');

                            rider_dncc_check = true;
                        }
                        else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            $("#deliveryNoteSubmitBtn").attr('disabled',true);
                        }
                    });
                }
               /* else{
                    $('#route').val(route).trigger('change');
                }*/

            });

            $('#route').on('change',function () {
                if(this.value){
                    $('#route_id').val(this.value);
                    $('#route').attr('disabled',true);
                    $('#scan_tracking').attr("disabled", false);
                    $("#deliveryNoteSubmitBtn").attr('disabled',false);
                }
            });


            $('#scan_tracking').attr("disabled","disabled");
            $('#scan_tracking').on('change',function() {
                $(this).val($(this).val().trim());
            });

            var rowsCount = 0;
            // function  countRows() {
            //     rowsCount = table.row().count();
            // }
            $('input#scan_tracking').focus();
            $('#delivery_note_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = parseInt(scan.val());
                var hub_id = $('#hub_id').val();
                var rider_id = $('#rider_id').val();
                if (tracking !== '' && Number.isNaN(tracking) == false) {
                    scan.attr('disabled', true);
                    //countRows();

                    if(rowsCount === 0) {
                        blockPagePermanently();
                        $.ajax({
                            url:'{{route('admin.delivery.note.shipment.info')}}',
                            method:'POST',
                            data: {
                                'tracking':tracking,
                                'rider_id':rider_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                UnblockPagePermanently();
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                scan_sound(2);
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
                                UnblockPagePermanently();
                            }else{
                                rowsCount += 1;
                                var rowNo = rowsCount;
                                var notification_check = '<input type="checkbox" class="form-control notification" name="notification['+data.shId+']" checked>';
                                var rider_information = '<input type="checkbox" class="form-control select select-checkbox rider_information" name="rider_information[]" checked>';
                                if(data.is_open_box==1){
                                    var open_box = '<input type="checkbox" checked="checked" class="form-control open_box" name="open_box['+ data.shId+']">';

                                }else{

                                    var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';
                                }
                                var consolidation = '';
                                if(data.consolidation_flag){
                                    consolidation = data.consolidation_details.order+'/'+data.consolidation_details.count;
                                }else{
                                    consolidation = '-';
                                }
                                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';
                                var row = table.row.add([rowNo,data.tracking_number,data.destination,data.consignee_name,data.phone,notification_check,rider_information,data.address,data.amount,data.service_type,data.shipment_status,data.rider_name,data.remarks, open_box,consolidation,remove]).node().id = data.shId;
                                table.draw(false);
                                $('tr#'+row).attr('class',data.class);
                                if(data.consolidation_flag){
                                    $('tr#'+row).attr('consolidation_id',data.consolidation_details.consolidation_id);
                                }
                                // table.rows(row).nodes().attr("class", data.class);
                                scan_sound(1);
                                UnblockPagePermanently();
                                shipment_ids.push(data.shId);
                                tracking_ids.push(data.tracking_number);
                                notification_ids.push(1);
                                rider_info_ids.push(1);
                                $('#hub_id').val(data.hub);

                                if(parseInt(data.ccd_shipment) == 1){
                                    ccd_shipment_ids.push(data.shId);
                                    ccd_tracking_numbers.push(data.tracking_number);

                                    var ccd_shipment_html = '';
                                    ccd_shipment_html += 'This shipment ' + data.tracking_number +' requires POS machine for Card swiping on delivery, please ensure that the rider has the training for using POS machine and the necessary arrangements (paper rolls and ink ready) for printing receipts.<br/>';
                                    content = document.createElement('div');
                                    content.innerHTML = ccd_shipment_html;
                                    swal({
                                        content: content,
                                        icon: 'info',
                                        buttons: {
                                            cancel: {
                                                text: 'Close',
                                                value: null,
                                                visible: true,
                                                closeModal: true,
                                            },
                                        },
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        dangerMode: true
                                    });
                                }
                                if(data.crm_request.cod_change != null || data.crm_request.address_change != null || data.crm_request.phone_one_change != null){
                                    var html = '';

                                    html += 'This Shipment with Tracking Number: ' + data.tracking_number + ' has following changes:<br/>';
                                    if(data.crm_request.cod_change != null){
                                        html += 'COD : '+ data.crm_request.cod_change + '<br/>';
                                    }
                                    if(data.crm_request.address_change != null){
                                        html += 'Address : '+ data.crm_request.address_change + '<br/>';
                                    }
                                    if(data.crm_request.phone_one_change != null){
                                        html += 'Phone : '+ data.crm_request.phone_one_change + '<br/>';
                                    }

                                    content = document.createElement('div');
                                    content.innerHTML = html;
                                    swal({
                                        content: content,
                                        icon: 'info',
                                        buttons: {
                                            cancel: {
                                                text: 'Close',
                                                value: null,
                                                visible: true,
                                                closeModal: true,
                                            },
                                        },
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        dangerMode: true
                                    });
                                }
                            }
                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                            table.button('.select_all_notifications').enable();
                            table.button('.select_none_notifications').enable();
                            table.button('.select_all_rider_informations').enable();
                            table.button('.select_none_rider_informations').enable();

                        });
                    } else {
                        var rider_id = $('#rider_id').val();
                        var is_indexed = $.inArray(tracking, tracking_ids);
                        if(is_indexed === -1){
                            blockPagePermanently();
                            // $('#hub_id').val('');
                            $.ajax({
                                url:'{{route('admin.delivery.note.shipment.info')}}',
                                method:'POST',
                                data: {
                                    'tracking':tracking,
                                    'hub_id':hub_id,
                                    'rider_id':rider_id,
                                    '_token':'{!! csrf_token() !!}'
                                }
                            }).done(function (data) {
                                if(data.status === 1){
                                    UnblockPagePermanently();
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    scan_sound(2);
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
                                    UnblockPagePermanently();
                                }else{
                                    // rowsCount += 1;
                                    // var rowNo = rowsCount;
                                    var rowNo = table.rows().count();
                                    var notification_check = '<input type="checkbox" class="form-control notification" name="notification['+data.shId+']" checked>';
                                    var rider_information = '<input type="checkbox" class="form-control rider_information" name="rider_information[]" checked>';
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';
                                    if(data.is_open_box==1){
                                        var open_box = '<input type="checkbox" checked="checked" class="form-control open_box" name="open_box['+ data.shId+']">';

                                    }else{

                                        var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';
                                    }
                                    var consolidation = '';
                                    if(data.consolidation_flag){
                                        consolidation = data.consolidation_details.order+'/'+data.consolidation_details.count;
                                    }else{
                                        consolidation = '-';
                                    }
                                    var row = table.row.add([rowNo+1,data.tracking_number,data.destination,data.consignee_name,data.phone,notification_check,rider_information,data.address,data.amount,data.service_type,data.shipment_status,data.rider_name,data.remarks, open_box,consolidation,remove]).node().id = data.shId;
                                    table.draw(false);
                                    $('tr#'+row).attr('class',data.class);
                                    if(data.consolidation_flag){
                                        $('tr#'+row).attr('consolidation_id',data.consolidation_details.consolidation_id);
                                    }

                                    scan_sound(1);
                                    UnblockPagePermanently();
                                    shipment_ids.push(data.shId);
                                    tracking_ids.push(data.tracking_number);
                                    notification_ids.push(1);
                                    rider_info_ids.push(1);
                                    table.order([0, 'desc']).draw();
                                    if(parseInt(data.ccd_shipment) == 1){
                                        ccd_shipment_ids.push(data.shId);
                                        ccd_tracking_numbers.push(data.tracking_number);

                                        var ccd_shipment_html = '';
                                        ccd_shipment_html += 'This shipment ' + data.tracking_number +' requires POS machine for Card swiping on delivery, please ensure that the rider has the training for using POS machine and the necessary arrangements (paper rolls and ink ready) for printing receipts.<br/>';
                                        content = document.createElement('div');
                                        content.innerHTML = ccd_shipment_html;
                                        swal({
                                            content: content,
                                            icon: 'info',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });
                                    }
                                    if(data.crm_request.cod_change != null || data.crm_request.address_change != null || data.crm_request.phone_one_change != null){
                                        var html = '';

                                        html += 'This Shipment with Tracking Number: ' + data.tracking_number + ' has following changes:<br/>';
                                        if(data.crm_request.cod_change != null){
                                            html += 'COD : '+ data.crm_request.cod_change + '<br/>';
                                        }
                                        if(data.crm_request.address_change != null){
                                            html += 'Address : '+ data.crm_request.address_change + '<br/>';
                                        }
                                        if(data.crm_request.phone_one_change != null){
                                            html += 'Phone : '+ data.crm_request.phone_one_change + '<br/>';
                                        }

                                        content = document.createElement('div');
                                        content.innerHTML = html;
                                        swal({
                                            content: content,
                                            icon: 'info',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });

                                    }
                                }
                                scan.val('');
                                scan.attr('disabled', false);
                                scan.focus();

                            });
                        }else{
                            var error = 'Tracking Number already scanned!';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            scan_sound(2);
                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        }
                    }
                }
            });
            $('body').on('click','a.deliverynoterow',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, shipment_ids);

                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                    tracking_ids.splice(index, 1);
                    notification_ids.splice(index, 1);
                    rider_info_ids.splice(index, 1);
                    rowsCount -= 1;

                    var ccd_index = $.inArray(rid, ccd_shipment_ids);

                    if (ccd_index !== -1) {
                        ccd_shipment_ids.splice(ccd_index, 1);
                        ccd_tracking_numbers.splice(ccd_index, 1);
                    }
                }

                table.row( $(this).parents('tr') ).remove().draw();
            });

            $('.datatable tbody').on('click', 'tr td.notification input', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(id, shipment_ids);
                if(notification_ids[index] === 0){
                    notification_ids[index] = 1;
                }else{
                    notification_ids[index] = 0;
                }

            });
            $('.datatable tbody').on('click', 'tr td.rider_information input', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                var index = $.inArray(id, shipment_ids);

                if(rider_info_ids[index] === 0){
                    rider_info_ids[index] = 1;
                }else{
                    rider_info_ids[index] = 0;
                }

            });

            $('#otp_submit').on('click', function () {
                otp_verification();
            });

            $('#otp_input').keypress(function (event) {
                if(event.keyCode == 13){
                    otp_verification();
                }
            });

            function otp_generation(){
                var rider = $('#rider_id').val();
                if(rider){

                    $.ajax({
                        url: '{!! route('admin.delivery.note.otp.generate') !!}',
                        method: 'POST',
                        data: {
                            'rider': rider,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 1){
                            $('#OtpModal').modal('show');
                            $('#otp_input').focus();
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        }
                    });
                }
                else{
                    var error = "Rider not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            }

            function otp_verification() {
                var otp = $('#otp_input').val();
                var rider = $('#rider_id').val();

                if (otp.length == 6) {
                    $.ajax({
                        url: '{!! route('admin.delivery.note.otp.verify') !!}',
                        type: 'POST',
                        data: {
                            'rider': rider,
                            'otp': otp,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        $('#otp_input').val('');
                        $('#otp_submit').attr('disabled', true);
                        if (data.status === 0) {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        } else {
                            $('#OtpModal').modal('hide');
                            create_delivery_note();
                        }
                    });
                }
            }

            var special_rider_name = '';
            var special_rider_phone = '';
            var special_rider_flag = false;
            var this_form;
            function create_delivery_note(){
                var rider = $('#rider_id').val();
                var route = $('#route_id').val();
                var ccd_flag = true;
                if(ccd_shipment_ids.length > 0){
                    if(ccd_rider != 1){
                        ccd_flag = false;
                    }
                }

                var errors = 0;
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
                if(errors == 0){
                    if(ccd_flag == true){
                        table.rows().nodes().each(function (index) {
                            var row = table.row(index);

                            if ($(row.node()).attr('consolidation_id')) {

                                var id = parseInt($(row.node()).attr('consolidation_id'));
                                //
                                var index = $.inArray(id, consolidation_ids);

                                if (index === -1) {
                                    consolidation_ids.push(id);
                                }
                            }
                        });
                        if (consolidation_ids.length > 0) {
                            $.ajax({
                                url: '{{route('admin.delivery.note.consolidation_check')}}',
                                method: 'POST',
                                data: {
                                    'consolidation_ids': consolidation_ids,
                                    'shipment_ids': shipment_ids,
                                    '_token': '{!! csrf_token() !!}'
                                }
                            }).done(function (data) {
                                if (data.missing_flag) {
                                    errors = 1;
                                    var html = '';

                                    html += 'The following Shipment(s) are missing from consolidation:<br/>';

                                    $.each(data.missing_shipments, function (index, tracking) {
                                        html += tracking + ', ';
                                    });

                                    html = html.slice(0, -2);

                                    content = document.createElement('div');
                                    content.innerHTML = html;
                                    swal({
                                        content: content,
                                        icon: 'warning',
                                        buttons: {
                                            cancel: {
                                                text: 'Close',
                                                value: null,
                                                visible: true,
                                                closeModal: true,
                                            },
                                        },
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        dangerMode: true
                                    });
                                } else {

                                    swal({
                                        title: 'Are You Sure?',
                                        text: 'Select Yes to create the Delivery Note!',
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
                                        closeOnEsc: false
                                    }).then(function (confirm) {
                                        if (confirm) {
                                            blockPagePermanently();
                                            open_box_ids = [];
                                            table.rows().every(function (index) {
                                                var node = $(this.node());
                                                if (node.find('td.open_box input').is(':checked')) {
                                                    open_box_ids.push(parseInt(node.attr('id')));
                                                }
                                            });
                                            $('#create_delivery_note_form button[type="submit"]').attr('disabled', 'disabled');
                                            $('#create_delivery_note_form input#shipment_ids').val(shipment_ids);
                                            $('#create_delivery_note_form input#open_box_ids').val(open_box_ids);
                                            $('#create_delivery_note_form input#notification_ids').val(notification_ids);
                                            $('#create_delivery_note_form input#rider_info_ids').val(rider_info_ids);
                                            $('#create_delivery_note_form input#selected_rider_id').val(rider);
                                            console.log($('#create_delivery_note_form input#selected_rider_id').val());
                                            $('#create_delivery_note_form input#selected_route_id').val(route);
                                            if (special_rider_flag) {
                                                $('#create_delivery_note_form input#special_rider_name').val(special_rider_name);
                                                $('#create_delivery_note_form input#special_rider_phone').val(special_rider_phone);
                                            }

                                            this_form.submit();
                                        }
                                    });


                                }
                            });
                        }
                        else{
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes to create the Delivery Note!',
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
                                closeOnEsc: false
                            }).then(function (confirm) {
                                if(confirm){
                                    blockPagePermanently();
                                    open_box_ids = [];
                                    table.rows().every(function(index) {
                                        var node = $(this.node());
                                        if(node.find('td.open_box input').is(':checked')){
                                            open_box_ids.push(parseInt(node.attr('id')));
                                        }
                                    });
                                    $('#create_delivery_note_form button[type="submit"]').attr('disabled', 'disabled');
                                    $('#create_delivery_note_form input#shipment_ids').val(shipment_ids);
                                    $('#create_delivery_note_form input#open_box_ids').val(open_box_ids);
                                    $('#create_delivery_note_form input#notification_ids').val(notification_ids);
                                    $('#create_delivery_note_form input#rider_info_ids').val(rider_info_ids);
                                    $('#create_delivery_note_form input#selected_rider_id').val(rider);
                                    $('#create_delivery_note_form input#selected_route_id').val(route);
                                    if(special_rider_flag){
                                        $('#create_delivery_note_form input#special_rider_name').val(special_rider_name);
                                        $('#create_delivery_note_form input#special_rider_phone').val(special_rider_phone);
                                    }

                                    this_form.submit();

                                }
                            });
                        }
                    }
                    else{
                        var ccd_html = '';

                        ccd_html += 'The following Shipment(s) are Credit Card on Delivery shipments and rider is not allowed/trained to use POS for CCD shipments:<br/>';

                        $.each(ccd_tracking_numbers, function (index, ccd_tracking) {
                            ccd_html += ccd_tracking + ', ';
                        });

                        ccd_html = ccd_html.slice(0, -2);

                        content = document.createElement('div');
                        content.innerHTML = ccd_html;
                        swal({
                            content: content,
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'Close',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        });
                    }
                }

            }



            $('#create_delivery_note_form').on('submit', function(event) {

                event.preventDefault();

                var count = 0;
                this_form = this;
                count = table.rows().count();

                var errors = 0;
                var rider = $('#rider_id').val();
                var route = $('#route_id').val();
                var operation_id = $('#operation_rider_type_id').val();
                var special = parseInt($('#rider_id').find(':selected').data('special'));


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
                if (operation_id !== '' && operation_id !== null) {

                    $('#operation_error').css('display', 'none');
                    $('#operation_rider_type_for_attendance').val(operation_id);

                } else {
                    var error = "Category not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#operation_error').css('display', 'block');
                }

                if(count > 0) {
                    if (errors === 0) {
                        if(special_rider_flag == false){
                            if(special == 1){
                                $('#SpecialRiderModal').modal('show');
                            }else{
                                if(rider_dncc_check == true){
                                    if(operation_id === '2'){
                                        create_delivery_note();
                                    }else{
                                        otp_generation();
                                    }
                                }
                                else{
                                    var error = "Rider can not be selected because previous delivery note is not been completed";
                                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        }

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

            $('#special_rider_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $('#add_special_rider_button').prop('disabled', true);
                    special_rider_name = $('#special_rider_name_input').val();
                    special_rider_phone = $('#special_rider_phone_input').val();
                    special_rider_flag = true;
                    form.reset();
                    $('#SpecialRiderModal').modal('hide');
                    otp_generation();
                    return false;
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
                            url: '{!! route('admin.delivery.note.shipment.piece_details') !!}',
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

                    $('#add_shipment_pieces_form button[type="submit"]').attr('disabled', 'disabled');
                    var shipment_id = $('#piece_shipment_id').val();
                    var hub_id = $('#hub_id').val();
                    var tracking_number = $(form).find('input.scan_piece_tracking_number').val();
                    if(hub_id == null || hub_id == ''){
                        $.ajax({
                            url: '{!! route('admin.delivery.note.shipment.info') !!}',
                            method: 'POST',
                            data: {
                                'tracking': tracking_number,
                                'pieces_confirm': 1,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if(data.status === 1){
                                    UnblockPagePermanently();
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    scan_sound(2);
                                }
                                else{
                                    rowsCount += 1;
                                    var rowNo = rowsCount;
                                    var notification_check = '<input type="checkbox" class="form-control notification" name="notification['+data.shId+']" checked>';
                                    var rider_information = '<input type="checkbox" class="form-control select select-checkbox rider_information" name="rider_information[]" checked>';
                                    if(data.is_open_box==1){
                                        var open_box = '<input type="checkbox" checked="checked" class="form-control open_box" name="open_box['+ data.shId+']">';

                                    }else{

                                        var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';
                                    }
                                    var consolidation = '';
                                    if(data.consolidation_flag){
                                        consolidation = data.consolidation_details.order+'/'+data.consolidation_details.count;
                                    }else{
                                        consolidation = '-';
                                    }
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';
                                    var row = table.row.add([rowNo,data.tracking_number,data.destination,data.consignee_name,data.phone,notification_check,rider_information,data.address,data.amount,data.service_type,data.shipment_status,data.rider_name,data.remarks, open_box,consolidation,remove]).node().id = data.shId;
                                    table.draw(false);
                                    $('tr#'+row).attr('class',data.class);
                                    if(data.consolidation_flag){
                                        $('tr#'+row).attr('consolidation_id',data.consolidation_details.consolidation_id);
                                    }
                                    // table.rows(row).nodes().attr("class", data.class);
                                    scan_sound(1);
                                    UnblockPagePermanently();
                                    shipment_ids.push(data.shId);
                                    tracking_ids.push(data.tracking_number);
                                    notification_ids.push(1);
                                    rider_info_ids.push(1);
                                    $('#hub_id').val(data.hub);

                                    if(parseInt(data.ccd_shipment) == 1){
                                        ccd_shipment_ids.push(data.shId);
                                        ccd_tracking_numbers.push(data.tracking_number);

                                        var ccd_shipment_html = '';
                                        ccd_shipment_html += 'This shipment ' + data.tracking_number +' requires POS machine for Card swiping on delivery, please ensure that the rider has the training for using POS machine and the necessary arrangements (paper rolls and ink ready) for printing receipts.<br/>';
                                        content = document.createElement('div');
                                        content.innerHTML = ccd_shipment_html;
                                        swal({
                                            content: content,
                                            icon: 'info',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });
                                    }
                                }
                            });
                    }
                    else{
                        blockPagePermanently();
                        // $('#hub_id').val('');
                        $.ajax({
                            url:'{{route('admin.delivery.note.shipment.info')}}',
                            method:'POST',
                            data: {
                                'tracking':tracking_number,
                                'hub_id':hub_id,
                                'pieces_confirm':1,
                                '_token':'{!! csrf_token() !!}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                UnblockPagePermanently();
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                scan_sound(2);
                            }else{
                                // rowsCount += 1;
                                // var rowNo = rowsCount;
                                var rowNo = table.rows().count();
                                var notification_check = '<input type="checkbox" class="form-control notification" name="notification['+data.shId+']" checked>';
                                var rider_information = '<input type="checkbox" class="form-control rider_information" name="rider_information[]" checked>';
                                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';
                                if(data.is_open_box==1){
                                    var open_box = '<input type="checkbox" checked="checked" class="form-control open_box" name="open_box['+ data.shId+']">';

                                }else{

                                    var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.shId+']">';
                                }
                                var consolidation = '';
                                if(data.consolidation_flag){
                                    consolidation = data.consolidation_details.order+'/'+data.consolidation_details.count;
                                }else{
                                    consolidation = '-';
                                }
                                var row = table.row.add([rowNo+1,data.tracking_number,data.destination,data.consignee_name,data.phone,notification_check,rider_information,data.address,data.amount,data.service_type,data.shipment_status,data.rider_name,data.remarks, open_box,consolidation,remove]).node().id = data.shId;
                                table.draw(false);
                                $('tr#'+row).attr('class',data.class);
                                if(data.consolidation_flag){
                                    $('tr#'+row).attr('consolidation_id',data.consolidation_details.consolidation_id);
                                }

                                scan_sound(1);
                                UnblockPagePermanently();
                                shipment_ids.push(data.shId);
                                tracking_ids.push(data.tracking_number);
                                notification_ids.push(1);
                                rider_info_ids.push(1);
                                table.order([0, 'desc']).draw();

                                if(parseInt(data.ccd_shipment) == 1){
                                    ccd_shipment_ids.push(data.shId);
                                    ccd_tracking_numbers.push(data.tracking_number);

                                    var ccd_shipment_html = '';
                                    ccd_shipment_html += 'This shipment ' + data.tracking_number +' requires POS machine for Card swiping on delivery, please ensure that the rider has the training for using POS machine and the necessary arrangements (paper rolls and ink ready) for printing receipts.<br/>';
                                    content = document.createElement('div');
                                    content.innerHTML = ccd_shipment_html;
                                    swal({
                                        content: content,
                                        icon: 'info',
                                        buttons: {
                                            cancel: {
                                                text: 'Close',
                                                value: null,
                                                visible: true,
                                                closeModal: true,
                                            },
                                        },
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        dangerMode: true
                                    });
                                }
                            }
                        });
                    }
                    $('#ShipmentPiecesModal').modal('hide');
                    return false;
                }
            });

            $('#ShipmentPiecesModal').on('hidden.bs.modal', function (e) {
                $('#scan_piece_tracking_number').val('');
                shipment_piece_ids = [];
                piece_table.clear().draw();
            });
            $('#OtpModal').on('shown.bs.modal', function () {
                $('#otp_input').focus();
            });
        });

        function camera_scan_detected(tracking_number) {
            $('#scan_tracking').val(tracking_number);

            $('#delivery_note_form').trigger('submit');
        }
    </script>
@endsection

