@extends('admin.layout.master')
@section('title','Station Deposit Notes')

@section('content')
    <h1 class="mb-1">
        Station Deposit Notes
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Scan DNCC" name="scan_dncc" id="scan_dncc">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Search By Tracking Number" name="search_tracking" id="search_tracking">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>


                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">SDN No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">No of DNCCs</th>
                        <th class="border-primary border-darken-1">Delivered Shipments</th>
                        <th class="border-primary border-darken-1">DNCC Amount</th>
                        {{--<th class="border-primary border-darken-1">Expense</th>--}}
                        {{--<th class="border-primary border-darken-1">Net Amount</th>--}}
                        <th class="border-primary border-darken-1">Deposited By</th>
                        <th class="border-primary border-darken-1">Company Bank</th>
                        <th class="border-primary border-darken-1">Deposited Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Deposit Slip</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


    <!--Deposit Slip Modal -->
    <div class="modal fade text-left" id="uploadDepositSlip" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="uploadDepositSlip"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Deposit Slip Upload</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <form id="sdn_upload_form" class="form" action="{{route('admin.delivery.sdn.slip')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="sdn_id" id="sdn_id"/>
                    <fieldset class="form-group">
                        <input type="file" class="form-control-file" id="deposit_slip" name="deposit_slip" accept="image/*" data-rule-required="true" data-msg-required="File is required" data-rule-extension="image/jpeg|image/jpg|image/png" data-msg-extension="Only file with extension jpeg or png allowed" data-rule-accept="application/image" data-msg-accept="Only Image file allowed">
                    </fieldset>

                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button id="DepositSlipButton" type="submit" class="btn btn-primary btn-block">Upload</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Deposit Slip Modal -->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/file-uploaders/dropzone.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/file-uploaders/dropzone.css')}}">
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/file-uploaders/dropzone.css')}}">--}}
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/dropzone.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    {{--    <script src="{{asset('app-assets/js/scripts/extensions/dropzone.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.sdn.list') }}',
                        data: {
                            'page':'all',
                            'scan_dncc':$('#scan_dncc').val(),
                            'search_tracking': $('#search_tracking').val()
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('SDN No.');
                            head.push('Hub');
                            head.push('No. of DNCCs');
                            head.push('Delivered Shipments');
                            head.push('DNCC Amount');
                            head.push('Deposited By');
                            head.push('Company Bank');
                            head.push('Deposited Date');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.sdn_id_padded);
                                row.push(values.hub);
                                row.push(values.dncc_count);
                                row.push(values.sdn_delivered_shipments);
                                row.push(values.sdn_amount);
                                row.push(values.deposited_by);
                                row.push(values.bank);
                                row.push(values.created_at);
                                row.push(values.status);

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
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Station Deposit Notes',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.delivery.sdn.list') }}',
                    data: function (d) {
                        d.scan_dncc = $('#scan_dncc').val();
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'sdn_id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'sdn' ,name: 'station_deposit_notes.id', class: 'align-middle text-center sdn'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'dncc_count' ,name: 'station_deposit_notes.dncc_count', class: 'align-middle dncc_count'},
                    { data:'sdn_delivered_shipments' ,name: 'station_deposit_notes.sdn_delivered_shipments', class: 'align-middle sdn_delivered_shipments'},
                    { data:'sdn_amount' ,name: 'station_deposit_notes.sdn_amount', class: 'align-middle sdn_amount'},
                    // { data:'sdn_expense' ,name: 'sdn_expense', class: 'align-middle sdn_expense'},
                    // { data:'sdn_net_amount' ,name: 'station_deposit_notes.sdn_net_amount', class: 'align-middle sdn_net_amount'},
                    { data:'deposited_by' ,name: 'admins.name', class: 'align-middle deposited_by'},
                    { data:'bank' ,name: 'banks_lists.id', class: 'align-middle bank'},
                    { data:'created_at' ,name: 'station_deposit_notes.created_at', class: 'align-middle created_at'},
                    { data:'status' ,name: 'status', class: 'align-middle status'},
                    { data:'deposit_slip' ,name: 'deposit_slip', class: 'align-middle deposit_slip',orderable: false, searchable: false},
                    { data:'action' ,name: 'action', class: 'align-middle action',orderable: false, searchable: false},
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Created</option>' +
                        '<option value="1">Deposited</option>' +
                        '</select>';
                    var bank_select = '<select name="bank_select" id="bank_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.deposit_slip') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.bank')){
                            $(bank_select).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data = $.map({!! $banks !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $banks !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#bank_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Bank",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#search_tracking').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                if (this.value.length == 0 || this.value.length >= 12) {
                    table.draw();
                }
            });

            $('#scan_dncc').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                table.draw();
            });

            $('#sdn_upload_form').bind('submit',function (e) {
                e.preventDefault();

                var url = '{!! route('admin.delivery.sdn.slip') !!}';

                var imagefile = $('#deposit_slip').val();

                if(!imagefile){
                    error = "Please select a deposit slip first!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }else{
                    $.ajax({
                        type:'post',
                        url:url,
                        enctype: 'multipart/form-data',
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: new FormData($(this)[0])
                    }).done(function (data) {
                        if(data.status == 1){

                            table.draw('false');
                            $('#deposit_slip').val('');
                            $('#uploadDepositSlip').modal('hide');
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }else{
                            $('#deposit_slip').val('');
                            $('#uploadDepositSlip').modal('hide');
                            toastr.error(data.error.deposit_slip[0], 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        }
                    });

                }
            });
            $('#uploadDepositSlip').on('shown.bs.modal',function (event) {
                var id = event.relatedTarget;
                var sdn = $(id).data('target-id');
                $('#sdn_id').val($(id).data('target-id'));

            });

            function printSDN(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
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

            $('body').on('click','.printSDN',function () {
                var sdn = $(this).parents('tr').attr('id');
                // console.log(sdn);
                printSDN(sdn);
            });


        });
    </script>
@endsection