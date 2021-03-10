@extends('admin.layout.master')

@section('title', 'Fuel Management')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Fuel Management
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="search_form" class=" mb-1 justify-content-center" novalidate="novalidate">
                                <div class="row justify-content-center">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Search Card Number" data-tags-input-name="card_number" name="card_number_search" id="card_number_search">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <select name="staff_select" id="staff_select" class="select2">
                                                @foreach($staffs as $staff)
                                                    <option > {{ $staff->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group">
                                            <select name="rider_select" id="rider_select" class="select2">
                                                @foreach($riders as $rider)
                                                    <option > {{ $rider->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group">
                                            <select name="fleet_select" id="fleet_select" class="select2">
                                                @foreach($fleets as $fleet)
                                                    <option> {{ $fleet->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row mb-2 justify-content-center ">
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                            </div>
                                            <input type="text" name="approved_at_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="approved_at_from" placeholder="Approved At From">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                            </div>
                                            <input type="text" name="approved_at_to" class="form-control bg-primary border-primary white rounded-right pickadate" id="approved_at_to" placeholder="Approved At To">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <button type="submit" id="search_filter_btn" class="mr-1 w-100 mb-1 btn btn-outline-primary btn-min-width search"><i class="la la-search"></i> Search</button>
                                        </div>
                                    </div>

                                </div>
                            </form>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Fuel Request Id</th>
                                    <th class="border-primary border-darken-1">Card Number</th>
                                    <th class="border-primary border-darken-1">Card Holder</th>
                                    <th class="border-primary border-darken-1">Card Holder Type</th>
                                    <th class="border-primary border-darken-1">Card Request Type</th>
                                    <th class="border-primary border-darken-1">Amount / Liter</th>
                                    <th class="border-primary border-darken-1">Fuel Type</th>
                                    <th class="border-primary border-darken-1">Fuel Deduction Type</th>
                                    <th class="border-primary border-darken-1">Requested by</th>
                                    <th class="border-primary border-darken-1">Approved by</th>
                                    <th class="border-primary border-darken-1">Approved at</th>
                                    <th class="border-primary border-darken-1">Updated at</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="FuelCardHolderTypeSelectModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="FuelCardHolderTypeSelectModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Select Fuel Card Holder Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="form-group text-center">
                            <select name="card_holder_type" id="card_holder_type" form="fuel_request_form" class="select2">
                                @foreach($card_holder_types as $card_holder_type)
                                    <option value="{{ $card_holder_type->id }}" > {{ $card_holder_type->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group text-center" id="fleet_vehicle_type_container">
                            <select name="fleet_vehicle_type" id="fleet_vehicle_type" form="fuel_request_form" class="select2">
                                @foreach($vehicle_types as $vehicle_type)
                                    <option value="{{ $vehicle_type->id }}" > {{ $vehicle_type->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="card_holder_type_value_select_btn" class="btn btn-success">Next</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="FuelRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="FuelRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Fuel Card Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="fuel_request_form" method="post" action="{{route('admin.user_management.fuel_management.store')}}">
                    @csrf
                    <div class="modal-body">
                        <div class="row p-1 mb-2">
                            <div class="col-12">
                                <fieldset class="form-group">
                                    <select name="card_request_type" id="card_request_type" class="select2 form-control">
                                        @foreach($card_request_types as $card_request_type)
                                            <option value="{{ $card_request_type->id }}" > {{ $card_request_type->name }} </option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-8 all_request_type reassign_request_type block_request_type unblock_request_type">
                                <fieldset class="form-group">
                                    <input type="text" class="form-control" name="card_number" id="search_card_number_input"  placeholder="Search Card Number">
                                </fieldset>
                            </div>
                            <div class="col-4 all_request_type reassign_request_type block_request_type unblock_request_type">
                                <fieldset class="form-group">
                                    <button type="button" id="search_card_number_btn" class="btn btn-success w-100">Search</button>
                                </fieldset>
                            </div>
                            <div class="col-12 all_request_type reassign_request_type block_request_type unblock_request_type">
                                <input type="hidden" name="card_request_id" id="card_request_id" />
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>Card Number</th>
                                            <th>Card Holder</th>
                                            <th>Card Holder Type</th>
                                            <th>Amount / Liter</th>
                                            <th>Fuel Type</th>
                                            <th>Fuel Deduction Type</th>
                                            <th>Requested By</th>
                                            <th>Approved By</th>
                                            <th>Approved At</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 all_request_type new_request_type reassign_request_type">
                                <fieldset class="form-group" id="card_holder_select_container">

                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="fuel_card_request_btn" class="btn btn-success">Submit</button>
                        <button type="button" onclick="$('#FuelRequestModal').modal('hide');$('#FuelCardHolderTypeSelectModal').modal('show');" class="btn btn-danger">Back</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ApproveModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ApproveModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id=""></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="fuel_request_approve_form" method="post" action="{{route('admin.user_management.fuel_management.approve')}}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="request_id" id="request_id">
                        <div class="row p-1 mb-2">
                            <div class="col-6">
                                <fieldset class="form-group">
                                    <select name="fuel_deduction_type" id="fuel_deduction_type" class="select2 form-control">
                                        @foreach($fuel_deduction_types as $fuel_deduction_type)
                                            <option value="{{ $fuel_deduction_type->id }}" > {{ $fuel_deduction_type->name }} </option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-6">
                                <fieldset class="form-group">
                                    <select name="fuel_type" id="fuel_type" class="select2 form-control">
                                        @foreach($fuel_types as $fuel_type)
                                            <option value="{{ $fuel_type->id }}" > {{ $fuel_type->name }} </option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12">
                                <fieldset class="form-group">
                                   <input type="text" name="amount" class="form-control" id="amount" placeholder="Amount / Liter"/>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="fuel_request_approve_btn" class="btn btn-success">Approve</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {


            var today = new Date();
            today.setHours(0,0,0,0);

            var approved_at_from = $('#search_form #approved_at_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                max: today,
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #approved_at_to').pickadate('picker').set('min', $('#search_form #approved_at_from').pickadate('picker').get('select'));
                    }
                }
            });

            var approved_at_to = $('#search_form #approved_at_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                max: today,
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #approved_at_from').pickadate('picker').set('max', $('#search_form #approved_at_to').pickadate('picker').get('select'));
                    }
                }
            });

            var card_number_selectize = $('#search_form #card_number_search').selectize({
                placeholder: 'Card Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                // onType: function(str) {
                //     var regex = /^[0-9,]+$/;
                //
                //     if (!regex.test(str)) {
                //         select[0].selectize.setTextboxValue('');
                //     }
                // },
                create: function(input) {
                    if (input.length >= 15 ) {
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
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.user_management.fuel_management.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Fuel Request ID');
                            head.push('Card Number');
                            head.push('Card Holder');
                            head.push('Card Holder Type');
                            head.push('Card Request Type');
                            head.push('Amount / Liter');
                            head.push('Fuel Type');
                            head.push('Fuel Deduction Type');
                            head.push('Requested By');
                            head.push('Approved By');
                            head.push('Approved At');
                            head.push('Updated At');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.fuel_request_id_for_excel);
                                row.push(values.card_number);
                                row.push(values.card_holder);
                                row.push(values.card_holder_type);
                                row.push(values.card_request_type);
                                row.push(values.amount);
                                row.push(values.fuel_type);
                                row.push(values.fuel_deduction_type);
                                row.push(values.requested_by);
                                row.push(values.approved_by);
                                row.push(values.approved_at);
                                row.push(values.updated_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({

                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                @if (session('role_id') == 1 || in_array(451, session('permissions')))
                 {
                    text: 'Add',
                    className: 'btn btn-primary add',
                    text: '<i class="la la-plus"></i> Add',
                    action: function (e, dt, node, config) {
                        $('#card_holder_type').val('')
                        $('#card_holder_type').trigger('change');
                        $('#FuelCardHolderTypeSelectModal').modal('show');
                    }

                },
                @endif
                @if (session('role_id') == 1 || in_array(452, session('permissions')))
                {
                    text: 'History',
                    className: 'btn btn-primary history',
                    text: '<i class="la la-history"></i> History',
                    action: function (e, dt, node, config) {
                        window.open("{!! route('admin.user_management.fuel_management.history.index') !!}",'_blank');
                    }
                }
                @endif
                ,{
                        extend: 'excel',
                        title: 'Fuel Card Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
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
                    url: '{{ route('admin.user_management.fuel_management.list') }}',
                    data: function (d) {
                        d.card_number = $('#card_number_search').val();
                        d.staff_card_holder =$('#staff_select').val();
                        d.rider_card_holder =$('#rider_select').val();
                        d.fleet_card_holder =$('#fleet_select').val();
                        d.aprroved_at_from = $('input[name="approved_at_from_formatted"]').val();
                        d.approved_at_to = $('input[name="approved_at_to_formatted"]').val();
                    },
                },
                rowId: 'id',
                order: [[12, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'fuel_request_id', name: 'fuel_request_id', class: 'align-middle fuel_request_id'},
                    {data: 'card_number', name: 'card_number', class: 'align-middle card_number'},
                    {data: 'card_holder', name: 'card_holder', class: 'align-middle card_holder'},
                    {data: 'card_holder_type', name: 'card_holder_type_id', class: 'align-middle card_holder_type'},
                    {data: 'card_request_type', name: 'fcrt.id', class: 'align-middle card_request_type'},
                    {data: 'amount', name: 'amount', class: 'align-middle amount'},
                    {data: 'fuel_type', name: 'fuel_type_id', class: 'align-middle fuel_type'},
                    {data: 'fuel_deduction_type', name: 'fuel_deduction_type_id', class: 'align-middle fuel_deduction_type'},
                    {data: 'requested_by', name: 'requested_by', class: 'align-middle requested_by'},
                    {data: 'approved_by', name: 'approved_by', class: 'align-middle approved_by'},
                    {data: 'approved_at', name: 'approved_at', class: 'align-middle approved_at'},
                    {data: 'updated_at', name: 'updated_at', class: 'align-middle updated_at'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var card_holder_type_select = '<select name="card_holder_type_select" id="card_holder_type_select" class="select2 form-control"></select>';
                    var fuel_type_select = '<select name="fuel_type_select" id="fuel_type_select" class="select2 form-control"></select>';
                    var fuel_deduction_type_select = '<select name="fuel_deduction_type_select" id="fuel_deduction_type_select" class="select2 form-control"></select>';
                    var card_request_type_select = '<select name="card_request_type_select" id="card_request_type_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.card_holder')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.card_holder_type')){
                            $(card_holder_type_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.fuel_type')){
                            $(fuel_type_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.fuel_deduction_type')){
                            $(fuel_deduction_type_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.card_request_type')){
                            $(card_request_type_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
                    var card_holder_type_data = $.map({!! $card_holder_types !!}, function (obj) {
                        obj.id = obj.id; // replace name with the property used for the text
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });
                    var fuel_type_data = $.map({!! $fuel_types !!}, function (obj) {
                        obj.id = obj.id; // replace name with the property used for the text
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });
                    var fuel_deduction_type_data = $.map({!! $fuel_deduction_types !!}, function (obj) {
                        obj.id = obj.id; // replace name with the property used for the text
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });
                    var card_request_type_data = $.map({!! $card_request_types !!}, function (obj) {
                        obj.id = obj.id; // replace name with the property used for the text
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });
                    $("#card_holder_type_select").prepend('<option value="" selected></option>').select2({
                        data:card_holder_type_data,
                        placeholder: "Select Card Holder Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0',
                    });
                    $("#fuel_type_select").prepend('<option value="" selected></option>').select2({
                        data:fuel_type_data,
                        placeholder: "Select Fuel Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#fuel_deduction_type_select").prepend('<option value="" selected></option>').select2({
                        data:fuel_deduction_type_data,
                        placeholder: "Select Fuel Deduction Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#card_request_type_select").prepend('<option value="" selected></option>').select2({
                        data:card_request_type_data,
                        placeholder: "Select Card Request Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $("#card_holder_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Card Holder Type",
                width:'100%',
                dropdownParent: $('#FuelCardHolderTypeSelectModal'),
            });
            $('#staff_select').prepend('<option value="" selected></option>').select2({
                placeholder: "Select Staff",
                width:'100%',
            });
            $('#rider_select').prepend('<option value="" selected></option>').select2({
                placeholder: "Select Rider",
                width:'100%',
            });
            $('#fleet_select').prepend('<option value="" selected></option>').select2({
                placeholder: "Select Fleet",
                width:'100%',
            });
            $("#card_request_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Card Request Type",
                width:'100%',
                dropdownParent: $('#FuelRequestModal'),
            });

            $("#fleet_vehicle_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Fleet Vehicle Type",
                width:'100%',
                dropdownParent: $('#FuelCardHolderTypeSelectModal'),
            });
            $("#fuel_deduction_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Fuel Deduction Type",
                width:'100%',
                dropdownParent: $('#ApproveModal'),

            });

            $("#fuel_type").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Fuel Type",
                width:'100%',
                dropdownParent: $('#ApproveModal'),
            });

            $('#amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
            });

            $('#FuelCardHolderTypeSelectModal #card_holder_type_value_select_btn').on('click',function () {
                    var card_holder_type = $('#card_holder_type').val();
                    var fleet_vehicle_type = $('#fleet_vehicle_type').val();
                    if(card_holder_type == '')
                    {
                        toastr.error('Please Select Card Holder Type', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        return;
                    }
                    if(card_holder_type == 3 && fleet_vehicle_type == '') {
                        toastr.error('Please Select Fleet Vehicle Type', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        return;
                    }

                    $.ajax({
                        url: '{!! route('admin.user_management.fuel_management.create') !!}',
                        method: 'get',
                        data: {
                            'card_holder_type': card_holder_type,
                            'fleet_vehicle_type' : fleet_vehicle_type,
                        }
                    })
                        .done(function (response) {
                            if (response.status == 1) {
                                $('#FuelCardHolderTypeSelectModal').modal('hide');
                                html = '';
                                html += '<select name="card_holder" id="card_holder" class="select2 form-control">';
                                $.each(response.data, function(index, values) {
                                    html += `<option value="${values.id}" > ${values.name} </option>`;
                                });
                                html += '</select>';

                                $('#FuelRequestModal .modal-body #card_holder_select_container').html(html);


                                $("#card_holder").prepend('<option value="" selected></option>').select2({
                                    placeholder: "Select Card Holder",
                                    width:'100%',
                                    allowClear: true,
                                    dropdownParent: $('#FuelRequestModal'),
                                });
                                $('#card_request_type').val('');
                                $('#card_request_type').trigger('change');
                                $('#FuelRequestModal').modal('show');
                            } else {
                                toastr.error(response.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });

            });

            $('#card_holder_type').on('change',function () {
                if($(this).val() == 3)
                {
                    $('#fleet_vehicle_type_container').show(1000);
                }
                else{
                    $('#fleet_vehicle_type_container').hide(1000);
                    $('#fleet_vehicle_type').val('')
                    $('#fleet_vehicle_type').trigger('change');
                }
            });

            $('#card_request_type').on('change',function () {
                $('#FuelRequestModal .modal-body input[type=text]').each(function (i,v) {
                    $(v).val('');
                });

                $('#FuelRequestModal .modal-body input[type=hidden]').each(function (i,v) {
                    $(v).val('');
                });

                $('#FuelRequestModal .modal-body .all_request_type .select2').each(function (i,v) {
                    $(v).val('');
                    $(v).trigger('change');
                });

                $('#FuelRequestModal table tbody').html('');

                if($(this).val() == '')
                {
                    $('.all_request_type').hide();
                }
                else{
                    if($(this).val() == 1)
                    {
                        $('.all_request_type').hide();
                        $('.new_request_type').show(1000);
                    }
                    else if($(this).val() == 2)
                    {
                        $('.all_request_type').hide();
                        $('.reassign_request_type').show(1000);
                    }
                    else if($(this).val() == 3)
                    {
                        $('.all_request_type').hide();
                        $('.block_request_type').show(1000);
                    }
                    else if($(this).val() == 4)
                    {
                        $('.all_request_type').hide();
                        $('.unblock_request_type').show(1000);
                    }
                }
            });

            $('#search_card_number_btn').on('click',function () {
                var card_number = $('#search_card_number_input').val();
                var card_holder_type = $('#card_holder_type').val();
                var fleet_vehicle_type = $('#fleet_vehicle_type').val();
                var request_type = $('#card_request_type').val();
                if(fleet_vehicle_type == '')
                {
                    fleet_vehicle_type = null;
                }
                if(card_number == '')
                {
                    toastr.error('Please Enter a Valid Card Number', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return;
                }
                $.ajax({
                    url: '{!! route('admin.user_management.fuel_management.search_by_card') !!}',
                    method: 'get',
                    data: {
                        'card_number': card_number,
                        'card_holder_type': card_holder_type,
                        'fleet_vehicle_type' : fleet_vehicle_type,
                        'request_type' : request_type,
                    }
                })
                    .done(function (response) {
                        if (response.status == 1) {
                            html = `<tr>
                                        <td>${response.data.card_number}</td>
                                        <td>${response.data.card_holder}</td>
                                        <td>${response.data.card_holder_type}</td>
                                        <td>${response.data.amount}`
                                if(response.data.fuel_deduction_type_id == 1)
                                {
                                    html += ' Rs';
                                }
                                else{
                                    html += ' Liter';
                                }

                                html +=`</td>
                                        <td>${response.data.fuel_type}</td>
                                        <td>${response.data.fuel_deduction_type}</td>
                                        <td>${response.data.requested_by}</td>
                                        <td>${response.data.approved_by}</td>
                                        <td>${response.data.approved_at}</td>
                                    </tr>`;
                                $('#FuelRequestModal #card_request_id').val(response.data.id);
                                $('#FuelRequestModal table tbody').html(html);
                        } else {
                            $('#FuelRequestModal #card_request_id').val('');
                            $('#FuelRequestModal table tbody').html('');
                            toastr.error(response.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
            });

            $('#fuel_request_form').on('submit',function (e) {
                var card_request_type = $('#card_request_type').val();
                var card_request_id = $('#card_request_id').val();
                var card_holder = $('#card_holder').val();

                console.log()
                if(card_request_type == '')
                {
                    toastr.error('Please Select Card Request Type', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    e.preventDefault();
                    return;
                }

                if(card_request_type != 1)
                {
                    if(card_request_id == '') {
                        toastr.error('Please Enter a Valid Card Number', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        e.preventDefault();
                        return;
                    }
                }

                if(card_request_type == 1 || card_request_type == 2)
                {
                    if(card_holder == '') {
                        toastr.error('Please Select Card Holder', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        e.preventDefault();
                        return;
                    }
                }

            })

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var request_id = $(this).parents('tr').attr('id');
                if($(this).hasClass('approve_new'))
                {
                    url = "{!! route('admin.user_management.fuel_management.approve') !!}";
                    heading = 'Approve Request';
                    $("#fuel_deduction_type").val('');
                    $("#fuel_deduction_type").trigger('change');

                    $("#fuel_type").val('');
                    $("#fuel_type").trigger('change');

                    $('#amount').val('');

                    $('#request_id').val(request_id);
                    $('#ApproveModal #fuel_request_approve_form').attr('action',url);
                    $('#ApproveModal .modal-title').html(heading);
                    $('#ApproveModal').modal('show');
                }

                if($(this).hasClass('approve'))
                {
                    var text = $(this).attr('data-msg');
                    swal({
                        text: text,
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
                        if (confirm) {
                            $('#fuel_request_approve_form #request_id').val(request_id);
                            $('#fuel_request_approve_form').submit();
                        }
                    });
                }

                if($(this).hasClass('edit'))
                {
                    url = "{!! route('admin.user_management.fuel_management.edit') !!}";
                    heading = 'Edit Request';
                    $("#fuel_deduction_type").val('');
                    $("#fuel_deduction_type").trigger('change');

                    $("#fuel_type").val('');
                    $("#fuel_type").trigger('change');

                    $('#amount').val('');

                    $('#request_id').val(request_id);
                    $('#ApproveModal #fuel_request_approve_form').attr('action',url);
                    $('#ApproveModal .modal-title').html(heading);
                    $('#ApproveModal').modal('show');
                }
            });

            $('#search_form').bind('submit', function (e) {
                e.preventDefault();
                table.draw();
            });
        });

    </script>
@endsection