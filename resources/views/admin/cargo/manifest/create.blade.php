@extends('admin.layout.master')

@section('title', 'Create Cargo Manifest')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Create Cargo Manifest
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            @if(session('success_html'))
                                <div class="alert alert-success">
                                    {!! session('success_html') !!}
                                </div>
                            @endif

                            @if(session('error_html'))
                                <div class="alert alert-danger">
                                    {!! session('error_html') !!}
                                </div>
                            @endif

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

                                   {{-- <input type="text" name="bag_weight" class="form-control bag_weight ml-1" placeholder="Bag Weight*" data-rule-required="true" data-msg-required="Bag Weight is required">--}}
                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                                </div>
                            </form>

                            <div id="information" class="information text-center">
                                <button type="button" name="info_button" class="btn btn-dark info_button" id="info_button">Scanned: <span class="scanned">{{$scanned_bags}}</span>/<span class="total">{{$total_bags}}</span></button>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Bag Number</th>
                                    <th class="border-primary border-darken-1">Shipments</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                   {{-- <th class="border-primary border-darken-1">Bag Weight</th>--}}
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary mr-2" id="master_cargo_consignment_confirm" disabled="disabled">Confirm</button>
                            </div>

                            <div class="modal fade" id="cargo_details" role="dialog" aria-labelledby="cargo_details_title" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <form class="form-horizontal" method="POST" action="{{ route('admin.cargo_manifest.store') }}" novalidate="novalidate">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="bag_ids" id="bag_ids">

                                            <div class="modal-header">
                                                <h4 class="modal-title" id="cargo_details_title">Cargo Manifest</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <input type="text" name="total_weight" placeholder="Total Weight*" readonly class="form-control" id="total_weight">
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <select name="shipping_mode" class="select2" id="shipping_mode" data-rule-required="true" data-msg-required="Shipping Mode is required">
                                                                @foreach($shipping_modes as $mode)
                                                                    <option value="{{$mode->id}}"> {{$mode->mode}} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <input type="text" name="route_name" placeholder="Route Name*" class="form-control" data-rule-required="true" id="route_name" data-msg-required="Route Name is Required" data-rule-minlength="3" data-msg-minlength="Route Name must be atleast 3 character long">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-2 text-center">
                                                                <label for=""><b>Vehicle Type :</b></label>

                                                                <label class="display-inline ml-1">Temporary</label>
                                                                <input type="checkbox" name="vehicle_type" id="vehicle_type" class="switchery vehicle_type" data-size="xs" data-switchery="true" checked>
                                                                <label class="display-inline ml-1">Fixed</label>

                                                    </div>
                                                </div>
                                                <div class="row">
                                                     <div class="col-3">
                                                        <div class="form-group" id="vehicle_number_container">
                                                            <select name="vehicle_number" class="select2 vehicle_number" id="vehicle_number" data-rule-required="true" data-msg-required="Vehicle Number is Required">
                                                            </select>
                                                        </div>
                                                         <div class="form-group d-none" id="vehicle_number_text_container">
                                                             <input type="text" name="vehicle_number_text" id="vehicle_number_text" class="form-control" placeholder="Vehicle Number*" data-rule-required="true" data-msg-required="Vehicle Number is Required">
                                                         </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <input type="text" name="driver_name" class="form-control" placeholder="Drivers Name*" id="driver_name" readonly data-rule-required="true" data-msg-required="Driver Name is Required">
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <input type="text" name="driver_phone" class="form-control" placeholder="Drivers Phone*" id="driver_phone" readonly data-rule-required="true" data-msg-required="Driver Phone is Required">
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <input type="text" name="vendor_name" placeholder="Vendors Name*" class="form-control" id="vendor_name" readonly data-rule-required="true" data-msg-required="Vendor Name is Required">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <table class="table table-bordered datatable" id="cargo_datatable" style="z-index: 3;width: 100%">
                                                        <thead>
                                                            <tr role="row" class="bg-primary white">
                                                                <th class="border-primary border-darken-1">S. No.</th>
                                                                <th class="border-primary border-darken-1"></th>
                                                                <th class="border-primary border-darken-1">Hub</th>
                                                                <th class="border-primary border-darken-1">Junctions</th>
                                                                <th class="border-primary border-darken-1">No. of Bags</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="submit_and_print_form" class="btn btn-primary" value="submit_and_print_form">Submit &amp; Print</button>
                                                                                                <button type="submit" name="submit_form" class="btn btn-primary" value="submit_form">Submit</button>
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

    <div class="modal fade" id="total_bag_details" role="dialog" aria-labelledby="total_bag_details_title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="cargo_details_title">Total Bags Details</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            var total_bags = @json($total_bags);
            var scanned_bags = @json($scanned_bags);
            @if (session('print'))
            $.ajax({
                url: '{!! route('admin.cargo_manifest.print') !!}',
                method: 'POST',
                data: {

                    'cargo_manifest_ids': '{{ session('print') }}',
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
            manifest_bag_weight = [];

            function add_draft_bags_count(){
                var draft_bags = @json($draft_bags);

                if(draft_bags.length > 0) {
                    $.each(draft_bags, function (index, value) {
                        bag_ids.push(value.bag_id);
                        manifest_bag_weight.push(value.weight);

                    });
                    if (bag_ids.length > 0) {
                        $('#master_cargo_consignment_confirm').prop('disabled', false);
                    }
                }
            }
            add_draft_bags_count();


            var cargo_table = $('#cargo_datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                autoWidth : false,
                paging:false,
                columns: [
                    {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {name: 'vehicle_serial_number', class: 'align-middle vehicle_serial_number', orderable: false},
                    {name: 'hub', class: 'align-middle hub'},
                    {name: 'junctions', class: 'align-middle junctions', orderable: false},
                    {name: 'bags', class: 'align-middle bags'},
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#add_bag_form input.bag_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

           /* $('#add_bag_form input.bag_weight').inputmask({
                'alias': 'decimal',
                'digits': 2,
                'allowMinus': false,
                'allowPlus': false
            });*/


            $('#add_bag_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#add_bag_form button.add').prop('disabled', true);

                    var bag_number = $(form).find('input.bag_number').val();
                  /*  var bag_weight = $(form).find('input.bag_weight').val();*/

                    $(form).find('input.bag_number').focus();
                    form.reset();


                    if (table.columns('.bag_number').data().eq(0).indexOf(parseInt(bag_number)) === -1) {
                        blockPagePermanently();
                        $.ajax({
                            url: '{!! route('admin.cargo_manifest.bag_details') !!}',
                            method: 'POST',
                            data: {
                                'bag_number': bag_number,
                               /* 'bag_weight': bag_weight,*/
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 30000,
                            // error: function (data) {
                            //     $('#add_bag_form button.add').prop('disabled', false);
                            //     UnblockPagePermanently();
                            //     scan_sound(2);
                            //     toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            // },
                            success: function(data) {
                                if (data.status == 0) {
                                    id = data.details.id;

                                    var index = $.inArray(id, bag_ids);
                                    if (index === -1) {

                                        $('#datatable').DataTable().ajax.reload();
                                       // $('#datatable').DataTable().draw();
                                        bag_ids.push(data.details.id);
                                        manifest_bag_weight.push(data.details.bag_weight);
                                        total_bags = data.details.total_bags;
                                        scanned_bags = scanned_bags + 1;
                                        scan_sound(1);

                                        $('#add_bag_form button.add').prop('disabled', false);

                                        $('#master_cargo_consignment_confirm').prop('disabled', false);

                                        $('#information .scanned').html(scanned_bags);
                                        $('#information .total').html(total_bags);
                                        UnblockPagePermanently();
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                    else{
                                        $('#add_bag_form button.add').prop('disabled', false);
                                        UnblockPagePermanently();
                                        scan_sound(2);
                                        var error = "Bag Already Exists";
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                "autoWidth": false,
                paging:false,
                ajax: '{{ route('admin.cargo_manifest.draft.list') }}',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: false,
                rowId:'bag_id',
                columns: [
                    {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'bag_number', name: 'cargo_manifest_draft_bags.seal_number', class: 'align-middle bag_number'},
                    {data:'shipments_count', name: 'cargo_manifest_draft_bags.shipments_count', class: 'align-middle shipments_count'},
                    {data:'origin', name: 'c.name', class: 'align-middle origin'},
                    {data:'destination', name: 'd.name', class: 'align-middle destination'},
                    {data:'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });




            $('#master_cargo_consignment_confirm').bind('click', function() {

                let total_weight = 0;

                $.each(manifest_bag_weight,function(){total_weight+=parseFloat(this) || 0; });

                $("#cargo_details #total_weight").val(total_weight);

                $("#cargo_details #route_name").val('');

                blockPagePermanently();
                $.ajax({
                    url: '{!! route('admin.cargo_manifest.cargo_details') !!}',
                    method: 'POST',
                    data: {
                        'bag_ids': bag_ids,
                        '_token': '{{ csrf_token() }}'
                    },
                    timeout: 30000,
                    error: function (data) {
                        $('#cargo_details').modal('hide');

                        UnblockPagePermanently();

                        toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    },
                    success: function (data) {
                        if(data.status == 0) {


                            $('#cargo_details').modal('show');
                            $('#cargo_details form #bag_ids').val(bag_ids);

                            $('#cargo_details form #vehicle_number').html("");
                            $.each(data.details.vehicles, function (index, vehicle) {
                                $('#cargo_details form #vehicle_number').append('<option value="' + vehicle.id + '" data-driver_name="' + vehicle.driver_name + '" data-driver_phone="' + vehicle.driver_phone + '" data-vendor_name="' + vehicle.vendor_name + '">' + vehicle.reg_number + '</option>');
                            });

                            $('#cargo_details form #vehicle_number').prepend('<option value="" selected="selected"></option>').select2({
                                width: '100%',
                                placeholder: 'Select Vehicle Number*'
                            }).bind('change', function () {
                                $(this).valid();
                                if ($(this).find(":selected").attr('data-driver_name') == "null") {
                                    $('#cargo_details form #driver_name').val("");
                                } else {
                                    $('#cargo_details form #driver_name').val($(this).find(":selected").attr('data-driver_name'));
                                }
                                if ($(this).find(":selected").attr('data-vendor_name') == "null") {
                                    $('#cargo_details form #vendor_name').val("");
                                } else {
                                    $('#cargo_details form #vendor_name').val($(this).find(":selected").attr('data-vendor_name'));
                                }
                                $('#cargo_details form #driver_phone').val($(this).find(":selected").attr('data-driver_phone'));
                            });

                            $("#cargo_details form #vehicle_type").on('change', function () {
                                if ($(this).prop('checked')) {
                                    $('#cargo_details form #vehicle_number').val("").trigger('change');
                                    $('#cargo_details form #vehicle_number_text').val("");
                                    $('#cargo_details form #vehicle_number_container').removeClass('d-none');
                                    $('#cargo_details form #vehicle_number_text_container').addClass('d-none');
                                    $('#cargo_details form input#driver_phone').val("");
                                    $('#cargo_details form input#driver_name').val("");
                                    $('#cargo_details form input#vendor_name').val("");
                                    $('#cargo_details form input#driver_phone').attr('readonly', true);
                                    $('#cargo_details form input#driver_name').attr('readonly', true);
                                    $('#cargo_details form input#vendor_name').attr('readonly', true);
                                } else {
                                    $('#cargo_details form #vehicle_number').val("").trigger('change');
                                    $('#cargo_details form #vehicle_number_text').val("");
                                    $('#cargo_details form #vehicle_number_container').addClass('d-none');
                                    $('#cargo_details form #vehicle_number_text_container').removeClass('d-none');
                                    $('#cargo_details form input#driver_phone').val("");
                                    $('#cargo_details form input#driver_name').val("");
                                    $('#cargo_details form input#vendor_name').val("");
                                    $('#cargo_details form input#driver_phone').attr('readonly', false);
                                    $('#cargo_details form input#driver_name').attr('readonly', false);
                                    $('#cargo_details form input#vendor_name').attr('readonly', false);
                                }
                            });

                            $('#cargo_details form input#driver_phone').inputmask({
                                'mask': '9999-9999999',
                                'clearIncomplete': true
                            });

                            $('#cargo_details form #shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                                width: '100%',
                                placeholder: 'Select Shipping Mode*'
                            }).bind('change', function () {
                                $(this).valid();
                            });
                            cargo_table.rows().remove();
                            $.each(data.details.details,function (hub,value){
                                vehicle_seal_number_input = "<input type='hidden' value='"+value['bag_ids']+"' name='bag_ids["+hub+"]' ><div class='form-group'><input type='text' name='vehicle_seal["+hub+"]' class='vehicle_seal form-control' id='vehicle_seal_"+hub+"' data-rule-required='true' data-msg-required='Vehicle Seal is required' placeholder='Enter Vehicle Seal No.*' ></div>";
                                junctions = value['junctions'];
                                var rowNo = cargo_table.rows().count();
                                cargo_table.row.add([rowNo+1,vehicle_seal_number_input,value["destination"],junctions,value['number_of_bags']]);
                                cargo_table.draw(false);
                            });
                            $("#cargo_details form .vehicle_seal").inputmask({
                                'alias': 'integer',
                                'allowMinus': false,
                                'allowPlus': false
                            });

                            UnblockPagePermanently();

                        }
                        else{
                            UnblockPagePermanently();
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                });
            });

            $('#cargo_details').on('shown.bs.modal', function (e) {
                cargo_table.draw(false);
            });

            $('#cargo_details form').validate({
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
                                text: 'Your Cargo Manifest is being created!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
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
                var obj = $(this);
                var index = $.inArray(bag_id, bag_ids);

                if(index !== -1){
                    $.ajax({
                        url: '{!! route('admin.cargo_manifest.draft.delete') !!}',
                        method: 'POST',
                        data: {
                            'bag_id': bag_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                    .done(function(data) {

                        if (data.status == 1 || data.status == 0) {

                            $(obj).closest("tr").remove();
                            bag_ids.splice(index,1);
                        }


                        total_bags = data.total_bags;
                        scanned_bags = scanned_bags - 1;
                        $('#information .scanned').html(scanned_bags);
                        $('#information .total').html(total_bags);

                        if(bag_ids.length == 0){

                            $('#add_bag_form button.add').prop('disabled', false);

                            $('#master_cargo_confirm').prop('disabled', false);

                            $("#master_cargo_consignment_confirm").prop('disabled',true);
                            $('#datatable').DataTable().clear().draw();
                        }
                    });

                }
            });

            $('#info_button').on('click', function () {
                $('#total_bag_details .modal-body').html('');
                if(bag_ids.length > 0){
                    $.ajax({
                        url: '{!! route('admin.cargo_manifest.total_bags') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                        }
                    })
                        .done(function (data) {
                            var html = '';
                            html += '<table class="table table-sm datatable text-center">';
                            html += '<thead><tr><th>S No.</th><th><strong>Seal Number</strong></th><th><strong>Origin</strong></th><th><strong>Destination</strong></th><th><strong>Status</strong></th></tr></thead>';
                            html += '<tbody>';
                            var ind = 0;
                            $.each(data.details, function (index, value) {
                                ind = ind + 1;
                                html += '<tr class="' + value.class + '"><td>' + ind + '</td>';
                                html += '<td>' + value.seal_number + '</td>';
                                html += '<td>' + value.origin + '</td>';
                                html += '<td>' + value.destination + '</td>';
                                html += '<td>' + value.status + '</td></tr>';

                            });
                            html += '</tbody></table>';

                            $('#total_bag_details .modal-body').html(html);

                            $('#total_bag_details').modal('show');
                        });
                }
            });

        });

        function camera_scan_detected(bag_number) {
            $('#add_bag_form input.bag_number').val(bag_number);

            $('#add_bag_form').trigger('submit');
        }
    </script>
@endsection