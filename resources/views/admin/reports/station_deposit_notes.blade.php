@extends('admin.layout.master')
@section('title','Station Deposit Notes Report')

@section('content')
    <h1 class="mb-1">
        Station Deposit Notes Report
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

                        <th class="border-primary border-darken-1">Deposited Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Adjustment Date</th>
                        <th class="border-primary border-darken-1">Adjustment Amount</th>
                        <th class="border-primary border-darken-1">Adjustment Reference</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

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
                        url: '{{ route('admin.reports.sdn.list') }}',
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
                            head.push('Deposited Date');
                            head.push('Status');
                            head.push('Adjustment Date');
                            head.push('Adjustment Amount');
                            head.push('Adjustment Reference');

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
                                row.push(values.adjustment_date);
                                row.push(values.adjustment_amount);
                                row.push(values.adjustment_ref);

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
                    url: '{{ route('admin.reports.sdn.list') }}',
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
                    { data:'adjustment_date' ,name: 'station_deposit_notes.adjustment_date', class: 'align-middle adjustment_date'},
                    { data:'sdn_adjustment_amount' ,name: 'station_deposit_notes.adjustment_amount', class: 'align-middle adjustment_amount'},
                    { data:'adjustment_ref' ,name: 'station_deposit_notes.adjustment_ref', class: 'align-middle adjustment_ref'},
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

                printSDN(sdn);
            });
            var route = '{!! route('admin.tracking.index') !!}';
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

        });
    </script>
@endsection