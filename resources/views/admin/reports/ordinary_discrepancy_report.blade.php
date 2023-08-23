@extends('admin.layout.master')
@section('title','Ordinary Discrepancy Report')
@section('content')
    <h1 class="mb-1">
        Ordinary Discrepancy Report
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
                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" data-value="{{ Carbon\Carbon::today() }}" placeholder="Search Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" data-value="{{ Carbon\Carbon::today() }}" placeholder="Search Date (To)">
                        </div>
                    </div>
                    
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                

                <form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>

                    <div class="form-group ml-1">
                        <button type="submit" name="add" class="btn btn-primary add" value="Add">Track</button>
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking no.</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Shipment Status</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Quantity - booking</th>
                            <th class="border-primary border-darken-1">COD Value</th>
                            <th class="border-primary border-darken-1">Shipment Content - booking</th>
                            <th class="border-primary border-darken-1">Shipment Content - Request Generate</th>
                            <th class="border-primary border-darken-1">Image</th>
                            <th class="border-primary border-darken-1">Quantity - Request Generate</th>
                            <th class="border-primary border-darken-1">Comment or Remarks</th>
                            <th class="border-primary border-darken-1">Created At</th>
                            <th class="border-primary border-darken-1">Created By</th>
                            <th class="border-primary border-darken-1">Admin Hub</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <!-- Shipment Tracking Modal -->
    {{-- <div class="modal fade" id="shipment_tracking_modal" data-backdrop="static" role="dialog" aria-labelledby="shipment_tracking_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 900px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipment_tracking_modal_title">Shipment Tracking</h4>
                </div>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <div class="row mb-2 justify-content-center" >
                                <div class="form-group input-group ml">
                                    <input type="text" name="origin" class="form-control origin" data-rule-required="true" value="origin">
                                    <input type="text" name="destination" class="form-control destination" data-rule-required="true" value="destination">
                                    <input type="text" name="shipment_status" class="form-control shipment_status" data-rule-required="true" value="shipment_status">
                                </div>
                                <div class="form-group input-group ml">
                                    <input type="text" name="origin" class="form-control origin" data-rule-required="true" value="origin">
                                    <input type="text" name="destination" class="form-control destination" data-rule-required="true" value="destination">
                                    <input type="text" name="shipment_status" class="form-control shipment_status" data-rule-required="true" value="shipment_status">
                                </div>
                                <div class="form-group input-group ml">
                                    <input type="text" name="origin" class="form-control origin" data-rule-required="true" value="origin">
                                    <input type="text" name="destination" class="form-control destination" data-rule-required="true" value="destination">
                                    <input type="text" name="shipment_status" class="form-control shipment_status" data-rule-required="true" value="shipment_status">
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Shipment Tracking Modal -->

    <div class="modal fade" id="shipment_tracking_modal" data-backdrop="static" role="dialog" aria-labelledby="shipment_tracking_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 900px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipment_tracking_modal_title">Shipment Tracking</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form action="#" method="post" accept-charset="utf-8">
                <div class="modal-body" style="padding: 5px;">
                        <div class="row p-1">
                            <div class="col">
                                <input class="form-control" name="firstname" placeholder="Firstname" type="text" required autofocus />
                            </div>
                            <div class="col">
                                <input class="form-control" name="lastname" placeholder="Lastname" type="text" required />
                            </div>
                            <div class="col">
                                <input class="form-control" name="lastname" placeholder="Lastname" type="text" required />
                            </div>
                        </div>
                        <div class="row p-1">
                            <div class="col">
                                <input class="form-control" name="firstname" placeholder="Firstname" type="text" required autofocus />
                            </div>
                            <div class="col">
                                <input class="form-control" name="lastname" placeholder="Lastname" type="text" required />
                            </div>
                            <div class="col">
                                <input class="form-control" name="lastname" placeholder="Lastname" type="text" required />
                            </div>
                        </div>
                        <div class="row p-1">
                            <div class="col">
                                <input class="form-control" name="firstname" placeholder="Firstname" type="text" required autofocus />
                            </div>
                            <div class="col">
                                <input class="form-control" name="lastname" placeholder="Lastname" type="text" required />
                            </div>
                            <div class="col">
                                <input class="form-control" name="lastname" placeholder="Lastname" type="text" required />
                            </div>
                        </div>
                    </div>  
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#add_shipment_form input.tracking_number').focus();

            $('#add_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#add_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#shipment_tracking_modal').modal('show');
                   
                }
            });


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

            $('#operation_rider_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Category*',
            });

            //Excel
            // jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            //     if ( this.context.length ) {
            //         body = [];
            //         var params = table.ajax.params();
            //         params.start = 0;
            //         params.length = -1;
            //         params.excel = true;
            //         var jsonResult = $.ajax({
            //             url: '{{ route('admin.delivery.history.list') }}',
            //             data: params,
            //             success: function (result) {
            //                 head = [];

            //                 head.push('S.No');
            //                 head.push('Delivery Note No.');
            //                 head.push('Status');
            //                 head.push('Hub');
            //                 head.push('Zone');
            //                 head.push('Rider ID');
            //                 head.push('Rider');
            //                 head.push('Area');
            //                 head.push('Rider Type');
            //                 head.push('Rider Category');
            //                 head.push('Route');
            //                 head.push('No. Of Shipments');
            //                 head.push('No. Of Shipments Delivered');
            //                 head.push('Assigned By');
            //                 head.push('Assigned Date');
            //                 head.push('Updated By');
            //                 head.push('Updated Date');
            //                 head.push('Cash Collected By');
            //                 head.push('Cash Collection Date');
            //                 head.push('DNCC Amount');
            //                 head.push('HBL Konnect  Amount');
            //                 head.push('Cash Amount');
            //                 head.push('One Link Payment Count');
            //                 head.push('Created Via');
            //                 head.push('Updated Via App');
            //                 head.push('Last Updated At');

            //                 $.each(result.data, function(index, values) {
            //                     row = [];


            //                     row.push(index + 1);
            //                     row.push(values.delivery_note_id_padded);
            //                     row.push(values.main_status);
            //                     row.push(values.hub);
            //                     row.push(values.zone_name);
            //                     row.push(values.rider_trax_id);
            //                     row.push(values.rider);
            //                     row.push(values.area);
            //                     row.push(values.rider_type);
            //                     row.push(values.operation_rider_id);
            //                     row.push(values.route);
            //                     row.push(values.shipments_count);
            //                     row.push(values.delivered_shipments);
            //                     row.push(values.assignee);
            //                     row.push(values.created_at);
            //                     row.push(values.updated_by);
            //                     row.push(values.updated_at);
            //                     row.push(values.cash_collected);
            //                     row.push(values.cash_collected_at);
            //                     row.push(values.amount);
            //                     row.push(values.transactions_amount);
            //                     row.push(values.cash_amount);
            //                     row.push(values.one_link_payment_count);
            //                     row.push(values.created_via);
            //                     row.push(values.updated_via_app);
            //                     row.push(values.last_updated_at);

            //                     body.push(row);
            //                 });
            //             },
            //             async: false
            //         });

            //         return {body: body, header: head};
            //     }
            // } );
            // var selected_rows = [];
            // var table = $('#datatable').DataTable({
            //     dom: '<"d-inline-block"l><"pull-right"B>tipr',
            //     buttons:[{
            //         extend: 'excel',
            //         title: 'Delivery Note History',
            //         className: 'btn btn-primary',
            //         text: '<i class="la la-file-excel-o"></i> Excel',
            //     },'reset'],
            //     scrollX: true, scrollY: '500px',
            //     lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            //     pageLength: 50,
            //     pagingType: 'full_numbers',
            //     processing: true,
            //     deferLoading: 0,
            //     language: {
            //         processing: data_table_loader
            //     },
            //     serverSide: true,
            //     ajax:{
            //         url: '{{ route('admin.delivery.history.list') }}',
            //         data: function (d) {
            //             d.search_date_from = $('input[name="search_date_from_formatted"]').val();
            //             d.search_date_to = $('input[name="search_date_to_formatted"]').val();
            //             d.operation_rider_id=$('#operation_rider_id').val();
            //         }
            //     },
            //     rowId: 'delivery_note_id',
            //     order: [[14, 'desc']],
            //     columns: [
            //         {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
            //         { data:'delivery_note' ,name: 'delivery_notes.id', class: 'align-middle text-center delivery_note'},
            //         { data:'main_status' ,name: 'main_status', class: 'align-middle status',orderable:false},
            //         { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
            //         { data:'zone_name' ,name: 'zn.name', class: 'align-middle zone_name'},
            //         { data:'rider_trax_id' ,name: 'riders.trax_id', class: 'align-middle rider_trax_id'},
            //         { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
            //         { data:'area' ,name: 'ca.name', class: 'align-middle area'},
            //         { data:'rider_type' ,name: 'rt.name', class: 'align-middle rider_type'},
            //         { data: 'operation_rider_id', name: 'riders.operation_rider_id', class: 'align-middle operation_rider_id'},
            //         { data:'route' ,name: 'route', class: 'align-middle route'},
            //         { data:'shipments_count_link' ,name: 'delivery_notes.shipments_count', class: 'align-middle shipments_count_link text-center'},
            //         { data:'delivered_shipments_link' ,name: 'delivery_notes.delivered_shipments', class: 'align-middle delivered_shipments_link text-center'},
            //         { data:'assignee' ,name: 'admins.name', class: 'align-middle assignee'},
            //         { data:'created_at' ,name: 'created_at', class: 'align-middle created_at'},
            //         { data:'updated_by' ,name: 'ub.name', class: 'align-middle updated_by'},
            //         { data:'updated_at' ,name: 'delivery_notes.updated_at', class: 'align-middle updated_at'},
            //         { data:'cash_collected' ,name: 'ccb.name', class: 'align-middle cash_collected'},
            //         { data:'cash_collected_at' ,name: 'delivery_notes.cash_collected_at', class: 'align-middle cash_collected_at'},
            //         { data:'amount' ,name: 'delivery_notes.received_cod_amount', class: 'align-middle amount'},
            //         { data:'fintech_shipments_charges' ,name: 'fintech_shipments_charges', class: 'align-middle fintech_shipments_charges', orderable: false, searchable: false},
            //         { data:'transactions_amount_link' ,name: 'hktdn.transactions_amount', class: 'align-middle transactions_amount'},
            //         { data:'cash_amount' ,name: 'hktdn.cash_amount', class: 'align-middle cash_amount', orderable: false, searchable: false},
            //         { data:'one_link_payment_count_button' ,name: 'delivery_notes.one_link_payment_count', class: 'align-middle text-center one_link_payment_count'},
            //         { data: 'created_via', name: 'delivery_notes.created_via_app', class: 'align-middle created_via'},
            //         { data:'updated_via_app' ,name: 'rdns.status', class: 'align-middle updated_via_app'},
            //         { data:'last_updated_at' ,name: 'delivery_notes.last_updated_at', class: 'align-middle last_updated_at'},
            //     ],
            //     rowCallback: function(row, data, index) {
            //         var info = table.page.info();

            //         $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    
            //         var fintech_sum = $(row).find('#myButton');
            //         var dccn_amount = parseFloat(data.amount.replace(/,/g, ''));
            //         fintech_sum = fintech_sum[0].innerText
            //         fintech_sum = parseFloat(fintech_sum)

            //         if(data.transactions_amount == null){
                        
            //             data.transactions_amount = 0
            //         }
            //         value = dccn_amount - fintech_sum - data.transactions_amount;

            //         $('td:eq(22)', row).html(value);


            //     },
            //     initComplete: function() {
            //         var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

            //         var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
            //         var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
            //         var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
            //         var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
            //             '<option value="0">Pending for Update</option>' +
            //             '<option value="1">Pending for Verification</option>' +
            //             '<option value="2">Cash Collected</option>' +
            //             '<option value="3">Completed</option>' +
            //             '<option value="4">Verified</option>' +
            //             '<option value="5">Canceled</option>' +
            //             '</select>';

            //         var updated_by_app_select = '<select name="updated_by_app_select" id="updated_by_app_select" class="select2 form-control">' +
            //             '<option value="0">No</option>' +
            //             '<option value="1">Partial</option>' +
            //             '<option value="2">Yes</option>' +
            //             '</select>';
            //         this.api().columns().every(function(column_id) {
            //             var column = this;
            //             var header = column.header();

            //             if ($(header).is('.serial_number') || $(header).is('.cash_amount') || $(header).is('.fintech_shipments_charges')) {
            //                 $(td).appendTo($(search));
            //             }else if($(header).is('.status')){
            //                 $(status_select).appendTo($(search))
            //                     .on( 'change', function () {
            //                         column.search($(this).val(), false, false, true).draw();
            //                     } ).wrap(td);
            //             }
            //             else if($(header).is('.updated_via_app')){
            //                 $(updated_by_app_select).appendTo($(search))
            //                     .on( 'change', function () {
            //                         column.search($(this).val(), false, false, true).draw();
            //                     } ).wrap(td);
            //             }
            //             else {
            //                 var current = $(input).appendTo($(search)).on('change', function() {
            //                     column.search($(this).val(), false, false, true).draw();
            //                 }).wrap(td).after(icon);

            //                 if (column.search()) {
            //                     current.val(column.search());
            //                 }
            //             }
            //         });
            //         $("#status_select").prepend('<option value="" selected></option>').select2({
            //             placeholder: "Select Status",
            //             width:'100%',
            //             containerCssClass: 'select-xs',
            //             dropdownCssClass: 'form-control-sm p-0'
            //         });

            //         $("#updated_by_app_select").prepend('<option value="" selected></option>').select2({
            //             placeholder: "Select Updated Via App",
            //             width:'100%',
            //             containerCssClass: 'select-xs',
            //             dropdownCssClass: 'form-control-sm p-0'
            //         });
            //         this.api().table().columns.adjust();
            //     }
            // });

            $('#search_filter_btn').on('click',function () {
                table.draw();
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


            $('#datatable tbody').on('click','tr td.one_link_payment_count button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                console.log(id);
                $('#shipment_tracking_modal').modal('show');

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
                        }
                    });

            });

        });

    </script>
@endsection