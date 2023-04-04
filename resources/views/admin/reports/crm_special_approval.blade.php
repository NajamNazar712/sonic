@extends('admin.layout.master')

@section('title', 'CRM Special Approval')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    <h1 class="mb-1">
        CRM Special Approval
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number(s)">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_request_number" id="search_request_number" placeholder="Search Request Number">
                        </fieldset>
                    </div>

                    <div class="col-4">
                       
                    </div>


                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="from_date"
                                   class="form-control bg-primary border-primary white rounded-right"
                                   id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::today() }}">
                        </div>

                    </div>

                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date"
                                   class="form-control bg-primary border-primary white rounded-right"
                                   id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>

                    <div class="col-2">
                        <button type="button" id="search_filter_btn"
                                class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i>
                            Search
                        </button>
                    </div>
                    
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3; width:100%">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Special Request ID.</th>
                        <th class="border-primary border-darken-1">Request No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Requested By</th>
                        <th class="border-primary border-darken-1">Requested Date</th>
                        <th class="border-primary border-darken-1">Approved By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        {{--<th class="border-primary border-darken-1">Approval Date</th> --}}
                        <th class="border-primary border-darken-1">Adjusted Percentage</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Adjusted Amount</th>
                        <th class="border-primary border-darken-1">Remaining Amount</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sar_admins_modal" data-backdrop="static" role="dialog" aria-labelledby="sar_admins_modal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="bookings_modal_title">Special Request Approval Details</h4>

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

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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

        .rejected_requests , .rejected_requests td u a{
            background-color: rgb(247, 97, 97);
            /* color: white !important; */
        }
        
        .approved_requests{
            background-color: rgb(59, 243, 59);
            /* color: white !important; */
        }


    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var select = $('#search_tracking_no').selectize({
                    placeholder: 'Search Tracking Number(s)',
                    delimiter: ',',
                    createOnBlur: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function (dropdown) {
                        dropdown.remove();
                    },
                    onType: function (str) {
                        var regex = /^[0-9,]+$/;

                        if (!regex.test(str)) {
                            select[0].selectize.setTextboxValue('');
                        }
                    },
                    create: function (input) {
                        if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                            return {
                                value: input,
                                text: input
                            }
                        }
                        else {
                            return false;
                        }
                    }
                });

            // $('#search_tracking_no').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false
            // });

            var select = $('#search_request_number').selectize({
                placeholder: 'Search Request Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
                onType: function (str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });

           
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
                    if(params !== undefined){
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    }
                    else{
                        params = {
                            'excel':true,
                        }
                    }
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.crm_special_approval.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];
                            
                            head.push('S.No');  
                            head.push('Shipper Name');
                            head.push('Special Request ID');
                            head.push('Request No.');
                            head.push('Tracking No.');
                            head.push('Requested By');
                            head.push('Requested Date');
                            // head.push('Approval By');
                            head.push('Status');
                            head.push('Adjusted Percentage');
                            head.push('COD Amount');
                            head.push('Adjusted Amount');
                            head.push('Remaining Amount');
                            
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.shipper_name);
                                row.push(values.id);
                                row.push(values.request_number);
                                row.push(values.tracking_number);
                                row.push(values.requested_by);
                                row.push(values.requested_date);
                                // row.push(values.approved_by_excel);
                                row.push(values.approved_status);
                                row.push(values.adjusted_percentage);
                                row.push(values.cod_amount);
                                row.push(values.adjusted_amount);
                                row.push(values.remaining_amount);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );            
            var index_column = 0;

            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'CRM Special Approval Report',
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
                deferLoading: 0,
                rowId: 'id',

                ajax: {
                    url: '{{ route('admin.reports.crm_special_approval.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {

                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                        d.search_request_number = $('#search_request_number').val();

                    }
                },
                // rowId: 'shipment_id',
                order: [[4, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipper_name', name: 'shipper.name', class: 'align-middle shipper_name'},
                    {data: 'id', name: 'sar.id', class: 'align-middle id'},
                    {data: 'id_padded_link', name: 'id_padded_link', class: 'align-middle id_padded_link'},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'requested_by', name: 'sarrequestedby.name', class: 'align-middle requested_by'},
                    {data: 'requested_date', name: 'sarrequestedby.created_at', class: 'align-middle requested_date'},
                    {data: 'approved_by', name: 'sarrequestedby.created_at', class: 'align-middle text-center approved_by', orderable: false, searchable: false},
                    {data: 'approved_status', name: 'approved_status', class: 'align-middle text-center approved_status'},
                    {data: 'adjusted_percentage', name: 'sar.adjusted_percentage', class: 'align-middle adjusted_percentage'},
                    {data: 'cod_amount', name: 's.amount', class: 'align-middle cod_amount'},
                    {data: 'adjusted_amount', name: 'adjustment.adjustment_amount', class: 'align-middle adjusted_amount'},
                    {data: 'remaining_amount', name: 'remaining_amount', class: 'align-middle remaining_amount'},
                    
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click', 'button.approved_by',  function(){
                var id = $(this).parents('tr').attr('id');
                // alert(id);
                if(id){
                    $.ajax({
                        url: '{!! route('admin.reports.crm_special_approval.get_approvers') !!}',
                        data: {
                            'sar_id': id,
                        }
                    })
                        .done(function(data) {
                            if(data.status){

                                $('#sar_admins_modal').modal('show');
                                var html = `<table class="table table-bordered"> <thead class="text-center"> <tr> <th> Name </th>  <th> Department </th>  <th> Designation </th>  <th> Status </th>  <th> Approved/Reject Date </th>  <th> Reason </th> </tr>  </thead> <tbody class="text-center">`;
                                 

                                $.each(data.sar_admins, function(index, admin_data) {

                                    var status = admin_data.approved_status == 1 ? "<td class='font-weight-bold'> Pending </td>" : admin_data.approved_status == 3 ? "<td class='text-success font-weight-bold'> Approved </td>" : admin_data.approved_status == 2 ? "<td class='text-danger font-weight-bold'> Rejected </td>" : "";
                                    var date = admin_data.approved_date ? admin_data.approved_date : '-';
                                    var reason = admin_data.reason ? admin_data.reason : '-';

                                    html +=`<tr>`;
                                        html +=`<td>`+admin_data.approver+`</td>`;
                                        html +=`<td>`+admin_data.department+`</td>`;
                                        html +=`<td>`+admin_data.designation+`</td>`;
                                        html += status;
                                        html +=`<td>`+date+`</td>`;
                                        html +=`<td>`+reason+`</td>`;
                                    html +=`</td>`;
                                });

                                html += `</tbody> </table>`;

                                
                                   
                                    ;
                                $('#sar_admins_modal .modal-body').html(html);
                            }

                        });
                }
            });

            $('#search_filter_btn').on('click',function () {
                console.log('this');
                table.draw();
            });

        });

    </script>
@endsection