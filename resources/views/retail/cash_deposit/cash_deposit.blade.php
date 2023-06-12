@extends('retail.layout.master')

@section('title', 'Cash Deposit')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <h1 class="mb-1">
                    Cash Deposit
                </h1>
            </div>
            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">
                        @include('retail.inc.messages')
                        <div class="row mb-2 justify-content-center">
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Search Date (From)" data-value="">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Search Date (To)" data-value="">
                                </div>
                            </div>
                            <div class="col-2">
                                <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                            </div>
                        </div>
                        <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Total CN Number Used</th>
                                <th class="border-primary border-darken-1">Retail Note</th>
                                <th class="border-primary border-darken-1">Trax/Franchise</th>
                                <th class="border-primary border-darken-1">Booking Code</th>
                                <th class="border-primary border-darken-1">Total</th>
                                <th class="border-primary border-darken-1">HBL Konnect Amount</th>
                                <th class="border-primary border-darken-1">Remaining/Cash</th>
                                <th class="border-primary border-darken-1">Booking Date</th>
                                <th class="border-primary border-darken-1">Status</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Total CN Number Used</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->

    {{-- HBL Konnect Cash  --}}
    <div class="modal fade" id="hbl_konnect_modal" data-backdrop="static" role="dialog" aria-labelledby="hbl_konnect_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="hbl_konnect_modal_title">Total Transactions</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="hbl_konnect_modal-body text-center">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- HBL Konnect Cash end --}}

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/chartjs/chart.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#to_date').pickadate('picker').set('min', $('#from_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#to_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#from_date').pickadate('picker').set('max', $('#to_date').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('retail.cash_deposit.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Total CN Number Used');
                            head.push('Retail Note');
                            head.push('Trax Center/Franchise');
                            head.push('Booking Code');
                            head.push('Total');
                            head.push('KBH Konnect Amount');
                            head.push('Remaining/Cash');
                            head.push('Booking Date');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.total_shipments);
                                row.push(values.performa_no);
                                row.push(values.category);
                                row.push(values.booking_code);
                                row.push(values.total_cash);
                                row.push(values.hbl_konnect_cash);//
                                row.push(values.remaining_cash);//
                                row.push(values.booking_date);
                                row.push(values.status);

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
                    {
                        text: 'Finalize RNCC',
                        className: 'btn btn-primary finalize_rncc',
                        enabled: true,
                        action: function (e, dt, node, config) {
                                swal({
                                    text: 'Are you sure, you want to Finalize RNCC?',
                                    icon: 'info',
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
                                            url: '{!! route('retail.cash_deposit.finalize_rncc') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}'
                                            }
                                        })
                                            .done(function (data) {
                                                if (data.status == 0) {
                                                    toastr.success(data.success, 'Success!', {
                                                        positionClass: 'toast-bottom-center',
                                                        containerId: 'toast-bottom-center'
                                                    });
                                                    print(data.parcel_receiving_id);
                                                } else {
                                                    toastr.error(data.error, 'Error!', {
                                                        positionClass: 'toast-top-center',
                                                        containerId: 'toast-top-center'
                                                    });
                                                }

                                                table.draw('false');
                                            });
                                    }
                                });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Cash Deposit',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
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
                    url: '{{ route('retail.cash_deposit.list') }}',
                    data: function (d) {
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'performa_no',
                order: [[6, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipments_button', name: 'retail_cash_deposits.total_cn', class: 'align-middle text-center shipments_button'},
                    {data: 'performa_button', name: 'retail_cash_deposits.id', class: 'align-middle text-center performa_button'},
                    {data: 'category', name: 'retail_cash_deposits.category', class: 'align-middle text-center category'},
                    {data: 'booking_code', name: 'ru.id', class: 'align-middle text-center booking_code'},
                    {data: 'total_cash', name: 'retail_cash_deposits.total_cash', class: 'align-middle text-center total_cash'},
                    {data: 'hbl_konnect_cash', name: 'retail_cash_deposits.total_cash', class: 'align-middle text-center hbl_konnect_cash'},//
                    {data: 'remaining_cash', name: 'retail_cash_deposits.total_cash', class: 'align-middle text-center remaining_cash'},//
                    {data: 'booking_date', name: 'retail_cash_deposits.created_at', class: 'align-middle text-center booking_date'},
                    {data: 'status', name: 'retail_cash_deposits.status', class: 'align-middle text-center status'}
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
                    var shipping_mode = '<select name="shipping_mode" id="shipping_mode" class="select2 form-control"></select>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Pending</option>' +
                        '<option value="1">Cash Collected</option>' +
                        '<option value="2">Deposited</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.destination_arrival') || $(header).is('.hbl_konnect_cash')) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.status')) {
                            $(drop_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }else if($(header).is('.shipping_mode')){
                            $(shipping_mode).appendTo($(search))
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
                    var data = $.map({!! $shipping_modes !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#shipping_mode").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Shipping Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });


            var route = '{!! route('retail.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.shipments_button button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('retail.cash_deposit.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'performa_no': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.status == 1) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                            $('#shipments_modal').modal('show');
                        }
                    });

            });

            $('#datatable tbody').on('click','tr td.performa_button button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                print(id);
            });
            function print(id) {
                $.ajax({
                    url: '{!! route('retail.cash_deposit.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function(data) {
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

            $('#search_filter_btn').on('click', function(){
                table.draw(true);
            });

            $('#datatable tbody').on('click','tr td.hbl_konnect_cash button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('.hbl_konnect_modal-body table tbody').html('');

                $.ajax({
                    url: '{!! route('retail.cash_deposit.hbl_konnect_cash') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'retail_note_id': id
                    }
                }).done(function (data) {
                    if (data) {
                        var html = '';
                        if (data.status == 1) {
                            $.each(data.data, function (index, value) {
                                html += `<tr>
                                    <td>${value.transaction_id}</td>
                                          <td>${value.amount}</td>
                                                <td>${value.created_at}</td>

                                                <tr>`

                            });
                        }
                        console.log(html);
                        $('.hbl_konnect_modal-body table tbody').html(html);
                        $('#hbl_konnect_modal').modal('show');
                    }
                });
            });
        });
    </script>
@endsection