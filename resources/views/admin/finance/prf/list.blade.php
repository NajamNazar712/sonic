@extends('admin.layout.master')
@section('title','Payment Requisition List')

@section('content')
    <h1 class="mb-1">
        Payment Requisition List
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <!-- <form id="track_form" class="justify-content-center m-2"  novalidate="novalidate">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" name="search_date_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_from" placeholder="Approved By Finance From">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_to" placeholder="Approved By Finance To">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <button type="submit" id="search_filter_btn" class="btn btn-outline-primary btn-min-width search"><i class="la la-search"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </form> -->

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">Request ID</th>
                        <th class="border-primary border-darken-1">Requester</th>
                        <th class="border-primary border-darken-1">Requester Department</th>
                        <th class="border-primary border-darken-1">Requested for Department </th>
                        <th class="border-primary border-darken-1">Requested Date</th>
                        <th class="border-primary border-darken-1">On Account of</th>
                        <th class="border-primary border-darken-1">Invoice or Document ID</th>
                        <th class="border-primary border-darken-1">Payee or Beneficiary Name</th>
                        <th class="border-primary border-darken-1">CNIC/NTN</th>
                        <th class="border-primary border-darken-1">Bank Name</th>
                        <th class="border-primary border-darken-1">Bank Title</th>
                        <th class="border-primary border-darken-1">Bank Account/IBAN</th>
                        <th class="border-primary border-darken-1">Particular or Description</th>
                        <th class="border-primary border-darken-1">Check/Instrument No.</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <!-- <th class="border-primary border-darken-1">Status Date</th> -->
                        <!-- <th class="border-primary border-darken-1">Documents</th> -->
                        <th class="border-primary border-darken-1">Required Approval</th>
                        <th class="border-primary border-darken-1">Approved By</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1"></th>

                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="addChequeModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="addChequeModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Add Cheque No.</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.finance.prf.add_cheque')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="addChequeForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="request_id" id="request_id" value="">
                        <div class="form-group">
                            <input type="number" name="cheque_no" id="cheque_no" class="form-control" placeholder="Cheque No.*" data-rule-required="true" data-msg-required="Cheque no is required">
                        </div>
                       
                        <div class="form-group">
                            <button type="submit" name="edit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="viewJourneyModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="viewJourneyModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Payment Requisition Journey </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center" id="journey_table">

                </div>
                <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="viewApprovalLogsModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="viewApprovalLogsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Payment Requisition Approval Logs </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center" id="approval_logs_table">

                </div>
                <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Cancellation Remarks Modal -->
    <div class="modal fade" id="cancelRemarksModal" tabindex="-1" role="dialog" aria-labelledby="cancelRemarksLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header bg-primary">
            <h5 class="modal-title text-white" id="cancelRemarksLabel">Cancel Requisition</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="cancel_req_id">
            <div class="form-group">
            <label>Cancellation Remarks <span class="text-danger">*</span></label>
            <textarea id="cancel_remarks" class="form-control" rows="3" placeholder="Enter reason for cancellation..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" id="confirmCancelBtn">Submit</button>
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
        #datatable td.date{
            min-width:110px;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // $('#track_form #search_date_from').pickadate({
            //     firstDay: 1,
            //     clear: '',
            //     selectYears: true,
            //     selectMonths: true,
            //     formatSubmit: 'yyyy-mm-dd 00:00:00',
            //     hiddenSuffix: '_formatted',
            //     onSet: function(context) {
            //         if (context.select) {
            //             $('#track_form #search_date_to').pickadate('picker').set('min', $('#track_form #search_date_from').pickadate('picker').get('select'));
            //         }
            //     }
            // });

            // $('#track_form #search_date_to').pickadate({
            //     firstDay: 1,
            //     clear: '',
            //     max: '{{ Carbon\Carbon::now() }}',
            //     format:'dd mmmm, yyyy',
            //     selectYears: true,
            //     selectMonths: true,
            //     formatSubmit: 'yyyy-mm-dd 23:59:59',
            //     hiddenSuffix: '_formatted',
            //     onSet: function(context) {
            //         if (context.select) {
            //             $('#track_form #search_date_from').pickadate('picker').set('max', $('#track_form #search_date_to').pickadate('picker').get('select'));
            //         }
            //     }
            // });
            
            const loggedInUserId = {{ Auth::id() }};
            const userPermissions = @json(session('permissions'));
            const  userRoleId = {{ session('role_id') }};


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.prf.ajaxList') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Request ID');
                            head.push('Requester');
                            head.push('Requester Department');
                            head.push('Requested for Department');
                            head.push('Requested Date');
                            head.push('On Account of');
                            head.push('Invoice or Document ID');
                            head.push('Payee or Beneficiary Name');
                            head.push('CNIC/NTN');
                            head.push('Bank Name');
                            head.push('Bank Title');
                            head.push('Bank Account/IBAN');
                            head.push('Particular or Description');
                            head.push('Check/Instrument No.');
                            head.push('Status');
                            head.push('Required Approval');
                            head.push('Approved By');
                            head.push('Amount');
                                    
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1); // S.No
                                row.push(values.request_id); // Request ID
                                row.push(values.requester_name); // Requester
                                row.push(values.requester_department); // Requester Department
                                row.push(values.requested_for_department); // Requested for Department
                                row.push(values.created_at); // Requested Date
                                row.push(values.account_of_name); // On Account of
                                row.push(values.invoice_no); // Invoice or Document ID
                                row.push(values.payee_name); // Payee or Beneficiary Name
                                row.push(values.ntn_cnic);
                                row.push(values.bank_name);
                                row.push(values.bank_title);
                                row.push(values.iban);
                                row.push(values.description); // Particular or Description
                                row.push(values.cheque_no); // Check/Instrument No.
                                row.push(values.status_name); // Status
                                row.push(values.required_approvals); // Required Approval
                                row.push(values.approved_by); // Required Approval
                                row.push(values.amount); // Amount
                             
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
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
                               
                                table.button('.approved').enable();
                                table.button('.complete').enable();

                                
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
                                    table.button('.approved').disable();
                                    table.button('.complete').disable();

                                }
                            }
                        });
                    }
                },{
                    text: '<i class="la la-cogs"></i> Approve',
                    className: 'btn btn-primary approved',
                    enabled:false,
                    action: function (e, dt, node, config) {

                        if(selected_rows.length === 0){
                            table.button('.approved').disable();
                            return false;
                        }
                        else if(selected_rows !== ''){
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select yes to approved selected requests.!',
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
                                    console.log(selected_rows);
                                    $.ajax({
                                        url: '{!! route('admin.finance.prf.approve') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'ids': selected_rows
                                        }
                                    }).done(function(data){
                                        if(data.status){
                                            table.rows().deselect();
                                            selected_rows = [];
                                            table.button('.approved').disable();
                                            table.draw(true);
                                            toastr.success(data.message, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                        }

                                    });
                                }
                            });

                        }else{
                            var error = ' Please Try again!';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            table.button('.approved').disable();
                        }
                    }
                }, @if(in_array(1049, session('permissions')) || session('role_id') == 1){

                    text: '<i class="la la-cogs"></i> Completed/Done',
                    className: 'btn btn-primary complete',
                    enabled:false,
                    action: function (e, dt, node, config) {

                        if(selected_rows.length === 0){
                            table.button('.complete').disable();
                            return false;
                        }
                        else if(selected_rows !== ''){
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select yes to mark as completed.!',
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
                                    console.log(selected_rows);
                                    $.ajax({
                                        url: '{!! route('admin.finance.prf.complete') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'ids': selected_rows
                                        }
                                    }).done(function(data){
                                        if (data.status) {
                                            table.rows().deselect();
                                            selected_rows = [];
                                            table.button('.complete').disable();

                                            let completedList = '';
                                            let skippedList = '';

                                            if (data.completed_ids.length > 0) {
                                                completedList = '<strong style="color:green;">Completed:</strong> ' + data.completed_ids.join(', ') + '<br>';
                                            }

                                            if (data.skipped_ids.length > 0) {
                                                skippedList = '<strong style="color:red;">Skipped (not valid status):</strong> ' + data.skipped_ids.join(', ');
                                            }

                                            let msg = completedList + skippedList;
                                            
                                            swal({
                                                title: "Process Summary",
                                                content: {
                                                    element: "div",
                                                    attributes: {
                                                        innerHTML: msg
                                                    }
                                                },
                                                icon: "info",
                                                buttons: {
                                                    confirm: {
                                                        text: "OK",
                                                        value: true,
                                                        visible: true,
                                                        className: "btn-primary",
                                                        closeModal: true
                                                    }
                                                }
                                            }).then(function () {
                                                // redraw after alert is closed
                                                table.draw(true);
                                            });
                                        }

                                    });
                                }
                            });

                        }else{
                            var error = ' Please Try again!';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            table.button('.complete').disable();
                        }
                    }
                }@endif
                ,{
                    extend: 'excel',
                    title: 'PRF',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.finance.prf.ajaxList') }}',
                    data: function (d) {
                        // d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        // d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        // d.rider = $('select[name="rider"]').val();
                    }
                },
                rowId: 'id',
                order: [1, 'desc'],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id', name: 'payment_requisitions.id', class: 'align-middle request_id'},
                    {data: 'requester_name', name: 'u.name', class: 'align-middle requester_name'},
                    {data: 'requester_department', name: 'ud.name', class: 'align-middle requester_department'},
                    {data: 'requested_for_department', name: 'rd.name', class: 'align-middle requested_for_department'},
                    {data: 'created_at', name: 'payment_requisitions.created_at', class: 'align-middle created_at'},
                    {data: 'account_of_name', name: 'ao.name', class: 'align-middle account_of_name'},
                    {data: 'invoice_no', name: 'payment_requisitions.invoice_no', class: 'align-middle invoice_no'},
                    {data: 'payee_name', name: 'payment_requisitions.payee_name', class: 'align-middle payee_name'},

                    {data: 'ntn_cnic', name: 'payment_requisitions.ntn_cnic', class: 'align-middle ntn_cnic'},
                    {data: 'bank_name', name: 'b.name', class: 'align-middle bank_name'},
                    {data: 'bank_title', name: 'payment_requisitions.bank_title', class: 'align-middle bank_title'},
                    {data: 'iban', name: 'payment_requisitions.iban', class: 'align-middle iban'},

                    {data: 'description', name: 'payment_requisitions.description', class: 'align-middle description'},
                    {data: 'cheque_no', name: 'payment_requisitions.cheque_no', class: 'align-middle cheque_no'},
                    {data: 'status_name', name: 'status.name', class: 'align-middle status text-center status_name'},
                    // {data: 'updated_at', name: 'payment_requisitions.updated_at', class: 'align-middle status_date'},
                    // {data: 'documents', name: 'documents', class: 'align-middle documents'},
                    {data: 'required_approvals', name: 'required_approvals', class: 'align-middle required_approvals'},
                    {data: 'approved_by', name: 'approved_by', class: 'align-middle approved_by'},
                    {data: 'amount', name: 'payment_requisitions.amount', class: 'align-middle text-end amount'},
                    {data: 'action', orderable: false, searchable: false, class: 'text-center align-middle action p-1'},
                    { data: 'can_approve', name: 'can_approve', visible: false },
                    { data: 'requester_id', name: 'payment_requisitions.requester_id', visible: false },
                    { data: 'status', name: 'payment_requisitions.status', visible: false },


                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    //$('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    //$('td:eq(0)', row).addClass('select-checkbox');
                     if (data.can_approve == 1 || ( userPermissions.includes(1049) && data.status == 5  ) || userRoleId == 1 ) {
                        $('td:eq(0)', row).addClass('select-checkbox');
                    } else {
                        $('td:eq(0)', row).removeClass('select-checkbox');
                    }

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                    
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        // if ($(header).is('.serial_number') ||  $(header).is('.action') || $(header).is('.select') || $(header).is('.sdn_update_logs') ) {
                        //     $(td).appendTo($(search));
                        // }
                        // else {
                        //     var current = $(input).appendTo($(search)).on('change', function() {
                        //         column.search($(this).val(), false, false, true).draw();
                        //     }).wrap(td).after(icon);

                        //     if (column.search()) {
                        //         current.val(column.search());
                        //     }
                        // }
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.approved').enable();
                    table.button('.complete').enable();
                    
                }
                else {
                    table.button('.approved').disable();
                    table.button('.complete').disable();

                }
            });


            $('body').on('click','button.add_cheque',function () {
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $('#request_id').val(id);
                    $('#addChequeModal').modal('show');
                }
            });

            $("#addChequeForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Cheque No is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });



            $('body').on('click', 'button.cancel_requisition', function () {
                var id = $(this).closest('tr').attr('id');

                if (id) {
                    $('#cancel_req_id').val(id);
                    $('#cancel_remarks').val('');
                    $('#cancelRemarksModal').modal('show');
                } else {
                    toastr.error('Statement ID Not Found, Please Try again!', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
            });

            // Handle confirm button inside modal
            $('#confirmCancelBtn').on('click', function () {
                var id = $('#cancel_req_id').val();
                var remarks = $('#cancel_remarks').val().trim();

                if (remarks === '') {
                    toastr.error('Please enter cancellation remarks!', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return;
                }

                $.ajax({
                    url: '{!! route('admin.finance.prf.cancel') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id,
                        'remarks': remarks
                    }
                }).done(function (data) {
                    if (data.status) {
                        $('#cancelRemarksModal').modal('hide');
                        table.draw(true);
                        toastr.success(data.message, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });



            $('body').on('click','button.view_journey',function () {               
                var id = $(this).parents('tr').attr('id');
                if(id){
                    //$('#request_id').val(id);

                    $.ajax({
                        url: '{!! route('admin.finance.prf.journey') !!}',
                        method: 'GET',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if(!data.status){
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }else{
                            var log_table = '';
                            if (data && data.data.length > 0) {
                                log_table = '<table class="table table-sm datatable">';
                                log_table += '<thead>';
                                log_table += '<tr>';
                                log_table += '<th><strong>Request ID</strong></th>';
                                log_table += '<th><strong>Status</strong></th>';
                                log_table += '<th><strong>Remarks</strong></th>';
                                log_table += '<th><strong>Updated By</strong></th>';
                                log_table += '<th><strong>Created At</strong></th>';
                                log_table += '</tr>';
                                log_table += '</thead>';
                                log_table += '<tbody>';

                                data.data.forEach(function (item) {
                                    log_table += '<tr>';
                                    log_table += '<td>' + (item.request_id ?? '-') + '</td>';
                                    log_table += '<td>' + (item.status ?? '-') + '</td>';
                                    log_table += '<td>' + (item.remarks ?? '-') + '</td>';
                                    log_table += '<td>' + (item.updated_by ?? '-') + '</td>';
                                    log_table += '<td>' + (item.created ?? '-') + '</td>';
                                    log_table += '</tr>';
                                });

                                log_table += '</tbody></table>';
                            } else {
                                log_table = '<p class="text-muted">No log entries found.</p>';
                            }

                            $('#journey_table').html(log_table);
                            $('#viewJourneyModal').modal('show');
                        }
                    });
                    
                }
            });


            $('body').on('click','button.approval_logs',function () {               
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $.ajax({
                        url: '{!! route('admin.finance.prf.approval_logs') !!}',
                        method: 'GET',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if(!data.status){
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }else{
                            var log_table = '';
                            if (data && data.data.length > 0) {
                                log_table = '<table class="table table-sm datatable">';
                                log_table += '<thead>';
                                log_table += '<tr>';
                                log_table += '<th><strong>Request ID</strong></th>';
                                log_table += '<th><strong>Level</strong></th>';
                                log_table += '<th><strong>Status</strong></th>';
                                log_table += '<th><strong>Updated By</strong></th>';
                                log_table += '<th><strong>Updated At</strong></th>';
                                log_table += '</tr>';
                                log_table += '</thead>';
                                log_table += '<tbody>';

                                data.data.forEach(function (item) {
                                    log_table += '<tr>';
                                    log_table += '<td>' + (item.request_id ?? '-') + '</td>';
                                    log_table += '<td>' + (item.level ?? '-') + '</td>';
                                    log_table += '<td>' + (item.status ?? '-') + '</td>';
                                    log_table += '<td>' + (item.approved_by ?? '-') + '</td>';
                                    log_table += '<td>' + (item.approved_at ?? '-') + '</td>';
                                    log_table += '</tr>';
                                });

                                log_table += '</tbody></table>';
                            } else {
                                log_table = '<p class="text-muted">No log entries found.</p>';
                            }

                            $('#approval_logs_table').html(log_table);
                            $('#viewApprovalLogsModal').modal('show');
                        }
                    });
                }
            });

        });
    </script>
@endsection