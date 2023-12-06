@extends('admin.layout.master')
@section('title','View Petty Cash Statement')

@section('content')

    <h1 class="mb-1">
        View Petty Cash Statement # {{$petty_statement->id}}
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                    <div class="row">
                        @if($petty_statement->zone_id != null)
                            <div class="col-6">
                                <fieldset class="form-group">
                                    <label class="font-medium-3">Statement Zone :</label>
                                    <span class="font-medium-4 font-weight-light">{!! $petty_statement->zone_name!!}</span>
                                </fieldset>
                            </div>
                        @endif
                        @if($petty_statement->hub_id != null)
                        <div class="col-6">
                            <fieldset class="form-group">
                                <label class="font-medium-3">Statement Hub :</label>
                                <span class="font-medium-4 font-weight-light">{!! $petty_statement->hub_name!!}</span>
                            </fieldset>
                        </div>
                        @endif
                        @if($petty_statement->sdn_id != null)
                            <div class="col-6">
                                <fieldset class="form-group">
                                    <label class="font-medium-3">Statement SDN :</label>
                                    <span class="font-medium-4 font-weight-light">{!! str_pad($petty_statement->sdn_id, 6, '0', STR_PAD_LEFT)!!}</span>
                                </fieldset>
                            </div>
                        @endif
                        <div class="col">
                            <fieldset class="form-group">
                                <label class="font-medium-3">Statement Reference Number :</label>
                                <span class="font-medium-4 font-weight-light">{!! $petty_statement->reference_no!!}</span>
                            </fieldset>
                        </div>
                            @if($petty_statement->from != null)
                        <div class="col-6">
                            <fieldset class="form-group">
                            <label class="font-medium-3">Statement From :</label>
                            <span class="font-medium-4 font-weight-light">{!! $petty_statement->from!!}</span>
                            </fieldset>
                        </div>
                            @endif
                            @if($petty_statement->from != null)
                        <div class="col-6 ">
                            <fieldset class="form-group">
                            <label class="font-medium-3">Statement To :</label>
                            <span class="font-medium-4 font-weight-light">{!! $petty_statement->to!!}</span>
                            </fieldset>
                        </div>
                            @endif

                            @if($petty_statement->date != null)
                                <div class="col-6 ">
                                    <fieldset class="form-group">
                                        <label class="font-medium-3">Statement Date :</label>
                                        <span class="font-medium-4 font-weight-light">{!! date("Y-m-d",strtotime($petty_statement->date))!!}</span>
                                    </fieldset>
                                </div>
                            @endif

                            @if($petty_statement->station_manager_id != null)
                                <div class="col-6 ">
                                    <fieldset class="form-group">
                                        <label class="font-medium-3">Station Manager Name :</label>
                                        <span class="font-medium-4 font-weight-light">{!! $petty_statement->station_manager_name!!}</span>
                                    </fieldset>
                                </div>
                            @endif

                        <div class="col-12">
                            <fieldset class="form-group">
                            <label class="font-medium-3">Total Amount :</label>
                            <span class="font-medium-4 font-weight-light">{!! $petty_statement->total_amount!!}</span>
                            </fieldset>
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
                            <th class="border-primary border-darken-1">Amount</th>
                            <th class="border-primary border-darken-1">Employee Id</th>
                            <th class="border-primary border-darken-1">Name</th>
                            <th class="border-primary border-darken-1">Designation</th>
                            <th class="border-primary border-darken-1">Reference No.</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">DNCC/RNCC</th>
                            <th class="border-primary border-darken-1">Delivered Shipments</th>
                            <th class="border-primary border-darken-1">Reference Document</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Updated By</th>
                            <th class="border-primary border-darken-1">Updated At</th>
                            <th class="border-primary border-darken-1">Action</th>
                        </tr>
                        </thead>
                    </table>
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

    <div class="modal fade" id="edit_petty_cash_fields" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <Form method="POST" id="edit_fields_form" enctype="multipart/form-data" action="{{route('admin.petty_cash.edit.edit_petty_cash')}}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Petty Cash</h5>
                        <button type="button" id="edit_fields_form_button_close_1" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group display-hidden">
                            <div id="petty_cash_id"></div>
                        </div>
                        <div class="form-group" id="select_head">
                            <select name="head_id" id="head_id" class="form-control select2" data-rule-required="true" data-msg-required="Head is required">
                                @foreach($heads as $head)
                                    <option value="{{ $head->id }}" > {{ $head->name }} </option>
                                @endforeach
                            </select>
                            <label id="head_id-error" class="error" for="head_id"></label>
                        </div>
                        <div class="form-group" id="select_title">
                            <select name="title_id" id="title_id" class="form-control select2" data-rule-required="true" data-msg-required="Title is required">
                            </select>
                            <label id="title_id-error" class="error" for="title_id"></label>
                        </div>
                        <div class="form-group" id="select_city">
                            <select name="city_id" id="city_id" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" > {{ $city->name }} </option>
                                @endforeach
                            </select>
                            <label id="city_id-error" class="error" for="city_id"></label>
                        </div>
                        <div class="form-group" id="expense_details">
                            <input type="text" name="expense_details" id="expense_details" value="expense_details" class="form-control" data-rule-required="true" data-msg-required="Expense Details is required" placeholder="Expense Details">
                            <label id="expense_details-error" class="error" for="expense_details"></label>
                        </div>
                        <div class="form-group" id="employee_trax_id">
                            <input type="text" name="employee_trax_id" id="employee_trax_id" value="employee_trax_id" class="form-control" data-rule-required="true" data-msg-required="Employee ID is required" placeholder="Employee ID">
                            <label id="employee_trax_id-error" class="error" for="employee_trax_id"></label>
                        </div>
                        <div class="form-group" id="reference_no">
                            <input type="text" name="reference_no" id="reference_no" value="reference_no" class="form-control" data-rule-required="true" data-msg-required="Reference No. is required" placeholder="Reference No">
                            <label id="reference_no-error" class="error" for="reference_no"></label>
                        </div>
                        <div class="form-group" id="remarks">
                            <input type="text" name="remarks" id="remarks" value="remarks" class="form-control" data-rule-required="true" data-msg-required="Remarks is required" placeholder="Remarks">
                            <label id="remarks-error" class="error" for="remarks"></label>
                        </div>
                        <div class="form-group" >
                            <label id="reference_document" for="reference_document">Upload Reference Document Image</label>
                            <input type="file" name="reference_document"  id="reference_document" class="form-control" />
                        </div>
                        <div class="form-group" >
                            <label id="reference_document_2" for="reference_document_2">Upload Reference Document Image</label>
                            <input type="file" name="reference_document_2"  id="reference_document2" class="form-control" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="edit_fields_form_button_close_2" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="edit_fields_form_button" class="btn btn-primary">Save changes</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <button type="button" id="edit_petty_cash_amount_button" class="btn btn-primary display-hidden" data-toggle="modal"
            data-target="#edit_petty_cash_amount">
        Edit Amount
    </button>
    <div class="modal fade" id="edit_petty_cash_amount" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{route('admin.petty_cash.edit.edit_petty_cash_amount')}}" id="edit_amount_form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Petty Cash Amount</h5>
                        <button type="button" id="edit_amount_form_button_close" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group display-hidden">
                            <div id="petty_cash_idd"></div>
                        </div>
                        <div class="form-group" id="select_title">
                            <input type="text" onkeyup="(this.value == 0) ? this.value = '' : ''" name="amount" id="amount" placeholder="Enter Amount" class="form-control amount" data-rule-required="true" data-msg-required="Amount is required" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="edit_amount_form_button_close1" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="edit_amount_form_button" class="btn btn-primary">Save changes</button>
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
        .total_amount_span{
            font-size: 24px;
            color: #64a0d2;
        }
        .error
        {
            color: #FF4961 !important;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: ['reset'],
                autoWidth: false,
                scrollX: true, scrollY:'500px',
                ajax: '{{ route('admin.petty_cash.approved.view.list',['id'=>$petty_statement->id]) }}',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: false,
                rowId: 'statement_detail_id',
                paging:false,
                ordering: false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data:'city_name' ,name: 'c.name', class: 'align-middle city_name'},
                    {data:'account_head' ,name: 'account_head', class: 'align-middle account_head'},
                    {data:'account_title' ,name: 'account_title', class: 'align-middle account_title'},
                    {data:'expense_details' ,name: 'petty_cash_statement_details.expense_details', class: 'align-middle details_of_expense'},
                    {data:'amount' ,name: 'petty_cash_statement_details.amount', class: 'align-middle expense_amount custom-col-width'},
                    {data:'employee_trax_id' ,name: 'a.trax_id', class: 'align-middle employee_trax_id custom-col-width'},
                    {data:'employee_name' ,name: 'a.name', class: 'align-middle employee_name custom-col-width'},
                    {data:'employee_designation' ,name: 'a.designation', class: 'align-middle employee_designation custom-col-width'},
                    {data:'reference_no' ,name: 'petty_cash_statement_details.reference_no', class: 'align-middle reference_no'},
                    {data:'remarks' ,name: 'petty_cash_statement_details.remarks', class: 'align-middle remarks'},
                    {data:'dncc' ,name: 'petty_cash_statement_details.dncc_id', class: 'align-middle dncc custom-col-width'},
                    {data:'delivered_shipments' ,name: 'petty_cash_statement_details.delivered_shipments', class: 'align-middle delivered_shipments custom-col-width'},
                    {data:'reference_document' ,name: 'reference_document', class: 'align-middle reference_document'},
                    {data:'status' ,name: 'petty_cash_statement_details.status', class: 'align-middle status'},
                    {data:'edit_by_admin' ,name: 'ad.name', class: 'align-middle edit_by'},
                    {data:'edit_at' ,name: 'petty_cash_statement_details.edit_at', class: 'align-middle edit_at'},
                    {data:'action' ,name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false},
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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.date') || $(header).is('.account_head') || $(header).is('.account_title') ||  $(header).is('.reference_document')) {
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

            $('#select_title').css('display','none');
            $('#head_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Chart Head",
                allowClear:true,
                dropdownParent:$('#edit_fields_form'),
            }).bind('change', function()
            {
                var id = parseInt($(this).val());

                $('#select_title').css('display','block');

                $.ajax({
                    url: '{{route('admin.petty_cash.make.titles')}}',
                    method: 'post',
                    data: {
                        account_head:id,
                        _token: '{{ csrf_token() }}',
                    }
                }).done(function (data) {
                    let html = '';
                    for (let i = 0; i < data.titles.length; i++) {
                        html += '<option value="' + data.titles[i].id + '" >' + data.titles[i].name + '</option>';
                    }
                    $('#title_id').html(html);
                });

                $('#title_id').prepend('<option selected></option>').select2({
                    width:'100%',
                    placeholder:"Select Title",
                    allowClear:true,
                    dropdownParent:$('#edit_fields_form'),
                });
                $('#city_id').prepend('<option selected></option>').select2({
                    width:'100%',
                    placeholder:"Select City",
                    allowClear:true,
                    dropdownParent:$('#edit_fields_form'),
                });
            });



            // getting values from action button and setting those values in modal fields
            $('#edit_petty_cash_fields').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var head_id = $(e.relatedTarget).data('account_head_id');
                var city_id = $(e.relatedTarget).data('account_city_id');
                var expense_details = $(e.relatedTarget).data('account_expense_details');
                var employee_trax_id = $(e.relatedTarget).data('account_employee_trax_id');
                var reference_no = $(e.relatedTarget).data('account_reference_no');
                var remarks = $(e.relatedTarget).data('account_remarks');
                var title_id = $(e.relatedTarget).data('account_title_id');
                let html = '';
                html += '<input name="petty_cash_id" value="' + id + '" >';
                $('#petty_cash_id').html(html);
                $('#head_id').val(head_id).trigger('change');
                $('#city_id').val(city_id).trigger('change');
                $('#expense_details').find('input').val(expense_details);
                $('#employee_trax_id').find('input').val(employee_trax_id);
                $('#reference_no').find('input').val(reference_no);
                $('#remarks').find('input').val(remarks);
                setTimeout(function() {
                    $('#title_id').val(title_id).trigger('change');
                }, 500);
            });

            $('#edit_fields_form_button').on('click', function (e) {
                var test = $('#edit_fields_form').valid();
                if (test === true) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Update !',
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
                            $('#edit_fields_form').submit();
                        }
                    });
                }
            });

            //Reset validiton error on closing modal
            $('#edit_fields_form_button_close_1').on('click', function (e) {
                $('#edit_fields_form').validate().resetForm();
            });

            //Reset validiton error on closing modal
            $('#edit_fields_form_button_close_2').on('click', function (e) {
                $('#edit_fields_form').validate().resetForm();
            });

            $('#amount').inputmask({
                'alias': 'numeric',
                'rightAlign': false,
                'allowMinus': false,
                'allowPlus': false,
                'mask': '9999999',
                'numericInput': true,
            });

            $( "#edit_amount_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
            });

            $('body').on('click', 'button.edit_amount', function () {
                var id = $(this).parents('tr').attr('id');
                let html = '';
                html += '<input name="petty_cash_id" value="' + id + '" >';
                $('#petty_cash_idd').html(html);
                $('#edit_petty_cash_amount_button').click();
            });

            $('#edit_amount_form_button').on('click', function (event) {
                var test = $('#edit_amount_form').valid();
                if (test === true) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Update Amount!',
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
                            $('#edit_amount_form').submit();
                            {{--var formData = $('#edit_amount_form').serialize();--}}
                            {{--event.preventDefault();--}}
                            {{--$.ajax({--}}
                            {{--    url: '{{route('admin.petty_cash.edit.edit_petty_cash_amount')}}', // the URL to submit the form data to--}}
                            {{--    method: 'POST',--}}
                            {{--    data: formData,--}}
                            {{--})--}}
                            //     .done(function (data) {
                            //     table.ajax.reload(null, false);
                            //     if (data.status == 1) {
                            //         toastr.success(data.success, 'Success!', {
                            //             positionClass: 'toast-top-center',
                            //             containerId: 'toast-top-center'
                            //         });
                            //     } else {
                            //         toastr.error(data.error, data.message, {
                            //             positionClass: 'toast-top-center',
                            //             containerId: 'toast-top-center'
                            //         });
                            //     }
                            //     $('#edit_amount_form_button_close').click();
                            //     document.getElementById("edit_amount_form").reset();
                            // });
                        }
                    });
                }
            });

            $('#edit_amount_form_button_close').on('click', function (e) {
                $("#edit_amount_form").validate().resetForm();
                $("#edit_amount_form")[0].reset();
                document.getElementById("edit_amount_form").reset();
            });
            $('#edit_amount_form_button_close1').on('click', function (e) {
                $("#edit_amount_form").validate().resetForm();
                $("#edit_amount_form")[0].reset();
                document.getElementById("edit_amount_form").reset();
            });
        });
    </script>
@endsection