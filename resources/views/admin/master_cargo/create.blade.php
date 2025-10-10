@extends('admin.layout.master')

@section('title', 'Create Master Cargo')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    @if($id == 1)
                        Create Onward Forwarding Cargo
                    @else
                        Create Master Cargo
                    @endif
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div id="camera_scan" class="d-none">
                                <div id="camera_view" class="camera_view"></div>
                            </div>

                            <form id="add_bag_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="form-group">
                                    <input type="text" name="bag_number" class="form-control bag_number" placeholder="Bag Number*" data-rule-required="true" data-msg-required="Bag Number is required">

                                    <div class="d-inline-block ml-1">
                                        <a href="#" id="camera_scan_initiate" tabindex="-1">
                                            <i class="ft-camera h1"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                                </div>
                            </form>

                            <div id="information" class="information text-center">
                                Hub: <span class="hub">None</span> | Scanned: <span class="scanned">0</span>/<span class="total">0</span>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Bag Number</th>
                                    <th class="border-primary border-darken-1">Shipments</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Actual Weight</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary mr-2" id="master_cargo_consignment_confirm" data-toggle="modal" data-target="#master_cargo_consignment" disabled="disabled">Confirm</button>
                            </div>

                            <div class="modal fade" id="master_cargo_consignment" role="dialog" aria-labelledby="master_cargo_consignment_title" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <form class="form-horizontal" method="POST" action="{{ route('admin.master_cargo.create.store') }}" novalidate="novalidate">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="onward_forwarding" value="{{$id}}">
                                            <input type="hidden" name="bag_ids" class="bag_ids">

                                            <div class="modal-header">
                                                <h4 class="modal-title" id="master_cargo_consignment_title">Master Cargo</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h4 class="form-section mb-2 text-center">Cargo Information</h4>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <input type="hidden" name="origin_hub_id" class="origin_hub_id">

                                                            <p class="mt-1 border-bottom border-light text-center font-medium-1 text-bold-600 origin"></p>
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <input type="hidden" name="destination_hub_id" class="destination_hub_id">

                                                            <p class="mt-1 border-bottom border-light text-center font-medium-1 text-bold-600 destination"></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
{{--                                                        <div class="form-group">--}}
{{--                                                            <select name="route_management_id" class="select2 route_management_id" data-rule-required="true" data-msg-required="Route is required">--}}
{{--                                                            </select>--}}
{{--                                                        </div>--}}
                                                    </div>
                                                    {{-- <div class="col">
                                                        <div class="form-group">
                                                            <select name="junction_1" class="select2 junction_1">
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <select name="junction_2" class="select2 junction_2">
                                                            </select>
                                                        </div>
                                                    </div> --}}

                                                    <div class="w-100"></div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <select name="transport_mode" class="select2 transport_mode" data-rule-required="true" data-msg-required="Transport Mode is required">
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <select name="transport_mode_vendor" class="select2 transport_mode_vendor" data-rule-required="true" data-msg-required="Vendor is required">
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div id="new_vendor" class="col d-none">
                                                        <div class="form-group">
                                                            <input type="text" name="vendor_name" class="form-control vendor_name" placeholder="Vendor Name*" data-rule-required="true" data-msg-required="Vendor Name is required">
                                                        </div>
                                                    </div>

                                                    <div class="w-100"></div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" name="actual_weight" class="form-control rounded-right actual_weight" placeholder="Actual Weight*" data-rule-required="true" data-msg-required="Actual Weight is required" readonly>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" name="vehicle" class="form-control rounded-right vehicle" placeholder="Vehicle Number*" data-rule-required="true" data-msg-required="Vehicle Number is required">
                                                        </div>
                                                    </div> --}}
                                                    <div class="col">
{{--                                                        <div class="form-group">--}}
{{--                                                            <select name="fleet_id" class="select2 fleet_id"  data-rule-required="true" data-msg-required="Route is required">--}}
{{--                                                            </select>--}}
{{--                                                        </div>--}}
                                                    </div>
                                                    <div class="w-100"></div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" name="driver_name" class="form-control rounded-right driver_name" placeholder="Driver Name*" data-rule-required="true" data-msg-required="Driver Name is required">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" name="phone_number" class="form-control rounded-right phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                                        </div>
                                                    </div>

                                                    <div class="w-100"></div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <select name="shipping_mode_id" class="select2 shipping_mode_select" data-rule-required="true" data-msg-required="Shipping Mode is required">
                                                            </select>
                                                        </div>
                                                    </div>
{{--                                                    <div class="col">--}}
{{--                                                        <div class="form-group">--}}
{{--                                                            <input type="text" name="cnic" class="form-control rounded-right cnic" placeholder="CNIC">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
                                                </div>
                                            </div>
                                            <div class="modal-footer text-center justify-content-around">
                                                <button type="submit" name="submit_and_print_form" class="btn btn-primary btn-block" value="submit_and_print_form">Submit &amp; Print</button>
{{--                                                <button type="submit" name="submit_form" class="btn btn-primary" value="submit_form">Submit</button>--}}
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            @if (session('print'))
            $.ajax({
                url: '{!! route('admin.master_cargo.in_transit.print') !!}',
                method: 'POST',
                data: {
                    'id': '{{ session('print') }}',
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
                    @endif
            var bag_ids = [];
            var hub_id = 0;
            var shipping_mode_id = 0;

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                autoWidth : false,
                paging:false,
                columns: [
                    {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {name: 'bag_number', class: 'align-middle bag_number', orderable: false},
                    {name: 'shipments', class: 'align-middle shipments', orderable: false},
                    {name: 'origin', class: 'align-middle origin', orderable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false},
                    {name: 'actual_weight', class: 'align-middle actual_weight', orderable: false},
                    {name: 'action', class: 'align-middle action',orderable: false, searchable: false}
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

            $('#add_bag_form input.bag_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#add_bag_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#add_bag_form button.add').prop('disabled', true);

                    var bag_number = $(form).find('input.bag_number').val();

                    form.reset();

                    if (table.columns('.bag_number').data().eq(0).indexOf(parseInt(bag_number)) === -1) {
                        blockPagePermanently();
                        $.ajax({
                            url: '{!! route('admin.master_cargo.create.bag_details') !!}',
                            method: 'POST',
                            data: {
                                'bag_number': bag_number,
                                'hub_id': hub_id,
                                'shipping_mode_id': shipping_mode_id,
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 10000,
                            error: function (data) {
                                $('#add_bag_form button.add').prop('disabled', false);
                                UnblockPagePermanently();
                                scan_sound(2);
                                toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            },
                            success: function(data) {
                                if (data.status == 0) {
                                    id = data.details.id;

                                    var index = $.inArray(id, bag_ids);

                                    if (index === -1) {
                                        var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger bag_remove"><i class="la la-close"></i></a>';
                                        var rowNo = table.rows().count();
                                        table.row.add([rowNo+1, data.details.bag_number, data.details.shipments, data.details.origin, data.details.destination, data.details.actual_weight,remove]).node().id = data.details.id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        bag_ids.push(data.details.id);
                                        scan_sound(1);
                                        $('#information .scanned').html(bag_ids.length);

                                        if (hub_id == 0) {
                                            hub_id = data.details.hub.id;

                                            $('#information .hub').html(data.details.hub.name);

                                            $('#information .total').html(data.details.total);
                                        }

                                        if (shipping_mode_id == 0) {
                                            shipping_mode_id = data.details.shipping_mode.id;

                                            // $('#information .shipping_mode').html(data.details.shipping_mode.name);
                                        }

                                        $('#add_bag_form button.add').prop('disabled', false);

                                        $('#master_cargo_consignment_confirm').prop('disabled', false);
                                        UnblockPagePermanently();
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else {
                                    $('#add_bag_form button.add').prop('disabled', false);
                                    UnblockPagePermanently();
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        });
                    }
                    else {
                        $('#add_bag_form button.add').prop('disabled', false);
                        scan_sound(2);
                        toastr.error('Bag has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });
            $('#master_cargo_consignment_confirm').bind('click', function() {

                // if ($('#master_cargo_consignment form .junction_1').hasClass('select2-hidden-accessible')) {
                //     $('#master_cargo_consignment form .junction_1').html('').select2('destroy');
                // }

                // if ($('#master_cargo_consignment form .junction_2').hasClass('select2-hidden-accessible')) {
                //     $('#master_cargo_consignment form .junction_2').html('').select2('destroy');
                // }

                if ($('#master_cargo_consignment form .transport_mode').hasClass('select2-hidden-accessible')) {
                    $('#master_cargo_consignment form .transport_mode').html('').select2('destroy');
                }

                if ($('#master_cargo_consignment form .transport_mode_vendor').hasClass('select2-hidden-accessible')) {
                    $('#master_cargo_consignment form .transport_mode_vendor').html('').select2('destroy');
                }

                if ($('#master_cargo_consignment form .shipping_mode_select').hasClass('select2-hidden-accessible')) {
                    $('#master_cargo_consignment form .shipping_mode_select').html('').select2('destroy');
                }
                blockPagePermanently();
                $.ajax({
                    url: '{!! route('admin.master_cargo.create.cargo_details') !!}',
                    method: 'POST',
                    data: {
                        'bag_ids': bag_ids,
                        '_token': '{{ csrf_token() }}'
                    },
                    timeout: 10000,
                    error: function (data) {
                        $('#master_cargo_consignment').modal('hide');

                        UnblockPagePermanently();

                        toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    },
                    success: function (data) {
                        // $('#master_cargo_consignment form .shipping_mode_id').val(shipping_mode_id);
                        $('#master_cargo_consignment form .bag_ids').val(bag_ids);

                        $('#master_cargo_consignment form .origin_hub_id').val(data.origin.id);
                        $('#master_cargo_consignment form .origin').html(data.origin.name);

                        $('#master_cargo_consignment form .destination_hub_id').val(data.destination.id);
                        $('#master_cargo_consignment form .destination').html(data.destination.name);

                        $('#master_cargo_consignment form .actual_weight').val(data.actual_weight);

                        // $.each(data.junctions, function(index, junction) {
                        //     $('#master_cargo_consignment form .junction_1').append('<option value="' + junction.id + '">' + junction.name + '</option>');
                        //     $('#master_cargo_consignment form .junction_2').append('<option value="' + junction.id + '">' + junction.name + '</option>');
                        // });


                        $.each(data.routes, function(index, route) {
                            $('#master_cargo_consignment form .route_management_id').append('<option value="' + route.id + '">' + route.route_code    + '</option>');
                        });

                        $('#master_cargo_consignment form .route_management_id').prepend('<option value="" selected="selected"></option>').select2({
                                width: '100%',
                                placeholder: 'Select Route'
                            }).bind('change', function() {
                                $(this).valid();
                            });

                            $.each(data.fleets, function(index, fleet) {
                                console.log(fleet)
                            $('#master_cargo_consignment form .fleet_id').append('<option value="' + fleet.id + '">' + fleet.reg_number    + '</option>');
                        });

                        $('#master_cargo_consignment form .fleet_id').prepend('<option value="" selected="selected"></option>').select2({
                                width: '100%',
                                placeholder: 'Select Fleet'
                            }).bind('change', function() {
                                $(this).valid();
                            });
                        // if(data.junction_1) {
                        //     $('#master_cargo_consignment form .junction_1').val(data.junction_1);
                        //     $('#master_cargo_consignment form .junction_1').select2({
                        //         width: '100%',
                        //         placeholder: 'Junction 1'
                        //     }).bind('change', function() {
                        //         $(this).valid();
                        //     });
                        // }
                        // else{
                        //     $('#master_cargo_consignment form .junction_1').prepend('<option value="" selected="selected"></option>').select2({
                        //         width: '100%',
                        //         placeholder: 'Junction 1'
                        //     }).bind('change', function() {
                        //         $(this).valid();
                        //     });
                        // }
                        // if(data.junction_2) {
                        //     $('#master_cargo_consignment form .junction_2').val(data.junction_2);
                        //     $('#master_cargo_consignment form .junction_2').select2({
                        //         width: '100%',
                        //         placeholder: 'Junction 2',
                        //         allowClear: true
                        //     });
                        // }
                        // else{
                        //     $('#master_cargo_consignment form .junction_2').prepend('<option value="" selected="selected"></option>').select2({
                        //         width: '100%',
                        //         placeholder: 'Junction 2',
                        //         allowClear: true
                        //     })
                        // }

                        $('#master_cargo_consignment form input.actual_weight').inputmask({
                            'alias': 'decimal',
                            'allowMinus': false,
                            'allowPlus': false,
                            'digits': 2,
                            'min': 0.1,
                            'max': 100000
                        });

                        $('#master_cargo_consignment form input.phone_number').inputmask({
                            'mask': '9999-9999999',
                            'clearIncomplete': true
                        });
                        $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});

                        $('#master_cargo_consignment form input.cnic').inputmask({
                            'mask': "99999-9999999-9",
                            'clearIncomplete': true
                        }).bind('change', function() {
                            $(this).valid();
                        });
                        $.each(data.shipping_modes, function(index, shipping_mode) {
                            $('#master_cargo_consignment form .shipping_mode_select').append('<option value="' + shipping_mode.id + '">' + shipping_mode.mode + '</option>');
                        });

                        $('#master_cargo_consignment form .shipping_mode_select').prepend('<option value="" selected="selected"></option>').select2({
                            width: '100%',
                            placeholder: 'Select Shipping Mode*'
                        }).bind('change', function() {
                            $(this).valid();
                        });

                        $.each(data.transport_modes, function(index, transport_mode) {
                            $('#master_cargo_consignment form .transport_mode').append('<option value="' + transport_mode.id + '">' + transport_mode.name + '</option>');
                        });

                        $('#master_cargo_consignment form .transport_mode').prepend('<option value="" selected="selected"></option>').select2({
                            width: '100%',
                            placeholder: 'Transport Mode*'
                        }).bind('change', function() {
                            $(this).valid();

                            $('#master_cargo_consignment form .transport_mode_vendor').html('');

                            $.each(transport_mode_vendors[this.value], function(index, vendor) {
                                var option = new Option(vendor.name, vendor.id, false, false);
                                $('#master_cargo_consignment form .transport_mode_vendor').append(option);
                            });

                            var option = new Option('Others', 0, false, false);
                            $('#master_cargo_consignment form .transport_mode_vendor').append(option);

                            $('#master_cargo_consignment form .transport_mode_vendor').val(null).trigger('change');

                        });

                        transport_mode_vendors = data.transport_mode_vendors;

                        $('#master_cargo_consignment form .transport_mode_vendor').prepend('<option value="" selected="selected"></option>').select2({
                            width: '100%',
                            placeholder: 'Vendor*'
                        }).bind('change', function() {
                            if (this.value) {
                                $(this).valid();
                            }
                            console.log(this.value);
                            if (this.value && this.value == 0) {
                                $('#master_cargo_consignment #new_vendor').removeClass('d-none');
                            }
                            else {
                                $('#master_cargo_consignment #new_vendor').addClass('d-none');

                                $('#master_cargo_consignment #vendor_name-error').remove();
                            }
                        });
                        $('#master_cargo_consignment form .transport_mode').val(2).trigger('change');
                        $('#master_cargo_consignment form .transport_mode').prop("disabled", true);
                        $('#master_cargo_consignment form .shipping_mode_select').val(1).trigger('change');
                        $('#master_cargo_consignment form .transport_mode_vendor').val(9).trigger('change');
                        $('#master_cargo_consignment form .transport_mode_vendor').prop("disabled", true);
                        UnblockPagePermanently();
                    }
                });
            });

            $('#master_cargo_consignment form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    var pressed_button = $(this.submitButton);

                    $(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    blockPagePermanently();

                    swal({
                        text: 'Are you sure you want to submit?',
                        icon: 'info',
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
                    }).then(function(confirm) {
                        if(confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Your Master cargo is being created!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            $('#master_cargo_consignment form .transport_mode').prop("disabled", false);
                            $('#master_cargo_consignment form .transport_mode_vendor').prop("disabled", false);
                            form.submit();
                        }
                        else {
                            $(form).find('button[type=submit]').prop('disabled', false);

                            UnblockPagePermanently();
                        }
                    });
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

            $('body').on('click','.bag_remove',function () {
                var bag_id = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(bag_id, bag_ids);
                if(index !== -1){
                    bag_ids.splice(index,1);
                    table.row( $(this).parents('tr') ).remove().draw();
                    if(bag_ids.length == 0){
                        $('#information .scanned').html(bag_ids.length);

                        hub_id = 0;
                        shipping_mode_id = 0;


                        $('#information .hub').text('None');

                        $('#information .total').text(0);


                        $('#information .shipping_mode').text('None');


                        $('#add_bag_form button.add').prop('disabled', false);

                        $('#master_cargo_confirm').prop('disabled', false);

                    }else{
                        $('#information .scanned').html(bag_ids.length);
                    }
                }


            });
        });

        function camera_scan_detected(bag_number) {
            $('#add_bag_form input.bag_number').val(bag_number);

            $('#add_bag_form').trigger('submit');
        }
    </script>
@endsection