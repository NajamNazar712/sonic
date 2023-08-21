@extends('admin.layout.master')
@section('title','Pending Cash Collection')

@section('content')
    <h1 class="mb-1">
        Pending Cash Collection COD
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                
                    <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" name="tracking_numbers" class="tracking_numbers ml-4" placeholder="Tracking Number(s)" id="tracking_numbers" style="width: 100%" >
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" name="dncc" class="dncc" placeholder="DNCC Number" id="dncc" style="width: 100%">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <button id="datatable_filter_btn" type="submit" class=" btn btn-outline-primary btn-min-width"><i
                                            class="la la-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                    <input type="hidden" name="delivery_note_ids" id="delivery_note_ids">


            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Delivery Note No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Area</th>
                        <th class="border-primary border-darken-1">Rider Type</th>
                        <th class="border-primary border-darken-1">Route</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Shipments Delivered</th>
                        <th class="border-primary border-darken-1">Assigned By</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Update Date</th>
                        <th class="border-primary border-darken-1">DNCC Amount</th>
                        <th class="border-primary border-darken-1">Fintech Charge</th>
                        <th class="border-primary border-darken-1">HBL Konnect Amount</th>
                        <th class="border-primary border-darken-1">Cash Amount</th>
                        <th class="border-primary border-darken-1">CCD Receipts</th>
                        <th class="border-primary border-darken-1">1Link Payment Shipment(s)</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>


    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->


<!--Shipments popup -->
<div class="modal fade" id="fintech_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="shipments_modal_title">Fintech Charges(s)</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-center">

                <table border="1">
                    <tr >
                        <th width="70">#</th>
                        <th width="140">Tracking #</th>
                        <th width="120">COD Amount</th>
                        <th width="150">Payment ID</th>
                        <th width="140">Transaction Date</th>
                    </tr>

                    <div id="no_transaction_message" style="display: none;">
                        <h3><strong>No transactions have been made.</strong></h3>
                    </div>

                    <tbody id="shipment_table">
                    <tbody>
                  </table>
                     </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Shipments popup -->



    <!--Delivered Shipments popup -->
    <div class="modal fade" id="delivered_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="delivered_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delivered_shipments_modal_title">Delivered Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <!-- one link payment details popup -->
    <div class="modal fade" id="one_link_payment_details_modal" data-backdrop="static" role="dialog" aria-labelledby="one_link_payment_details_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 900px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="one_link_payment_details_modal_title">1Link Payment Shipment(s)</h4>
                </div>
                <div class="modal-body justify-content-center">
                    <table class="table table-hover table-responsive" id="one_link_payment_details_table">
                        <thead>
                            <th>S.NO</th>
                            <th>Tracking Number</th>
                            <th>Transaction ID</th>
                            <th>Amount </th>
                            <th>Transaction Date</th>
                            <th>Transaction Time</th>
                            <th>Created at</th>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- one link payment details popup -->
    <!--HBL Konnect Information -->
    <div class="modal fade" id="transactions_information_modal" data-backdrop="static" role="dialog" aria-labelledby="transactions_information_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white" id="transactions_information_modal_title">HBL Konnect Amount</h4>

                    <button type="button" class="close white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--HBL Konnect Information -->


    <div class="modal fade text-left" id="ViewCCDSlip" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="ViewCCDSlip"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">CCD Receipts </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body text-center">
                        <form id="upload_image_form" class="form-horizontal"
                              action="{{route('admin.delivery.cash_collection.pending.shipments.upload_ccd_receipt')}}"
                              method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="ccd_delivery_note_id" id="ccd_delivery_note_id" value="">
                            <table class="table table-bordered datatable" id="ccd_slip_table" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">

                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking No</th>
                                    <th class="border-primary border-darken-1">CCD Slip</th>
                                    <th class="border-primary border-darken-1">CCD Upload</th>

                            </tr>
                            </thead>
                        </table>
                        <hr>
                        <div class="row justify-content-center">
                                <div class="col-6">
                                    <button type="submit" id="upload_ccd" class="btn btn-primary upload_ccd" name="upload">
                                    Upload
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Deposit Slip Snatch Upload-->
    <div class="modal fade text-left" id="uploadPCCDepositSlip" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="uploadPCCDepositSlip"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Deposit Slip Upload For <span></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="pcc_upload_form" class="form" action="{{route('admin.delivery.cash_collection.pending.snatch_collect')}}" method="post"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="delivery_note_id" id="delivery_note_id"/>
                        <input type="hidden" name="action" id="action"/>
                        <div class="row">
                            <div class="col-12">
                                <h2>Delivery Note: <span></span></h2>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <input type="text" name="amount" id="amount" placeholder="Amount" class="form-control amount" data-rule-required="true" data-msg-required="Deposit Amount is Required">
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-group">
                                    <textarea name="remarks" id="" cols="20" rows="3" placeholder="Remarks" class="form-control" data-rule-required="true" data-msg-required="Remarks are Required" id="remarks"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <input class="form-control form-control" id="deposit_slip" type="file" name="deposit_slip" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Deposit Slip is required">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="DepositSlipButton" type="submit" class="btn btn-primary btn-block">
                                    Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Deposit Slip Snatch Upload-->


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

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
            border-color: #666EE8;
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.cash_collection.pending.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Delivery Note No.');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Rider');
                            head.push('Area');
                            head.push('Rider Type');
                            head.push('Route');
                            head.push('No. Of Shipments');
                            head.push('No. Of Shipments Delivered');
                            head.push('Assigned By');
                            head.push('Assigned Date');
                            head.push('Updated By');
                            head.push('Updated Date');
                            head.push('DNCC Amount');
                            head.push('HBL Konnect  Amount');
                            head.push('Cash Amount');
                            head.push('One Link Payment Count');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.delivery_note_id_padded);
                                row.push(values.hub);
                                row.push(values.zone_name);
                                row.push(values.rider);
                                row.push(values.area);
                                row.push(values.rider_type);
                                row.push(values.route);
                                row.push(values.shipments_count);
                                row.push(values.delivered_shipments);
                                row.push(values.assignee);
                                row.push(values.created_at);
                                row.push(values.updated_by);
                                row.push(values.updated_at);
                                row.push(values.amount);
                                row.push(values.transactions_amount);
                                row.push(values.cash_amount);
                                row.push(values.one_link_payment_count);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || in_array(106, session('permissions')))
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                    text: '<i class="la la-creative-commons"></i> Cash Collect',
                    className: 'btn btn-primary cash_collect_all',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        if(selected_rows != ''){
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes to collect cash!',
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
                                    $('#delivery_note_ids').val(selected_rows);
                                    var delivery_note_ids = $('#delivery_note_ids').val();
                                    // console.log(delivery_note_ids)
                                    if(delivery_note_ids != ''){
                                        $.ajax({
                                            url:'{!! route('admin.delivery.cash_collection.pending.all') !!}',
                                            method:'POST',
                                            data:{
                                                'delivery_note_ids':delivery_note_ids,
                                                '_token':'{{csrf_token()}}'
                                            }
                                        }).done(function (data) {
                                            table.button(0).disable();
                                            if(data.status == 1){
                                                $('#delivery_note_ids').val('');
                                                selected_rows = [];
                                                hub_ids = [];
                                                table.draw();
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                            }else{
                                                $('#delivery_note_ids').val('');
                                                table.draw();
                                                hub_ids = [];
                                                selected_rows = [];
                                                $msg = data.error;
                                                if(data.notes != null){
                                                    $.each(data.notes,function (index,id) {
                                                        $msg += '<br>';
                                                        $msg += 'Delivery Note # '+id;
                                                    });
                                                }
                                                toastr.error($msg, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                            }
                                        });
                                    }

                                }
                            });

                        }else{
                            var error = "Something went wrong please refresh page and try again!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        }
                    }
                },
                    {
                        extend: 'excel',
                        title: 'Pending Cash Collection',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }, {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    hub_id = $(row.node()).data('hub');

                                    var allow = false;

                                    if(hub_ids.length == 0) {
                                        hub_ids.push(hub_id);

                                        allow = true;
                                    }
                                    else if(hub_ids[0] == hub_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.cash_collect_all').enable();
                                    }
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

                              if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                row.deselect();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index !== -1) {
                                    selected_rows.splice(index, 1);
                                }

                                if (selected_rows.length == 0) {
                                    table.button('.cash_collect_all').disable();

                                    hub_ids.splice(index, 1);
                                }
                              }
                            });
                        }
                    },'reset'],
                @else
                    buttons:[{
                    extend: 'excel',
                    title: 'Completed Deliveries',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                @endif
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url:'{{ route('admin.delivery.cash_collection.pending.list') }}',
                    data:function (d) {
                        d.tracking_numbers = $('#tracking_numbers').val();
                        d.dncc = $('#dncc').val();
                    }
                    },
                rowId: 'delivery_note_id',
                order: [[14, 'desc']],
                columns: [
                    {data: 'delivery_note_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'delivery_note' ,name: 'delivery_notes.id', class: 'align-middle text-center delivery_note'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'zone_name' ,name: 'zn.name', class: 'align-middle zone'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'area' ,name: 'ca.name', class: 'align-middle area'},
                    { data:'rider_type' ,name: 'rt.name', class: 'align-middle rider_type'},
                    { data:'route' ,name: 'route', class: 'align-middle route'},
                    { data:'shipments_count_link' ,name: 'delivery_notes.shipments_count', class: 'align-middle shipments_count_link text-center'},
                    { data:'delivered_shipments_link' ,name: 'delivered_shipments', class: 'align-middle delivered_shipments_link text-center'},
                    { data:'assignee' ,name: 'admins.name', class: 'align-middle assignee'},
                    { data:'created_at' ,name: 'created_at', class: 'align-middle created_at'},
                    { data:'updated_by' ,name: 'ub.name', class: 'align-middle updated_by'},
                    { data:'updated_at' ,name: 'delivery_notes.updated_at', class: 'align-middle updated_at'},
                    { data:'amount' ,name: 'delivery_notes.received_cod_amount', class: 'align-middle amount'},
                    { data:'count_fintech_shipments' ,name: 'count_fintech_shipments', class: 'align-middle count_fintech_shipments',orderable: false, searchable: false},
                    { data:'transactions_amount_link' ,name: 'hktdn.transactions_amount', class: 'align-middle transactions_amount'},
                    { data:'cash_amount' ,name: 'hktdn.cash_amount', class: 'align-middle cash_amount',orderable: false, searchable: false},
                    { data:'ccd_image' ,name: 'ccd_image', class: 'align-middle ccd_image',orderable: false, searchable: false},
                    { data:'one_link_payment_count_button' ,name: 'delivery_notes.one_link_payment_count', class: 'align-middle text-center one_link_payment_count'},
                    { data:'action' ,name: 'action', class: 'align-middle action',orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.delivery_note_id, selected_rows) !== -1) {
                        table.row(row).select();
                    }

                    var fintech_sum = $(row).find('#myButton');
                    var dccn_amount = parseFloat(data.amount.replace(/,/g, ''));
                    fintech_sum = fintech_sum[0].innerText
                    fintech_sum = parseFloat(fintech_sum)

                    if(data.transactions_amount == null){
                        
                        data.transactions_amount = 0
                    }
                    value = dccn_amount - fintech_sum - data.transactions_amount;

                    console.log(data.transactions_amount)
                    $('td:eq(18)', row).html(value);

                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.ccd_image') || $(header).is('.cash_amount')) {
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
            var hub_ids = [];

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {

                var id = parseInt($(this).parent('tr').attr('id'));
                var hub_id = $(this).parents('tr').data('hub');
                if(hub_ids.length == 0){
                    hub_ids.push(hub_id);
                    var index = $.inArray(id, selected_rows);

                    if (index === -1) {
                        selected_rows.push(id);
                    }
                    else {
                        selected_rows.splice(index, 1);
                    }

                    if (selected_rows.length > 0) {
                        table.button('.cash_collect_all').enable();
                    }
                    else {
                        table.button('.cash_collect_all').disable();
                        hub_ids.splice(index, 1);
                    }
                }else{
                    if(hub_ids[0] == hub_id){
                        var index = $.inArray(id, selected_rows);

                        if (index === -1) {
                            selected_rows.push(id);
                        }
                        else {
                            selected_rows.splice(index, 1);
                        }

                        if (selected_rows.length > 0) {
                            table.button('.cash_collect_all').enable();
                        }
                        else {
                            hub_ids.splice(index, 1);
                            table.button('.cash_collect_all').disable();
                        }
                    }else{
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        return false;
                    }

                }

            });

            $('body').on('click','.cash_collect',function () {
                var rowid = $(this).parents('tr').attr('id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to collect cash!',
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
                        $.ajax({
                            url:'{!! route('admin.delivery.cash_collection.pending.collect') !!}',
                            method:'POST',
                            data:{
                                'delivery_note_id':rowid,
                                '_token':'{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                table.draw();
                                selected_rows = [];
                                hub_ids = [];
                                if(selected_rows.length == 0){
                                    table.button('.cash_collect_all').disable();
                                }
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        });
                    }
                });

            });

            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
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
                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
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

            var select_dncc = $('#track_form #dncc').selectize({
                placeholder: 'DNCC Number(s)',
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
                        select_dncc[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (input.length >= 2 && Math.floor(input) == input && $.isNumeric(input)) {
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

            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
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
            $('body').on('click','.printdeliverynote',function () {
                var deliverynote = $(this).parents('tr').attr('id');
                // console.log(deliverynote);
                print(deliverynote);
            });
            function printDNCC(id) {
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

            $('body').on('click','.printDNCC',function () {
                var note_id = $(this).parents('tr').attr('id');
                printDNCC(note_id);
            });

            // $('#search_tracking').on('change',function () {
            //     table.draw();
            //
            // });

            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.shipments_count_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.cash_collection.pending.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });

            });

            $('#datatable tbody').on('click','tr td.one_link_payment_count button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                console.log(id);
                $('#one_link_payment_details_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.cash_collection.pending.onelinkpayment') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                .done(function(data) {
                    if (data) {
                        var html = '';

                        if (data.transaction_data) {
                            // $.each(data.shipments, function(index, tracking_number) {
                            //     html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                            // });
                            $.each(data.transaction_data, function(index, values) {
                                    html+= `

                                        <tr>
                                            <td>${index+1}</td>
                                            <td>${values.tracking_number}</td>
                                            <td>${values.transaction_authentication_id}</td>
                                            <td>${values.transaction_amount}</td>
                                            <td>${values.transaction_date}</td>
                                            <td>${values.transaction_time}</td>
                                            <td>${values.created_at}</td>
                                        </tr>

                                    `;
                            });
                            $('#one_link_payment_details_table tbody').html(html);
                        
                        
                        
                        }
                        // $('#one_link_payment_details_modal .modal-body').html(html);
                    }
                });

            });

            $('#datatable tbody').on('click','tr td.delivered_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#delivered_shipments_modal .modal-body').html('');
                $('#delivered_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.cash_collection.pending.shipments.delivered') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#delivered_shipments_modal .modal-body').html(html);
                        }
                    });

            });
            $('#datatable tbody').on('click','tr td.transactions_amount button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#transactions_information_modal .modal-body').html('');
                $('#transactions_information_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.cash_collection.pending.transactions.information') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                    .done(function(data) {
                        if (data.status === 1) {
                            var html = '';
                            html += '<table class="table table-bordered text-center">';
                            html += '<thead><tr class="bg-primary white"><th>S No.</th><th><strong>Transaction ID</strong></th><th><strong>Amount</strong></th><th><strong>Deposited At</strong></th></tr></thead>';
                            html += '<tbody>';
                            $.each(data.details, function (index, value) {
                                console.log(value);
                                var ind = index + 1;
                                html += '<tr class=""><td>' + ind + '</td>';
                                html += '<td>' + value.transaction_id + '</td>';
                                html += '<td>' + value.amount + '</td>';
                                html += '<td>' + value.deposited_at + '</td>';

                            });
                            html += '</tbody></table>';

                            $('#transactions_information_modal .modal-body').html(html);
                            $('#transactions_information_modal').modal('show');
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });

            });

            //--------upload image function
            $('#upload_image_form').validate({

                submitHandler: function (form) {
                    swal({
                        text: 'Are you sure you want to upload the picture ?',
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
                        if(confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Adding CCD Image!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            form.submit();
                        }
                        else {
                            $(form).find('button[type=submit]').prop('disabled', false);
                        }
                    });
                }
            });


            $('#datatable tbody').on('click','tr td.ccd_image button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                /*$('#ViewFCCDSlip .modal-body').html('');*/
                $.ajax({
                    url: '{!! route('admin.delivery.cash_collection.pending.shipments.ccd_slip') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                    .done(function (data){
                        if(data.status){
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }else{
                            $('#ViewCCDSlip').modal('show');
                            $('#ccd_delivery_note_id').val(id);
                            ccd_slip_table = $('#ccd_slip_table').DataTable({
                                dom: 'ltipr',
                                ordering:false,
                                paging:false,
                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                    {name: 'tracking_number', class: 'align-middle tracking_number form-group'},
                                    {name: 'ccd_image', class: 'align-middle ccd_image form-group'},
                                    {name: 'ccd_upload', class: 'align-middle ccd_upload form-group'}
                                ],

                                rowCallback: function(row, data, index) {
                                    var info = ccd_slip_table.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                                initComplete: function() {

                                    // this.api().table().columns.adjust();
                                }
                            });

                            $.each(data.ccd_slips, function (index, value) {
                                ccd_slip_table.row.add([0, value.tracking_number, value.ccd_image, value.ccd_upload]);
                                ccd_slip_table.draw(true);
                            });
                        }
                    });
            });

            $('#ViewCCDSlip').on('hidden.bs.modal', function () {
                ccd_slip_table.clear();
                ccd_slip_table.destroy();
            });

            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                var tracking_numbers = $('#track_form .tracking_numbers').val();
                var dncc = $('#track_form .dncc').val();
                if (tracking_numbers != '' || dncc != '' ) {
                    table.draw();
                }
            });

            $.validator.addMethod('maxsize', function(value, element, params) {
                if ($(element).attr('type') === 'file') {
                    if (element.files && element.files.length) {
                        for (var c = 0; c < element.files.length; c++) {
                            if (element.files[c].size > params) {
                                return false;
                            }
                        }
                    }
                }
                return true;
            }, $.validator.format("File Size must not exceed {0} bytes."));
            $('#datatable tbody').on('click', 'tr td.action a', function (){
                var note_id_pad = $(this).data('note');
                if($(this).hasClass('snatch')){
                    var id = parseInt($(this).parents('tr').attr('id'));
                    if(id){
                        $('#pcc_upload_form #delivery_note_id').val(id);
                        $('#pcc_upload_form #action').val(2);
                        $('#uploadPCCDepositSlip').modal('show');
                        $('#pcc_upload_form h2 span').text(note_id_pad);
                        $('#uploadPCCDepositSlip .modal-title span').text('Snatch');
                    }
                }
                else if($(this).hasClass('deduction')){
                    var id = parseInt($(this).parents('tr').attr('id'));
                    if(id){
                        $('#pcc_upload_form #delivery_note_id').val(id);
                        $('#pcc_upload_form #action').val(3);
                        $('#uploadPCCDepositSlip').modal('show');
                        $('#pcc_upload_form h2 span').text(note_id_pad);
                        $('#uploadPCCDepositSlip .modal-title span').text('Deduction');
                    }
                }
            });
            $('#amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'groupSeparator': ',',
                'autoGroup': true,
                'min': 0,
                'max': 1000000
            });
            $('#uploadPCCDepositSlip').on('hidden.bs.modal', function () {
                $("#pcc_upload_form").validate().resetForm();
                $("#pcc_upload_form")[0].reset();
                $("#pcc_upload_form #deposit_slip").val('');
            });

            $('#pcc_upload_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function (form) {
                    $(form).find('button[type=submit]').prop('disabled', true);
                    swal({
                        text: 'Are you sure you want to update deposit slip?',
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
                        if(confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Cash collection is being updated!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            form.submit();
                        }
                        else {
                            $(form).find('button[type=submit]').prop('disabled', false);
                        }
                    });
                }
            });

        });


    function fintechshipmentsshowfintech(event,id){
        var y = 1;
        $("#shipment_table").html('');
        $.ajax({
            type : 'get',
            url  : "{{route('admin.delivery.cash_collection.pending.fintechshipment')}}",
            data : {id:id},
            success:function(res){
                if(res.status == 200){
                    $("#fintech_modal").modal('show');
                    $("#no_transaction_message").hide();
                    $("#fintech_modal tr").show();

                    for(let x of res.data){
                        $("#shipment_table").append(`
                            <tr>
                            <td>${y++}</td>
                            <td>${x.trackingNo}</td>
                            <td>${x.COD_amount}</td>
                            <td>${x.transaction_id}</td>
                            <td>${x.Date}</td>
                            </tr>
                        `);
                    }
                } else{
                    $("#fintech_modal").modal('show');
                    $("#fintech_modal tr").hide();
                    $("#no_transaction_message").show();


                }
            }
        });
    }





    </script>
@endsection