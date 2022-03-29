
@extends('admin.layout.master')
@section('title','Verify Deliveries')

@section('content')
    <h1 class="mb-1">
        Verify Deliveries(Delivery Note: {{str_pad($delivery_note_id, 6, '0', STR_PAD_LEFT)}})
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center">
                    <div class="col-5">
                        <h3>Delivery Ratio {{$percentage}}%</h3>
                    </div>
                    <div class="col-4">
                        <h3>Call Verification Ratio {{$verification_percentage}}% = {{$verification_shipments_count}} Parcels</h3>
                    </div>
                </div>
                <form id="status_update_form" action="{{route('admin.delivery.receive.verify.status.submit')}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" value="{{$delivery_note_id}}" id="delivery_note" name="delivery_note_id">
                    <input type="hidden" value="{{$shipments_count}}" id="shipments_count" name="shipments_count">
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <input type="hidden" name="submit_button_id" id="submit_button_id">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Shipment ID</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Consignee</th>
                            <th class="border-primary border-darken-1">Consignee Phone</th>
                            <th class="border-primary border-darken-1">Collection Amount</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Reason</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Received/Refused By</th>
                            <th class="border-primary border-darken-1">Address</th>
                            <th class="border-primary border-darken-1">Open Box</th>
                            <th class="border-primary border-darken-1">Open Box Type</th>
                            <th class="border-primary border-darken-1">Rider Name</th>
                            <th class="border-primary border-darken-1">Rider Status</th>
                            <th class="border-primary border-darken-1">Rider Reason</th>
                            <th class="border-primary border-darken-1">Fake Status</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Call Verification</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Rider Location</th>
                            <th class="border-primary border-darken-1">Existing Location</th>
                            <th class="border-primary border-darken-1">Confirm Location</th>
                            <th class="border-primary border-darken-1">CCD Slip</th>
                            <th class="border-primary border-darken-1">OTP Entered</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center preventsubmit">
                        @if($delivery_note_status == 0)
                        <div class="col-2">
                            <button id="statusUpdateSubmit" rel="update" type="submit" class="btn btn-primary btn-block">Update Status</button>
                        </div>
                        <div class="col-2">
                            <button id="statusVerifySubmit" rel="verify" type="submit" class="btn btn-primary btn-block">Verify Status</button>
                        </div>
                        @endif
                        @if($delivery_note_status == 1)
                            <div class="col-2">
                                <button id="printDNCC" type="button" class="btn btn-warning btn-block">Print DNCC</button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="ConsigneeInformationModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConsigneeInformationModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Consignee</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="consignee_info_div"></div>
                </div>
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
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <form id="items_scan_form" action="#">
                                <div class="form-group">
                                    <input type="text" name="item_number" class="form-control item_number" placeholder="Shipment Item Number Scan*" data-rule-required="true" data-msg-required="Item Number is required">
                                </div>

                            </form>
                        </div>
                    </div>

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
    <!--Distribution Modal -->
    <div class="modal fade text-left" id="DistributionModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DistributionModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Distribution</h4>
                  
                </div>
                <div class="modal-body  text-center">

                    <form id="distribution_form" action="{{route('admin.delivery.receive.distribution.submit')}}" method="post">
                        <table class="table table-bordered datatable" id="distributiontable" style="z-index: 3;">
                            <thead>
                            @csrf
                            @method('PUT')
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Product Type</th>
                                <th class="border-primary border-darken-1">Booked Items/SKUs</th>
                                <th class="border-primary border-darken-1">Booked units per item</th>
                                <th class="border-primary border-darken-1">Total units</th>
                                <th class="border-primary border-darken-1">Total Delivered Units</th>
                                <th class="border-primary border-darken-1">Total Return Units</th>
                                <th class="border-primary border-darken-1">Total Delivered Items/SKUs</th>
                                <th class="border-primary border-darken-1">Total Return Items/SKUs</th>
                                <th class="border-primary border-darken-1">Total Amount</th>

                            </tr>
                            </thead>
                        </table>
                       {{-- <div class="row justify-content-center mb-2">
                            <div class="col">
                                <h4><U>Total Collection Amount:</U> Rs: <span id="cod"></span></h4>
                            </div>
                        </div>--}}
                        <input type="hidden" name="distribution_id_list" id="distribution_id_list">
                        <input type="hidden" name="delivery_note_distribution" id="delivery_note_distribution">
                        <input type="hidden" name="distribution_shipment_id" id="distribution_shipment_id">
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="DistributionUpdate" type="submit" class="btn btn-primary btn-block">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Distribution Modal -->
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

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }
        table.dataTable tbody tr td.call_verification {
           text-align:center;
        }
        table.dataTable tbody tr td.fake_status {
           text-align:center;
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
        .checkbox_overlay {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#label_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Labeling*'
            });
            var shipment_status = [];
            var shipment_reason = [];
            var note_id = $('#delivery_note').val();
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:['reset'],
                scrollX: true,
                paging:false,
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: false,
                ajax: '{{ route('admin.delivery.receive.verify.status.list',['id'=>$delivery_note_id]) }}',
                rowId: 'shId',
                // order: [[1, 'desc']],
                ordering: false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data:'shipment_id_padded',name: 'shipments.id', class: 'align-middle shipment_id'},
                    {data:'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data:'consignee_name',name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data:'consignee_phone',name: 'shipments.consignee_phone', class: 'align-middle consignee_phone'},
                    {data:'amount',name: 'shipments.amount', class: 'align-middle amount'},
                    {data:'status',name: 'status', class: 'align-middle status statusOnChange',orderable: false, searchable: false},
                    {data:'reason',name: 'reason', class: 'align-middle reason reasonSelect',orderable: false, searchable: false},
                    {data:'remarks',name: 'remarks', class: 'align-middle remarks',orderable: false, searchable: false},
                    {data:'received_or_refused_by',name: 'sj.received_or_refused_by', class: 'align-middle received_or_refused_by',orderable: false, searchable: false},
                    {data:'address',name: 'shipments.consignee_address', class: 'align-middle address'},
                    {data:'open_box',name: 'open_box', class: 'align-middle text-center open_box',orderable: false, searchable: false},
                    {data:'open_box_type',name: 'open_box_type', class: 'align-middle text-center open_box_type',orderable: false, searchable: false},

                    {data:'rider_name',name: 'riders', class: 'align-middle status form-group rider_name',orderable: false, searchable: false},

                    {data:'rider_status',name: 'rss.name', class: 'align-middle status form-group rider_status',orderable: false, searchable: false},
                    {data:'rider_reason',name: 'rssr.name', class: 'align-middle reason form-group rider_reason',orderable: false, searchable: false},
                    {data:'fake_status',name: 'fake_status', class: 'align-middle fake_status',orderable: false, searchable: false},
                    {data:'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data:'call_verification',name: 'call_verification', class: 'align-middle call_verification',orderable: false, searchable: false},
                    {data:'destination',name: 'oc.name', class: 'align-middle destination'},
                    {data:'shipper',name: 'users.name', class: 'align-middle shipper'},
                    {data:'service_type',name: 'bt.booking_type', class: 'align-middle service_type'},
                    {data:'rider_location',name: 'rider_location', class: 'align-middle text-center rider_location',orderable: false, searchable: false},
                    {data:'existing_location',name: 'existing_location', class: 'align-middle text-center existing_location',orderable: false, searchable: false},
                    {data:'confirm_location',name: 'confirm_location', class: 'align-middle text-center confirm_location',orderable: false, searchable: false},
                    {data:'ccd_image',name: 'ccd_image', class: 'align-middle text-center ccd_image',orderable: false, searchable: false},
                    {data:'otp_entered',name: 'otp_entered', class: 'align-middle text-center otp_entered',orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    var status = data.shId in shipment_status;
                    if(status == false){
                        shipment_status[data.shId] = data.current_status_id;
                    }

                    var reason = data.shId in shipment_reason;
                    if(reason == false){
                        shipment_reason[data.shId] = data.reason_id;
                    }

                    // shipment_reason[data.shId] = data.current_status_id;
                },
                drawCallback: function (settings) {
                    $(".reasonDrop").prepend('<option value="" ></option>').select2({
                        placeholder: "Select a Reason",
                        width:'100%'
                    });
                    $(".statusDrop").prepend('<option value="" ></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%'
                    });
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();
                    $.each(data,function (key,value) {

                        if(shipment_status.length !== 0){
                            $('select[name="status_drop['+value.shId+']"]').val(shipment_status[value.shId]).trigger('change');
                        }else{
                            $('select[name="status_drop['+value.shId+']"]').val(value.current_status_id).trigger('change');
                        }
                        if(shipment_reason.length !== 0){
                            $('select[name="reason_drop['+value.shId+']"]').val(shipment_reason[value.shId]).trigger('change');
                        }else{
                            var reasonId = $('select[name="reason_drop['+value.shId+']"]').attr('reasonId');
                            $('select[name="reason_drop['+value.shId+']"]').val(reasonId).trigger('change');

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

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.reason') || $(header).is('.remarks') || $(header).is('.action') || $(header).is('.call_verification') || $(header).is('.fake_status') || $(header).is('.received_or_refused_by') || $(header).is('.open_box') || $(header).is('.open_box_type')) {
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

            $('body').on('select2:select','.statusOnChange .statusDrop',function (e) {
                var rowid = parseInt($(this).parents('tr').attr('id'));

                $('#statusUpdateSubmit').removeAttr('disabled');
                $('#statusVerifySubmit').removeAttr('disabled');

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
                var reasonSelection = $(this).find(':selected').val();
                shipment_reason[rowid] = reasonSelection;
            });
            $('body').on('click','.clear',function () {
                var status = $(this).parents().closest('tr').find('.statusDrop');
                var reason = $(this).parents().closest('tr').find('.reasonDrop');
                status.val('').trigger("change");
                reason.val('').trigger("change");
                $('.remarks input').val('');
                // $('.reasonDrop').val('').trigger("change");
            });

            $('#status_update_form').on('keypress',function (e) {
                if(e.which == 13) {
                    e.preventDefault();
                }
            });
            var shipments = [];
            var status_array = [];
            $('#status_update_form').bind('submit', function(event) {
                    var verify_form = this;
                    var submitting = false;
                    event.preventDefault();
                    var btn = $(document.activeElement).attr('id');
                    $('#submit_button_id').val(btn);

                    $('#statusVerifySubmit').prop('disabled', true);
                    $('#statusUpdateSubmit').prop('disabled', true);
                
                    $.each($('#datatable tr td.statusOnChange select'), function (key, value) {

                        $(this).find(':selected').removeAttr('disabled');
                        var pre_status = $(this).attr('status');
                        var selected = $(this).find(':selected').val();
                        var tracking = $(this).parents('tr').find('td.tracking_number').text();
                        if (pre_status !== selected) {
                            var newStatus = $(this).find(':selected').text();
                            var oldStatus = $(this).find('option[value="' + pre_status + '"]').text();
                            status_array[key] = {'tracking': tracking, 'old': oldStatus, 'new': newStatus};
                        }


                    });

                    if (status_array.length > 0) {
                        var content_dispute = '';
                        content_dispute += 'These shipments are found different in statuses.' + "<br>";
                        $.each(status_array, function (key, value) {
                            if (value !== undefined) {
                                content_dispute += value.tracking + ' (' + value.old + ')' + ' (' + value.new + ')' + "<br>";
                            }
                        });
                        content = document.createElement('div');
                        content.innerHTML = content_dispute;
                        swal({
                            title: 'Dispute Different Statuses!',
                            content: content,
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
                                blockPagePermanently();

                                $(table.table().header()).find('input').val('');
                                $(table.table().header()).find('select').val('').trigger('change.select2');
                                table.columns().search('').draw();
                                var shipment = $('#shipment_ids');
                                var id = '';
                                var count = table.data().count();
                                for (var i = 0; i < count; i++) {
                                    id = table.row(i).id();
                                    shipments.push(id);
                                }
                                shipment.val(shipments);
                                $('#statusVerifySubmit').prop('disabled', true);
                                $('#statusUpdateSubmit').prop('disabled', true);
                                
                                if (!submitting) {
                                    submitting = true;

                                    verify_form.submit();
                                }
                            }
                            else {
                                $('#statusVerifySubmit').removeAttr('disabled');
                                $('#statusUpdateSubmit').removeAttr('disabled');
                            }
                        });
                    } else {
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to update/verify the status of shipments!',
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
                                blockPagePermanently();

                                $(table.table().header()).find('input').val('');
                                $(table.table().header()).find('select').val('').trigger('change.select2');
                                table.columns().search('').draw();
                                var shipment = $('#shipment_ids');
                                var id = '';
                                var count = table.data().count();
                                for (var i = 0; i < count; i++) {
                                    id = table.row(i).id();
                                    shipments.push(id);
                                }
                                shipment.val(shipments);
                                $('#statusVerifySubmit').prop('disabled', true);
                                $('#statusUpdateSubmit').prop('disabled', true);

                                if (!submitting) {
                                    submitting = true;

                                    verify_form.submit();
                                }
                            }
                            else {
                                $('#statusVerifySubmit').removeAttr('disabled');
                                $('#statusUpdateSubmit').removeAttr('disabled');
                            }
                        });


                    }


            });

            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.dncc.print') !!}',
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
                    });
            }

            $('#printDNCC').on('click',function () {
                var note_id = $('#delivery_note').val();
                print(note_id);
            });


            $('body').on('click', 'button.consignee_info_label', function () {
                var phone = $(this).attr('rel');
                if(phone){
                    $.ajax({
                        url: '{!! route('admin.settings.blacklist.search.consignee') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'phone': phone
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {
                                details = data.details;
                                $('#consignee_information_id').val(details.consignee.id);
                                var html = '<div class="row mb-1">';

                                html += '<div class="col-4">Consignee Name :</div><div class="col-8">'+ details.consignee.name +'</div>';
                                html += '<div class="col-4">Consignee Phone Number 1 :</div><div class="col-8">'+ details.consignee.phone +'</div>';
                                var consignee_phone = '';
                                if(details.consignee.phone2 != null){
                                    consignee_phone = details.consignee.phone2;
                                }
                                html += '<div class="col-4">Consignee Phone Number 2 :</div><div class="col-8">'+ consignee_phone +'</div>';
                                html += '<div class="col-4">Consignee Address :</div><div class="col-8">'+ details.consignee.address +'</div>';
                                html += '<div class="col-4">Consignee City :</div><div class="col-8">'+ details.consignee.city +'</div>';

                                html += '</div>';
                                if ('blacklist' in details) {
                                    html += '<div class="row p-1" style="background-color: '+ details.blacklist.color +'; color:white;">';
                                    html += '<div class="col-12">';
                                    html += '<table class="table table-sm table-bordered mb-0">';
                                    html += '<tbody>';
                                    html += '<tr>';
                                    html += '<td><strong>Total Shipments</strong></td>';
                                    html += '<td><strong>Delivered</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '<td><strong>Undelivered</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '<td><strong>Return Confirmed</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '</tr>';
                                    html += '<tr>';
                                    html += '<td>' + details.blacklist.total_shipments + '</td>';
                                    html += '<td>' + details.blacklist.delivered + '</td>';
                                    html += '<td>' + details.blacklist.delivered_ratio + ' %</td>';
                                    html += '<td>' + details.blacklist.undelivered + '</td>';
                                    html += '<td>' + details.blacklist.undelivered_ratio + ' %</td>';
                                    html += '<td>' + details.blacklist.return + '</td>';
                                    html += '<td>Rs. ' + details.blacklist.return_ratio + ' %</td>';
                                    html += '</tr>';
                                    html += '</tbody>';
                                    html += '</table>';
                                    html += '</div></div>';
                                }

                                $('#consignee_info_div').html(html);

                                $('#ConsigneeInformationModal').modal('show');

                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {

                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
            });

            //on page load ajax
            var trybuy_ids = [];
            var distribution_ids = [];
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
                                    {name: 'receiving', class: 'align-middle receiving position-relative'},

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
                                        var inp = "<div class='checkbox_overlay'></div><input type='checkbox' checked class='form-control bought' name='bought["+value.pid+"]'>";
                                        trybuy.row.add([rowNo+1,value.type,value.description,value.price,inp]).node().id = value.pid;
                                        trybuy.draw(false);
                                        $('#cod').text(data.total_cod);
                                    });


                                }else{
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });


                        }
                        else if (data.status == 4) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                            $('#DistributionModal').modal('show');
                            // checkShipmentStatuses();

                            $('#distributiontable').append("<tfoot><tr><th colspan='2'>Total:</th><th id='total_booked_items' class='align-middle pl-2'></th><th id='total_booked_units_per_item' class='align-middle pl-2'></th><th id='total_unit' class='align-middle pl-2'></th><th id='total_delivered_units' class='align-middle pl-2'></th><th id='total_return_unit' class='align-middle pl-2'></th><th id='total_delivered_skus' class='align-middle pl-2'></th><th id='total_return_skus' class='align-middle pl-2'></th><th id='amount_total' class='align-middle pl-2'></th></tr></tfoot>");
                            distribution = $('#distributiontable').DataTable({
                                dom: 'ltipr',
                                paging:false,

                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                    {name: 'product_type', class: 'align-middle product_type'},
                                    {name: 'total_sku', class: 'align-middle total_sku'},
                                    {name: 'units_per_item', class: 'align-middle units_per_item'},
                                    {name: 'total_unit', class: 'align-middle total_unit'},
                                    {name: 'total_delivered_unit', class: 'align-middle total_delivered_unit'},
                                    {name: 'total_return_unit', class: 'align-middle total_return_unit'},
                                    {name: 'total_delivered_skus', class: 'align-middle total_delivered_skus'},
                                    {name: 'total_return_skus', class: 'align-middle total_return_skus'},
                                    {name: 'total_amount', class: 'align-middle total_amount'},

                                ],
                                rowCallback: function(row, data, index) {
                                    var info = distribution.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                }
                            });

                            //distribution shipment id for modal
                            $('#distribution_shipment_id').val(data.distribution);
                            $('#delivery_note_distribution').val(delivery_note);
                            $.ajax({
                                url:'{!! route('admin.delivery.receive.distribution') !!}',
                                type:'POST',
                                dataType:'json',
                                data: {
                                    'delivery_note_id': delivery_note,
                                    'distribution':data.distribution,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if(data.status == 0){
                                    var rowNo = distribution.rows().count();
                                    var total_units = '';
                                    $.each(data.data,function (key,value){
                                        distribution_ids.push(value.pid);
                                        total_units = value.items * value.units_per_item;
                                        var total_delivered_units = "<input type='text' value='"+value.total_delivered_units+"' class='form-control text-center total_delivered_units' data-rule-required='true' data-msg-required='Delivered Units is required' name='total_delivered_units["+value.pid+"]' id='total_delivered_units["+value.pid+"]' form='distribution_form'>";
                                        var amount = "<input type='text' readonly value= '0'  class='form-control text-center amount'  name='amount["+value.pid+"]' id='amount["+value.pid+"]'  > <input type='hidden' value= "+ value.price +"  form='distribution_form'>";
                                        var total_delivered_skus = "<input type='text' value= '0' class='form-control text-center total_delivered_skus' name='total_delivered_skus["+value.pid+"]' id='total_delivered_skus["+value.pid+"]' readonly form='distribution_form'>";


                                        distribution.row.add([rowNo+1,value.type,value.items,value.units_per_item,total_units,total_delivered_units,total_units,total_delivered_skus,value.items,amount]).node().id = value.pid;
                                        distribution.draw(false);
                                    });
                                    $('#distributiontable .total_delivered_units').trigger('change');
                                    calculate_total();
                                }
                                else if (data.status == 1) {
                                    $('#DistributionModal').modal('hide');
                                }

                                $("#distribution_form .total_delivered_units").inputmask({
                                    'alias': 'integer',
                                    'allowMinus': false,
                                    'allowPlus': false,
                                    'min': 0,
                                });
                            });

                        }
                        else if (data.status == 0) {

                        }
                    }
                    shipments_count = shipments_count-1;
                });
            }

            $('#distributiontable').on('change', ".total_delivered_units", update_distribution);


            function update_distribution() {

                var total_delivered_units = parseInt($(this).val());
                var currentRow = $(this).closest("tr");
                var total_units = currentRow.find("td:eq(4)").text();
                var booked_skus = currentRow.find("td:eq(2)").text();
                var booked_units_per_item = currentRow.find("td:eq(3)").text();
                var return_units = total_units - total_delivered_units;

                var total_amount =  currentRow.find("td:eq(9)").find("input[type='hidden']").val();

                if(total_delivered_units > 0 && total_delivered_units <= total_units){
                    currentRow.find("td:eq(6)").text(return_units);
                    var new_amount = Math.round((total_amount/total_units) * total_delivered_units);
                    currentRow.find("td:eq(9)").find("input[type='text']").val(new_amount);
                    var sku = Math.floor(total_delivered_units/booked_units_per_item);
                    currentRow.find("td:eq(7)").find("input").val(sku);
                    var return_sku = booked_skus - (Math.floor(total_delivered_units/booked_units_per_item));
                    currentRow.find("td:eq(8)").text(return_sku);
                }
             else if(total_delivered_units == 0){
                    currentRow.find("td:eq(9)").find("input[type='text']").val(0);
                    currentRow.find("td:eq(8)").text(booked_skus);
                    currentRow.find("td:eq(6)").text(total_units);
                    currentRow.find("td:eq(7)").find("input[type='text']").val(0);
                }
                else if(total_delivered_units > total_units){
                    toastr.error('Quantity Exceeded!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    $('#DistributionUpdate').prop('disabled', true);
                }

                validate_flag = true;
                $('#distributiontable tbody tr').each(function (){
                    if(parseInt($(this).find("td:eq(5)").find("input[type='text']").val()) > parseInt($(this).find("td:eq(4)").text()))
                    {
                        validate_flag = false;
                    }
                });
                if(validate_flag)
                {
                    $('#DistributionUpdate').prop('disabled', false);
                }

                calculate_total();
            }

            function calculate_total(){
                var total_booked_items = 0;
                var total_booked_units_per_item = 0;
                var total_units = 0;
                var total_delivered_units = 0;
                var total_return_units = 0;
                var total_delivered_skus = 0;
                var total_return_skus = 0;
                var amount_total = 0;

                $('#total_booked_items').html('');
                $('#total_booked_units_per_item').html('');
                $('#total_unit').html('');
                $('#total_delivered_units').html('');
                $('#total_return_unit').html('');
                $('#total_delivered_skus').html('');
                $('#total_return_skus').html('');
                $('#amount_total').html('');


                $(".total_sku").each(function() {

                    var value = $(this).text();
                    if(!isNaN(value) && value.length != 0) {
                        total_booked_items += parseFloat(value);
                    }
                });
                $('#total_booked_items').html(total_booked_items);

                $(".units_per_item").each(function() {

                    var value = $(this).text();
                    if(!isNaN(value) && value.length != 0) {
                        total_booked_units_per_item += parseFloat(value);
                    }
                });
                $('#total_booked_units_per_item').html(total_booked_units_per_item);
                
                $(".total_unit").each(function() {

                    var value = $(this).text();
                    if(!isNaN(value) && value.length != 0) {
                        total_units += parseFloat(value);
                    }
                });
                $('#total_unit').html(total_units);

                $(".total_delivered_units").each(function() {

                    var value = $(this).val();
                    if(!isNaN(value) && value.length != 0) {
                        total_delivered_units += parseFloat(value);
                    }
                });
                $('#total_delivered_units').html(total_delivered_units);

                $(".total_return_unit").each(function() {

                    var value = $(this).text();
                    if(!isNaN(value) && value.length != 0) {
                        total_return_units += parseFloat(value);
                    }
                });
                $('#total_return_unit').html(total_return_units);

                $(".total_delivered_skus").each(function() {

                    var value = $(this).val();
                    if(!isNaN(value) && value.length != 0) {
                        total_delivered_skus += parseFloat(value);
                    }
                });
                $('#total_delivered_skus').html(total_delivered_skus);
                
                $(".total_return_skus").each(function() {

                    var value = $(this).text();
                    if(!isNaN(value) && value.length != 0) {
                        total_return_skus += parseFloat(value);
                    }
                });
                $('#total_return_skus').html(total_return_skus);

                $(".amount").each(function() {
                    var value = $(this).val();
                    if(!isNaN(value) && value.length != 0) {
                        amount_total += parseFloat(value);
                    }
                });
                $('#amount_total').html(amount_total);
            }

            checkShipmentStatuses();

            $('#items_scan_form input.item_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#items_scan_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {

                    var item_number = $(form).find('input.item_number').val();

                    form.reset();

                    if (trybuy.rows('[id='+ item_number +']').any()) {
                        var item = $('tr#'+item_number).find('.receiving input');
                        if(item.is(':checked')){
                            item.attr('checked', false);
                            item_scanned_cod_change(item);
                        }else{
                            toastr.error('Item has been scanned already!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }else{
                        toastr.error('Item not found in the list!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            function item_scanned_cod_change(bought){
                var check = $(bought);
                var id = parseInt($(bought).parents('tr').attr('id'));
                var price = $(bought).parents('tr').find('td.item_price').text();
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
            }
            //replacement modal bind
            $('#replacement_form').bind('submit',function (e) {
                e.preventDefault();
            });
            $( "#distribution_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('td'));
                },
                submitHandler: function(form) {
                    $('#distribution_id_list').val(distribution_ids);
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    form.submit();

                }
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
                // blockPagePermanently();
                var this_form = this;
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
                // if(checkbox_count > 0){
                // UnblockPagePermanently();

                // }else{
                //     UnblockPagePermanently();
                //     var error = "Select at-least one item!";
                //     toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                // }
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to update Try & Buy Delivery!',
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
                        this_form.submit();
                    }
                });
            });

            $('#trybuy_form').bind('submit',function (e) {
                // blockPagePermanently();
                var this_form = this;
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
                // if(checkbox_count > 0){
                // UnblockPagePermanently();

                // }else{
                //     UnblockPagePermanently();
                //     var error = "Select at-least one item!";
                //     toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                // }
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to update Try & Buy Delivery!',
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
                        this_form.submit();
                    }
                });
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

            $('#AICUpdate').on('click', function () {
                var status = $('#iad_status').val();
                var id = $('#iad_shipment_id').val();
                var remark_input = $('#datatable tr#'+id).find('td.remarks input');
                var remark = remark_input.val();
                remark = remark+ ' ' + status;
                remark_input.val(remark);
                $('#IncompleteAddressModal').modal('hide');
            });

            $('#IncompleteAddressModal').on('hide.bs.modal', function () {
                $('#iad_status').val('');
                $('#iad_shipment_id').val('');
                $('.iad_radio').prop('checked', false);
                $('#AICUpdate').attr('disabled', true);
                $('#other_description').parent('fieldset').addClass('d-none');
                $('#other_description').val('');
            });
        });
    </script>
@endsection