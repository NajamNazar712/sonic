
@extends('admin.layout.master')
@section('title','Dispute Shipments')

@section('content')
    <h1 class="mb-1">
        Dispute Shipments
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">Image</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Added At</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="AddShipmentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddShipmentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Dispute Shipment</h4>

                </div>
                <form id="add_dispute_shipment_form" class="justify-content-center" novalidate="novalidate" method="post" action="{{ route('admin.dispute.shipments.submit') }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="modal-body text-center">

                        <div class="form-group">
                            <input type="text" name="tracking_number" id="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">

                        </div>

                        <div class="form-group">
                            <select name="reason_id" id="reason_select" class="form-control select2" data-rule-required="true" data-msg-required="Reason is required">
                                @foreach($reasons as $reason)
                                    <option value="{{$reason->id}}">{{$reason->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea name="remarks" id="remarks" rows="5" class="form-control" data-rule-required="true" data-msg-required="Remarks are required"></textarea>
                        </div>
                        <div>
                            <h2>Images Upload</h2>
                        </div>
                        <div class="col form-group">
                            <input class="form-control form-control-sm" type="file" name="image_1" id="upload_attachment" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-rule-required="true" data-msg-required="Image is required" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                        </div>
                        <div class="col form-group">
                            <input class="form-control form-control-sm" type="file" name="image_2" id="upload_attachment" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-rule-required="true" data-msg-required="Image is required" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                        </div>
                    </div>
                    <div class="modal-footer text-center">
                        <div class="col-3">
                            <button id="" type="button" class="btn btn-danger btn-block" data-dismiss="modal">Close</button>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="la la-plus"></i> Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style type="text/css">
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            $('#add_dispute_shipment_form #tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            })

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.dispute.shipments.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Shipper');
                            head.push('Reason');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('COD Amount');
                            head.push('Actual Weight');
                            head.push('Status');
                            head.push('Remarks');
                            head.push('Created At');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.reason);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.amount);
                                row.push(values.actual_weight);
                                row.push(values.status);
                                row.push(values.remarks);
                                row.push(values.created_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );


            $("#reason_select").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Reason",
                width:'100%',
                dropdownParent: $('#AddShipmentModal')
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true,
                buttons: [
                        @if (session('role_id') == 1 || in_array(704, session('permissions')))
                    {
                        text: '<i class="la la-plus"></i> Add Dispute Shipment',
                        className: 'btn btn-primary',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddShipmentModal').modal('show');
                        }
                    },
                        @endif

                    {
                        extend: 'excel',
                        title: 'Dispute Shipments',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.dispute.shipments.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'dispute_id',
                order: [[8, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'reason', name: 'dr.name', class: 'align-middle reason' , orderable: false, searchable: false},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'actual_weight', name: 'shipments.actual_weight', class: 'align-middle actual_weight'},
                    {data: 'image_view', name: 'image_view', class: 'align-middle image_viewa', orderable: false, searchable: false},
                    {data: 'status', name: 'ds.name', class: 'align-middle status' , orderable: false, searchable: false},
                    {data: 'remarks', name: 'v2_disputes.remarks', class: 'align-middle remarks'},
                    {data: 'created_at', name: 'v2_disputes.created_at', class: 'align-middle created_at'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.reason') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.action') || $(header).is('.image_view')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    this.api().table().columns.adjust();
                }
            });



            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            $('#datatable tbody').on('click','tr td.action a',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    if($(this).hasClass('update')){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to update dispute!',
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
                                $.ajax({
                                    url:"{{route('admin.dispute.shipments.update')}}",
                                    method:'POST',
                                    data:{
                                        'dispute_id':id,
                                        '_token':'{{ csrf_token() }}',
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                    }
                                    UnblockPagePermanently();

                                });
                            }
                        });

                    }else if($(this).hasClass('remaining_piece')){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to wait for remaining pieces!',
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
                                $.ajax({
                                    url:"{{route('admin.multiple_pieces.hold.wait_remaining_pieces')}}",
                                    method:'POST',
                                    data:{
                                        'shipment_id':id,
                                        '_token':'{{ csrf_token() }}',
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                    }
                                    UnblockPagePermanently();

                                });
                            }
                        });
                    }else if($(this).hasClass('return_to_shipper')){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to wait for Return Back To Shipper!',
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
                                $.ajax({
                                    url:"{{route('admin.multiple_pieces.hold.return_back_to_shipper')}}",
                                    method:'POST',
                                    data:{
                                        'shipment_id':id,
                                        '_token':'{{ csrf_token() }}',
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                    }
                                    UnblockPagePermanently();

                                });
                            }
                        });
                    }
                    else if($(this).hasClass('image_upload')){
                        $('#uploadImage #shipment_image_id').val(id);
                        $('#uploadImage').modal('show');
                    }
                }


            });

            $('#add_dispute_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Shipment is being added to Dispute!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();

                }
            });

            $('#AddShipmentModal').on('hidden.bs.modal',function () {
                $('#add_dispute_shipment_form')[0].reset();
                var form_errors = $('#add_dispute_shipment_form');
                form_errors.validate().resetForm();
                $('#add_dispute_shipment_form #reason_select').val('').trigger('change');
            });


        });
    </script>
@endsection