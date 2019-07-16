
@extends('admin.layout.master')
@section('title','Receive Deliveries')

@section('content')
    <h1 class="mb-1">
        Receive Deliveries(Delivery Note: {{str_pad($delivery_note_id, 6, '0', STR_PAD_LEFT)}})
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="status_update_form" action="{{route('admin.delivery.receive.add.status')}}" method="post">
                    @csrf
                    <input type="hidden" value="{{$delivery_note_id}}" id="delivery_note" name="delivery_note_id">
                    <input type="hidden" value="{{$shipments_count}}" id="shipments_count" name="shipments_count">
                    <input type="hidden" name="shipment_ids" id="shipment_ids">

                    <div class="row justify-content-center">
                        <div class="col-4">
                            <h3>Delivery Ratio {{$percentage}}%</h3>
                        </div>
                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="select_all_status" id="select_all_status" class="form-control select2">
                                    @foreach($shipment_statuses as $status)
                                        <option value="{{$status->id}}">{{$status->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        @if(!$delivery_note_status == 1)
                            <div class="col-3">
                                <button type="button" id="submit_selected_status" disabled class="mr-1 mb-1 btn btn-primary btn-min-width"><i class="la la-list-alt"></i> Bulk Update </button>
                            </div>
                        @endif
                    </div>

                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1"></th>
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Shipment ID</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Consignee</th>
                            <th class="border-primary border-darken-1">Collection Amount</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Reason</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Received/Refused By</th>
                            <th class="border-primary border-darken-1">Address</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Current Status</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Attempts Count</th>
                            <th class="border-primary border-darken-1">Clear</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center">
                        @if($delivery_note_status == 0)
                            <div class="mr-1">
                                <button id="statusSubmit" type="submit" disabled class="btn btn-primary btn-block">Update Status</button>
                            </div>
                        @endif
                        @if($delivery_note_status == 1)
                            <div class="mr-1">
                                <button id="printDNCC" type="button" class="btn btn-warning btn-block">Print DNCC</button>
                            </div>
                        @else
                            @if($undelivered_printed == 1)
                                <div class="mr-1">
                                    <button id="printTempDNCC" type="button" class="btn btn-warning btn-block">Print Temporary DNCC</button>
                                </div>
                            @endif
                        @endif
                        @if($shipment_update == 1)
                            <div class="mr-1 ml-1">
                                <button id="printUndeliveredDNCC" type="button" class="btn btn-warning btn-block">Print Undelivered Performa</button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!--Replacement Modal -->
    <div class="modal fade text-left" id="ReplacementModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ReplacementModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Replacement Shipment(s) Weight</h4>
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                    {{--<span aria-hidden="true">&times;</span>--}}
                    {{--</button>--}}
                </div>
                <div class="modal-body  text-center">
                    <form id="replacement_form" action="{{route('admin.delivery.receive.replacements.submit')}}" method="post">
                        <table class="table table-bordered datatable" id="replacementtable" style="z-index: 3;">
                            <thead>
                            @csrf
                            @method('PUT')
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Tracking No.</th>
                                <th class="border-primary border-darken-1">Service Type</th>
                                <th class="border-primary border-darken-1">Weight Of Shipment</th>

                            </tr>
                            </thead>
                        </table>

                        <input type="hidden" name="shipment_id_list" id="shipment_id_list">
                        <input type="hidden" name="delivery_note_id" value="{{$delivery_note_id}}">

                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="ReplacementUpdate" type="submit" class="btn btn-primary btn-block">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Replacement Modal -->
    <!--Try&Buy Modal -->
    <div class="modal fade text-left" id="TryBuyModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="TryBuyModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Try &amp; Buy Delivery</h4>
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                    {{--<span aria-hidden="true">&times;</span>--}}
                    {{--</button>--}}
                </div>
                <div class="modal-body  text-center">
                    <form id="trybuy_form" action="{{route('admin.delivery.receive.trybuys.submit')}}" method="post">
                        <table class="table table-bordered datatable" id="trybuytable" style="z-index: 3;">
                            <thead>
                            @csrf
                            @method('PUT')
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Product Type</th>
                                <th class="border-primary border-darken-1">Product Description</th>
                                <th class="border-primary border-darken-1">Item Price</th>
                                <th class="border-primary border-darken-1">Receiving</th>

                            </tr>
                            </thead>
                        </table>
                        <div class="row justify-content-center mb-2">
                            <div class="col">
                                <h4><U>Total Collection Amount:</U> Rs: <span id="cod"></span></h4>
                            </div>
                        </div>
                        <input type="hidden" name="trybuy_id_list" id="trybuy_id_list">
                        <input type="hidden" name="trybuy_cod" id="trybuy_cod">
                        <input type="hidden" name="item_checked" id="item_checked">
                        <input type="hidden" name="item_unchecked" id="item_unchecked">
                        <input type="hidden" name="delivery_note_trybuy" id="delivery_note_trybuy">
                        <input type="hidden" name="trybuy_shipment_id" id="trybuy_shipment_id">
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="TrybuyUpdate" type="submit" class="btn btn-primary btn-block">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Try&Buy Modal -->
    <!--Non Service Modal -->
    <div class="modal fade text-left" id="NonServiceModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="NonServiceModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Out of Service Area Shipment(s) Charges</h4>
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                    {{--<span aria-hidden="true">&times;</span>--}}
                    {{--</button>--}}
                </div>
                <div class="modal-body  text-center">
                    <form id="nsa_form" action="{{route('admin.delivery.receive.nsa_shipments.submit')}}" method="post">
                        <table class="table table-bordered datatable" id="nsatable" style="z-index: 3;">
                            <thead>
                            @csrf
                            @method('PUT')
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Tracking No.</th>
                                <th class="border-primary border-darken-1">Consignee Address</th>
                                <th class="border-primary border-darken-1">Destination</th>
                                <th class="border-primary border-darken-1">Shipper Name</th>
                                <th class="border-primary border-darken-1">Estimated Charges</th>
                                <th class="border-primary border-darken-1">Remarks</th>

                            </tr>
                            </thead>
                        </table>

                        <input type="hidden" name="nsa_shipment_ids" id="nsa_shipment_ids">
                        <input type="hidden" name="delivery_note_id" value="{{$delivery_note_id}}">

                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="NsaUpdate" type="submit" class="btn btn-primary btn-block">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Non Service Modal -->
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
        table.dataTable tbody tr.statusUpdated {
            background-color:yellow;
            color: #000;
        }
        table.dataTable tbody tr.statusDelivered {
            background-color:springgreen;
            color: #000;
        }
        table.dataTable tbody tr.statusReturn {
            background-color: #ef5753;
            color: #000;
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

        table.dataTable tbody tr td.status,
        table.dataTable tbody tr td.reason,
        table.dataTable tbody tr td.remarks {
            min-width: 110px !important;
            max-width: 150px !important;
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
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#select_all_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Selected Status Update',
                width:'100%',
                allowClear:true
            });
            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 3,
                'min': 0.00,
                'max': 10000
            });
            var shipment_status = [];
            var shipment_reason = [];
            var shipment_remarks = [];
            var selected_rows = [];
            var note_id = $('#delivery_note').val();
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true,
                buttons: [
                        @if(!$delivery_note_status == 1)
                        {{--{--}}
                        {{--text: 'Delivered',--}}
                        {{--className: 'btn btn-primary delivered',--}}
                        {{--enabled: false,--}}
                        {{--action: function (e, dt, node, config) {--}}
                        {{--if(selected_rows !== ''){--}}
                        {{--swal({--}}
                        {{--title: 'Are You Sure?',--}}
                        {{--text: 'Select Yes to mark shipments as Delivered!',--}}
                        {{--icon: 'warning',--}}
                        {{--buttons: {--}}
                        {{--cancel: {--}}
                        {{--text: 'No',--}}
                        {{--value: null,--}}
                        {{--visible: true,--}}
                        {{--closeModal: true,--}}
                        {{--},--}}
                        {{--confirm: {--}}
                        {{--text: 'Yes',--}}
                        {{--value: true,--}}
                        {{--visible: true,--}}
                        {{--closeModal: true--}}
                        {{--}--}}
                        {{--},--}}
                        {{--closeOnClickOutside: false,--}}
                        {{--closeOnEsc: false,--}}
                        {{--dangerMode: true--}}
                        {{--}).then(function (confirm) {--}}
                        {{--if (confirm) {--}}

                        {{--table.rows().nodes().each(function(index) {--}}
                        {{--var row = table.row(index);--}}
                        {{--if ($(row.node()).hasClass('selected')) {--}}
                        {{--var id = parseInt(row.id());--}}
                        {{--var remarks = $(row.node()).find('td.remarks input').val();--}}
                        {{--shipment_remarks[id] = remarks;--}}
                        {{--}--}}
                        {{--});--}}

                        {{--$.ajax({--}}
                        {{--url: '{!! route('admin.delivery.receive.delivered') !!}',--}}
                        {{--method: 'POST',--}}
                        {{--data: {--}}
                        {{--'shipment_ids': selected_rows,--}}
                        {{--'delivery_note_id': note_id,--}}
                        {{--'_token': '{{ csrf_token() }}',--}}
                        {{--'remark': shipment_remarks--}}
                        {{--}--}}
                        {{--}).done(function (data) {--}}
                        {{--if(data.status === 0){--}}

                        {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

                        {{--}else{--}}
                        {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

                        {{--}--}}
                        {{--location.reload();--}}

                        {{--});--}}
                        {{--}--}}
                        {{--});--}}


                        {{--}else{--}}
                        {{--var error = "Something went wrong please refresh page and try again!";--}}
                        {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

                        {{--}--}}
                        {{--}--}}

                        {{--}, --}}
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.delivered').enable();
                                    $('#submit_selected_status').attr('disabled', false);
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                }
                            });
                            if (selected_rows.length == 0) {
                                table.button('.delivered').disable();
                            }
                            $('#submit_selected_status').attr('disabled', true);
                        }
                    },
                    @endif
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: false,
                paging: false,
                ajax: '{{ route('admin.delivery.receive.add.list',['id'=>$delivery_note_id]) }}',
                rowId: 'shId',
                order: [[2, 'asc']],
                ordering: false,
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'shipment_id_padded',name: 'shipments.id', class: 'align-middle shipment_id'},
                    {data:'tracking_number',name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data:'consignee_name',name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data:'collection_amount',name: 'shipments.amount', class: 'align-middle amount'},
                    {data:'status',name: 'status', class: 'align-middle status statusOnChange',orderable: false, searchable: false},
                    {data:'reason',name: 'reason', class: 'align-middle reason reasonSelect',orderable: false, searchable: false},
                    {data:'remarks',name: 'remarks', class: 'align-middle remarks',orderable: false, searchable: false},
                    {data:'received_or_refused_by',name: 'received_or_refused_by', class: 'align-middle received_or_refused_by',orderable: false, searchable: false},
                    {data:'address',name: 'shipments.consignee_address', class: 'align-middle address'},
                    {data:'destination',name: 'oc.name', class: 'align-middle destination'},
                    {data:'shipper',name: 'shipper', class: 'align-middle shipper'},
                    {data:'current_status',name: 'current_status', class: 'align-middle current_status'},
                    {data:'service_type',name: 'service_type', class: 'align-middle service_type'},
                    {data:'attempts' ,name: 'shipments.id', class: 'align-middle attempts'},
                    {data:'action',name: 'action', class: 'align-middle action',orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                drawCallback: function (settings) {

                    $(".reasonDrop").prepend('<option value="" selected="selected"></option>').select2({
                        placeholder: "Select a Reason",
                        width:'100%'
                    });
                    $(".statusDrop").prepend('<option value="" selected="selected"></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%'
                    });
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();
                    $.each(data,function (key,value) {
                        if(shipment_status.length !== 0){
                            $('select[name="status_drop['+value.shId+']"]').val(shipment_status[value.shId]).trigger('change');
                        }
                        if(shipment_reason.length !== 0){
                            $('select[name="reason_drop['+value.shId+']"]').val(shipment_reason[value.shId]).trigger('change');
                        }
                        if(shipment_remarks.length !== 0){
                            $('input[name="remarks['+value.shId+']"]').val(shipment_remarks[value.shId]);
                        }
                    });
                },
                initComplete: function() {

                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.reason') || $(header).is('.remarks') || $(header).is('.action') || $(header).is('.received_or_refused_by')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change keypress', function() {
                                if ($(header).is('.amount')){
                                    var value = $(this).val().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                                    column.search(value, false, false, true).draw();
                                }
                                else {
                                    column.search($(this).val(), false, false, true).draw();
                                }
                            }).wrap(td).after(icon);
                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.delivered').enable();
                    $('#submit_selected_status').attr('disabled', false);
                }
                else {
                    table.button('.delivered').disable();
                    $('#submit_selected_status').attr('disabled', true);
                }
            });



            $('body').on('select2:select','.statusOnChange .statusDrop',function (e) {
                var rowid = parseInt($(this).parents('tr').attr('id'));

                $('#statusSubmit').removeAttr('disabled');
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
                        });
                        reason.val('').trigger('change');
                    }else{
                        reason.empty().trigger('change');
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });
            $('body').on('select2:select','.reasonSelect .reasonDrop',function (e) {
                var rowid = parseInt($(this).parents('tr').attr('id'));
                var reasonSelection = $(this).find(':selected');
                var reason_status = reasonSelection.val();
                shipment_reason[rowid] = reason_status;
            });
            $('body').on('click','.clear',function () {
                var status = $(this).parents().closest('tr').find('.statusDrop');
                var reason = $(this).parents().closest('tr').find('.reasonDrop');
                status.val('').trigger("change");
                reason.val('').trigger("change");
                $('.remarks input').val('');
            });
            $('#status_update_form').on('keypress',function (e) {
                if(e.which == 13) {
                    e.preventDefault();
                }
            });
            var shipments = [];
            $('#status_update_form').bind('submit', function(event) {
                event.preventDefault();
                var this_form = this;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to change the status of shipments!',
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
                    if (confirm) {

                        var shipment = $('#shipment_ids');
                        event.preventDefault();
                        var id = '';
                        var count = table.data().count();
                        for(var i = 0;i<count;i++){
                            id = table.row( i ).id();
                            shipments.push(id);
                        }
                        shipment.val(shipments);
                        blockPagePermanently();
                        this_form.submit();
                    }
                });

            });
            //on page load ajax
            var trybuy_ids = [];
            var shipment_id_list = [];
            var shipments_count = $('#shipments_count').val();
            function checkShipmentStatuses(){
                var delivery_note = $('#delivery_note').val();
                $.ajax({
                    url: '{!! route('admin.delivery.receive.shipmentstatuscheck') !!}',
                    method: 'POST',
                    data: {
                        'delivery_note_id': delivery_note,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {


                    if(shipments_count>0) {
                        if (data.status == 1) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                            checkShipmentStatuses();
                        } else if (data.status == 2) {

                            $('#ReplacementModal').modal('show');

                            var repl = $('#replacementtable').DataTable({
                                dom: 'ltipr',
                                paging: false,
                                columns: [
                                    {
                                        orderable: false,
                                        searchable: false,
                                        name: 'serial_number',
                                        class: 'align-middle serial_number',
                                        targets: 1,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    {
                                        name: 'tracking_number',
                                        class: 'align-middle tracking_number',
                                        orderable: false,
                                        searchable: false
                                    },
                                    {
                                        name: 'service_type',
                                        class: 'align-middle service_type',
                                        orderable: false,
                                        searchable: false
                                    },
                                    {name: 'weight', class: 'align-middle weight', orderable: false, searchable: false},

                                ],
                                rowCallback: function (row, data, index) {
                                    var info = repl.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                            });

                            $.ajax({
                                url: '{!! route('admin.delivery.receive.replacements') !!}',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    'delivery_note_id': delivery_note,
                                    'replacements': data.replacement,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status == 0) {
                                    var rowNo = repl.rows().count();
                                    $.each(data.data, function (key, value) {
                                        var inp = "<div class='form-group mb-0'><input class='form-control decimal' name='weight[" + value.id + "]' placeholder='Enter Weight'  data-rule-required='true' data-msg-required='Weight is required!'></div>";
                                        repl.row.add([rowNo + 1, value.tracking_number, value.booking_type_id, inp]).node().id = value.id;
                                        repl.draw(false);
                                        shipment_id_list.push(value.id);
                                        $('.decimal').inputmask({
                                            'alias': 'decimal',
                                            'allowMinus': false,
                                            'allowPlus': false,
                                            'rightAlign': false,
                                            'digits': 3,
                                            'min': 0.01,
                                            'max': 10000
                                        });
                                    });

                                } else {
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });


                        }else if (data.status == 9){
                            console.log(data.non_service_area_shipments)
                            $('#NonServiceModal').modal('show');

                            var nsatable = $('#nsatable').DataTable({
                                dom: 'ltipr',
                                paging: false,
                                columns: [
                                    {
                                        orderable: false,
                                        searchable: false,
                                        name: 'serial_number',
                                        class: 'align-middle serial_number',
                                        targets: 1,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    { name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false  },
                                    { name: 'address', class: 'align-middle address', orderable: false, searchable: false },
                                    { name: 'destinatoin', class: 'align-middle destinatoin', orderable: false, searchable: false},
                                    { name: 'shipper', class: 'align-middle shipper', orderable: false, searchable: false},
                                    { name: 'charges', class: 'align-middle charges', orderable: false, searchable: false},
                                    { name: 'remarks', class: 'align-middle remarks', orderable: false, searchable: false},

                                ],
                                rowCallback: function (row, data, index) {
                                    var info = nsatable.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                            });

                            $.ajax({
                                url: '{!! route('admin.delivery.receive.nsa_shipments_data') !!}',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    'delivery_note_id': delivery_note,
                                    'nsa_shipments': data.non_service_area_shipments,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status == 0) {
                                    var rowNo = nsatable.rows().count();
                                    $.each(data.shipments, function (key, value) {
                                        var charges = "<div class='form-group mb-0'><input class='form-control decimal' name='charges[" + value.id + "]' placeholder='Enter Estimated Charges'  data-rule-required='true' data-msg-required='Estimated Charges is required!'></div>";
                                        var remarks = "<div class='form-group mb-0'><input class='form-control' name='remarks[" + value.id + "]' placeholder='Enter Remarks'  data-rule-required='true' data-msg-required='Remark is required!' value='" + value.remarks + "'></div>";
                                        // console.log(value.tracking_number)
                                        nsatable.row.add([rowNo + 1, value.tracking_number, value.consignee_address, value.consignee_city_id, value.user_id, charges, remarks]).node().id = value.id;
                                        nsatable.draw(false);
                                        shipment_id_list.push(value.id);
                                        $('.decimal').inputmask({
                                            'alias': 'integer',
                                            'allowMinus': false,
                                            'allowPlus': false,
                                            'rightAlign': false,
                                            'min': 0,
                                            'max': 100000
                                        });
                                    });

                                } else {
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });

                        } else if (data.status == 3) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                            $('#TryBuyModal').modal('show');
                            // checkShipmentStatuses();


                            trybuy = $('#trybuytable').DataTable({
                                dom: 'ltipr',
                                paging:false,

                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                    {name: 'product_type', class: 'align-middle product_type'},
                                    {name: 'product_description', class: 'align-middle product_description'},
                                    {name: 'item_price', class: 'align-middle item_price'},
                                    {name: 'receiving', class: 'align-middle receiving'},

                                ],
                                rowCallback: function(row, data, index) {
                                    var info = trybuy.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                }
                            });

                            //trybuy shipment id for modal
                            $('#trybuy_shipment_id').val(data.try);
                            $.ajax({
                                url:'{!! route('admin.delivery.receive.trybuys') !!}',
                                type:'POST',
                                dataType:'json',
                                data: {
                                    'delivery_note_id': delivery_note,
                                    'trybuy':data.try,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {

                                if(data.status == 0){
                                    var rowNo = trybuy.rows().count();
                                    $.each(data.data,function (key,value) {
                                        trybuy_ids.push(value.pid);
                                        var inp = "<input type='checkbox' checked class='form-control bought' name='bought["+value.pid+"]'>";
                                        // console.log(value.tracking_number)
                                        trybuy.row.add([rowNo+1,value.type,value.description,value.price,inp]).node().id = value.pid;
                                        trybuy.draw(false);
                                        // shipment_id_list.push(value.id);
                                        $('#cod').text(data.total_cod);
                                    });


                                }else{
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });


                        } else if (data.status == 0) {

                        }
                    }
                    shipments_count = shipments_count-1;
                });
            }
            checkShipmentStatuses();

            function print(id,temp = null) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.dncc.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'temporary':temp,
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
            function printUndelivered(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.undelivered.print') !!}',
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
                        location.reload();

                    });
            }

            $('#printDNCC').on('click',function () {
                var note_id = $('#delivery_note').val();
                print(note_id);
            });
            $('#printTempDNCC').on('click',function () {
                var note_id = $('#delivery_note').val();
                var temporary = 'temporary';
                print(note_id,temporary);
            });
            $('#printUndeliveredDNCC').on('click',function () {
                var note_id = $('#delivery_note').val();
                printUndelivered(note_id);
            });


            $('body').on('click','.receiving input:checkbox',function () {
                var check = $(this);
                var id = parseInt($(this).parents('tr').attr('id'));
                var price = $(this).parents('tr').find('td.item_price').text();
                var total_cod = $('#cod').text();
                var newcod = '';
                if($.isNumeric(price)){
                    if(check.is(':checked')){
                        newcod = parseInt(total_cod) + parseInt(price);
                        $('#cod').text(newcod);
                        trybuy_ids.push(id);

                    }else{
                        trybuy_ids.splice( $.inArray(id, trybuy_ids), 1 );
                        newcod = parseInt(total_cod) - parseInt(price);
                        $('#cod').text(newcod);
                    }
                }
            });
            //replacement modal bind
            $('#replacement_form').bind('submit',function (e) {
                e.preventDefault();

                // this.submit();
            });
            $( "#replacement_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#shipment_id_list').val(shipment_id_list);
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    form.submit();

                }
            });
            //end replacement
            $('#trybuy_form').bind('submit',function (e) {
                blockPagePermanently();
                e.preventDefault();
                var total = $('#cod').text();
                total = parseInt(total);
                var deliverynote_id = $('#delivery_note').val();
                $('#trybuy_cod').val(total);
                $('#trybuy_id_list').val(trybuy_ids);
                var checkbox_count = $('.bought:checked').length;
                var uncheckbox_count = $('input:checkbox.bought').length;
                $('#item_checked').val(checkbox_count);
                $('#item_unchecked').val(uncheckbox_count);
                $('#delivery_note_trybuy').val(deliverynote_id);
                if(checkbox_count > 0){
                    UnblockPagePermanently();
                    this.submit();
                }else{
                    UnblockPagePermanently();
                    var error = "Select at-least one item!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });

            var shipment_remarks_obj = {};
            var shipment_received_refused_obj = {};
            var submit_all_status_flag = true;
            $('#submit_selected_status').on('click', function () {
                var select_all_status = $('#select_all_status').val();
                var delivery_note = $('#delivery_note').val();
                if(selected_rows.length > 0){
                    if(select_all_status != ''){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to change the status of shipments!',
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
                            if (confirm) {
                                var not_updated_shipments = [];
                                var shipment_remarks_obj = {};
                                var shipment_received_refused_obj = {};
                                // blockPagePermanently();
                                submit_all_status_flag = true;
                                if(select_all_status == 14){
                                    table.rows().nodes().each(function(index) {
                                        var row = table.row(index);
                                        if ($(row.node()).hasClass('selected')) {
                                            var id = parseInt(row.id());
                                            var amount = parseInt($(row.node()).attr('amount'));
                                            var remarks = $(row.node()).find('td.remarks input').val();
                                            shipment_remarks_obj[id] = remarks;
                                            if(amount == 0){
                                                var receiver_name =  $(row.node()).find('td.received_or_refused_by input').val();
                                                if($.trim(receiver_name) == ''){
                                                    not_updated_shipments.push($(row.node()).find('td.tracking_number').text()) ;
                                                    submit_all_status_flag = false;
                                                }else{
                                                    shipment_received_refused_obj[id] = receiver_name;
                                                }
                                            }


                                        }
                                    });
                                    if(submit_all_status_flag == false){

                                        var html = '';
                                        $.each(not_updated_shipments, function(index, tracking_number) {
                                            html += tracking_number + '<br/>';
                                        });

                                        html += '<br/>Update Received/Refused By for all Shipment(s) of 0 (zero) amount!';

                                        content = document.createElement('div');
                                        content.innerHTML = html;
                                        swal({
                                            title: 'Names Not Updated',
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

                                }else{
                                    table.rows().nodes().each(function(index) {
                                        var row = table.row(index);
                                        if ($(row.node()).hasClass('selected')) {
                                            var id = parseInt(row.id());
                                            var remarks = $(row.node()).find('td.remarks input').val();
                                            shipment_remarks_obj[id] = remarks;

                                        }
                                    });
                                }

                                if(submit_all_status_flag){
                                    swal({
                                        title: 'Please Wait!',
                                        text: 'Shipment(s) are being updated!',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                    $.ajax({
                                        url: '{!! route('admin.delivery.receive.add.status.all') !!}',
                                        method: 'POST',
                                        data: {
                                            'shipment_ids': selected_rows,
                                            'selected_status': select_all_status,
                                            'delivery_note_id': delivery_note,
                                            'remarks': shipment_remarks_obj,
                                            'received_or_refused_by': shipment_received_refused_obj,
                                            '_token': '{{ csrf_token() }}',
                                        }
                                    }).done(function (data) {
                                        if(data.status === 1){
                                            UnblockPagePermanently();
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                        }else{
                                            UnblockPagePermanently();
                                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                        }
                                        location.reload();

                                    });
                                }
                            }
                        });
                    }
                    else{
                        var error = "Please Select A Status!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
                else{
                    var error = "Select at-least one shipment!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            });

            $('#nsatable').on('change', 'td.remarks input', function () {
                $(this).val($(this).val().trim());
            });
            //nsa modal bind
            $('#nsa_form').bind('submit',function (e) {
                e.preventDefault();
            });
            $( "#nsa_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#nsa_shipment_ids').val(shipment_id_list);
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    form.submit();

                }
            });


        });
    </script>
@endsection