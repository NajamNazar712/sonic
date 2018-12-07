@extends('admin.layout.master')
@section('title','Add Lost Shipments')

@section('content')
    <h1 class="mb-1">
        Add Lost Shipments
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="lost_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>

                    <div class="form-group ml-1">
                        <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                    </div>
                </form>

                <form id="update_lost_form" action="{{route('admin.delivery.lost.add.shipments.store')}}" class="form-horizontal" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            <th class="border-primary border-darken-1">Amount</th>
                            <th class="border-primary border-darken-1">Shipping Mode</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1"></th>
                        </tr>
                        </thead>
                    </table>
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary btn-block" disabled id="update_lost_form_submit">Submit</button>

                        </div>

                    </div>
                </form>

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

            var shipment_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                "autoWidth": false,
                paging:false,
                ordering:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'tracking_number', class: 'align-middle tracking_number'},
                    {name: 'shipper_name', class: 'align-middle shipper_name form-group'},
                    {name: 'origin', class: 'align-middle origin form-group'},
                    {name: 'destination', class: 'align-middle destination form-group'},
                    {name: 'hub', class: 'align-middle hub form-group'},
                    {name: 'amount', class: 'align-middle amount'},
                    {name: 'mode', class: 'align-middle mode'},
                    {name: 'service_type', class: 'align-middle service_type'},
                    {name: 'action', class: 'align-middle action'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {

                    this.api().table().columns.adjust();
                }
            });
            var rowsCount = 0;

            $('#lost_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            {{--var cities_array = @json($cities);--}}
            $('#lost_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#lost_shipment_form button.add').prop('disabled', true);

                    var tracking_number = $(form).find('input.tracking_number').val();

                    form.reset();

                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        $.ajax({
                            url: '{!! route('admin.delivery.lost.add.shipment.info') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 1) {
                                    id = data.details.id;

                                    var index = $.inArray(id, shipment_ids);

                                    if (index === -1) {

                                        var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
                                        table.row.add([0, data.details.tracking_number, data.details.shipper_name, data.details.origin, data.details.destination, data.details.hub, data.details.amount,data.details.mode,data.details.service_type, action]).node().id = data.details.id;
                                        table.draw(false);

                                        shipment_ids.push(data.details.id);

                                        $('#lost_shipment_form button.add').prop('disabled', false);

                                        $('#update_lost_form_submit').prop('disabled', false);

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else {
                                    $('#lost_shipment_form button.add').prop('disabled', false);

                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                    else {
                        $('#lost_shipment_form button.add').prop('disabled', false);

                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            // function validateEmail(email) {
            //     var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            //     return re.test(email);
            // }

            $('#update_lost_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    // $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to addd to Lost Shipments!',
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
                            $('#update_lost_form button[type="submit"]').attr('disabled', 'disabled');
                            $('#update_lost_form input#shipment_ids').val(shipment_ids);
                            form.submit();
                        }
                    });

                    // form.submit();
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
                    $('#update_lost_form button[type="submit"]').attr('disabled', 'disabled');
                }
            });

        });
    </script>
@endsection