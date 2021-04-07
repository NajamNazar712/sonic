@extends('admin.layout.master')
@section('title','Station Recovery Report')

@section('content')
    <h1 class="mb-1">
        Station Recovery Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date" placeholder="Date" data-value="{{Carbon\Carbon::today()}}">
                        </div>
                    </div>

                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <form id="recovery_form" action="{{ route('admin.reports.station_recovery.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="form_save" id="form_save">
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Station</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">No of Delivered Parcels</th>
                        <th class="border-primary border-darken-1">Last Day Balance</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Total Amount</th>
                        <th class="border-primary border-darken-1">Deposit Amount</th>
                        <th class="border-primary border-darken-1">Bank Name</th>
                        <th class="border-primary border-darken-1">ADJ/Correction</th>
                        <th class="border-primary border-darken-1">Difference</th>
                        <th class="border-primary border-darken-1">Percentage</th>
                        <th class="border-primary border-darken-1">Reason</th>
                    </tr>
                    </thead>
                </table>
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
    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
        table.dataTable tbody tr td.banks_list {
              width:230px;
          }
        table.dataTable tbody tr td.reason {
            width:200px;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    {{--    <script src="{{asset('app-assets/js/scripts/extensions/dropzone.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            var date = $('#search_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.station_recovery.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Station');
                            head.push('Contact Person');
                            head.push('No of Delivered Parcels');
                            head.push('Last Day Balance');
                            head.push('Amount');
                            head.push('Total Amount');
                            head.push('Deposit Amount');
                            head.push('Bank Name');
                            head.push('ADJ/Correction');
                            head.push('Difference');
                            head.push('Percentage');
                            head.push('Reason');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.hub);
                                row.push(values.zone);
                                row.push(values.delivered_shipments);
                                row.push(values.last_day_balance);
                                row.push(values.amount);
                                row.push(values.total_amount);
                                row.push(values.deposit_amount);
                                row.push(values.banks_list_excel);
                                row.push(values.adjustment_amount);
                                row.push(values.difference_amount);
                                row.push(values.percentage);
                                row.push(values.reason);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        title: 'Save',
                        className: 'btn btn-success save d-none',
                        text: '<i class="la la-save"></i> Save',
                        action:function (e) {
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes to update!',
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
                                if(confirm){
                                    $('#form_save').val(1);
                                    $('#recovery_form').submit();
                                }
                            });

                        }
                    },
                    {
                        title: 'Update',
                        className: 'btn btn-primary update',
                        text: '<i class="la la-edit"></i> Update',
                        action:function (e) {
                            table.button('.save').node().removeClass('d-none');
                            table.button('.update').node().addClass('d-none');
                            edit_table();
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Station Recovery Notes',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.station_recovery.list') }}',
                    data: function (d) {
                        d.search_date = $('input[name="search_date_formatted"]').val();
                    }
                },
                rowId: 'recovery_id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'hub' ,name: 'h.name', class: 'align-middle text-center hub'},
                    { data:'zone' ,name: 'zones.name', class: 'align-middle text-center zone'},
                    { data:'delivered_shipments' ,name: 'station_recovery_reports.delivered_shipments', class: 'align-middle text-center delivered_shipments'},
                    { data:'last_day_balance' ,name: 'station_recovery_reports.last_day_balance', class: 'align-middle text-center last_day_balance'},
                    { data:'amount' ,name: 'station_recovery_reports.amount', class: 'align-middle text-center amount'},
                    { data:'total_amount' ,name: 'station_recovery_reports.total_amount', class: 'align-middle text-center total_amount'},
                    { data:'deposit_amount' ,name: 'station_recovery_reports.deposit_amount', class: 'align-middle text-center deposit_amount'},
                    { data:'banks_list' ,name: 'banks_list', class: 'align-middle text-center banks_list', orderable: false, searchable: false},
                    { data:'adjustment_amount' ,name: 'station_recovery_reports.adjustment_amount', class: 'align-middle text-center adjustment_amount'},
                    { data:'difference_amount' ,name: 'station_recovery_reports.difference_amount', class: 'align-middle text-center difference_amount'},
                    { data:'percentage' ,name: 'station_recovery_reports.percentage', class: 'align-middle text-center percentage'},
                    { data:'reason' ,name: 'station_recovery_reports.reason', class: 'align-middle text-center reason'}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            function edit_table() {
                var bank_ids_array = [];
                table.rows().nodes().each(function(index) {
                    var bank_ids = '';
                    var row = table.row(index);
                    var id = parseInt(row.id());
                    var deposit_amount = $(row.node()).find('td.deposit_amount').text();
                    var deposit_amount_input = '<input class="form-control form-control-sm deposit_amount" name="deposit_amount['+ id +']" placeholder="Deposit" value="'+ deposit_amount +'">';
                    $(row.node()).find('td.deposit_amount').html(deposit_amount_input);
                    var bank_select = '<select style="width:230px;" name="bank_select['+ id +'][]" multiple="multiple" class="select2 bank_select form-control"></select>';
                    bank_ids = $(row.node()).find('td.banks_list input').val();
                    $(row.node()).find('td.banks_list').html(bank_select);

                    if(typeof bank_ids !== 'undefined'){
                        bank_ids_array[id] = bank_ids.split(',');

                    }
                    var adjustment_amount = $(row.node()).find('td.adjustment_amount').text();
                    var adjustment_amount_input = '<input class="form-control form-control-sm adjustment_amount" name="adjustment_amount['+ id +']" placeholder="ADJ Amount" value="'+ adjustment_amount +'">';
                    $(row.node()).find('td.adjustment_amount').html(adjustment_amount_input);
                    var reason = $(row.node()).find('td.reason').text();
                    var reason_input = '<textarea style="width:200px;" class="form-control form-control-sm reason" name="reason['+ id +']" placeholder="Reason">'+ reason +'</textarea>';
                    $(row.node()).find('td.reason').html(reason_input);

                });
                $('input.deposit_amount').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                    'min': 0.00,
                });
                $('input.adjustment_amount').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                    'min': 0.00,
                });
                var bank = $.map({!! $banks_lists !!}, function (obj) {
                    obj.id = obj.id;
                    obj.text = obj.name;

                    return obj;
                });
                $(".select2.bank_select").select2({
                    data:bank,
                    placeholder: "Select Bank",
                    width:'100%',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                if(bank_ids_array.length > 0){
                    $.each(bank_ids_array, function (index, val) {
                        if(typeof val !== 'undefined'){
                            $("select[name='bank_select["+ index +"][]']").val(val).trigger('change');
                        }
                    });
                }

            }
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });
    </script>
@endsection