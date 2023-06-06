@extends('admin.layout.master')

@section('title', 'Create Handover Note')

@section('content')



        <h1 class="mb-1">
            Create Handover Note
        </h1>

        <div class="card">
            <div class="card-content" aria-expanded="true">
                <div class="card-body">
                    @include('admin.inc.messages')

                    <form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <input type="hidden" name="delivery_location_mapping" id="delivery_location_mapping">
                        <div class="form-group">
                            <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">

                        </div>

                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                    </form>
                    <div class="row mb-2 justify-content-center">

                        <div class="col-2">
                            <fieldset class="form-group">
                                <select name="hub" id="hub" class="form-control select2 dynamic" data-dependent="from"
                                 required>
                                    @foreach($hubs as $hub)
                                        <option value="{{$hub->id}}">{{$hub->name}}</option>
                                    @endforeach
                                </select>
                                <div class="danger" id="hub_error" style="display:none;">This field is required</div>
                            </fieldset>

                        </div>
                        <div class="col-2">
                            <fieldset class="form-group">
                                <select name="from" id="from" class="form-control select2" required>
                                </select>
                                <div class="danger" id="from_error" style="display:none;">This field is required</div>
                            </fieldset>
                        </div>

                        <div class="col-2">
                            <fieldset class="form-group">
                                <input type="text" name="from_dept_area_desg" id="from_dept_area_desg" class="form-control from_dept_area_desg width-215" placeholder="From Person Dept/Area/DES*">
                                <div class="danger" id="from_dept_area_desg_error" style="display:none;">This field is required</div>
                            </fieldset>
                        </div>

                        <div class="col-2">
                            <fieldset class="form-group">
                                <select name="to" id="to" class="form-control select2" required>
                                </select>
                                <div class="danger" id="to_error" style="display:none;">This field is required</div>
                            </fieldset>
                        </div>

                        <div class="col-2">
                            <fieldset class="form-group">
                                <input type="text" name="to_dept_area_desg" id="to_dept_area_desg" class="form-control to_dept_area_desg" placeholder=" To Person Dept/Area/DES*">
                                <div class="danger" id="to_dept_area_desg_error" style="display:none;">This field is required</div>
                            </fieldset>
                        </div>
                        {{ csrf_field() }}
                    </div>

                    <table class="table table-bordered datatable" id="datatable" style="width:100%; z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Phone No</th>
                            <th class="border-primary border-darken-1">Pickup Date</th>
                            <th class="border-primary border-darken-1">Special Instruction</th>
                            <th class="border-primary border-darken-1"></th>
                        </tr>
                        </thead>
                    </table>

                    <form id="arrival_of_shipments_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.handover.create.store') }}" novalidate="novalidate">
                        {{ csrf_field() }}

                        <input type="hidden" name="shipment_ids" class="shipment_ids">
                        <input type="hidden" name="hub_id" class="hub_id">
                        <input type="hidden" name="from" class="from">
                        <input type="hidden" name="from_dept_area_desg" class="from_dept_area_desg">
                        <input type="hidden" name="to" class="to">
                        <input type="hidden" name="to_dept_area_desg" class="to_dept_area_desg">

                        <div class="form-group ml-1">
                            <button type="submit" name="confirm" class="btn btn-primary confirm" value="Confirm" disabled="disabled">Confirm</button>
                        </div>
                    </form>
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
                        
                        <div class="row justify-content-center">
                            <div class="form-group col-5">
                                <input type="text" name="scan_piece_tracking_number" id="scan_piece_tracking_number" class="form-control scan_piece_tracking_number" placeholder="Scan Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required" disabled="disabled">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="form-group col-5">
                                <input type="text" name="pieces_weight" id="pieces_weight" class="form-control pieces_weight" placeholder="Weight (kg)*" data-rule-required="true" data-msg-required="Weight is required" data-rule-range="[0.01,100000]" data-msg-range="Weight needs to be from 0.01 to 100000" disabled="disabled">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('#hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub*',
            });
            $('#from').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select From Person*',
            });
            $('#to').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select To Person*',
            });

            $('.dynamic').change(function(){
                if($(this).val() != '')
                {
                    var value = $(this).val();
                    var dependent = $(this).data('dependent');
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: '{!! route('admin.handover.create.fetch') !!}',
                        method:"POST",
                        data:{value:value, _token:_token,dependent:dependent},
                        success:function(result){
                            $('#'+dependent).html(result);
                            $('#to').empty();
                            $('#from option').clone().appendTo('#to');  
                            $('#to').find('option').get(0).remove();
                            $("#to").prepend("<option value='' selected='selected'>Select To Person</option>");
                            // $('#to').find('option').get(0).remove();
                        }
                    })
                }
                
            });


           
            $("#from").change(function()
           {    
               $('#to').empty();
                 $('#from option').clone().appendTo('#to');
                 $('#to').find('option').get(0).remove();
                 $("#to").prepend("<option value='' selected='selected'>Select To Person</option>");
                
                 var value = $(this).val();
                $("#to option[value='"+value+"']").remove();
             });

            
            // $('#hub').change(function(){
            // $('#from').val('');
            // });
            // $('#from').change(function(){
            // $('#to').val('');
            // });

            //end

            var shipment_ids = [];
            var hub_id = '';
        
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
           
            var shipment_ids = [];
            var shipment_piece_ids = [];
            var all_shipment_piece_ids = [];
           
            $('#add_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#add_shipment_form button.add').prop('disabled', true);

                    var tracking_number = $(form).find('input.tracking_number').val();
                    var delivery_location_mapping = $('#delivery_location_mapping').val();
                    
                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        $.ajax({
                            url: '{!! route('admin.handover.create.shipment_details') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                'delivery_location_mapping': delivery_location_mapping,
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
                                        table.row.add([rowNo + 1, data.details.tracking_number, data.details.shipper, data.details.phone_number, data.details.pickup_date,data.details.special_instructions, remove_button]).node().id = data.details.id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                        shipment_ids.push(data.details.id);
                                        // console.log(shipment_ids);
                                        // console.log(data.details.id);

                                        $('#delivery_location_mapping').val(data.details.delivery_area);
                                        $('#add_shipment_form button.add').prop('disabled', false);

                                        $('#arrival_of_shipments_form button.confirm').prop('disabled', false);

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                //Open Modal if Shipment Pieces are greater than 1 
                                if(data.status == 3){
                                    $('#scan_piece_tracking_number').prop('disabled', true);
                                    $('#pieces_weight').prop('disabled', true);
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
                                            $('#total_piece_count').html('Total Shipment Piece(s): ' + data.details.pieces);
                                            var check = parseInt(piece_rowNo) + 1;
                                            if(parseInt(data.details.piece) === parseInt(check)){
                                                $('#scan_piece_tracking_number').prop('disabled', false);
                                                $('#pieces_weight').prop('disabled', false);
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
                            });
                    }
                    else {
                        $('#add_shipment_form button.add').prop('disabled', false);

                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            $('body').on('select2:select','.statusOnChange .statusDrop',function (e) {
                // var rowid = parseInt($(this).parents('tr').attr('id'));

                // $('#statusSubmit').removeAttr('disabled');
                var statusSelection = $(this).find(':selected');
                var status = statusSelection.val();
                shipment_status[rowid] = status;
                var reason = statusSelection.closest('td').next('td').find('.reasonDrop');
                $.ajax({
                    url:'{!! route('admin.delivery.receive.reason') !!}',
                    type:'POST',
                    dataType:'json',
                    data: {
                        'status':status,
                        'shipment_id': rowid,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 0){
                        reason.empty().trigger('change');
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            reason.append(newOption).trigger('change');
                            reason.attr('data-rule-required', 'true');
                            reason.attr('data-msg-required', 'Reason is required');
                        });
                        reason.val('').trigger('change');
                    }else{
                        reason.empty().trigger('change');
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });
                


            $('#arrival_of_shipments_form').bind('submit', function(e) {
                e.preventDefault();
                hub_id =  $('#hub :selected').val();
                from =  $('#from :selected').val();
                from_dept_area_desg =  $('#from_dept_area_desg').val();
                to =  $('#to :selected').val();
                to_dept_area_desg =  $('#to_dept_area_desg').val();
                errors = 0;
               
                if (hub_id !== '' && hub_id !== null) {
                    $('#hub_error').css('display', 'none');
                }
                else {
                    var error = "Hub not selected!";
                    // toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#hub_error').css('display', 'block');
                } 
                if (from !== '' && from !== null) {
                    $('#from_error').css('display', 'none');
                }
                else{
                    var error = "From not selected!";
                    // toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#from_error').css('display', 'block');
                }

                if (from_dept_area_desg !== '' && from_dept_area_desg !== null) {
                    $('#from_dept_area_desg_error').css('display', 'none');
                }
                else{
                    var error = "From Person Designation Required";
                    // toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#from_dept_area_desg_error').css('display', 'block');
                }

                if (to !== '' && to !== null) {
                    $('#to_error').css('display', 'none');
                }
                else {
                    var error = "To not selected!";
                    // toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#to_error').css('display', 'block');
                } 

                if (to_dept_area_desg !== '' && to_dept_area_desg !== null) {
                    $('#to_dept_area_desg_error').css('display', 'none');
                }
                else {
                    var error = "Person Designation Required";
                    // toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#to_dept_area_desg_error').css('display', 'block');
                } 
                
                $('#arrival_of_shipments_form input.shipment_ids').val(shipment_ids);
                $('#arrival_of_shipments_form input.hub_id').val(hub_id);
                $('#arrival_of_shipments_form input.from_dept_area_desg').val(from_dept_area_desg);
                $('#arrival_of_shipments_form input.from').val(from);
                $('#arrival_of_shipments_form input.to').val(to);
                $('#arrival_of_shipments_form input.to_dept_area_desg').val(to_dept_area_desg);
                
                var form = this;
            //    console.log('hub_id '+hub_id);
            if(errors !=1){
                swal({
                   
                    text: 'Are you sure, Select Yes to create the Handover Note?',
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
                            text: 'Handovers are being created!',
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
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
            });

            $('#ShipmentPiecesModal').on('hide.bs.modal', function (e) {
                $('#scan_piece_tracking_number').val('');
                $('#pieces_weight').val('');
                shipment_piece_ids = [];
                piece_table.clear().draw();
            });

            $('#add_shipment_pieces_form input.scan_piece').focus();

            $('#add_shipment_pieces_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    var shipment_id = $('#piece_shipment_id').val();
                    var tracking_number = $(form).find('input.scan_piece_tracking_number').val();
                    var pieces_id = shipment_piece_ids;
                    var weight = $(form).find('input.pieces_weight').val();

                    toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                            form.reset();
                            remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';

                            var rowNo = table.rows().count();
                                    var new_row = table.row.add([rowNo + 1, tracking_number, shipment_id, pieces_id, weight, remove_button]).draw().node();
                                    new_row.id = data.details.id;
                                    table.draw(false);
                                    table.order([0, 'desc']).draw();
                                    scan_sound(1);
                                    // shipment_ids.push(data.details.id);
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
                                    $('#ShipmentPiecesModal').modal('hide');
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
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 5000,
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
                                        $('#pieces_weight').prop('disabled', false);
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
                        $('#pieces_weight').prop('disabled', true);
                        $('#piece_confirm').prop('disabled', true);
                    }
                }
            });

            
        });

    </script>
@endsection