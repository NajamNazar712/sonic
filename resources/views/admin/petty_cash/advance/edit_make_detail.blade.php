@extends('admin.layout.master')
@section('title','Edit Advance Petty Cash Statement')

@section('content')
    <h1 class="mb-1">
         Edit Advance Petty Cash Statement # {{$petty_statement->id}}
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="edit_statement_form" action="{{route('admin.petty_cash.advance.statements.edit_make_detail_submit')}}" method="post" enctype="multipart/form-data">
                    {{--@method('PUT')--}}
                    @csrf
                    <input type="hidden" name="selected_rows" id="selected_rows">
                    <input type="hidden" name="petty_statement_id" id="petty_statement_id" value="{{$petty_statement->id}}">
                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_sdn" id="select_statement_sdn" disabled class="form-control select2" data-rule-required="true" data-msg-required="SDN is required">
                                    @foreach($sdns as $sdn)
                                        <option value="{{$sdn->id}}">{{str_pad($sdn->id, 6, '0', STR_PAD_LEFT)}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col ">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>
                                <input type="text" name="select_statement_date" disabled data-value="{{$petty_statement->from}}" class="form-control pickadate bg-primary border-primary white rounded-right" id="select_statement_date" placeholder="Select Date" data-rule-required="true" data-msg-required="Date is required">
                            </div>
                        </div>
                        <div class="col">
                            <fieldset class="form-group">
                                <input type="text" class="form-control reference_no" disabled name="reference_no" id="reference_no" placeholder="Statement Reference No." data-rule-required="true" data-msg-required="Statement Reference No. is required" value="{{$petty_statement->reference_no}}">
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_zone" disabled id="select_statement_zone" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                                    @foreach($zones as $zone)
                                        <option value="{{$zone->id}}">{{$zone->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_hub" disabled id="select_statement_hub" class="form-control select2" data-rule-required="true" data-msg-required="Hub is required">
                                </select>
                            </fieldset>
                        </div>

                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_station_manager" disabled id="select_statement_station_manager" class="form-control select2" data-rule-required="true" data-msg-required="Station Manager is required">
                                    @foreach($operation_managers as $manager)
                                        <option value="{{$manager->id}}">{{$manager->name}} @if($manager->trax_id != '')({{$manager->trax_id}}) @endif</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col">
                            <b class="total_amount_span"> Total Amount : <span id="statements_total_amount">{{$petty_statement->total_amount}}</span></b>
                        </div>
                    </div>
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">City / Location</th>
                            <th class="border-primary border-darken-1">Account Head</th>
                            <th class="border-primary border-darken-1">Account Title</th>
                            <th class="border-primary border-darken-1">Details of Expense</th>
                            <th class="border-primary border-darken-1"> Amount </th>
                            <th class="border-primary border-darken-1"> Employee Id </th>
                            <th class="border-primary border-darken-1"> Name </th>
                            <th class="border-primary border-darken-1"> Designation </th>
                            <th class="border-primary border-darken-1">Reference No.</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1"> DNCC/RNCC </th>
                            <th class="border-primary border-darken-1"> Delivered Shipments </th>
                            <th class="border-primary border-darken-1">Reference Documents</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1"></th>

                        </tr>
                        </thead>
                    </table>
                    @if($petty_statement->status != 6)
                        <div class="row justify-content-center">
                            <div class="">
                                <button id="statement_submit" type="submit"  class="btn btn-primary btn-block" disabled>Update Details</button>
                            </div>
                            @if((session('role_id') == 1) || in_array(173, session('permissions')) || in_array(190, session('permissions')) || in_array(191, session('permissions')))
                                @if((session('role_id') == 1) || ($petty_statement->status == 0 && session('department_id') == 6) || ($petty_statement->status == 1 && (session('department_id') == 6)) || ($petty_statement->status == 2 && session('department_id') == 4))
{{--                                    <div class="ml-1">--}}
{{--                                        <button id="statement_approve" type="button"  class="btn btn-primary btn-block">Approve</button>--}}
{{--                                    </div>--}}
{{--                                    <div class="ml-1">--}}
{{--                                        <button id="statement_reject" type="button"  class="btn btn-danger btn-block">Reject</button>--}}
{{--                                    </div>--}}
                                @endif
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AmountLogModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AmountLogModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Petty Cash Statement Detail Amount</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center" id="amount_log_table">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
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
    <style type="text/css">
        /*.custom-col-width{*/
        /*min-width: 100px;*/
        /*}*/
        /*th.expense_amount, th.reference_no{*/
        /*width: 80px;*/
        /*}*/
        /*.custom-hub-col-width{*/
        /*min-width: 80px;*/
        /*}*/
        /*.date-col-width{*/
        /*min-width: 190px;*/
        /*}*/
        /*.date-col-width{*/
        /*min-width: 200px;*/
        /*}*/
        .total_amount_span{
            font-size: 24px;
            color: #64a0d2;
        }

        .custom-col-width{
            min-width: 150px;
        }
        div.picker .picker__holder{
            width: 250px;
        }
        .doe-col-width{
            min-width: 250px;
        }
        .date-col-width{
            min-width: 100px;
        }

        textarea {
            resize: both;
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            let first = false;
            let dncc_data = "";
            let cities_data = "";

            $('#select_statement_sdn').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select SDN No.',
                width:'100%',
            }).bind('change',function(){
                var sdn = $(this).val();
                if(first) {
                    $.ajax({
                        url: '{!! route('admin.petty_cash.make.dncc') !!}',
                        type: 'POST',
                        data: {
                            'sdn_id': sdn,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status) {
                            dncc_data = "";
                            $.each(data.data, function (i, v) {
                                dncc_data += "<option value='" + v.id + "' data-count='" + v.count + "'>" + v.text + "</option>";
                            });

                            $(".delivered_shipment_input").val('');
                            $('.dncc_select').each(function (elm) {
                                $(this).empty().trigger('change');
                                $(this).html(dncc_data);
                                $(this).val('').trigger('change');
                            });
                        }
                    });
                }
            });

            $('#select_statement_sdn').val('{!! $petty_statement->sdn_id!!}').trigger('change');

            $('#select_statement_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Zone',
                width:'100%',
            }).bind('change',function(){
                var zone = $(this).val();
                if(first) {
                    $.ajax({
                        url: '{!! route('admin.petty_cash.make.hubs') !!}',
                        type: 'POST',
                        data: {
                            'zone': zone,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status) {
                            var hub = $('#select_statement_hub');
                            hub.empty().trigger('change');
                            $.each(data.data, function (key, value) {
                                var newOption = new Option(value.name, value.id, false, false);
                                hub.append(newOption);
                            });
                            hub.val('').trigger('change');
                        }
                    });
                }
            });

            $('#select_statement_zone').val('{!! $petty_statement->zone_id!!}').trigger('change');

            var hub_array = @json($hub_array);
            var hub_data = $.map(hub_array[{!! $petty_statement->zone_id!!}], function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
            });


            $('#select_statement_hub').prepend('<option value="" selected="selected"></option>').select2({
                data: hub_data,
                placeholder:'Select Hub',
                width:'100%',
            }).bind('change',function(){
                var hub = $(this).val();
                if(first) {
                    $.ajax({
                        url: '{!! route('admin.petty_cash.make.cities') !!}',
                        type: 'POST',
                        data: {
                            'hub': hub,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status) {
                            cities_data = $.map(data.data, function (obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;
                                return obj;
                            });

                            $('.city_select').each(function (elm) {
                                $(this).empty().trigger('change');
                                $(this).prepend('<option value="" selected="selected"></option>').select2({
                                    data: cities_data,
                                    placeholder: "Select a Cityy"
                                });
                                $(this).val('').trigger('change');
                            });
                        }
                    });
                }
            });

            $('#select_statement_hub').val('{!! $petty_statement->hub_id!!}').trigger('change');

            $('#select_statement_station_manager').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Station Manager',
                width:'100%',
            });

            $('#select_statement_station_manager').val('{!! $petty_statement->station_manager_id!!}').trigger('change');

            $('#select_statement_date').pickadate({
                firstDay: 1,
                today: '',
                clear: '',
                close: '',
                weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
                showMonthsShort: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
            });

            var selected_rows = [];
            var rows_count = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    title: 'Edit Details',
                    className: 'btn btn-primary edit_btn',
                    text: '<i class="la la-plus"></i> Edit Details',
                    action:function (e) {
                        edit_finance();
                        $('#statement_submit').attr('disabled', false);
                        table.button('.edit_btn').disable();

                    }
                },'reset'],

                autoWidth: false,
                scrollX: true, scrollY:'500px',
                ajax: '{{ route('admin.petty_cash.advance.statements.edit_make_detail.list',['id'=>$petty_statement->id]) }}',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: false,
                rowId: 'statement_detail_id',
                paging:false,
                ordering: false,
                autoWidth: true,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data:'city_name' ,name: 'c.name', class: 'align-middle city_name custom-col-width form-group'},
                    {data:'account_head' ,name: 'account_head', class: 'align-middle account_head custom-col-width form-group'},
                    {data:'account_title' ,name: 'account_title', class: 'align-middle account_title custom-col-width form-group'},
                    {data:'expense_details' ,name: 'petty_cash_statement_details.expense_details', class: 'align-middle details_of_expense form-group doe-col-width'},
                    {data:'amount' ,name: 'petty_cash_statement_details.amount', class: 'align-middle expense_amount custom-col-width form-group'},
                    {data:'employee_trax_id' ,name: 'a.trax_id', class: 'align-middle employee_trax_id custom-col-width form-group'},
                    {data:'employee_name' ,name: 'petty_cash_statement_details.employee_name', class: 'align-middle custom-col-width employee_name form-group'},
                    {data:'employee_designation' ,name: 'petty_cash_statement_details.employee_designation', class: 'align-middle employee_designation custom-col-width form-group'},
                    {data:'reference_no' ,name: 'petty_cash_statement_details.reference_no', class: 'align-middle custom-col-width reference_no form-group'},
                    {data:'remarks' ,name: 'petty_cash_statement_details.remarks', class: 'align-middle custom-col-width remarks'},
                    {data:'dncc' ,name: 'petty_cash_statement_details.dncc_id', class: 'align-middle dncc custom-col-width form-group'},
                    {data:'delivered_shipments' ,name: 'petty_cash_statement_details.delivered_shipments', class: 'align-middle delivered_shipments custom-col-width form-group'},
                    {data:'reference_document' ,name: 'reference_document', class: 'align-middle reference_document custom-col-width form-group'},
                    {data:'status' ,name: 'petty_cash_statement_details.status', class: 'align-middle status'},
                    {data:'action' ,name: 'action', class: 'align-middle action'}
                ],

                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                drawCallback: function (settings) {
                    var this_table = this;

                    $(".head_select").select2({
                        placeholder: "Select Account Head",
                        width:'100%'
                    }).bind('change', function() {
                        this_table.api().table().columns.adjust();
                    });

                    $(".title_select").select2({
                        placeholder: "Select Account Title",
                        width:'100%'
                    }).bind('change', function() {
                        this_table.api().table().columns.adjust();
                    });

                    $(".city_select").select2({
                        placeholder: "Select City",
                        width:'100%'
                    }).bind('change', function() {
                        this_table.api().table().columns.adjust();
                    });

                    $(".dncc_select").select2({
                        placeholder: "Select DNCC/RNCC",
                        width:'100%',
                        allowClear:true,
                    }).bind('change', function() {
                        this_table.api().table().columns.adjust();
                        let count = $(this).find(':selected').attr('data-count');
                        let index = $(this).attr('name');
                        index = index.substring(5, index.length-1);
                        $('input[name="delivered_shipment_count['+index+']"]').val(count);
                    });

                    $(".employee_select").select2({
                        placeholder: "Select Employee Id",
                        width:'100%'
                    }).bind('change', function() {
                        this_table.api().table().columns.adjust();
                    });


                    $(".amount_input").inputmask({
                        'alias': 'decimal',
                        'allowMinus': false,
                        'allowPlus': false,
                        'rightAlign': false,
                        'digits': 2,
                        'min': 0.00,
                        'max': 6000000.00
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

                        if ($(header).is('.serial_number') || $(header).is('.status') || $(header).is('.date') || $(header).is('.hub_name') || $(header).is('.account_head') || $(header).is('.account_title') || $(header).is('.action') || $(header).is('.reference_document')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change keypress', function() {
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

            $('#edit_statement_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('td.form-group'));
                },
                submitHandler: function(form) {


                    $('#selected_rows').val(selected_rows);
                    form.submit();
                }
            });
            var result;
            $.validator.addMethod("reference_no",
                function(value, element) {
                    if(value > 3) {

                        $.ajax({
                            type: "POST",
                            url: '{!! route('admin.petty_cash.make.reference') !!}', // script to validate in server side
                            data: {reference_id: value,'_token': '{!! csrf_token() !!}'},
                            success: function (data) {
                                if(data === 'true'){
                                    result = false;
                                }else{
                                    result = true;
                                }
                            }
                        });
                        return result;
                    }
                },
                "Statement Reference Number already exists."
            );




            $('body').on('select2:select','.account_head .head_select',function () {
                var rowid = parseInt($(this).parents('tr').attr('id'));
                var selected_head = $(this).find(':selected');
                var head = parseInt(selected_head.val());
                var title = selected_head.closest('td').next('td').find('.title_select');
                $.ajax({
                    url:'{!! route('admin.petty_cash.make.titles') !!}',
                    type:'POST',
                    data: {
                        'account_head':head,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status){
                        title.empty().trigger('change');
                        $.each(data.titles,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            title.append(newOption).trigger('change');
                        });
                        title.val('').trigger('change');
                    }
                });
            });

            $('#edit_statement_form').on('keypress',function (e) {
                if(e.which == 13) {
                    e.preventDefault();
                }
            });

            $('body').on('click','.action button.approve', function () {
                var current = $(this);
                var id = parseInt($(this).parents('tr').attr('id'));
                var status = parseInt($(this).parents('tr').attr('status'));
                if(status == 0 || status == 1) {
                    swal({
                        text: 'Are you sure, you want to approve this statement?',
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
                            $.ajax({
                                url: '{!! route('admin.petty_cash.advance.statements.detail_edit.approve') !!}',
                                method: 'POST',
                                data: {
                                    'detail_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status) {
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    current.parents('td').prev('td').text('Approved');
                                    current.parents('tr').attr('status',2);
                                    location.reload();
                                }
                                else{
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            });
                        }
                    });

                }else{
                    var error = 'Current Petty Cash Statement Detail already Approved!';
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
            });
            $('body').on('click','.action button.reject', function () {
                var current = $(this);
                var id = parseInt($(this).parents('tr').attr('id'));
                var status = parseInt($(this).parents('tr').attr('status'));
                if(status == 0 || status == 2) {
                    swal({
                        text: 'Are you sure, you want to reject this statement?',
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
                            $.ajax({
                                url: '{!! route('admin.petty_cash.advance.statements.detail_edit.reject') !!}',
                                method: 'POST',
                                data: {
                                    'detail_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status) {
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    current.parents('td').prev('td').text('Rejected');
                                    current.parents('tr').attr('status',1);
                                    current.parents('td').html('');
                                    location.reload();
                                }
                                else{
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            });
                        }
                    });

                }
                else{
                    var error = 'Current Petty Cash Statement Detail already rejected!';
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
            });

            function edit_ops(){
                table.rows().nodes().each(function(index) {
                    var row = table.row(index);
                    var id = parseInt(row.id());
                    if($(row.node()).attr('status') == 0 || $(row.node()).attr('status') == 2){
                        $(row.node()).find('td.details_of_expense textarea').attr('disabled',false);
                        $(row.node()).find('td.expense_amount input').attr('disabled',false);
                        $(row.node()).find('td.reference_no input').attr('disabled',false);
                        $(row.node()).find('td.remarks textarea').attr('disabled',false);
                        $(row.node()).find('td.reference_document input').attr('disabled',false);
                        var index = $.inArray(id, selected_rows);
                        if(index === -1){
                            selected_rows.push(id);
                        }
                    }

                });
            }

            function edit_finance() {
                $("#select_statement_sdn").attr('disabled',false);
                $(".dncc_select").attr('disabled',false);
                table.rows().nodes().each(function(index) {
                    var row = table.row(index);
                    var id = parseInt(row.id());
                    if($(row.node()).attr('status') == 0 || $(row.node()).attr('status') == 2){
                        $(row.node()).find('td.account_head select').attr('disabled',false);
                        $(row.node()).find('td.account_title select').attr('disabled',false);
                        $(row.node()).find('td.zone select').attr('disabled',false);
                        $(row.node()).find('td.details_of_expense textarea').attr('disabled',false);
                        $(row.node()).find('td.expense_amount input').attr('disabled',false);
                        $(row.node()).find('td.reference_no input').attr('disabled',false);
                        $(row.node()).find('td.remarks textarea').attr('disabled',false);
                        $(row.node()).find('td.reference_document input').attr('disabled',false);
                        var index = $.inArray(id, selected_rows);
                        if(index === -1){
                            selected_rows.push(id);
                        }
                    }

                });
            }

            $('body').on('change','td.expense_amount input', function () {
                var total_amount = 0;
                table.rows().nodes().each(function(index) {
                    var row = table.row(index);
                    if($(row.node()).find('td.expense_amount input').val() != ''){

                        total_amount += parseInt($(row.node()).find('td.expense_amount input').val());
                    }


                });
                //$('#statements_total_amount').text(total_amount);
            });
            $('#statement_approve').on('click', function (e) {
                e.preventDefault();
                var current = $(this);
                var id = '{{$petty_statement->id}}';
                var status = parseInt({{$petty_statement->status}});
                if(status == 0 || status == 1 || status == 2) {
                    $('#statement_approve').attr('disabled', true);
                    $.ajax({
                        url: '{!! route('admin.petty_cash.statements.edit.approve') !!}',
                        method: 'POST',
                        data: {
                            'detail_id': id,
                            'approve_all': 1,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status) {
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            setTimeout(function() {
                                window.location = '{{ route('admin.petty_cash.statements.edit',['id' => $petty_statement->id]) }}';
                            }, 2500);
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }else{
                    var error = 'Current Petty Cash Statement Detail already Approved!';
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
            });



            $('#datatable').on('click', 'td .amount_log', function(){
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $.ajax({
                        url: '{!! route('admin.petty_cash.statements.view.amount') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if(data.status){
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }else{
                            var log_table = '';
                            if(data.amount){
                                log_table += '<table class="table table-sm datatable">';
                                log_table += '<thead>';
                                log_table += '<tr role="row">';
                                log_table += '<th><strong>Actual Amount</strong></th>';
                                log_table += '<th><strong>Station Amount</strong></th>';
                                log_table += '<th><strong>Operation Amount</strong></th>';
                                log_table += '<th><strong>Finance Amount</strong></th>';

                                log_table += '</tr>';
                                log_table += '</thead>';
                                log_table += '<tbody>';

                                log_table += '<tr>';
                                log_table += '<td>' + data.amount.actual + '</td>';
                                log_table += '<td>' + data.amount.station + '</td>';
                                log_table += '<td>' + data.amount.ope + '</td>';
                                log_table += '<td>' + data.amount.finance + '</td>';
                                log_table += '</tr>';

                                log_table += '</tbody>';
                                log_table += '</table>';
                            }


                            $('#amount_log_table').html(log_table);
                            $('#AmountLogModal').modal('show');
                        }
                    });
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

            $('#statement_reject').on('click',  function (e) {
                e.preventDefault();
                var id = '{{$petty_statement->id}}';
                var status = parseInt({{$petty_statement->status}});
                if(status == 0 || status == 1 || status == 2) {
                    $('#statement_reject').attr('disabled', true);

                    $.ajax({
                        url: '{!! route('admin.petty_cash.statements.reject_all') !!}',
                        method: 'POST',
                        data: {
                            'statement_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 0) {
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            setTimeout(function() {
                                window.location = '{{ route('admin.petty_cash.statements.index') }}';
                            }, 2500);
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }else{
                    var error = 'Current Petty Cash Statement Detail already updated!';
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
            });

            first = true;
        });
    </script>
@endsection