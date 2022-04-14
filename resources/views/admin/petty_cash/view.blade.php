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
                            <th class="border-primary border-darken-1"> Amount </th>
                            <th class="border-primary border-darken-1"> Employee Id </th>
                            <th class="border-primary border-darken-1">Name </th>
                            <th class="border-primary border-darken-1"> Designation </th>
                            <th class="border-primary border-darken-1">Reference No.</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1"> DNCC/RNCC </th>
                            <th class="border-primary border-darken-1"> Delivered Shipments</th>
                            <th class="border-primary border-darken-1">Reference Document</th>
                            <th class="border-primary border-darken-1"> Status</th>
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
                    {data:'employee_name' ,name: 'petty_cash_statement_details.employee_name', class: 'align-middle employee_name custom-col-width'},
                    {data:'employee_designation' ,name: 'petty_cash_statement_details.employee_designation', class: 'align-middle employee_designation custom-col-width'},
                    {data:'reference_no' ,name: 'petty_cash_statement_details.reference_no', class: 'align-middle reference_no'},
                    {data:'remarks' ,name: 'petty_cash_statement_details.remarks', class: 'align-middle remarks'},
                    {data:'dncc' ,name: 'petty_cash_statement_details.dncc_id', class: 'align-middle dncc custom-col-width'},
                    {data:'delivered_shipments' ,name: 'petty_cash_statement_details.delivered_shipments', class: 'align-middle delivered_shipments custom-col-width'},
                    {data:'reference_document' ,name: 'reference_document', class: 'align-middle reference_document'},
                    {data:'status' ,name: 'petty_cash_statement_details.status', class: 'align-middle status'},
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
        });
    </script>
@endsection