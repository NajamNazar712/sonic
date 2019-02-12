@extends('admin.layout.master')
@section('title','Make Petty Cash Statement')

@section('content')
    <h1 class="mb-1">
        Make Petty Cash Statement
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="make_statement_form" action="{{route('admin.petty_cash.make.submit')}}" method="post">
                    @csrf
                    <input type="hidden" name="selected_rows" id="selected_rows">
                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_hub" id="select_statement_hub" class="form-control select2" data-rule-required="true" data-msg-required="Hub is required">
                                    @foreach($hub_cities as $city)
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

                                <input type="text" name="select_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="select_date_from" placeholder="Date (From)" data-rule-required="true" data-msg-required="Date (From) is required">
                            </div>
                        </div>
                        <div class="col ">
                            <div class="form-group input-group ml-1">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>

                                <input type="text" name="select_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="select_date_to" placeholder="Date (To)" data-rule-required="true" data-msg-required="Date (To) is required">
                            </div>
                        </div>
                        <div class="col">
                            <fieldset class="form-group">
                                <input type="text" class="form-control reference_no" name="reference_no" id="reference_no" placeholder="Statement Reference No." data-rule-required="true" data-msg-required="Statement Reference No. is required">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <b class="total_amount_span"> Total Amount : <span id="statements_total_amount">0</span></b>
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
                        <th class="border-primary border-darken-1"></th>

                    </tr>
                    </thead>
                </table>
                <div class="row justify-content-center">
                    <div class="">
                        <button id="statement_submit" type="submit"  class="btn btn-primary btn-block">Make Statement</button>
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
        th.expense_amount, th.reference_no{
            width: 80px;
        }
        .custom-hub-col-width{
            min-width: 100px;
        }
        .date-col-width{
            min-width: 200px;
        }
        td .picker__day {
            padding: 1px;
        }
        .date .picker td {
            border: transparent;
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
            // $('.reference_no').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false,
            //     'rightAlign': false,
            // });
            $('#select_statement_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });


            $('#make_statement_form #select_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var current_date = $('input[name="select_date_from_formatted"]').val();
                        var future = future_date(current_date);
                        $('#make_statement_form #select_date_to').pickadate('picker').set({'select': future},{muted: true});
                    }
                }
            });

            function future_date(from_date) {
                var today = new Date(from_date);
                // var tomorrow = new Date();
                today.setDate(today.getDate()+32);
                return today
            }

            $('#make_statement_form #select_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var current_date = $('input[name="select_date_to_formatted"]').val();
                        var past = past_date(current_date);
                        $('#make_statement_form #select_date_from').pickadate('picker').set({'select': past},{muted: true});
                    }
                }
            });
            function past_date(to_date) {
                var today = new Date(to_date);
                // var tomorrow = new Date();
                today.setDate(today.getDate()-32);
                return today;
            }
            var selected_rows = [];
            var rows_count = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add Row',
                    className: 'btn btn-primary',
                    text: '<i class="la la-plus"></i> Add Row',
                    action:function (e) {
                        add_row();
                    }
                }],
                autoWidth: false,
                scrollX: true, scrollY:'270px',
                ordering:false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'account_head', class: 'align-middle account_head custom-col-width form-group'},
                    {name: 'account_title', class: 'align-middle account_title custom-col-width form-group'},
                    {name: 'hub', class: 'align-middle hub custom-hub-col-width form-group'},
                    {name: 'date', class: 'align-middle date date-col-width form-group'},
                    {name: 'details_of_expense', class: 'align-middle details_of_expense form-group'},
                    {name: 'amount', class: 'align-middle expense_amount form-group'},
                    {name: 'reference_no', class: 'align-middle reference_no form-group'},
                    {name: 'remarks', class: 'align-middle remarks'},
                    {name: 'action', class: 'align-middle action'},
                ],

                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {

                    this.api().table().columns.adjust();
                }
            });

            $('#make_statement_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    
                    $('#selected_rows').val(selected_rows);
                    form.submit();
                }
            });
            var result;
            $.validator.addMethod("reference_no",
                function(value, element) {
                    result = false;
                    if(value.length > 3) {
                        $.ajax({
                            type: "POST",
                            async: false,
                            url: '{!! route('admin.petty_cash.make.reference') !!}', // script to validate in server side
                            data: {reference: value,'_token': '{!! csrf_token() !!}'},
                            success: function (data) {

                                if(data === "false"){
                                    result = true;
                                }else{
                                    result = false;
                                }

                            }
                        });

                       return result;
                    }
                },
                "Statement Reference Number already exists."
            );

            $('body').on('change','#datatable tr td.details_of_expense input,#datatable tr td.remarks input',function() {
                $(this).val($(this).val().trim());
            });

            function add_row() {
                rows_count++;
                selected_rows.push(rows_count);
                var heads_select = '<select class="form-control select2 head_select" name="head['+rows_count+']" data-rule-required="true" data-msg-required="Account Head is required"></select>';
                var titles_select = '<select class="form-control select2 title_select" name="title['+rows_count+']" data-rule-required="true" data-msg-required="Account Title is required"></select>';
                var hub_select = '<select class="form-control hub_select select2" name="hub['+rows_count+']" data-rule-required="true" data-msg-required="City is required"></select>';
                var date_input = '<div class="form-group input-group mb-0"><div class="input-group-prepend"><span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left"><span class="la la-calendar-o"></span></span></div><input type="text" name="date['+rows_count+']" class="form-control pickadate-short-string bg-primary border-primary white rounded-right" placeholder="Date" data-rule-required="true" data-msg-required="Date (From) is required"></div>';

                var expense_detail_input = '<textarea class="form-control" name="expense['+rows_count+']" placeholder="Expense Details" data-rule-required="true" data-msg-required="Expense Detail is required"></textarea>';
                var amount_input = '<input class="form-control amount" name="amount['+rows_count+']" placeholder="Amount"  data-rule-required="true" data-msg-required="Amount is required">';
                var reference_input = '<input class="form-control reference_row" name="reference['+rows_count+']" placeholder="Reference No" data-rule-required="true" data-msg-required="Reference No. is required">';
                var remarks_input = '<textarea class="form-control" name="remarks['+rows_count+']" placeholder="Remarks"></textarea>';
                if(rows_count == 1){
                    var remove = '';
                }else{
                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove_row"><i class="la la-close"></i></a>';

                }

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

                table.row.add([0, heads_select,titles_select,hub_select,date_input,expense_detail_input,amount_input,reference_input,remarks_input,remove]).node().id = rows_count;
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
                    placeholder:'Select a City',
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
            add_row();

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

            $('body').on('click', '.remove_row',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, selected_rows);

                if (index !== -1) {
                    selected_rows.splice(index, 1);
                }

                table.row( $(this).parents('tr') ).remove().draw();
                calculate_amount();

            });
            function calculate_amount() {
                var total_amount = 0;
                table.rows().nodes().each(function(index) {
                    var row = table.row(index);
                    if($(row.node()).find('td.expense_amount input').val() != ''){

                        total_amount += parseInt($(row.node()).find('td.expense_amount input').val());
                    }


                });
                $('#statements_total_amount').text(total_amount);
            }
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