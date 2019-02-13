@extends('admin.layout.master')
@section('title','Edit Petty Cash Statement')

@section('content')
    <h1 class="mb-1">
        Edit Petty Cash Statement # {{$petty_statement_details->id}}
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="edit_statement_form" action="{{route('admin.petty_cash.statements.edit.submit')}}" method="post">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="selected_rows" id="selected_rows">
                    <input type="hidden" name="petty_statement_id" id="petty_statement_id" value="{{$petty_statement_details->id}}">
                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_hub" id="select_statement_hub" class="form-control select2" disabled data-rule-required="true" data-msg-required="Hub is required">
                                    @foreach($hubs as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col ">
                            <div class="form-group input-group ml-1">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>

                                <input type="text" name="select_date_from" disabled class="form-control pickadate bg-primary border-primary white rounded-right" id="select_date_from" placeholder="Date (From)" data-rule-required="true" data-msg-required="Date (From) is required" data-value="{{$petty_statement_details->from}}">
                            </div>
                        </div>
                        <div class="col ">
                            <div class="form-group input-group ml-1">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>

                                <input type="text" name="select_date_to" disabled class="form-control pickadate bg-primary border-primary white rounded-right" id="select_date_to" placeholder="Date (To)" data-rule-required="true" data-msg-required="Date (To) is required" data-value="{{$petty_statement_details->to}}">
                            </div>
                        </div>
                        <div class="col">
                            <fieldset class="form-group">
                                <input type="text" class="form-control reference_no" disabled name="reference_no" id="reference_no" placeholder="Statement Reference No." data-rule-required="true" data-msg-required="Statement Reference No. is required" value="{{$petty_statement_details->reference_no}}">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <b class="total_amount_span"> Total Amount : <span id="statements_total_amount">{{$petty_statement_details->total_amount}}</span></b>
                        </div>
                    </div>
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Account Head</th>
                            <th class="border-primary border-darken-1">Account Title</th>
                            <th class="border-primary border-darken-1">City / Location</th>
                            <th class="border-primary border-darken-1">Date</th>
                            <th class="border-primary border-darken-1">Details of Expense</th>
                            <th class="border-primary border-darken-1"> Amount </th>
                            <th class="border-primary border-darken-1">Reference No.</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1"></th>

                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center">
                        <div class="">
                            <button id="statement_submit" type="submit"  class="btn btn-primary btn-block" disabled>Update Details</button>
                        </div>
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
        .custom-col-width{
            min-width: 150px;
        }
        .date-col-width{
            min-width: 200px;
        }
        .total_amount_span{
            font-size: 24px;
            color: #64a0d2;
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

    <script type="text/javascript">
        $(document).ready(function () {
            $('.reference_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
            });
            $('#select_statement_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#select_statement_hub').val('{!! $petty_statement_details->hub_id!!}').trigger('change');
            var old_date_limit = '{{ Carbon\Carbon::now()->subDays(2)->toDateString() }}';
            var future_date_limit = '{{ Carbon\Carbon::now()->addDays(28)->toDateString() }}';

            $('#edit_statement_form #select_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                min: new Date(old_date_limit),
                max: new Date(future_date_limit),
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    // if (context.select) {
                    //     $('#edit_statement_form #select_date_to').pickadate('picker').set('min', $('#edit_statement_form #select_date_from').pickadate('picker').get('select'));
                    // }
                }
            });
            $('#edit_statement_form #select_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                min: new Date(old_date_limit),
                max: new Date(future_date_limit),
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    // if (context.select) {
                    //     $('#edit_statement_form #select_date_from').pickadate('picker').set('max', $('#edit_statement_form #select_date_to').pickadate('picker').get('select'));
                    // }
                }
            });

            var selected_rows = [];
            var rows_count = 0;
            var table = $('#datatable').DataTable({
                @if(($petty_statement_details->status == 0 && session('department_id') == 6) || ($petty_statement_details->status == 1 && session('department_id') == 6))
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Edit Details',
                    className: 'btn btn-primary',
                    text: '<i class="la la-plus"></i> Edit Details',
                    action:function (e) {
                        edit_ops();
                        $('#statement_submit').attr('disabled', false);
                    }
                }],
                @elseif(session('role_id') == 1 || ($petty_statement_details->status == 2 && (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14)))
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Edit Details',
                    className: 'btn btn-primary',
                    text: '<i class="la la-plus"></i> Edit Details',
                    action:function (e) {
                        edit_finance();
                        $('#statement_submit').attr('disabled', false);

                    }
                }],
                @else
                dom: 'ltipr',
                @endif
                autoWidth: false,
                scrollX: true, scrollY:'200px',
                ajax: '{{ route('admin.petty_cash.statements.edit.list',['id'=>$petty_statement_details->id]) }}',
                processing: true,
                serverSide: false,
                rowId: 'statement_detail_id',
                paging:false,
                ordering: false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data:'account_head' ,name: 'account_head', class: 'align-middle account_head custom-col-width form-group'},
                    {data:'account_title' ,name: 'account_title', class: 'align-middle account_title custom-col-width form-group'},
                    {data:'hub_name' ,name: 'h.name', class: 'align-middle hub_name custom-col-width form-group'},
                    {data:'date' ,name: 'date', class: 'align-middle date form-group'},
                    {data:'expense_details' ,name: 'petty_cash_statement_details.expense_details', class: 'align-middle details_of_expense form-group'},
                    {data:'amount' ,name: 'petty_cash_statement_details.amount', class: 'align-middle expense_amount form-group'},
                    {data:'reference_no' ,name: 'petty_cash_statement_details.reference_no', class: 'align-middle reference_no form-group'},
                    {data:'remarks' ,name: 'petty_cash_statement_details.remarks', class: 'align-middle remarks'},
                    {data:'status' ,name: 'petty_cash_statement_details.status', class: 'align-middle status'},
                    {data:'action' ,name: 'action', class: 'align-middle action'}
                ],

                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                drawCallback: function (settings) {

                    $(".head_select").select2({
                        placeholder: "Select Account Head",
                        width:'100%'
                    });
                    $(".title_select").select2({
                        placeholder: "Select Account Title",
                        width:'100%'
                    });
                    $(".hub_select").select2({
                        placeholder: "Select Hub",
                        width:'100%'
                    });
                    // $(".statusDrop").prepend('<option value="" selected="selected"></option>').select2({
                    //     placeholder: "Select a Status",
                    //     width:'100%'
                    // });
                    // var api = new $.fn.dataTable.Api( settings );
                    // var data = api.rows( {page:'current'} ).data();
                    // $.each(data,function (key,value) {
                    //     if(shipment_status.length !== 0){
                    //         $('select[name="status_drop['+value.shId+']"]').val(shipment_status[value.shId]).trigger('change');
                    //     }
                    //     if(shipment_reason.length !== 0){
                    //         $('select[name="reason_drop['+value.shId+']"]').val(shipment_reason[value.shId]).trigger('change');
                    //     }
                    // });
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    // var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                    //     '<option value="0">Pending</option>' +
                    //     '<option value="1">Rejected</option>' +
                    //     '<option value="2">Approved</option>' +
                    //     '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.status') || $(header).is('.date') || $(header).is('.hub_name') || $(header).is('.account_head') || $(header).is('.account_title') || $(header).is('.action')) {
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
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    // table.rows().nodes().each(function(index) {
                    //     var row = table.row(index);
                    //     console.log(row.id())
                    //     var id = parseInt(row.id());
                    //         // selected_rows.push(parseInt(row.id()));
                    //     console.log(id)
                    // });
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


            function add_row() {
                rows_count++;
                selected_rows.push(rows_count);
                var heads_select = '<select class="form-control select2 head_select" name="head['+rows_count+']" data-rule-required="true" data-msg-required="Account Head is required"></select>';
                var titles_select = '<select class="form-control select2 title_select" name="title['+rows_count+']" data-rule-required="true" data-msg-required="Account Title is required"></select>';
                var hub_select = '<select class="form-control hub_select select2" name="hub['+rows_count+']" data-rule-required="true" data-msg-required="Hub is required"></select>';
                var date_input = '<div class="form-group input-group mb-0"><div class="input-group-prepend"><span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left"><span class="la la-calendar-o"></span></span></div><input type="text" name="date['+rows_count+']" class="form-control pickadate-short-string bg-primary border-primary white rounded-right" placeholder="Date" data-rule-required="true" data-msg-required="Date (From) is required"></div>';

                var expense_detail_input = '<input class="form-control" name="expense['+rows_count+']" placeholder="Enter Expense Details" data-rule-required="true" data-msg-required="Expense Detail is required">';
                var amount_input = '<input class="form-control amount" name="amount['+rows_count+']" placeholder="Enter Amount">';
                var reference_input = '<input class="form-control reference_row" name="reference['+rows_count+']" placeholder="Enter Reference No" data-rule-required="true" data-msg-required="Amount is required">';
                var remarks_input = '<input class="form-control" name="remarks['+rows_count+']" placeholder="Enter Remarks">';
                var heads = $.map({!! $heads !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                });
                var hubs_select = $.map({!! $cities !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                });

                table.row.add([0, heads_select,titles_select,hub_select,date_input,expense_detail_input,amount_input,reference_input,remarks_input]).node().id = rows_count;
                table.draw(true);
                $('select[name="head['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data:heads,
                    placeholder:'Select Account Head',
                    allowClear:true
                });
                $('select[name="title['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    placeholder:'Select Account Title',
                    allowClear:true
                });
                $('select[name="hub['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data: hubs_select,
                    placeholder:'Select Hub',
                    allowClear:true
                });
                $('.reference_row').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                });
                $('input[name="date['+rows_count+']"]').pickadate({
                    firstDay: 1,
                    clear: '',
                    weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
                    showMonthsShort: true,
                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                    hiddenSuffix: '_formatted',
                    onSet: function(context) {

                    }
                });

                $('.amount').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                    'min': 0.00,
                    'max': 1000000.00
                });

            }


            $('body').on('select2:select','.account_head .head_select',function () {
                var rowid = parseInt($(this).parents('tr').attr('id'));
                var selected_head = $(this).find(':selected');
                var head = parseInt(selected_head.val());
                var title = selected_head.closest('td').next('td').find('.title_select');
                // if(head == 1){
                //     selected_head.closest('td').next('td').next('td').find('.hub_select').prop("disabled",false);
                // }else{
                //     selected_head.closest('td').next('td').next('td').find('.hub_select').prop("disabled",true);
                // }
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
                    $.ajax({
                        url: '{!! route('admin.petty_cash.statements.edit.approve') !!}',
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
            $('body').on('click','.action button.reject', function () {
                var current = $(this);
                var id = parseInt($(this).parents('tr').attr('id'));
                var status = parseInt($(this).parents('tr').attr('status'));
                if(status == 0 || status == 2) {
                    $.ajax({
                        url: '{!! route('admin.petty_cash.statements.edit.reject') !!}',
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
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
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
                        selected_rows.push(id);
                    }

                });
            }

            function edit_finance() {
                table.rows().nodes().each(function(index) {
                    var row = table.row(index);
                    var id = parseInt(row.id());
                    if($(row.node()).attr('status') == 0 || $(row.node()).attr('status') == 2){
                        $(row.node()).find('td.account_head select').attr('disabled',false);
                        $(row.node()).find('td.account_title select').attr('disabled',false);
                        $(row.node()).find('td.hub_name select').attr('disabled',false);
                        // if($(row.node()).find('td.account_head select').val() == 1){
                        // }
                        $(row.node()).find('td.details_of_expense textarea').attr('disabled',false);
                        $(row.node()).find('td.expense_amount input').attr('disabled',false);
                        $(row.node()).find('td.reference_no input').attr('disabled',false);
                        $(row.node()).find('td.remarks textarea').attr('disabled',false);
                        selected_rows.push(id);
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
                $('#statements_total_amount').text(total_amount);
            });
            {{--$('#reference_no').on('change',function () {--}}
            {{--var reference_handle = $(this);--}}
            {{--var reference = $(this).val();--}}
            {{--$.ajax({--}}
            {{--url: '{!! route('admin.petty_cash.make.reference') !!}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--'reference_id': reference,--}}
            {{--'_token': '{{ csrf_token() }}'--}}
            {{--}--}}
            {{--}).done(function (data) {--}}
            {{--if(data.status){--}}
            {{--reference_handle.val('');--}}
            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}--}}
            {{--})--}}
            {{--});--}}
        });
    </script>
@endsection