
@extends('admin.layout.master')
@section('title','Petty Cash Expense Summary Report')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Petty Cash Expense Summary Report
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center mb-2" id="search_form">
                                <div class="col-3">
                                    <fieldset class="form-group">
                                        <select name="search_acount_title" id="search_acount_title" class="form-control select2">
                                            <option value="">Select Account Title</option>
                                            @forelse($petty_cash_account_title as $account_title)
                                                <option value="{{ $account_title->id }}">{{ $account_title->name }}</option>
                                            @empty
                                                <option value="">Account Titles Not Found!</option>
                                            @endforelse
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-3">
                                    <div class="form-group input-group ml-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>

                                        <input type="date" name="select_date_from" class="form-control bg-primary border-primary white rounded-right" id="select_date_from" placeholder="Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                                    </div>
                                </div>
                                <div class="col-3 ">
                                    <div class="form-group input-group ml-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>

                                        <input type="date" name="select_date_to" class="form-control bg-primary border-primary white rounded-right" id="select_date_to" placeholder="Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                                    </div>

                                </div>



                                <div class="col-3">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col">
                                    <b class="total_amount_span"> Total Amount : <span id="statements_total_amount">0</span></b>
                                </div>
                            </div>
                        </div>
                        <div id="petty_cash_summary_report" class="d-none mb-3 ml-1 mr-1">
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Account Head</th>
                                    <th class="border-primary border-darken-1">Account Title</th>
                                    <th class="border-primary border-darken-1">City / Location</th>
                                    <th class="border-primary border-darken-1"> Amount </th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_request_form" method="post">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number(s)</h2>
                            </div>

                            <input type="hidden" id="requested_shipment_ids">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <hr>

                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block d-none">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><div class="modal fade text-left" id="UpdateConsigneeInfoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="UpdateConsigneeInfoModal"
               aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Consignee Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="update_consignee_info_form" method="post" action="{{route('admin.cx_quick_tracking.update')}}">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number</h2>
                            </div>

                            <input type="hidden" name="update_consignee_info_shipment_id" id="update_consignee_info_shipment_id">
                            <div class="row old_scroll" id="update_consignee_info_shipment">

                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <input type="text" name="update_consignee_name" class="form-control" placeholder="Consignee Name*" id="update_consignee_name" id="update_consignee_address" data-rule-required="true" data-msg-required="Consignee Name is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <input type="text" name="update_consignee_address" class="form-control" placeholder="Consignee Address*" id="update_consignee_address" data-rule-required="true" data-msg-required="Consignee Address is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <input type="text" name="update_consignee_phone" class="form-control phone_number" id="update_consignee_phone" placeholder="Consignee Phone Number*" data-rule-required="true" data-msg-required="Consignee Phone Number is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <textarea class="form-control" name="update_special_instructions" id="update_special_instructions" rows="5" placeholder="Enter Special Instructions Here..."></textarea>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="Update_consignee_info_button" type="submit" class="btn btn-primary btn-block">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_acount_title').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Account Title',
                width: '100%',
                allowClear: true
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.petty_cash_expense_summary.petty_cash_summary_report') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Account Head');
                            head.push('Account Title');
                            head.push('City');
                            head.push('Amount');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.account_head);
                                row.push(values.account_title);
                                row.push(values.city);
                                row.push(values.amount);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header:head};
                }
            } );
            var table;
            function init(){

                table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    scrollX: false, scrollY: '500px',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            title: 'Petty Cash Expense Summary Report',
                            className: 'btn btn-primary',
                            text:'<i class="la la-file-excel-o"></i> Excel',
                        },
                    ],
                    lengthMenu: [[10, 50, 100], [10, 50, 100]],
                    pageLength: 10,
                    pagingType: 'full_numbers',
                    processing: true,
                    language: {
                        processing: data_table_loader
                    },
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.reports.petty_cash_expense_summary.petty_cash_summary_report') }}",
                        type: 'GET',
                        data: function (d) {
                            d.search_acount_title = $('#search_acount_title').val();
                            d.date_from           = $('#select_date_from').val();
                            d.date_to             = $('#select_date_to').val();
                            d._token              = "{{ csrf_token() }}";
                        }
                    },
                    rowId: 'title_id',
                    order: [1, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        {data: 'account_head', name: 'petty_cash.account_head', class: 'align-middle account_head'},
                        {data: 'account_title', name: 'petty_cash.account_title', class: 'align-middle account_title'},
                        {data: 'city', name: 'petty_cash.city', class: 'align-middle city'},
                        {data: 'amount', name: 'petty_cash.amount', class: 'align-middle amount'},
                    ],
                    rowCallback: function (row, data, index) {
                        var info = table.page.info();
                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    },
                    initComplete: function () {

                        this.api().table().columns.adjust();
                    }
                });
            }
            var total_amount = 0;
            table.rows().nodes().each(function(index) {
                var row = table.row(index);
                if($(row.node()).find('td.expense_amount input').val() != ''){

                    total_amount += parseInt($(row.node()).find('td.expense_amount input').val());
                }


            });
            $('#statements_total_amount').text(total_amount);
            $('#search_filter_btn').on('click', function () {
                if($('#petty_cash_summary_report').hasClass('d-none')) {
                    $('#petty_cash_summary_report').removeClass('d-none');
                    init();
                }
                else {
                    table.draw();
                }
            });
        });
    </script>
@endsection