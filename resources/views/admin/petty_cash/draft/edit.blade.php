@extends('admin.layout.master')
@section('title','Edit Petty Cash Statement Draft')

@section('content')
    <h1 class="mb-1">
        Edit Petty Cash Statement Draft # {{$petty_statement_draft->id}}
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="edit_statement_form" action="{{route('admin.petty_cash.draft.edit.submit')}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="selected_rows" id="selected_rows">
                    <input type="hidden" name="petty_draft_id" id="petty_draft_id" value="{{$petty_statement_draft->id}}">
                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_sdn" id="select_statement_sdn" class="form-control select2" data-rule-required="true" data-msg-required="SDN is required">
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

                                <input type="text" name="select_statement_date" data-value="{{$petty_statement_draft->date}}" class="form-control pickadate bg-primary border-primary white rounded-right" id="select_statement_date" placeholder="Select Date" data-rule-required="true" data-msg-required="Date is required">
                            </div>
                        </div>
                        <div class="col">
                            <fieldset class="form-group">
                                <input type="text" class="form-control reference_no" disabled name="reference_no" id="reference_no" placeholder="Statement Reference No." data-rule-required="true" data-msg-required="Statement Reference No. is required" value="{{$petty_statement_draft->reference_no}}">
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_zone" id="select_statement_zone" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                                    @foreach($zones as $zone)
                                        <option value="{{$zone->id}}">{{$zone->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_hub" id="select_statement_hub" class="form-control select2" data-rule-required="true" data-msg-required="Hub is required">
                                </select>
                            </fieldset>
                        </div>

                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_station_manager" id="select_statement_station_manager" class="form-control select2" data-rule-required="true" data-msg-required="Station Manager is required">
                                    @foreach($operation_managers as $manager)
                                        <option value="{{$manager->id}}">{{$manager->name}} @if($manager->trax_id != '')({{$manager->trax_id}}) @endif</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <b class="total_amount_span"> Total Amount : <span id="statements_total_amount">{{$petty_statement_draft->total_amount}}</span></b>
                        </div>
                    </div>
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Account Head</th>
                            <th class="border-primary border-darken-1">Account Title</th>
                            <th class="border-primary border-darken-1">City / Location</th>
                            <th class="border-primary border-darken-1">Details of Expense</th>
                            <th class="border-primary border-darken-1">Employee Id </th>
                            <th class="border-primary border-darken-1">Name </th>
                            <th class="border-primary border-darken-1">Designation </th>
                            <th class="border-primary border-darken-1">DNCC/PNCC </th>
                            <th class="border-primary border-darken-1">Delivered Shipment Count </th>
                            <th class="border-primary border-darken-1">Amount </th>
                            <th class="border-primary border-darken-1">Reference No.</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Reference Documents</th>
                            <th class="border-primary border-darken-1"></th>

                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center">
                        <div class="col-2">
                            <button id="statement_submit" type="submit"  class="btn btn-primary btn-block" name="submit_button" value="create"><i class="la la-list"></i> Make Statement</button>
                        </div>
                        <div class="col-2">
                            <button id="statement_draft" type="submit"  class="btn btn-success btn-block" name="submit_button" value="draft"><i class="la la-save"></i> Save Draft</button>
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
        /*.custom-col-width{*/
            /*max-width: 150px !important;*/
        /*}*/
        /*th.expense_amount, th.reference_no{*/
            /*width: 80px;*/
        /*}*/
        /*.custom-hub-col-width{*/
            /*min-width: 80px;*/
        /*}*/
        .date-col-width{
            min-width: 150px;
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
            min-width: 200px;
        }
        .total_amount_span{
            font-size: 24px;
            color: #64a0d2;
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
            let cities_data = $.map({!! $city_array[$petty_statement_draft->hub_id] !!}, function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
            });

            let dncc_data = "{!! $dncc_array !!}";

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

            $('#select_statement_sdn').val('{!! $petty_statement_draft->sdn_id!!}').trigger('change');

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

            $('#select_statement_zone').val('{!! $petty_statement_draft->zone_id!!}').trigger('change');

            var hub_array = @json($hub_array);
            var hub_data = $.map(hub_array[{!! $petty_statement_draft->zone_id!!}], function (obj) {
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
                                    placeholder: "Select a City"
                                });
                                $(this).val('').trigger('change');
                            });
                        }
                    });
                }
            });

            $('#select_statement_hub').val('{!! $petty_statement_draft->hub_id!!}').trigger('change');

            $('#select_statement_station_manager').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Station Manager',
                width:'100%',
            });

            $('#select_statement_station_manager').val('{!! $petty_statement_draft->station_manager_id!!}').trigger('change');

            var min_date_limit = '{{ Carbon\Carbon::now()->subDays(3)->toDateString() }}';
            var all_max_date = new Date();

            $('#select_statement_date').pickadate({
                firstDay: 1,
                today: '',
                min: new Date(min_date_limit),
                max: all_max_date,
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
                scrollX: true, scrollY: '400px',
                buttons:[{
                    title: 'Add Row',
                    className: 'btn btn-primary mb-1',
                    text: '<i class="la la-plus"></i> Add Row',
                    action:function (e) {
                        add_row();
                    }
                }],
                ordering:false,
                paging:false,
                autoWidth: true,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'account_head', class: 'align-middle account_head custom-col-width form-group'},
                    {name: 'account_title', class: 'align-middle account_title custom-col-width form-group'},
                    {name: 'city', class: 'align-middle city custom-col-width form-group'},
                    {name: 'details_of_expense', class: 'align-middle details_of_expense doe-col-width form-group'},
                    {name: 'employee_id', class: 'align-middle employee_id custom-col-width form-group'},
                    {name: 'name', class: 'align-middle name custom-col-width form-group'},
                    {name: 'designation', class: 'align-middle designation custom-col-width form-group'},
                    {name: 'dncc', class: 'align-middle dncc custom-col-width form-group'},
                    {name: 'delivered_shipment_count', class: 'align-middle delivered_shipment_count custom-col-width form-group'},
                    {name: 'amount', class: 'align-middle expense_amount custom-col-width form-group'},
                    {name: 'reference_no', class: 'align-middle reference_no custom-col-width form-group'},
                    {name: 'remarks', class: 'align-middle custom-col-width remarks'},
                    {name: 'image', class: 'align-middle image custom-col-width form-group'},
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

            $('#edit_statement_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {


                    $('#selected_rows').val(selected_rows);
                    form.submit();
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


            rows_count = 0;
            function add_row() {

                rows_count++;
                selected_rows.push(rows_count);
                var heads_select = '<select class="form-control select2 head_select" name="head['+rows_count+']" data-rule-required="true" data-msg-required="Account Head is required"></select>';
                var titles_select = '<select class="form-control select2 title_select" name="title['+rows_count+']" data-rule-required="true" data-msg-required="Account Title is required"></select>';
                var city_select = '<select class="form-control form-control-sm select2 city_select" name="city['+rows_count+']" data-rule-required="true" data-msg-required="City is required"></select>';
                var expense_detail_input = '<textarea class="form-control form-control-sm" rows="5" maxlength="300" name="expense['+rows_count+']" placeholder="Expense Details" data-rule-required="true" data-msg-required="Expense Detail is required"></textarea>';

                var dncc_select = '<select class="form-control form-control-sm select2 dncc_select" name="dncc['+rows_count+']"></select>';
                var delivered_shipment_input = '<input class="form-control form-control-sm delivered_shipment_input" readonly name="delivered_shipment_count['+rows_count+']" placeholder="Total Delivered Shipments">';

                var employee_select = '<select class="form-control form-control-sm select2 employee_select" name="employee['+rows_count+']"></select>';
                var employee_name_input = '<input class="form-control form-control-sm employee_name" readonly name="employee_name['+rows_count+']" placeholder="Employee Name">';
                var employee_designation_input = '<input class="form-control form-control-sm employee_designation" readonly name="employee_designation['+rows_count+']" placeholder="Employee Designation">';
                var amount_input = '<input class="form-control form-control-sm amount" name="amount['+rows_count+']" placeholder="Amount" data-rule-required="true" data-msg-required="Amount is required">';
                var reference_input = '<input class="form-control form-control-sm reference_row" name="reference['+rows_count+']" placeholder="Reference No">';
                var remarks_input = '<input class="form-control form-control-sm" name="remarks['+rows_count+']" placeholder="Remarks">';

                var upload_image = '<input class="form-control form-control-sm" type="file" name="upload_image'+rows_count+'" data-rule-extension="jpeg|jpg|png|xls|xlsx|pdf" data-msg-extension="Only file with extension jpeg, jpg, pdf, xls, xlsx or png allowed" data-rule-required="true" data-msg-required="Reference Document is required" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."><br><input class="form-control form-control-sm" type="file" name="upload_2_image_'+rows_count+'" data-rule-extension="jpeg|jpg|png|xls|xlsx|pdf" data-msg-extension="Only file with extension jpeg, jpg, pdf, xls, xlsx or png allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">';
                if(rows_count == 1){
                    var remove = '';
                }else{
                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                }

                var heads = $.map({!! $heads !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                });

                var employees = $.map({!! $employees !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.trax_id;
                    return obj;
                });
                table.row.add([0, heads_select,titles_select,city_select,expense_detail_input,employee_select,employee_name_input,employee_designation_input,dncc_select,delivered_shipment_input,amount_input,reference_input,remarks_input,upload_image,remove]).node().id = rows_count;
                table.draw(true);
                $('select[name="head['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data:heads,
                    placeholder:'Select Account Head',
                    allowClear:true,
                    dropdownCssClass: 'form-control-sm p-0'
                });
                $('select[name="title['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    placeholder:'Select Account Title',
                    allowClear:true,
                    dropdownCssClass: 'form-control-sm p-0'
                });

                $('select[name="employee['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data: employees,
                    placeholder:'Select Employee Id',
                    dropdownCssClass: 'form-control-sm p-0',
                    allowClear:true,

                });
                $('select[name="city['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data: cities_data,
                    placeholder:'Select a City',
                    dropdownCssClass: 'form-control-sm p-0'
                });

                $('select[name="dncc['+rows_count+']"]').html(dncc_data);
                $('select[name="dncc['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    placeholder:'Select DNCC/PNCC',
                    dropdownCssClass: 'form-control-sm p-0',
                    allowClear:true,
                }).bind('change',function (){
                    let count = $(this).find(':selected').attr('data-count');
                    let index = $(this).attr('name');
                    index = index.substring(5, index.length-1);
                    $('input[name="delivered_shipment_count['+index+']"]').val(count);
                });

                $('.reference_row').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                });

                $('.amount').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                    'min': 0.00,
                    'max': 6000000.00
                });

            }

            $('body').on('change','#datatable tr td.details_of_expense textarea,#datatable tr td.remarks textarea',function() {
                $(this).val($(this).val().trim());
            });

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

            $('body').on('change','.employee_id .employee_select',function () {
                var rowid = parseInt($(this).parents('tr').attr('id'));
                var selected_employee = $(this).find(':selected');
                var employee = parseInt(selected_employee.val());
                var employee_name = selected_employee.closest('td').next('td').find('.employee_name');
                var employee_designation = employee_name.closest('td').next('td').find('.employee_designation');
                employee_name.val("");
                employee_designation.val("");
                $.ajax({
                    url:'{!! route('admin.petty_cash.make.employee') !!}',
                    type:'POST',
                    data: {
                        'employee':employee,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status){
                        employee_name.val(data.name);
                        employee_designation.val(data.designation);
                    }
                });
            });

            $('#edit_statement_form').on('keypress',function (e) {
                if(e.which == 13) {
                    e.preventDefault();
                }
            });

            $('body').on('click', 'a.remove_row',function () {
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
                        $(row.node()).find('td.reference_document input').attr('disabled',false);
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

            $('#statement_approve').on('click', function (e) {
                e.preventDefault();
                var current = $(this);
                var id = '{{$petty_statement_draft->id}}';
                var status = parseInt({{$petty_statement_draft->status}});
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
                                window.location = '{{ route('admin.petty_cash.statements.edit',['id' => $petty_statement_draft->id]) }}';
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


            $('#statement_reject').on('click',  function (e) {
                e.preventDefault();
                var id = '{{$petty_statement_draft->id}}';
                var status = parseInt({{$petty_statement_draft->status}});
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

            function load_data() {

                @foreach($petty_statement_draft->petty_cash_statement_draft_details as $key => $data)
                        var head = '{{$data->account_head_id}}';
                        var title = '{{$data->account_title_id}}';
                        var city = '{{$data->city_id}}';
                        var employee_id = '{{$data->employee_id}}';
                        var employee_name = '{{$data->employee_name}}';
                        var employee_designation = '{{$data->employee_designation}}';
                        var dncc = '{{$data->dncc_id}}';
                        var delivered_shipments = '{{$data->delivered_shipments}}';
                        var expense = '{{str_replace(array("\n", "\r"), '',$data->expense_details)}}';
                        var amount = '{{$data->amount}}';
                        var reference_no = '{{$data->reference_no}}';
                        var remarks = '{{str_replace(array("\n", "\r"), '',$data->remarks)}}';
                        var reference_document = $.trim('{{$data->reference_document}}');
                        var reference_document_2 = $.trim('{{$data->reference_document_2}}');

                    load_row(head, title,city, expense,employee_id,employee_name,employee_designation,dncc,delivered_shipments, amount, reference_no, remarks, reference_document,reference_document_2);
                @endforeach
            }
            load_data();

            function load_row(head, title,city, expense,employee_id,employee_name,employee_designation,dncc,delivered_shipments, amount, reference, remarks, reference_document,reference_document_2) {
                var image_url = '{{asset('/storage/petty_cash_statement_details_draft')}}';
                rows_count++;
                selected_rows.push(rows_count);
                var heads_select = '<select class="form-control select2 head_select" name="head['+rows_count+']" data-rule-required="true" data-msg-required="Account Head is required"></select>';
                var titles_select = '<select class="form-control select2 title_select" name="title['+rows_count+']" data-rule-required="true" data-msg-required="Account Title is required"></select>';
                var city_select = '<select class="form-control form-control-sm select2 city_select" name="city['+rows_count+']" data-rule-required="true" data-msg-required="City is required"></select>';

                var dncc_select = '<select class="form-control form-control-sm select2 dncc_select" name="dncc['+rows_count+']"></select>';
                var delivered_shipment_input = '<input class="form-control form-control-sm delivered_shipment_input" readonly name="delivered_shipment_count['+rows_count+']" placeholder="Total Delivered Shipments">';

                var expense_detail_input = '<textarea class="form-control form-control-sm" rows="5" maxlength="300" name="expense['+rows_count+']" placeholder="Expense Details" data-rule-required="true" data-msg-required="Expense Detail is required">'+ expense +'</textarea>';
                var employee_select = '<select class="form-control form-control-sm select2 employee_select" name="employee['+rows_count+']"></select>';
                var employee_name_input = '<input class="form-control form-control-sm employee_name" readonly name="employee_name['+rows_count+']" placeholder="Employee Name" value="'+employee_name+'">';
                var employee_designation_input = '<input class="form-control form-control-sm employee_designation" readonly name="employee_designation['+rows_count+']" placeholder="Employee Designation" value="'+employee_designation+'" >';
                var amount_input = '<input class="form-control form-control-sm amount" name="amount['+rows_count+']" placeholder="Amount" value="'+ amount +'" data-rule-required="true" data-msg-required="Amount is required">';
                var reference_input = '<input class="form-control form-control-sm reference_row" name="reference['+rows_count+']" placeholder="Reference No"  value="'+reference+'">';
                var remarks_input = '<input class="form-control form-control-sm" name="remarks['+rows_count+']" placeholder="Remarks" value="'+ remarks +'">';
                var upload_image = '<div class="text-center">';
                if(reference_document != ''){
                    upload_image += '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="'+ image_url +'/'+ reference_document +'" target="_blank">View</a></button><input type="hidden" name="image_'+ rows_count +'" value="'+reference_document+'">';
                }
                upload_image += '<input class="form-control form-control-sm" type="file" name="upload_image'+rows_count+'" data-rule-extension="jpeg|jpg|png|xls|xlsx|pdf" data-msg-extension="Only file with extension jpeg, jpg, pdf, xls, xlsx or png allowed"  data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."';
                if(reference_document == ''){
                    upload_image += ' data-rule-required="true" data-msg-required="Reference Document is required"';
                }

                upload_image += '  value="'+reference_document+'" value=""></div>';

                upload_image += '<br><div class="text-center">';

                if(reference_document_2 != ''){
                    upload_image += '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="'+ image_url +'/'+ reference_document_2 +'" target="_blank">View</a></button><input type="hidden" name="image_2_'+ rows_count +'" value="'+reference_document_2+'">';
                }
                upload_image += '<input class="form-control form-control-sm" type="file" name="upload_2_image'+rows_count+'" data-rule-extension="jpeg|jpg|png|xls|xlsx|pdf" data-msg-extension="Only file with extension jpeg, jpg, pdf, xls, xlsx or png allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."';

                upload_image += '  value="'+reference_document_2+'" value=""></div>';

                if(rows_count == 1){
                    var remove = '';
                }else{
                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';
                }

                var heads = $.map({!! $heads !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                });
                var titles_array = @json($titles);

                var head_title = $.map(titles_array[head], function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                });

                var employees = $.map({!! $employees !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.trax_id;
                    return obj;
                });
                table.row.add([0, heads_select,titles_select,city_select,expense_detail_input,employee_select,employee_name_input,employee_designation_input,dncc_select,delivered_shipment_input,amount_input,reference_input,remarks_input,upload_image,remove]).node().id = rows_count;
                table.draw(true);
                $('select[name="head['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data:heads,
                    placeholder:'Select Account Head',
                    allowClear:true,
                    dropdownCssClass: 'form-control-sm p-0'
                });
                $('select[name="head['+rows_count+']"]').val(head).trigger('change');
                $('select[name="title['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data:head_title,
                    placeholder:'Select Account Title',
                    allowClear:true,
                    dropdownCssClass: 'form-control-sm p-0'
                });
                $('select[name="title['+rows_count+']"]').val(title).trigger('change');

                $('select[name="employee[' + rows_count + ']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data: employees,
                    placeholder: 'Select Employee Id',
                    dropdownCssClass: 'form-control-sm p-0',
                    allowClear:true,

                });

                $('select[name="employee[' + rows_count + ']"]').val(employee_id).trigger('change');

                $('select[name="city[' + rows_count + ']"]').prepend('<option value="" selected="selected"></option>').select2({
                    data: cities_data,
                    placeholder: 'Select a City',
                    dropdownCssClass: 'form-control-sm p-0'
                });

                $('select[name="city[' + rows_count + ']"]').val(city).trigger('change');

                $('select[name="dncc['+rows_count+']"]').html(dncc_data);
                $('select[name="dncc['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                    placeholder:'Select DNCC/PNCC',
                    dropdownCssClass: 'form-control-sm p-0',
                    allowClear:true,
                }).bind('change',function (){
                    let count = $(this).find(':selected').attr('data-count');
                    let index = $(this).attr('name');
                    index = index.substring(5, index.length-1);
                    $('input[name="delivered_shipment_count['+index+']"]').val(count);
                });
                $('select[name="dncc[' + rows_count + ']"]').val(dncc).trigger('change');

                $('.reference_row').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                });

                $('.amount').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                    'min': 0.00,
                    'max': 6000000.00
                });

            }


            first = true;
        });
    </script>
@endsection