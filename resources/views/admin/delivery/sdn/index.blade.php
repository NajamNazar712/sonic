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
                        <th class="border-primary border-darken-1">Deposited Amount</th>

                        <th class="border-primary border-darken-1">Deposited By</th>
                        {{--<th class="border-primary border-darken-1">Company Bank</th>--}}
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
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Deposit Slip Upload</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="sdn_upload_form" class="form" action="{{route('admin.delivery.sdn.slip')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="sdn_id" id="sdn_id"/>
                            <input type="hidden" name="deposit_rows" id="deposit_rows"/>
                        <table class="table table-bordered datatable" id="sdn_upload_table" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Date</th>
                                <th class="border-primary border-darken-1">Bank Name</th>
                                <th class="border-primary border-darken-1">Amount</th>
                                <th class="border-primary border-darken-1">Deposit Slip</th>
                                <th class="border-primary border-darken-1"></th>

                            </tr>
                            </thead>
                        </table>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button id="DepositSlipButton" type="submit" class="btn btn-primary btn-block" disabled>Upload</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Deposit Slip Modal -->
    <!--Shipments popup -->
    <div class="modal fade" id="dncc_modal" data-backdrop="static" role="dialog" aria-labelledby="dncc_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="dncc_modal_title">No. Of DNCC(s)</h4>

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
    <!--Shipments popup -->
    <div class="modal fade" id="delivered_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="delivered_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delivered_shipments_modal_title">Delivered Shipment(s)</h4>

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
                                <th class="border-primary border-darken-1">Amount </th>
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
                            head.push('Deposited Amount');
                            head.push('Deposited By');
                            // head.push('Company Bank');
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
                                row.push(values.sdn_deposit_amount);
                                row.push(values.deposited_by);
                                // row.push(values.bank);
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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Station Deposit Notes',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                'reset'],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
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
                    { data:'dncc_link' ,name: 'station_deposit_notes.dncc_count', class: 'align-middle dncc_link text-center'},
                    { data:'delivered_shipments_link' ,name: 'station_deposit_notes.sdn_delivered_shipments', class: 'align-middle delivered_shipments_link text-center'},
                    { data:'sdn_amount' ,name: 'station_deposit_notes.sdn_amount', class: 'align-middle sdn_amount'},
                    { data:'sdn_deposit_amount' ,name: 'station_deposit_notes.sdn_deposit_amount', class: 'align-middle sdn_deposit_amount'},
                    // { data:'sdn_expense' ,name: 'sdn_expense', class: 'align-middle sdn_expense'},
                    // { data:'sdn_net_amount' ,name: 'station_deposit_notes.sdn_net_amount', class: 'align-middle sdn_net_amount'},
                    { data:'deposited_by' ,name: 'admins.name', class: 'align-middle deposited_by'},
                    // { data:'bank' ,name: 'banks_lists.id', class: 'align-middle bank'},
                    { data:'created_at' ,name: 'station_deposit_notes.created_at', class: 'align-middle created_at'},
                    { data:'status' ,name: 'status', class: 'align-middle status'},
                    { data:'deposit_slip' ,name: 'deposit_slip', class: 'align-middle deposit_slip',orderable: false, searchable: false},
                    { data:'action' ,name: 'action', class: 'align-middle action',orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();

                    if($('#scan_dncc').val() != ''){
                            if(data.length > 0){
                                scan_sound(1);
                            }else{
                                scan_sound(2);
                            }
                    }
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



            {{--$('#sdn_upload_form').bind('submit',function (e) {--}}
                {{--e.preventDefault();--}}

                {{--var url = '{!! route('admin.delivery.sdn.slip') !!}';--}}

                {{--var imagefile = $('#deposit_slip').val();--}}

                {{--if(!imagefile){--}}
                    {{--error = "Please select a deposit slip first!";--}}
                    {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
                {{--}else{--}}
                    {{--$.ajax({--}}
                        {{--type:'post',--}}
                        {{--url:url,--}}
                        {{--enctype: 'multipart/form-data',--}}
                        {{--processData: false,--}}
                        {{--contentType: false,--}}
                        {{--cache: false,--}}
                        {{--data: new FormData($(this)[0])--}}
                    {{--}).done(function (data) {--}}
                        {{--if(data.status == 1){--}}

                            {{--table.draw('false');--}}
                            {{--$('#deposit_slip').val('');--}}
                            {{--$('#uploadDepositSlip').modal('hide');--}}
                            {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

                        {{--}else{--}}
                            {{--$('#deposit_slip').val('');--}}
                            {{--$('#uploadDepositSlip').modal('hide');--}}
                            {{--toastr.error(data.error.deposit_slip[0], 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

                        {{--}--}}
                    {{--});--}}

                {{--}--}}
            {{--});--}}

            $('#sdn_upload_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var pressed_button = $(this.submitButton);
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to upload Deposit Slip!',
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


                            // $(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');
                            $('#deposit_rows').val(selected_rows);
                            // console.log($('#upload_image').val());
                            form.submit();
                        }
                    });

                }
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
            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.dncc_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#dncc_modal .modal-body').html('');
                $('#dncc_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.sdn.dn') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'sdn_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var notes = '<div>DNCC Number(s) :</div>';

                            if (data.delivery_notes) {
                                $.each(data.delivery_notes, function(index, value) {
                                    notes += '<u><a href="javascript:void(0);" class="dncc_print" dnid="'+value+'">'+value+'</a></u><br>';
                                });
                            }
                            $('#dncc_modal .modal-body').html(notes);


                        }
                    });

            });
            $('#datatable tbody').on('click','tr td.delivered_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#delivered_shipments_modal .modal-body').html('');
                $('#delivered_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.sdn.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'sdn_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '<div><b>Delivered Shipment(s) :</b></div>';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, value) {
                                    html += 'DNCC Number '+ index +': <br>';
                                    $.each(value, function (ind, tracking_number) {
                                        html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                    });
                                });
                            }
                            $('#delivered_shipments_modal .modal-body').html(html);

                        }
                    });

            });
            $('body').on('click','a.dncc_print',function(){
                var id = parseInt($(this).attr('dnid'));
                printDNCC(id);
            });
            function printDNCC(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.dncc.print') !!}',
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
{{--            var banks_list = @json($banks);--}}
            var banks_list = $.map({!! $banks !!}, function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
            });
            var deposit_table;
            var selected_rows = [];

            var rows_count = 0;
            $('#uploadDepositSlip').on('shown.bs.modal', function (event) {
                var id = event.relatedTarget;
                var sdn = $(id).data('target-id');
                $('#sdn_id').val($(id).data('target-id'));



                deposit_table = $('#sdn_upload_table').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons:[{
                        title: 'Add Row',
                        className: 'btn btn-primary mb-1',
                        text: '<i class="la la-plus"></i> Add Row',
                        action:function (e) {
                            add_row();
                        }
                    }],
                    ordering:false,
                    paging:false,
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        {name: 'date', class: 'align-middle date date-col-width form-group', width: '20%'},
                        {name: 'bank_name', class: 'align-middle bank_name form-group'},
                        {name: 'amount', class: 'align-middle expense_amount form-group'},
                        {name: 'deposit_slip', class: 'align-middle deposit_slip form-group'},
                        {name: 'action', class: 'align-middle action'},
                    ],

                    rowCallback: function(row, data, index) {
                        var info = deposit_table.page.info();

                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                    },
                    initComplete: function() {

                        // this.api().table().columns.adjust();
                    }
                });


                function add_row() {
                    rows_count++;
                    var date_input = '<div class="form-group input-group input-group-sm mb-0"><div class="input-group-prepend"><span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left"><span class="la la-calendar-o"></span></span></div><input type="text" name="date['+rows_count+']" id="deposit_date_' + rows_count + '" class="form-control pickadate-short-string bg-primary border-primary white rounded-right" placeholder="Date*" data-rule-required="true" data-msg-required="Date is required"></div>';
                    var bank_select = '<select class="form-control hub_select select2" name="bank['+rows_count+']" data-rule-required="true" data-msg-required="Bank is required"></select>';
                    var amount_input = '<input class="form-control form-control-sm amount" name="amount['+rows_count+']" placeholder="Amount*" data-rule-required="true" data-msg-required="Amount is required">';
                    var deposit_slip = '<input class="form-control form-control-sm" type="file" name="deposit_slip_'+rows_count+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Deposit Slip is required">';
                    if(rows_count == 1){
                        var remove = '';
                    }else{
                        var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                    }
                    // deposit_table.row.add(0,1,2,3,4,5);
                    deposit_table.row.add([0, date_input,bank_select,amount_input,deposit_slip,remove]).node().id = rows_count;
                    deposit_table.draw(true);
                    // $('#sdn_upload_table tbody').append(html);
                    $('#DepositSlipButton').attr('disabled', false);
                    selected_rows.push(rows_count);
                    $('select[name="bank['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                        data:banks_list,
                        placeholder:'Select Bank',
                        allowClear:true,
                        width:'100%',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#deposit_date_' + rows_count).pickadate({
                        firstDay: 1,
                        today: '',
                        clear: '',
                        close: '',
                        weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
                        showMonthsShort: true,
                        formatSubmit: 'yyyy-mm-dd 00:00:00',
                        hiddenSuffix: '_formatted',
                        onOpen: function() {
                            // $('#deposit_date_' + rows_count+'_root').css('top', '-262px');
                        },
                    });
                    $('input.amount').inputmask({
                        'alias': 'decimal',
                        'allowMinus': false,
                        'allowPlus': false,
                        'rightAlign': false,
                        'digits': 2,
                        'min': 0.00,
                        'max': 10000000.00
                    });
                }
            });


            $('body').on('click', 'a.remove_row',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, selected_rows);

                if (index !== -1) {
                    selected_rows.splice(index, 1);
                }
                deposit_table.row( $(this).parents('tr') ).remove().draw();
            });
            var deposit_slip_table;
            $('body').on('click','.deposit_slip_view', function () {
               var id = $(this).parents('tr').attr('id');
               if(id){
                   $.ajax({
                       url:'{!! route('admin.delivery.sdn.slip_view') !!}',
                       type:'POST',
                       data: {
                           'sdn_id':id,
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
                                    {name: 'amount', class: 'align-middle expense_amount form-group'},
                                    {name: 'deposit_slip', class: 'align-middle deposit_slip form-group'}
                                ],

                                rowCallback: function(row, data, index) {
                                    var info = deposit_slip_table.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                                initComplete: function() {

                                    // this.api().table().columns.adjust();
                                }
                            });

                            $.each(data.slips, function (index, value) {
                                deposit_slip_table.row.add([0, value.date, value.bank, value.amount, value.image]);
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
            $('#uploadDepositSlip').on('hidden.bs.modal', function () {
                deposit_table.clear();
                deposit_table.destroy();
                selected_rows = [];
            });
        });
    </script>
@endsection