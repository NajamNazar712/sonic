@extends('admin.layout.master')
@section('title','Month Closing Pending')


@section('content')
    <h1 class="mb-1">
        Month Closing-Pending
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                        <div class="col-4 form-group">
                            <input type="text" name="tracking_numbers" class="dt_search tracking_numbers"
                                   placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">
                        </div>
                        <div class="col-3 form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right height-5-per" id="search_date_from" placeholder="Search Date (From)">
                        </div>


                        <div class="col-3 form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right height-5-per" id="search_date_to" placeholder="Search Date (To)">
                        </div>


                        <div class="col-2 form-group">
                            <button type="button" id="search_filter_btn" class="btn btn-primary"><i class="la la-search"></i> Search</button>
                        </div>
                    </form>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking ID.</th>
                        <th class="border-primary border-darken-1">Current Status</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Number</th>
                        <th class="border-primary border-darken-1">Claim ID </th>
                        <th class="border-primary border-darken-1">Claim Type</th>
                        <th class="border-primary border-darken-1">Closing Type</th>
                        <th class="border-primary border-darken-1">Consignee Address</th>
                        <th class="border-primary border-darken-1">Comments</th>
                        <th class="border-primary border-darken-1">Closing Status</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Actions</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="closing_type_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="closing_type_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Closing Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="closing_status_form" class="mb-1 mt-2" method="POST" action="{{ route('admin.month_closing.pending.closing_type_update') }}" novalidate="novalidate">
                    {{ csrf_field() }}
                    <input type="hidden" name="shipment_ids" id="month_closing_status_shipment_ids">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="closing_type_id" id="closing_type_select" class="form-control select2" data-rule-required="true" data-msg-required="Closing Status is required">
                            @foreach($closing_types as $closing_type)
                                <option value="{{ $closing_type->id }}" > {{ $closing_type->name }} </option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="assign_agentSubmit">Assign</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_responsible_modal" role="dialog" aria-labelledby="add_responsible_modal_title" aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title" id="add_responsible_modal_title">Assign Responsible(s)</h4>

                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
                 <div class="modal-body text-center">
                     <form id="add_responsible_form" class="form-horizontal mb-1 justify-content-center" method="post" action="{{ route('admin.month_closing.pending.assign') }}" novalidate="novalidate">
                        @csrf
                         <input type="hidden" name="shipment_ids" id="responsible_person_shipment_ids">
                         <div class="form-group">
                             <label for="user_switch" class="font-medium-2 text-bold-600 mr-1">User(s)</label>
                             <input type="checkbox" name="user_switch" id="user_switch" class="switchery user_switch" data-color="success" data-size="sm"/>
                             <label for="user_switch" class="font-medium-2 text-bold-600 mr-1">Rider(s)</label>
                         </div>
                         <div class="form-group" id="users_div">
                             <select name="responsible_persons[]" id="responsible_persons" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                 @foreach($admins as $admin)
                                     <option rel="{{$admin->name}}" value="{{$admin->id}}">{{$admin->name}}  {{ ($admin->designation != null)? '( '.$admin->designation.' )':'' }} {{ (isset($admin->role->department)? '( '.$admin->role->department->name.' )':'') }} </option>
                                 @endforeach
                             </select>
                         </div>
                         <div class="form-group d-none" id="riders_div">
                             <select name="rider_responsible_persons[]" id="rider_responsible_persons" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                 @foreach($riders as $rider)
                                     <option rel="{{$rider->name}}" value="{{$rider->id}}">{{$rider->name}} - {{ $rider->city->name }}  ({{ $rider->rider_category->name }})</option>
                                 @endforeach
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="deduct_switch" class="font-medium-2 text-bold-600 mr-1">Deduct All</label>
                             <input type="checkbox" name="deduct_switch" id="deduct_switch" class="switchery deduct_switch" data-color="success" data-size="sm"/>
                             <label for="deduct_switch" class="font-medium-2 text-bold-600 mr-1">Deduct Individually</label>
                         </div>
                         <div id="deduct_all_div">
                             <div class="form-group">
                                 <input type="text" name="deduct_amount" class="form-control deduct_amount" placeholder="Deduct Amount for selected person(s)" data-rule-required="true" data-msg-required="Deduct Amount is required">
                             </div>
                         </div>
                         <div id="deduct_individual_div" style="display: none;">

                         </div>

                         <div class="form-group">
                             <textarea name="remarks" class="form-control remarks" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks required"></textarea>

                         </div>
                         <div class="form-group ml-1">
                             <button type="submit" name="save" class="btn btn-primary save" value="save">Save</button>
                         </div>
                     </form>

                 </div>

             </div>
         </div>
     </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            $('#responsible_persons').select2({
                width:'100%',
                placeholder:"Search User",
                allowClear:true,
            });
            $('#rider_responsible_persons').select2({
                width:'100%',
                placeholder:"Search Rider",
                allowClear:true,
            });
            $('#closing_type_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Closing Type"
            });
            $('.deduct_amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 10000000
            });
            //Selectize
            var select = $('#search_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.month_closing.pending.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Current Status');
                            head.push('COD Amount');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Shipper');
                            head.push('Consignee Name');
                            head.push('Number');
                            head.push('Claim ID');
                            head.push('Claim Type');
                            head.push('Closing Type');
                            head.push('Consignee Address');
                            head.push('Comments');
                            head.push('Closing Status');
                            head.push('Arrival Date');


                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.current_status);
                                row.push(values.cod_amount);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.shipper);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
                                row.push(values.claim_id);
                                row.push(values.claim_type);
                                row.push(values.closing_type);
                                row.push(values.consignee_address);
                                row.push(values.remarks);
                                row.push(values.closing_status);
                                row.push(values.arrival_date);


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
                @if (session('role_id') == 1 || count(array_intersect([408, 409, 410, 411, 412], session('permissions'))) !== 0)

                buttons: [
                    @if (session('role_id') == 1 || in_array(412, session('permissions')))
                    {
                        text: 'Assign Responsible',
                        className: 'btn btn-primary assign_responsible_multiple',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                $('#add_responsible_modal').modal('show');
                                $('#responsible_person_shipment_ids').val(selected_rows);
                            }else{
                                var error = "No shipments selected!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                    @endif
                    @if (session('role_id') == 1 || in_array(410, session('permissions')))
                    {
                        text: 'Switch To Resolve',
                        className: 'btn btn-primary resolve',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to update!',
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

                                        $.ajax({
                                            url:"{{route('admin.month_closing.pending.resolved')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids':selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                            }
                                        }).done(function (data) {
                                            UnblockPagePermanently();
                                            selected_rows = [];
                                            table.rows().deselect();
                                            table.button('.resolved').disable();
                                            table.button('.closing_status').disable();
                                            table.button('.assign_responsible_multiple').disable();
                                            table.draw(true);
                                            if(data.status == 0){
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else{
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            }

                                        });
                                    }
                                });


                            }else{
                                var error = "Not selected any shipments!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                    @endif
                    @if (session('role_id') == 1 || in_array(411, session('permissions')))
                    {
                        text: 'Closing Type',
                        className: 'btn btn-primary closing_type_status',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                $('#closing_type_modal').modal('show');
                            }else{
                                var error = "No shipments selected!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                    @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());
                                    row.select();

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.resolve').enable();
                                    table.button('.closing_type_status').enable();
                                    table.button('.assign_responsible_multiple').enable();

                                }
                            });
                        }
                    },
                    {
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
                                        table.button('.resolve').disable();
                                        table.button('.closing_type_status').disable();
                                        table.button('.assign_responsible_multiple').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Month Closing Pending',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'
                ],
                @else
                buttons:[{
                    extend: 'excel',
                    title: 'Month Closing Pending',
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
                    url: '{{ route('admin.month_closing.pending.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#search_form .tracking_numbers').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'shipment_id',
                order: [[2, 'desc']],
                columns: [
                    {data: 'shipment_id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'current_status', name: 'ss.name', class: 'align-middle current_status'},
                    {data: 'cod_amount', name: 'shipments.amount', class: 'align-middle cod_amount'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 'consignee_phone', class: 'align-middle consignee_phone'},
                    {data: 'claim_id_link', name: 'cr.id', class: 'align-middle claim_id_link'},
                    {data: 'claim_type', name: 'crn.type', class: 'align-middle claim_type'},
                    {data: 'closing_type', name: 'mct.name', class: 'align-middle closing_type'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'remarks', name: 'mc.remarks', class: 'align-middle remarks'},
                    {data: 'closing_status', name: 'mcs.name', class: 'align-middle closing_status'},
                    {data: 'arrival_date', name: 'sj.created_at', class: 'align-middle arrival_date'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    $('td:eq(0)', row).addClass('select-checkbox');
                    if ($.inArray(data.shipment_id, selected_rows) !== -1) {
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

                        if ( $(header).is('.select') || $(header).is('.serial_number') ||  $(header).is('.action') ) {
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
                    table.button('.resolve').enable();
                    table.button('.closing_type_status').enable();
                    table.button('.assign_responsible_multiple').enable();

                }
                else {
                    table.button('.resolve').disable();
                    table.button('.closing_type_status').disable();
                    table.button('.assign_responsible_multiple').disable();

                }

            });

            $('#user_switch').on('change',function(){
                user_switch = document.querySelector('#user_switch');
                if(user_switch.checked === true) {
                    $('#riders_div').removeClass('d-none');
                    $('#users_div').addClass('d-none');
                }
                else{
                    $('#users_div').removeClass('d-none');
                    $('#riders_div').addClass('d-none');
                }
                deduct_amount_switch_change = document.querySelector('#deduct_switch');
                if(deduct_amount_switch_change.checked === true) {
                    $('#deduct_all_div').slideDown();
                    $('#deduct_individual_div').slideUp();
                    $('#deduct_switch').trigger('click');
                }
            });

            $('#deduct_switch').on('change',function(){
                deduct_amount_switch_change = document.querySelector('#deduct_switch');
                user_switch = document.querySelector('#user_switch');
                var users_count = null;
                var responsible_ids = null;
                if(user_switch.checked === true) {
                    users_count = $('#rider_responsible_persons').val().length;
                }
                else{
                    users_count = $('#responsible_persons').val().length;
                }
                if(users_count > 0){
                    if(deduct_amount_switch_change.checked === true){
                        var html = '';
                        if(user_switch.checked === true) {
                            responsible_ids = $('#rider_responsible_persons').val();
                        }
                        else{
                            responsible_ids = $('#responsible_persons').val();
                        }
                        $.each(responsible_ids, function (index, value) {
                            var name = null;
                            if(user_switch.checked === true) {
                                name = $('#rider_responsible_persons').find('option[value="'+value+'"]').attr('rel');
                            }
                            else{
                                name = $('#responsible_persons').find('option[value="'+value+'"]').attr('rel');
                            }
                           html += '<div class="form-group row justify-content-center">\n' +
                               '                                 <div class="col-3">\n' +
                               '                                     <label for="deduct_amount" class="mb-0 align-middle">'+ name +'</label>\n' +
                               '                                 </div>\n' +
                               '                                 <div class="form-group mb-0 col-6">\n' +
                               '                                     <input type="text" id="deduct_amount_'+ value +'" name="deduct_amount_individual['+ value +']" class="form-control deduct_amount validated" placeholder="Deduct Amount" data-rule-required="true" data-msg-required="Deduct Amount is required">\n' +
                               '                                 </div>\n' +
                               '                             </div>';

                        });
                        $('#deduct_individual_div').html(html);
                        $('.deduct_amount').inputmask({
                            'alias': 'integer',
                            'allowMinus': false,
                            'allowPlus': false,
                            'rightAlign': false,
                            'min': 0,
                            'max': 10000000
                        });
                        $(".deduct_amount .validated").each(function(){
                            $( this ).rules( "add", {
                                required: true,
                            });
                        });
                        $('#deduct_individual_div').slideDown();
                        $('#deduct_all_div').slideUp();

                    }
                    else{
                        $('#deduct_all_div').slideDown();
                        $('#deduct_individual_div').slideUp();
                    }
                }
                else{
                    if(deduct_amount_switch_change.checked === true){
                        var error = 'Select atleast one responsible person!';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $('#deduct_switch').trigger('click');
                    }

                }

            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                if(shipment_id){
                    if ($(this).hasClass('assign_responsible')) {
                        $('#add_responsible_modal').modal('show');
                        $('#responsible_person_shipment_ids').val(shipment_id);
                    }
                }
            });

            $('#add_responsible_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Responsible Person(s) are being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('#edit_responsible_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Responsible Person(s) are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#closing_status_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    $('#month_closing_status_shipment_ids').val(selected_rows);
                    swal({
                        title: 'Please Wait!',
                        text: 'Closing Type is being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });


        });
    </script>
@endsection
