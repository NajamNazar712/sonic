@extends('admin.layout.master')
@section('title','International Shipments Status')

@section('content')
    <h1 class="mb-1">
        International Shipments Status
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="international_shipment_form" class="form-inline mb-3 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>
                    <input type="hidden" name="default_status_id" id="default_status_id" value="0">
                    <div class="form-group ml-1">
                        <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                    </div>

                </form>

                <form id="update_shipment_form" action="{{route('admin.international.shipment_status.update')}}" class="form-horizontal" method="POST">
                    {{ csrf_field() }}

                    <div class="row justify-content-center">
                        <div class="col-4">
                            <div class="form-group">
                                <select name="shipment_status_id" id="shipment_status_select" class="form-control select2" data-rule-required="true" data-msg-required="Status is required" >
                                    @foreach($shipment_status as $status)
                                        <option value="{{$status->id}}">{{$status->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1"></th>
                        </tr>
                        </thead>
                    </table>
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary btn-block" disabled id="update_shipment_form_submit">Submit</button>

                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="AddSealNumberModal" role="dialog" aria-labelledby="AddSealNumberModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Seal Number</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_seal_form" action="{{route('admin.international.shipment_status.updatemodal')}}" method="POST" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="hidden" id="shipment_status_id" name="shipment_status_id">
                            <input type="hidden" id="shipment_ids" name="shipment_ids">
                            <input type="text" name="seal_number" id="seal_number_input" maxlength="9" class="form-control integer" placeholder="Enter Seal Number" data-rule-required="true" data-msg-required="Seal Number is required">

                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary" value="Add">Add Seal Number</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

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

    <script type="text/javascript">
        $(document).ready(function () {
            $('#shipment_status_select').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select a status',
                width:'100%'
            });
            $('#add_seal_form .integer').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 999999999
            });
            var shipment_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                "autoWidth": false,
                paging:false,

                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'shipper_name', class: 'align-middle shipper_name form-group', orderable: false},
                    {name: 'origin', class: 'align-middle origin form-group', orderable: false},
                    {name: 'destination', class: 'align-middle destination form-group', orderable: false},
                    {name: 'status', class: 'align-middle status form-group', orderable: false},
                    {name: 'action', class: 'align-middle action', orderable: false},
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();
                    //
                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {

                    this.api().table().columns.adjust();
                }
            });
            var rowsCount = 0;

            $('#international_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            {{--var cities_array = @json($cities);--}}
            $('#international_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#international_shipment_form button.add').prop('disabled', true);

                    var tracking_number = $(form).find('input.tracking_number').val();
                    var default_status_id = $('#default_status_id').val();
                    form.reset();

                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        blockPagePermanently();
                        $.ajax({
                            url: '{!! route('admin.international.shipment_status.shipment_info') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                'default_status_id': default_status_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 1) {
                                    UnblockPagePermanently();
                                    id = data.details.id;

                                    var index = $.inArray(id, shipment_ids);

                                    if (index === -1) {
                                        var rowNo = table.rows().count();

                                        var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
                                        table.row.add([rowNo + 1, data.details.tracking_number, data.details.shipper_name, data.details.origin, data.details.destination, data.details.status, action]).node().id = data.details.id;
                                        table.draw(false);
                                        scan_sound(1);
                                        table.order([0, 'desc']).draw();

                                        shipment_ids.push(data.details.id);
                                        if($('#default_status_id').val() == 0){
                                            $('#default_status_id').val(data.details.status_id);
                                        }
                                        $('#international_shipment_form button.add').prop('disabled', false);

                                        $('#update_shipment_form_submit').prop('disabled', false);

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else {
                                    UnblockPagePermanently();
                                    $('#international_shipment_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                    else {
                        $('#international_shipment_form button.add').prop('disabled', false);
                        scan_sound(2);
                        toastr.error('Shipment has been scanned already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });


            $('#update_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    //$(form).find('button[type=submit]').attr('disabled', 'disabled');
                    var shipment_status_id = $('#shipment_status_select').val();
                    if(shipment_status_id == 3 || shipment_status_id == 21){
                        $('#AddSealNumberModal').modal('show');
                        $('#add_seal_form #shipment_ids').val(shipment_ids);
                        $('#add_seal_form #shipment_status_id').val(shipment_status_id);
                    }
                    else{
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to update Shipments!',
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
                            if(confirm){
                                blockPagePermanently();
                                $('#update_shipment_form button[type="submit"]').attr('disabled', 'disabled');
                                $('#update_shipment_form input#shipment_ids').val(shipment_ids);
                                form.submit();
                            }
                        });
                    }
                }
            });


            $('body').on('click','.action a.removerow',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, shipment_ids);

                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                }
                table.row( $(this).parents('tr') ).remove().draw();
                if(shipment_ids.length == 0){
                    $('#update_shipment_form button[type="submit"]').attr('disabled', 'disabled');
                    $('#default_status_id').val(0);
                }
            });

            $('#add_seal_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update Shipments!',
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
                        if(confirm){
                            blockPagePermanently();
                            $('#update_shipment_form button[type="submit"]').attr('disabled', 'disabled');
                            form.submit();
                        }
                    });
                }
            });

            $('#AddSealNumberModal').on('hide.bs.modal', function (e) {
                $('#seal_number_input').val('');
            });

        });
    </script>
@endsection