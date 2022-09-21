@extends('admin.layout.master')

@section('title', 'Wholesale Invoices')

@section('content')
    <h1 class="mb-1">
        Wholesale Invoices
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Invoice ID</th>
                        <th class="border-primary border-darken-1">Invoice Number</th>
                        <th class="border-primary border-darken-1">Shipper ID</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Total Courier Charges</th>
                        <th class="border-primary border-darken-1">Service Charges on Inv.</th>
                        <th class="border-primary border-darken-1">GST</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Updated At</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>



    <div class="modal fade" id="edit_invoice_modal" role="dialog" aria-labelledby="edit_invoice_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edit Invoice</h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">

                    <form id="edit_invoice_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.international.wholesale.invoices.edit') }}" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <input type="hidden" name="invoice_id" id="invoice_id">
                        <div class="row">
                            <div class="col-6">
                                <h4 id="invoice_number">Invoice Number: <span class="font-weight-bold"></span></h4>
                            </div>
                            <div class="col-6">
                                <h4 id="shipper_name">Shipper Name: <span class="font-weight-bold"></span></h4>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="total_courier_charges">Total Courier Charges</label>
                                    <input name="total_courier_charges" id="total_courier_charges" class="form-control select2" placeholder="Total Courier Charges*" data-rule-required="true"  data-msg-required="Total Courier Charges are required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="service_charges">Service Charges</label>
                                    <input type="text" class="form-control" name="service_charges" id="service_charges" placeholder="Service Charges*" data-rule-required="true"  data-msg-required="Service Charges are required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="gst">GST</label>
                                    <input type="text" class="form-control" name="gst" id="gst" placeholder="GST*" data-rule-required="true"  data-msg-required="GST is required">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="form-group ml-1">
                                <button type="submit" class="btn btn-primary width-200" id="EditInvoiceSubmitButton">Update</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="view_history_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="view_history_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">View History</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <table class="table table-bordered" id="view_history_datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Invoice Number</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Total Courier Charges</th>
                            <th class="border-primary border-darken-1">Service Charges</th>
                            <th class="border-primary border-darken-1">GST</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Updated By</th>
                            <th class="border-primary border-darken-1">Updated At</th>

                        </tr>
                        </thead>
                    </table>

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



            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.international.wholesale.invoices.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Invoice ID');
                            head.push('Invoice No.');
                            head.push('Shipper ID');
                            head.push('Shipper Name');
                            head.push('Total Courier Charges');
                            head.push('Service Charges');
                            head.push('GST');
                            head.push('Created At');
                            head.push('Status');
                            head.push('Updated By');
                            head.push('Updated At');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.invoice_id);
                                row.push(values.invoice_number);
                                row.push(values.shipper_id);
                                row.push(values.shipper_name);
                                row.push(values.total_courier_charges);
                                row.push(values.service_charges);
                                row.push(values.gst);
                                row.push(values.created_at);
                                row.push(values.invoice_status);
                                row.push(values.updated_by);
                                row.push(values.updated_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if ((session('role_id') == 1 || in_array(813, session('permissions'))))
                    {
                        text: '<i class="ft-plus-circle"></i> Receive',
                        className: 'btn btn-primary mark_as_received',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                swal({
                                    text: 'Are you sure, you want to mark this invoice resolved?',
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
                                            url: '{!! route('admin.international.wholesale.invoices.bulk_resolved') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'invoice_ids': selected_rows
                                            }
                                        }).done(function (data) {
                                            if (data.status == 1) {

                                                table.rows().deselect();

                                                selected_rows = [];

                                                table.button('.mark_as_received').disable();

                                                table.draw('false');
                                                toastr.success(data.success, 'Success!', {
                                                    positionClass: 'toast-bottom-center',
                                                    containerId: 'toast-bottom-center'
                                                });

                                            } else {

                                                toastr.error(data.error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });

                                            }
                                            UnblockPagePermanently();


                                        });

                                    }
                                });
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

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.mark_as_received').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.mark_as_received').disable();
                                    }
                                }
                            });
                        }
                    },{
                        extend: 'excel',
                        title: 'Wholesale Invoices',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                scrollX: true, scrollY: '500px',
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.international.wholesale.invoices.list') }}',
                rowId: 'invoice_id',
                order: [[9, 'desc']],
                columns: [

                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'invoice_id_padded', name: 'wholesale_invoices.id', class: 'align-middle text-center invoice_id_padded'},
                    {data:'invoice_number', name: 'wholesale_invoices.invoice_number', class: 'align-middle text-center invoice_number'},
                    {data:'shipper_id_padded', name: 'wu.id', class: 'align-middle text-center shipper_id_padded'},
                    {data:'shipper_name', name: 'wu.name', class: 'align-middle text-center shipper_name'},
                    {data:'total_courier_charges', name: 'wholesale_invoices.total_courier_charges', class: 'align-middle text-center total_courier_charges'},
                    {data:'service_charges', name: 'wholesale_invoices.service_charges', class: 'align-middle text-center service_charges'},
                    {data:'gst', name: 'wholesale_invoices.gst', class: 'align-middle text-center gst'},
                    {data:'created_at', name: 'wholesale_invoices.created_at', class: 'align-middle text-center created_at'},
                    {data:'invoice_status', name: 'invoice_status', class: 'align-middle text-center status', orderable: false},
                    {data:'updated_by', name: 'ub.name', class: 'align-middle text-center updated_by'},
                    {data:'updated_at', name: 'wholesale_invoices.updated_at', class: 'align-middle text-center updated_at'},

                    {data:'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if (data.invoice_status === 'Pending') {
                        $('td:eq(0)', row).addClass('select-checkbox');

                        if ($.inArray(data.id, selected_rows) !== -1) {
                            table.row(row).select();
                        }
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
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

                    $('#status_select').prepend('<option value="" selected></option>').select2({
                        data: [{id: 1, name: "Pending", text: "Pending"},{id: 2, name: "Received", text: "Received"}],
                        placeholder: 'Select Status',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('.datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.mark_as_received').enable();
                }
                else {
                    table.button('.mark_as_received').disable();
                }
            });


            var view_history_table;
            view_history_table = $('#view_history_datatable').DataTable({
                dom: 'ltipr',
                ordering:false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'invoice_number', class: 'align-middle invoice_number'},
                    {name: 'shipper_name', class: 'align-middle shipper_name'},
                    {name: 'total_courier_charges', class: 'align-middle total_courier_charges'},
                    {name: 'service_charges', class: 'align-middle service_charges'},
                    {name: 'gst', class: 'align-middle gst'},
                    {name: 'status', class: 'align-middle status'},
                    {name: 'updated_by', class: 'align-middle updated_by'},
                    {name: 'updated_at', class: 'align-middle updated_at'},
                ],

                rowCallback: function(row, data, index) {
                    var info = view_history_table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
            });

            $('#view_history_modal').on('hidden.bs.modal', function () {

                view_history_table.clear().draw();
            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id) {
                    if ($(this).hasClass('view_history')) {
                        $.ajax({
                            url: '{!! route('admin.international.wholesale.invoices.view_history') !!}',

                            data: {
                                'invoice_id': id
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {

                                    $.each(data.details, function (index, detail){
                                        view_history_table.row.add([0, detail.invoice_number, detail.shipper_name, detail.total_courier_charges, detail.service_charges, detail.gst, detail.status, detail.updated_by, detail.updated_at]).node().id = detail.id;

                                    });
                                    view_history_table.draw(false);
                                    $('#view_history_modal').modal('show');

                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            });
                    }
                    else if ($(this).hasClass('edit')) {
                        $.ajax({
                            url: '{!! route('admin.international.wholesale.invoices.edit') !!}',

                            data: {
                                'invoice_id': id
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    $('#edit_invoice_form #invoice_id').val(id);
                                    $('#edit_invoice_form #shipper_name span').text(data.details.shipper_name);
                                    $('#edit_invoice_form #invoice_number span').text(data.details.invoice_number);
                                    $('#edit_invoice_form #total_courier_charges').val(data.details.total_courier_charges);
                                    $('#edit_invoice_form #service_charges').val(data.details.service_charges);
                                    $('#gst').val(data.details.gst);

                                    $('#edit_invoice_modal').modal('show');
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }

                                table.draw('false');
                            });
                    }
                    else if ($(this).hasClass('resolved')) {
                        swal({
                            text: 'Are you sure, you want to mark this invoice resolved?',
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
                                    url: '{!! route('admin.international.wholesale.invoices.resolved') !!}',
                                    method: 'POST',
                                    data: {
                                        '_token': '{{ csrf_token() }}',
                                        'invoice_id': id
                                    }
                                }).done(function (data) {
                                    if (data.status == 1) {

                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });

                                    } else {

                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });

                                    }
                                    UnblockPagePermanently();


                                });
                            }
                        });
                    }
                    else if ($(this).hasClass('general_print')) {
                        $.ajax({
                            url: '{!! route('admin.international.wholesale.invoices.general_print') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'invoice_id': id
                            }
                        })
                            .done(function (data) {
                                var tab = window.open('', '_blank');

                                if (!tab) {
                                    swal({
                                        title: 'Popup Blocker Enabled!',
                                        text: 'Please add this site to your exception list.',
                                        icon: 'error',
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                } else {
                                    tab.document.write(data);
                                    tab.document.close();
                                    tab.focus();
                                }
                            });
                    }
                    else if ($(this).hasClass('consolidated_print')) {
                        $.ajax({
                            url: '{!! route('admin.international.wholesale.invoices.consolidated_print') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'invoice_id': id
                            }
                        })
                            .done(function (data) {
                                var tab = window.open('', '_blank');

                                if (!tab) {
                                    swal({
                                        title: 'Popup Blocker Enabled!',
                                        text: 'Please add this site to your exception list.',
                                        icon: 'error',
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                } else {
                                    tab.document.write(data);
                                    tab.document.close();
                                    tab.focus();
                                }
                            });
                    }
                }
            });


            $('#edit_invoice_modal').on('hidden.bs.modal', function () {
                $('#edit_invoice_form').validate().resetForm();
            });

            $('#edit_invoice_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Invoice is being updated!',
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