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

                    <div class="form-group">
                        <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">

                    </div>

                    <div class="form-group ml-1">
                        <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                    </div>
                </form>
                <div class="row mb-2 justify-content-center">
                    {{ csrf_field() }}
                </div>
                

                <form id="arrival_of_shipments_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.handover.create.store') }}" novalidate="novalidate">
                    {{ csrf_field() }}

                    <input type="hidden" name="shipment_ids" class="shipment_ids">
                    <input type="hidden" name="hub_id" class="hub_id">
                    <input type="hidden" name="from" class="from">
                    <input type="hidden" name="to" class="to">

                    <div class="form-group ml-1">
                        <button type="submit" name="confirm" class="btn btn-primary confirm" value="Confirm" disabled="disabled">Confirm</button>
                    </div>
                </form>
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

            var shipment_ids = [];

            $('#add_shipment_form input.tracking_number').focus();



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

                        $.ajax({
                            url: '{!! route('admin.handover.create.shipment_details') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
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
                                       /* var rowNo = table.rows().count();
                                        table.row.add([rowNo + 1, data.details.tracking_number, data.details.shipper, data.details.phone_number, data.details.pickup_date,data.details.special_instructions, remove_button]).node().id = data.details.id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                        shipment_ids.push(data.details.id);
                                        // console.log(shipment_ids);
                                        // console.log(data.details.id);*/


                                        $('#add_shipment_form button.add').prop('disabled', false);

                                        $('#arrival_of_shipments_form button.confirm').prop('disabled', false);

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else {
                                    $('#add_shipment_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
            });

            $('#arrival_of_shipments_form').bind('submit', function(e) {
                e.preventDefault();
                hub_id =  $('#hub :selected').val();
                from =  $('#from :selected').val();
                to =  $('#to :selected').val();
                errors = 0;

                if (hub_id !== '' && hub_id !== null) {
                    $('#rider_error').css('display', 'none');
                }
                else {
                    var error = "Hub not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#hub_error').css('display', 'block');
                }
                if (from !== '' && from !== null) {
                    $('#rider_error').css('display', 'none');
                }
                else{
                    var error = "From not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#from_error').css('display', 'block');
                }
                if (to !== '' && to !== null) {
                    $('#rider_error').css('display', 'none');
                }
                else {
                    var error = "To not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#to_error').css('display', 'block');
                }

                $('#arrival_of_shipments_form input.shipment_ids').val(shipment_ids);
                $('#arrival_of_shipments_form input.hub_id').val(hub_id);
                $('#arrival_of_shipments_form input.from').val(from);
                $('#arrival_of_shipments_form input.to').val(to);
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

        });

    </script>
@endsection