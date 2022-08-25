@extends('admin.layout.master')

@section('title', 'Return revert')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Return Revert
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="alert alert-warning" style="display: none" id="revert_div">

                            </div>
                            <form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="col-lg-4 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="tracking_number" class="tracking_number tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                                    </div>
                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                                </div>
                            </form>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Return Note</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>

                            <form id="arrival_of_shipments_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.return.revert.submit') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="shipment_ids" class="shipment_ids">
                                <div class="form-group ml-1">
                                    <button type="submit" name="confirm" class="btn btn-primary confirm" value="Confirm" disabled="disabled">Revert</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            // $('#add_shipment_form input.tracking_number').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false
            // });

            var select = $('.tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
                onType: function (str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });
            var control = select[0].selectize;

            $('#add_shipment_form input.tracking_number').focus();
            var shipment_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                paging:false,
                autoWidth:false,
                "ordering": false,
                columns: [
                    {orderable: false, searchable: false,sortable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'return_note', class: 'align-middle return_note', orderable: false, searchable: false},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'shipper', class: 'align-middle shipper', orderable: false, searchable: false},
                    {name: 'remove', class: 'align-middle remove', sortable: false, orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                },
                initComplete: function() {
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();
                    });

                    this.api().table().columns.adjust();
                }
            });          
            $('#add_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    var tracking_number = $(form).find('input.tracking_number').val();
                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        $.ajax({
                            url: '{!! route('admin.return.revert.shipment_details') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 5000,
                            error: function (data) {
                                form.reset();

                                $('#add_shipment_form input.tracking_number').val('').focus();

                                $('#add_shipment_form button.add').prop('disabled', false);
                                scan_sound(2);
                                toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            },
                            success: function(data) {
                                remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
                                if (data.status == 0) {
                                    $.each(data.details, function(index2, values) {
                                        id = values.id;

                                        var index = $.inArray(id, shipment_ids);

                                        if (index === -1) {
                                            var rowNo = table.rows().count();
                                            remove_button += '<input type="hidden" value="' + id + '" class="remove_pickup_request">';
                                            table.row.add([rowNo + 1, values.return_note, values.tracking_number, values.shipper, remove_button]).node().id = values.id;
                                            table.draw(false);
                                            table.order([0, 'desc']).draw();
                                            scan_sound(1);
                                            shipment_ids.push(values.id);

                                            $('#add_shipment_form button.add').prop('disabled', false);

                                            $('#arrival_of_shipments_form button.confirm').prop('disabled', false);

                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                    });
                                    control.clear();

                                }
                                if(data.ids) {
                                    var er = "";
                                    $.each(data.ids, function(index3, values) {
                                        er+=values +" , ";
                                    });
                                    if(er) {
                                        $('#revert_div').html(`CN# ${er} Shipment Status do not match`);
                                        $('#revert_div').show();
                                    }else{
                                        $('#revert_div').hide()
                                    }
                                    scan_sound(2);
                                    control.clear();
                                    // $('#add_shipment_form button.add').prop('disabled', false);
                                    // toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        });
                    }
                    else {
                        // $('#add_shipment_form button.add').prop('disabled', false);
                        scan_sound(2);
                        $('#add_shipment_form input.tracking_number').focus();
                        control.clear();
                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });


            $('#arrival_of_shipments_form').bind('submit', function(e) {
                e.preventDefault();

                $('#arrival_of_shipments_form input.shipment_ids').val(shipment_ids);
                debugger;
                var form = this;
                swal({
                    text: 'Are you sure, you want to Revert these Shipments?',
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
                            text: 'Shipments are being reverting!',
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

                var index = $.inArray(id, shipment_ids);

                if (index !== -1) {
                    shipment_ids.splice(index, 1);

                    if (shipment_ids.length == 0) {
                        $('#arrival_of_shipments_form button.confirm').prop('disabled', true);
                    }
                    table.row( $(this).parents('tr') ).remove().draw();
                }
                $('#add_shipment_form input.tracking_number').focus();
            });

        });

    </script>
@endsection