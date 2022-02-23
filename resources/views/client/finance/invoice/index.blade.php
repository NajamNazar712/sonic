@extends('client.layout.master')

@section('title', 'Invoices')

@section('content')
    <h1 class="mb-1">
         Invoices
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')
                <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                      <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                      </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To">
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="form-group">
                            <button type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                         </div>
                    </div>
                </form>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Invoice Number</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Total Charges</th>
                        <th class="border-primary border-darken-1">Total GST</th>
                        <th class="border-primary border-darken-1">Total Invoice Amount</th>
                        <th class="border-primary border-darken-1">Generation Date</th>
                        <th class="border-primary border-darken-1">Invoicing Cycle</th>
                        <th class="border-primary border-darken-1">Invoicing Date</th>
                        <th class="border-primary border-darken-1">Invoice Type</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
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
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.finance.invoice.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Invoice No.');
                            head.push('City');
                            head.push('Total Charges');
                            head.push('Total GST');
                            head.push('Total Invoice Amount');
                            head.push('Generation Date');
                            head.push('Invoicing Cycle');
                            head.push('Invoicing Date');
                            head.push('Invoice Type');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.invoice_number);
                                row.push(values.city);
                                row.push(values.total_charges);
                                row.push(values.total_gst);
                                row.push(values.total_invoice_amount);
                                row.push(values.created_at);
                                row.push(values.invoicing_cycle);
                                row.push(values.invoicing_date);
                                row.push(values.invoice_type);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #to_date').pickadate('picker').set('min', $('#track_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });

            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #from_date').pickadate('picker').set('max', $('#track_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    extend: 'excel',
                    title: 'Invoices',
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
                ajax:{
                    url: '{{ route('cod.finance.invoice.list') }}',
                    data: function (d) {
                        d.from_date = $('input[name="from_date_formatted"]').val();
                        d.to_date = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[0, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'invoice_number_button', name: 'invoices.invoice_number', class: 'align-middle text-center invoice_number'},
                    {data:'city', name: 'c.name', class: 'align-middle text-center shipper'},
                    {data:'total_charges', name: 'invoices.total_charges', class: 'align-middle text-center total_charges'},
                    {data:'total_gst', name: 'invoices.total_gst', class: 'align-middle text-center total_gst'},
                    {data:'total_invoice_amount', name: 'invoices.total_invoice_amount', class: 'align-middle text-center total_invoice_amount'},
                    {data:'created_at', name: 'invoices.created_at', class: 'align-middle text-center generation_date'},
                    {data:'invoicing_cycle', name: 'ic.name', class: 'align-middle text-center invoicing_cycle'},
                    {data:'invoicing_date', name: 'invoices.invoicing_date', class: 'align-middle text-center invoicing_date'},
                    {data:'invoice_type', name: 'invoices.invoice_type', class: 'align-middle text-center invoice_type'},
                    {data:'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
                    var invoice_type_select = '<select name="invoice_type_select" id="invoice_type_select" class="select2 form-control">' +
                        '<option value="1">Courier Invoice</option>' +
                        '<option value="2">Packaging Invoice</option>' +
                        '</select>';


                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.aging') || $(header).is('.overdue_by') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.invoice_type')){
                            $(invoice_type_select).appendTo($(search))
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

                    $("#invoice_type_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Invoice Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });


            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                var type_id = table.row($(this).parents('tr')).data().type_id;
                console.log(type_id,id);
                if ($(this).hasClass('detail_print')) {
                    var url ='';
                    if(type_id == 2){
                        url = '{!! route('cod.finance.invoice.detail_print') !!}';
                    }
                    else{
                        url = '{!! route('cod.finance.invoice.reimbursement.detail_print') !!}';
                    }

                    if (id) {
                        $.ajax({
                            url:url,
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
                }
            });

            $('#datatable tbody').on('contextmenu', 'tr td.invoice_number button', function(e) {
                e.preventDefault();

                var id = parseInt($(this).parents('tr').attr('id'));

                if (id) {
                    $.ajax({
                        url: '{!! route('cod.finance.invoice.detail_print') !!}',
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
                    window.open('{!! route('cod.finance.invoice.export_to_excel') !!}?id=' + id, '_blank');
                }
                else if ($(this).hasClass('print_origin_wise')) {
                    $.ajax({
                        url: '{!! route('cod.finance.invoice.print_origin_wise') !!}',
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
            $('#track_form').bind('submit', function (e) {
                e.preventDefault();
                var from_date = $('#track_form #from_date').val();
                var to_date = $('#track_form #to_date').val();

                if ((from_date != '' && to_date != '')) {
                    table.draw();
                }
            });
        });
    </script>
@endsection