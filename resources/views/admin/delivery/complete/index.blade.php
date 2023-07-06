@extends('admin.layout.master')
@section('title','Completed Deliveries')
@section('content')
    <h1 class="mb-1"> 
        Completed Deliveries COD 
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-4 m-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Scan To Select" id="select_dn">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-9">
                        <form id="track_form" class="form-inline" novalidate="novalidate">
                            <div class="col-4">
                                <div class="form-group">
                                    <input type="text" class="tracking_numbers form-control"  name="tracking_numbers" id="search_tracking">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <input type="text" name="dncc" class="form-control dncc" id="dncc">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <button id="datatable_filter_btn" type="submit" class=" btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li class="primary border-primary round"><a
                                                    data-action="collapse">Legend
                                                <i class="ft-minus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-body p-1">
                                    <h4 class=" info">Legend</h4>
                                    <input type="hidden" id="legend_filter">

                                    <table class="table mb-0" id="legends_table">
                                        <tbody>
                                        <tr class="legends">
                                            <td class="align-middle" id="2" style="background-color: yellow;">Snatch / Deduction</td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <form id="post_delivery_note_ids_form" action="{{route('admin.delivery.completed.deposit.dncc')}}" method="post">
                    @csrf
                    <input type="hidden" name="delivery_note_ids" id="delivery_note_ids">
                </form>

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
                        <th class="border-primary border-darken-1">Cash Collected By</th>
                        <th class="border-primary border-darken-1">Cash Collection Date</th>

                        <th class="border-primary border-darken-1">Fintech Charges</th>

                        <th class="border-primary border-darken-1">DNCC Amount</th>
                        <th class="border-primary border-darken-1">HBL Konnect Amount</th>
                        <th class="border-primary border-darken-1">Cash Amount</th>
                        <th class="border-primary border-darken-1">One Link Payment Count</th>
                        <th class="border-primary border-darken-1">Deposit Amount</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Deposit Slip</th>
                    </tr>
                    </thead>
                </table>

            </div>
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


    <div class="modal fade" id="fintech_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Fintech Charges(s)</h4>
    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
    
                    <table>
                        <tr>
                          <th>Tracking #</th>
                          <th>Fintech Charges</th>
                          <th>Created Date</th>
                        </tr>
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

    <!-- one link payment details popup -->
    <div class="modal fade" id="one_link_payment_details_modal" data-backdrop="static" role="dialog" aria-labelledby="one_link_payment_details_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 900px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="one_link_payment_details_modal_title">One Link Payment(s)</h4>
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

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

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
        .selectize-control {
            width: 300px !important;
        }
        tr.pcc_snatch_deduction {
            background-color: yellow;
            color: black;
        }
        .legends{
            cursor:pointer;
        }
        .legends tr td{
            color:black;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

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
                        url: '{{ route('admin.delivery.completed.list') }}',
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
                            head.push('Cash Collected By');
                            head.push('Cash Collection Date');
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
                                row.push(values.cash_collected);
                                row.push(values.cash_collected_at);
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
                @if (session('role_id') == 1 || in_array(41, session('permissions')))
                    buttons: [{
                        text: 'Deposit DNCC',
                        className: 'btn btn-primary delivered',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Deposit DNCC!',
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
                                            $('#post_delivery_note_ids_form').submit();
                                        }
                                    }
                                });


                            }else{
                                var error = "Something went wrong please refresh page and try again!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        }
                    },{
                        extend: 'excel',
                        title: 'Completed Deliveries',
                        className: 'btn btn-primary',
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

                                    table.button('.delivered').enable();
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
                                table.button('.delivered').disable();

                                hub_ids.splice(index, 1);
                            }
                          }
                        });
                    }
                },
                    'reset'],
                @else
                    buttons:[{
                    extend: 'excel',
                    title: 'Completed Deliveries',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'],
                @endif
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url:'{{ route('admin.delivery.completed.list') }}',
                    data:function (d) {
                        //d.search_tracking = $('#search_tracking').val();
                        d.legend_filter = $('#legend_filter').val();
                        d.tracking_numbers = $('#search_tracking').val();
                        d.dncc = $('#dncc').val();
                    }
                },
                rowId: 'delivery_note_id',
                order: [[13, 'desc']],
                columns: [
                    {data: 'delivery_note_id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'delivery_note' ,name: 'delivery_notes.id', class: 'align-middle text-center delivery_note'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'zone_name' ,name: 'zn.name', class: 'align-middle zone'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'area' ,name: 'ca.name', class: 'align-middle area'},
                    { data:'rider_type' ,name: 'rt.name', class: 'align-middle rider_type'},
                    { data:'route' ,name: 'route', class: 'align-middle route'},
                    { data:'shipments_count_link' ,name: 'delivery_notes.shipments_count', class: 'align-middle shipments_count_link text-center'},
                    { data:'delivered_shipments_link' ,name: 'delivery_notes.delivered_shipments', class: 'align-middle delivered_shipments_link text-center'},
                    { data:'assignee' ,name: 'admins.name', class: 'align-middle assignee'},
                    { data:'created_at' ,name: 'created_at', class: 'align-middle created_at'},
                    { data:'updated_by' ,name: 'ub.name', class: 'align-middle updated_by'},
                    { data:'updated_at' ,name: 'delivery_notes.updated_at', class: 'align-middle updated_at'},
                    { data:'cash_collected' ,name: 'ccb.name', class: 'align-middle cash_collected'},
                    { data:'cash_collected_at' ,name: 'delivery_notes.cash_collected_at', class: 'align-middle cash_collected_at'},
                    { data:'fintech_charges' ,name: 'fintech_charges', class: 'align-middle fintech_charges'},
                    { data:'amount' ,name: 'delivery_notes.received_cod_amount', class: 'align-middle amount'},
                    { data:'transactions_amount_link' ,name: 'hktdn.transactions_amount', class: 'align-middle transactions_amount'},
                    { data:'cash_amount' ,name: 'hktdn.cash_amount', class: 'align-middle cash_amount', orderable: false, searchable: false},
                    { data:'one_link_payment_count_button' ,name: 'delivery_notes.one_link_payment_count', class: 'align-middle text-center one_link_payment_count'},
                    { data:'deposit_amount' ,name: 'dcc.amount', class: 'align-middle deposit_amount', orderable: false},
                    { data:'remarks' ,name: 'dcc.remarks', class: 'align-middle remarks', orderable: false},
                    { data:'deposit_slip_view' ,name: 'deposit_slip_view', class: 'align-middle deposit_slip_view', orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    if(data.status == 1 && data.cash_collection_status == 1){
                        $('td:eq(0)', row).addClass('select-checkbox');
                    }
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.delivery_note_id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.cash_amount') || $(header).is('.deposit_slip_view')) {
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
                        table.button('.delivered').enable();
                    }
                    else {
                        table.button('.delivered').disable();
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
                            table.button('.delivered').enable();
                        }
                        else {
                            hub_ids.splice(index, 1);
                            table.button('.delivered').disable();
                        }
                    }else{
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        return false;
                    }

                }

            });

            var select = $('#track_form #search_tracking').selectize({
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
            // });
            $('#select_dn').on('change',function () {
                var id = $(this).val();
                row = table.row('#' + id);
                if(row.length >0) {
                    row.select();
                    scan_sound(1);
                    if (hub_ids.length == 0) {
                        hub_ids.push(row.data().hub_id);
                    }
                    var index = $.inArray(id, selected_rows);

                    if (index === -1) {
                        selected_rows.push(id);
                    }
                else {
                        row.deselect();
                        selected_rows.splice(index, 1);
                    }

                    if (selected_rows.length > 0) {
                        table.button('.delivered').enable();
                    }
                    else {
                        table.button('.delivered').disable();
                        hub_ids.splice(index, 1);
                    }
                }else{
                    scan_sound(2);
                    var error = "Delivery Note not found!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                $(this).val('');

            });

            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.shipments_count_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.completed.shipments') !!}',
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
            $('#datatable tbody').on('click','tr td.delivered_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#delivered_shipments_modal .modal-body').html('');
                $('#delivered_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.completed.shipments.delivered') !!}',
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

            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                var tracking_numbers = $('#track_form .tracking_numbers').val();
                var dncc = $('#track_form .dncc').val();
                if (tracking_numbers != '' || dncc != '' ) {
                    table.draw();
                }
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
                                            <td>${values.tracking_no}</td>
                                            <td>${values.tran_auth_id}</td>
                                            <td>${values.amount}</td>
                                            <td>${values.tran_date_formated}</td>
                                            <td>${values.tran_time_formated}</td>
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
            $('table#legends_table').on('click', 'tr td', function(){
                var id = parseInt($(this).attr('id'));
                if(id){
                    $('#legend_filter').val(id);
                    table.draw()
                }
            });

        });

        function fintechshipmentsshow(event,id){
    $("#shipment_table").html('');
    $.ajax({
        type : 'get',
        url  : "{{route('admin.delivery.cash_collection.pending.showshipment')}}",
        data : {id:id},
        success:function(res){
            $("#fintech_modal").modal('show');
            for(let x of res.data){
                $("#shipment_table").append(`
                    <tr>
                    <td>${x.trackingNo}</td>
                    <td>${x.fintech_charges}</td>
                    <td>${x.Date}</td>
                    </tr>
                `);
            }
            
        }
        
    });
}



    </script>
@endsection