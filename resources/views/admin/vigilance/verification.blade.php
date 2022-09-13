@extends('admin.layout.master')
@section('title','Vigilance Verification')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Vigilance Verification
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center mb-2" id="search_form">
                                <div class="col-3">
                                    <fieldset>
                                        <input type="text" class="form-control" placeholder="Scan Delivery Note" id="search_delivery_note">
                                    </fieldset>
                                </div>

                                <div class="col-3">
                                    <fieldset class="form-group">
                                        <select name="search_rider" id="search_rider" class="form-control select2">
                                            @foreach($riders as $rider)
                                                <option value="{{$rider->id}}">{{$rider->name}} - {{$rider->trax_id}} - {{$rider->city->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <div>
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                        <div id="tracking_info" class="d-none mb-3 ml-1 mr-1">
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Delivery Note</th>
                                    <th class="border-primary border-darken-1">Shipper Name</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1">COD Amount</th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="row d-none" id="verify_div">
                            <div class="col-12">
                                <form id="scan_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                    <div class="form-group pr-2">
                                        <label id="dncc_title" class="d-none">Delivery Note Number : <span></span></label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                                    </div>

                                    <div class="form-group ml-1">
                                        <button type="submit" name="add" class="btn btn-primary add" value="Add">Scan</button>
                                    </div>
                                </form>

                                <form id="verify_shipment_form" action="{{route('admin.vigilance.verification.add')}}" class="form mb-1" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="delivery_note_id" id="verify_delivery_note_id">
                                    <table class="table table-bordered datatable" id="verify_datatable" style="z-index: 3;">
                                        <thead>
                                        <tr role="row" class="bg-primary white">

                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">Vigilance Verified CN</th>
                                            <th class="border-primary border-darken-1">Tracking No.</th>
                                            <th class="border-primary border-darken-1">Origin</th>
                                            <th class="border-primary border-darken-1">Destination</th>
                                            <th class="border-primary border-darken-1">Status Date and Time</th>
                                            <th class="border-primary border-darken-1">Status</th>
                                            <th class="border-primary border-darken-1">Amount</th>
                                        </tr>
                                        </thead>
                                    </table>
                                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                                    <input type="hidden" name="shipments_verify" id="shipments_verify">
                                    <div class="row justify-content-center">
                                        <div class="col-3">
                                            <button type="submit" class="btn btn-primary btn-block" disabled id="verify_shipment_form_submit">Submit</button>

                                        </div>

                                    </div>
                                </form>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
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
        #verify_datatable .verify.vf{
            background-color: greenyellow;
            color: black;
            font-weight: bold;
        }
        #verify_datatable .verify.ex{
            background-color: #f82020;
            color: black;
            font-weight: bold;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_delivery_note').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });


            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Rider',
                width: '100%',
                allowClear: true
            });

            $("#search_form").keyup(function(event) {
                if (event.keyCode === 13) {
                    $("#search_filter_btn").click();
                }
            });



            var table;
            function init(){

                table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l>tipr',
                    lengthMenu: [[10, 50, 100, 200], [10, 50, 100, 200]],
                    bInfo: false,
                    pageLength: 200,
                    pagingType: 'full_numbers',
                    processing: true,
                    language: {
                        processing: data_table_loader
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.vigilance.verification.list') }}',
                        data: function (d) {
                            d.search_delivery_note_id = $('#search_delivery_note').val();
                            d.search_rider = $('#search_rider').val();
                        }
                    },
                    order: [3, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_numbers'},
                        {data: 'delivery_note', name: 'delivery_notes.id', class: 'align-middle delivery_note'},
                        // {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
                        // {data: 'shipment_status', name: 'ss.name', class: 'align-middle shipment_status'},
                        {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                        {data: 'rider', name: 'r.name', class: 'align-middle rider'},
                        {data: 'amount', name: 's.amount', class: 'align-middle amount'}
                    ],
                    rowCallback: function (row, data, index) {
                        var info = table.page.info();
                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                        if(index === 0){
                            $('#dncc_title').removeClass('d-none');
                            $('#dncc_title span').text(data.delivery_note);
                            $('#verify_delivery_note_id').val(data.delivery_note_id);
                        }
                    },
                    initComplete: function () {

                        this.api().table().columns.adjust();
                    }
                });

            }
            $('#search_filter_btn').on('click', function () {
                if($('#tracking_info').hasClass('d-none')) {
                    $('#tracking_info').removeClass('d-none');
                    init();
                    $('#verify_div').removeClass('d-none');
                }
                else {
                    table.draw();
                    $('#dncc_title').addClass('d-none');
                    $('#verify_delivery_note_id').val('');
                }
            });

            var shipment_ids = [];
            var shipments_verify = [];
            var vtable = $('#verify_datatable').DataTable({
                dom: 'ltipr',
                paging:false,
                "autoWidth": false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'verify', class: 'align-middle verify form-group', orderable: false},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'origin', class: 'align-middle origin form-group', orderable: false},
                    {name: 'destination', class: 'align-middle destination form-group', orderable: false},
                    {name: 'status_date', class: 'align-middle status_date', orderable: false},
                    {name: 'shipment_status', class: 'align-middle shipment_status', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                ],
                rowCallback: function(row, data, index) {
                },
                initComplete: function() {

                    this.api().table().columns.adjust();
                }
            });

            $('#verify_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#scan_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#scan_shipment_form button.add').prop('disabled', true);
                    var delivery_note_id = $('#verify_delivery_note_id').val();
                    if(!delivery_note_id){
                        toastr.error('Delivery Note not found, please refresh and try again!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    var tracking_number = $.trim($(form).find('input.tracking_number').val());

                    form.reset();

                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        blockPagePermanently();
                        $.ajax({
                            url: '{!! route('admin.vigilance.verification.info') !!}',
                            method: 'POST',
                            data: {
                                'delivery_note_id': delivery_note_id,
                                'tracking_number': tracking_number,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 1) {
                                    UnblockPagePermanently();
                                    id = data.details.id;
                                    var index = $.inArray(id, shipment_ids);
                                    console.log(index);
                                    if (index === -1) {
                                        var rowNo = vtable.rows().count();
                                        var shipment_id = data.details.id;

                                        vtable.row.add([rowNo + 1, data.details.verify, data.details.tracking_number, data.details.origin, data.details.destination,data.details.status_date,data.details.shipment_status, data.details.amount]).node().id = shipment_id;
                                        vtable.draw(false);
                                        if(data.details.verify_id == 1){
                                            scan_sound(1);
                                        }else{
                                            scan_sound(2);
                                        }
                                        vtable.order([0, 'desc']).draw();
                                        var verify_id = data.details.verify_id;
                                        shipment_ids.push(shipment_id);
                                        shipments_verify.push(verify_id);
                                        if(verify_id == 1){
                                            $('#verify_datatable tr#'+ id + ' td.verify').addClass('vf');
                                        }
                                        else{
                                            $('#verify_datatable tr#'+ id + ' td.verify').addClass('ex');
                                        }


                                        $('#scan_shipment_form button.add').prop('disabled', false);

                                        $('#verify_shipment_form_submit').prop('disabled', false);

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        UnblockPagePermanently();
                                        $('#scan_shipment_form button.add').prop('disabled', false);
                                        scan_sound(2);
                                        toastr.error('Tracking Number Already Scanned', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                }
                                else {
                                    UnblockPagePermanently();
                                    $('#scan_shipment_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                    else {
                        $('#scan_shipment_form button.add').prop('disabled', false);
                        scan_sound(2);
                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            $('#verify_shipment_form').validate({
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
                        text: 'Select Yes to Verify Shipments!',
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
                            $('#verify_shipment_form button[type="submit"]').attr('disabled', 'disabled');
                            $('#verify_shipment_form input#shipment_ids').val(shipment_ids);
                            $('#verify_shipment_form input#shipments_verify').val(shipments_verify);
                            form.submit();
                        }
                    });

                    // form.submit();
                }
            });
        });
    </script>
@endsection