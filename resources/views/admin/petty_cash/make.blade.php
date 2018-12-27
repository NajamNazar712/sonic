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

                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_hub" id="select_statement_hub" class="form-control select2" data-rule-required="true" data-msg-required="Hub is required">
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
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Account Head</th>
                        <th class="border-primary border-darken-1">Account Title</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Date</th>
                        <th class="border-primary border-darken-1">Details of Expense</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Reference No.(For supporting Receipt/Document)</th>
                        <th class="border-primary border-darken-1">Remarks</th>

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
            var old_date_limit = '{{ Carbon\Carbon::now()->subDays(2)->toDateString() }}';
            var future_date_limit = '{{ Carbon\Carbon::now()->addDays(28)->toDateString() }}';

            $('#make_statement_form #select_date_from').pickadate({
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
                    //     $('#make_statement_form #select_date_to').pickadate('picker').set('min', $('#make_statement_form #select_date_from').pickadate('picker').get('select'));
                    // }
                }
            });
            $('#make_statement_form #select_date_to').pickadate({
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
                    //     $('#make_statement_form #select_date_from').pickadate('picker').set('max', $('#make_statement_form #select_date_to').pickadate('picker').get('select'));
                    // }
                }
            });

            var selected_rows = [];
            var rows_count = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add Row',
                    className: 'btn btn-primary',
                    text: '<i class="la la-plus"></i> Add Row',
                    action:function (e) {

                    }
                }],
                "autoWidth": false,
                scrollX: true, scrollY:'350px',
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'account_head', class: 'align-middle account_head'},
                    {name: 'account_title', class: 'align-middle account_title'},
                    {name: 'hub', class: 'align-middle hub'},
                    {name: 'date', class: 'align-middle date'},
                    {name: 'details_of_expense', class: 'align-middle details_of_expense'},
                    {name: 'amount', class: 'align-middle amount'},
                    {name: 'reference_no', class: 'align-middle reference_no'},
                    {name: 'remarks', class: 'align-middle remarks'},
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
                                console.log(data)
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
                console.log('Rows Counnt '+rows_count)
                var heads_select = '<select class="form-control select2" name="head['+rows_count+']" ></select>';
                var titles_select = '<select class="form-control select2" name="title['+rows_count+']" ></select>';
                var hub_select = '<select class="form-control select2" disabled name="hub['+rows_count+']" ></select>';
                var date_input = '<div class="form-group input-group"><div class="input-group-prepend"><span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left"><span class="la la-calendar-o"></span></span></div><input type="text" name="date['+rows_count+']" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Date" data-rule-required="true" data-msg-required="Date (From) is required"></div>';
                var heads = $.map({!! $heads !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                });

                table.row.add([0, heads_select,titles_select,hub_select,date_input,0,0,0,0]).draw(true);
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
                    placeholder:'Select Hub',
                    allowClear:true
                });

                $('input[name="date['+rows_count+']"]').pickadate({
                    firstDay: 1,
                    clear: '',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                    hiddenSuffix: '_formatted',
                    onSet: function(context) {

                    }
                });


            }
            add_row();


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