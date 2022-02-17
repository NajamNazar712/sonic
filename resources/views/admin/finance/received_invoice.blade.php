@extends('admin.layout.master')

@section('title', 'Received Invoices')

@section('content')
    <h1 class="mb-1">
        Received Invoices
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Invoice Number</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Total Charges</th>
                        <th class="border-primary border-darken-1">Total GST</th>
                        <th class="border-primary border-darken-1">Total Invoice Amount</th>
                        <th class="border-primary border-darken-1">Generation Date</th>
                        <th class="border-primary border-darken-1">Due Date</th>
                        <th class="border-primary border-darken-1">Received Date</th>
                        <th class="border-primary border-darken-1">Company Bank</th>
                        <th class="border-primary border-darken-1">Invoicing Cycle</th>
                        <th class="border-primary border-darken-1">Received Amount</th>
                        <th class="border-primary border-darken-1">Tax Amount</th>
                        <th class="border-primary border-darken-1">Deposit Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Status Id</th>
                        <th class="border-primary border-darken-1">Invoicing Date</th>
                        <th class="border-primary border-darken-1">Invoicing Cycle</th>
                        <th class="border-primary border-darken-1">Invoice Type</th>
                        <th class="border-primary border-darken-1">Payment Type</th>
                      {{--  <th class="border-primary border-darken-1">Status</th>--}}
                    </tr>
                    </thead>
                </table>

                <div class="modal fade" id="mark_as_received" role="dialog" aria-labelledby="mark_as_received_title" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <form class="form-horizontal" method="POST" action="{{ route('admin.finance.invoices.mark_as_received') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="id" class="id">

                                <div class="modal-header">
                                    <h4 class="modal-title" id="mark_as_received_title">Mark as Received<span></span></h4>

                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <select name="company_bank" class="select2 company_bank" data-rule-required="true" data-msg-required="Company Bank is required">
                                            @foreach($company_banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="received_amount" class="form-control received_amount" placeholder="Received Amount*" data-rule-required="true" data-msg-required="Received Amount is required" data-rule-number="true" data-msg-number="Received Amount should to be a valid number">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="tax_amount" class="form-control tax_amount" placeholder="Tax Amount*" data-rule-required="true" data-msg-required="Tax Amount is required" data-rule-number="true" data-msg-number="Tax Amount should to be a valid number">
                                    </div>

                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
											<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
												<span class="la la-calendar-o"></span>
											</span>
                                        </div>

                                        <input type="text" name="deposit_date" class="form-control pickadate bg-primary border-primary white rounded-right deposit_date" id="deposit_date" placeholder="Deposit Date*" data-rule-required="true" data-msg-required="Deposit Date is required">
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary ml-auto">Mark as Received</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade text-left" id="ViewDepositSlip" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewDepositSlip"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary white">
                                <h4 class="modal-title white">Deposit Slips View</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <table class="table table-bordered datatable" id="deposit_slip_table" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">

                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Date</th>
                                        <th class="border-primary border-darken-1">Bank Name</th>
                                        <th class="border-primary border-darken-1">Deposit Slip</th>

                                    </tr>
                                    </thead>
                                </table>

                                <hr>
                                <div class="row justify-content-center">
                                    <div class="col-3">
                                        <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#mark_as_received form select.company_bank').prepend('<option value="" selected></option>').select2({
                placeholder: 'Select Company Bank',
                width:'100%'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#mark_as_received form input.received_amount').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('#mark_as_received form input.tax_amount').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('#mark_as_received form input.deposit_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    $('#mark_as_received input.deposit_date').valid();
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.invoices.received_list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Invoice No.');
                            head.push('Shipper');
                            head.push('City');
                            head.push('Total Charges');
                            head.push('Total GST');
                            head.push('Total Invoice Amount');
                            head.push('Generation Date');
                            head.push('Invoicing Cycle');
                            head.push('Invoicing Date');
                            head.push('Aging');
                            head.push('Due Date');
                            head.push('Overdue By');
                            head.push('Received Date');
                            head.push('Company Bank');
                            head.push('Received Amount');
                            head.push('Tax Amount');
                            head.push('Deposit Date');
                           // head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.invoice_number);
                                row.push(values.shipper);
                                row.push(values.city);
                                row.push(values.total_charges);
                                row.push(values.total_gst);
                                row.push(values.total_invoice_amount);
                                row.push(values.created_at);
                                row.push(values.invoicing_cycle);
                                row.push(values.invoicing_date);
                                row.push(values.aging);
                                row.push(values.due_date);
                                row.push(values.overdue_by);
                                row.push(values.received_date);
                                row.push(values.company_bank);
                                row.push(values.received_amount);
                                row.push(values.tax_amount);
                                row.push(values.deposit_date);
                                //row.push(values.status);

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
                buttons: [{
                    extend: 'excel',
                    title: 'Received Invoices',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                    'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.finance.invoices.received_list') }}',
                rowId: 'id',
                order: [[0, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'invoice_number', name: 'invoice_number', class: 'align-middle text-center invoice_number'},
                    {data:'shipper', name: 'shipper', class: 'align-middle text-center shipper'},
                    {data:'city', name: 'city', class: 'align-middle text-center city'},
                    {data:'total_charges', name: 'total_charges', class: 'align-middle text-center total_charges'},
                    {data:'total_gst', name: 'total_gst', class: 'align-middle text-center total_gst'},
                    {data:'total_invoice_amount', name: 'total_invoice_amount', class: 'align-middle text-center total_invoice_amount'},
                    {data:'created_at', name: 'created_at', class: 'align-middle text-center created_at'},
                    {data:'due_date', name: 'due_date', class: 'align-middle text-center due_date'},
                    {data:'received_date', name: 'received_date', class: 'align-middle text-center received_date'},
                    {data:'company_bank', name: 'company_bank', class: 'align-middle text-center company_bank'},
                    {data:'invoicing_cycle', name: 'invoicing_cycle', class: 'align-middle text-center invoicing_cycle'},
                    {data:'received_amount', name: 'received_amount', class: 'align-middle text-center received_amount'},
                    {data:'tax_amount', name: 'tax_amount', class: 'align-middle text-center tax_amount'},
                    {data:'deposit_date', name: 'deposit_date', class: 'align-middle text-center deposit_date'},
                    {data:'status', name: 'status', class: 'align-middle text-center status'},
                    {data:'status_id', name: 'status_id', class: 'align-middle text-center status_id'},
                    {data:'invoicing_date', name: 'invoicing_date', class: 'align-middle text-center invoicing_date'},
                    {data:'invoicing_cycle', name: 'invoicing_cycle', class: 'align-middle text-center invoicing_cycle'},
                    {data:'invoice_type', name: 'invoice_type', class: 'align-middle text-center invoice_type'},
                    {data:'payment_type', name: 'payment_type', class: 'align-middle text-center payment_type'},
                    //{data:'upload_slip', name: 'invoices.deposit_date', class: 'align-middle text-center upload_slip', orderable: false, searchable: false},
                 /*   {data:'status', name: 'invoices.status_id', class: 'align-middle text-center status'},*/
                    //{data:'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
                    var company_bank_select = '<select name="company_bank_select" id="company_bank_select" class="select2 form-control"></select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.aging') || $(header).is('.overdue_by') || $(header).is('.action') || $(header).is('.upload_slip')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.company_bank')) {
                            $(company_bank_select).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.status')) {
                            $(status_select).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
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

                    var company_banks = $.map({!! $company_banks !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#company_bank_select').prepend('<option value="" selected></option>').select2({
                        data: company_banks,
                        placeholder: 'Select Company Bank',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var statuses = $.map({!! $invoice_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#status_select').prepend('<option value="" selected></option>').select2({
                        data: statuses,
                        placeholder: 'Select Status',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('#mark_as_received form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });

            $('#datatable tbody').on('click', 'tr td.invoice_number button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if (id) {
                    $.ajax({
                        url: '{!! route('admin.finance.invoices.print') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id
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
            });

            $('#datatable tbody').on('contextmenu', 'tr td.invoice_number button', function(e) {
                e.preventDefault();

                var id = parseInt($(this).parents('tr').attr('id'));

                if (id) {
                    $.ajax({
                        url: '{!! route('admin.finance.invoices.print') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id,
                            'header': true
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
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('export_to_excel')) {
                    window.open('{!! route('admin.finance.invoices.export_to_excel') !!}?id=' + id, '_blank');
                }
                else if ($(this).hasClass('email_reminder')) {
                    $.ajax({
                        url: '{!! route('admin.finance.invoices.email_reminder') !!}',
                        method: 'PUT',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                            table.draw('false');
                        });
                }
                else if ($(this).hasClass('mark_as_received')) {
                    $('#mark_as_received form input.id').val(id);

                    $('#mark_as_received form select.company_bank').val('').change();
                    $('#mark_as_received form input.received_amount').val('');
                    $('#mark_as_received form input.tax_amount').val('');
                    $('#mark_as_received form input.pickadate').val('');

                    $('#mark_as_received form label.danger').remove();


                    $('#mark_as_received').modal('show');
                }
                else if ($(this).hasClass('print_origin_wise')) {
                    $.ajax({
                        url: '{!! route('admin.finance.invoices.print_origin_wise') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id
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
                else if ($(this).hasClass('print_gst_wise')) {
                    $.ajax({
                        url: '{!! route('admin.finance.invoices.print_gst_wise') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id
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
            });

            var deposit_slip_table;
            $('body').on('click','.deposit_slip_view', function () {
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $.ajax({
                        url:'{!! route('admin.finance.invoices.slip_view') !!}',
                        type:'POST',
                        data: {
                            'invoice_id':id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status){
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }else{
                            $('#ViewDepositSlip').modal('show');
                            deposit_slip_table = $('#deposit_slip_table').DataTable({
                                dom: 'ltipr',
                                ordering:false,
                                paging:false,
                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                    {name: 'date', class: 'align-middle date date-col-width form-group'},
                                    {name: 'bank_name', class: 'align-middle bank_name form-group'},
                                    {name: 'deposit_slip', class: 'align-middle deposit_slip form-group'}
                                ],

                                rowCallback: function(row, data, index) {
                                    var info = deposit_slip_table.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                                initComplete: function() {

                                }
                            });

                            $.each(data.slips, function (index, value) {
                                deposit_slip_table.row.add([0, value.date, value.bank,  value.image]);
                                deposit_slip_table.draw(true);
                            });
                        }
                    });
                }
            });

            $('#ViewDepositSlip').on('hidden.bs.modal', function () {
                deposit_slip_table.clear();
                deposit_slip_table.destroy();
            });
        });
    </script>
@endsection