@extends('admin.layout.master')

@section('title', 'Outstanding Shipments Report')

@section('content')
    <h1 class="mb-1">
        Outstanding Shipments Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="search_form" class=" mb-1 justify-content-center" novalidate="novalidate">

                <div class="row mb-2 justify-content-center">
                        <div class="col-2">
                            <div class="form-group mb-1">
                                <select name="search_recovery_status" class="select2" id="recovery_status_select" data-rule-required="true" data-msg-required="Status is required">
                                    {{--<option value="0">All</option>--}}
                                    <option value="1" selected="selected">Outstanding</option>
                                    <option value="7">Resolved</option>
                                    <option value="11">Revert Requested</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="form-group mb-1">
                                <select name="hub" class="select2" id="hub">


                                    @foreach($hubs as $hub)
                                        <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                                </div>

                                <input type="text" name="delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="delivery_date_from" placeholder="Delivery Date (From)">
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                                </div>

                                <input type="text" name="delivery_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="delivery_date_to" placeholder="Delivery Date (To)">
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="form-group">
                                <button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
                            </div>
                        </div>


                </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Consignee</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Recovery Status</th>
                        <th class="border-primary border-darken-1">Current Status</th>
                        <th class="border-primary border-darken-1">Payment Status</th>
                        <th class="border-primary border-darken-1">Operation Status Date/Time</th>
                        <th class="border-primary border-darken-1">Verification Status Date/Time</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">DNCC</th>
                        <th class="border-primary border-darken-1">SDN</th>
                        <th class="border-primary border-darken-1">Aging</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
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
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #recovery_status_select').select2({
                width: '100%',
                placeholder: 'Recovery Status*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#search_form #hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Hub',
                allowClear:true,
            }).bind('change', function() {
                table.draw();
            });

            $('#search_form #delivery_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #delivery_date_to').pickadate('picker').set('min', $('#search_form #delivery_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #delivery_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #delivery_date_from').pickadate('picker').set('max', $('#search_form #delivery_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form').on('submit',function (e) {
                e.preventDefault();
                table.draw();
            });
            // $('#search_form').validate({
            //     errorClass: 'danger',
            //     successClass: 'success',
            //     errorPlacement: function(error, element) {
            //         error.addClass('w-100').appendTo(element.parents('form'));
            //     },
            //     submitHandler: function(form) {
            //         table.draw();
            //
            //         return false;
            //     }
            // });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.outstanding_shipments.list') }}',
                        data:{
                            'page': 'all',
                            'search_recovery_status': $('#search_form #recovery_status_select').val(),
                            'hub': $('#search_form #hub').val(),
                            'delivery_date_from': $('#search_form input[name="delivery_date_from_formatted"]').val(),
                            'delivery_date_to': $('#search_form input[name="delivery_date_to_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Tracking Number');
                            head.push('Consignee');
                            head.push('Address');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Account No.');
                            head.push('Shipper');
                            head.push('Service Type');
                            head.push('Amount');
                            head.push('Recovery Status');
                            head.push('Current Status');
							head.push('Payment Status');
                            head.push('Operation Status Date/Time');
                            head.push('Verification Status Date/Time');
                            head.push('Rider Name');
                            head.push('Remarks');
                            head.push('DNCC');
                            head.push('SDN');
                            head.push('Aging');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.consignee);
                                row.push(values.address);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.account_no);
                                row.push(values.shipper);
                                row.push(values.service_type);
                                row.push(values.amount);
                                row.push(values.recovery_status);
                                row.push(values.current_status);
                                row.push(values.payment_status);
                                row.push(values.operation_status_date);
                                row.push(values.verification_status_date);
                                row.push(values.rider_name);
                                row.push(values.remarks);
                                row.push(values.dncc);
                                row.push(values.sdn);
                                row.push(values.aging);


                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );

            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Outstanding Shipments Report',
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
                    url: '{{ route('admin.reports.outstanding_shipments.list') }}',
                    data: function (d) {
                        d.search_recovery_status = $('#search_form #recovery_status_select').val();
                        d.hub = $('#search_form #hub').val();
                        d.delivery_date_from = $('#search_form input[name="delivery_date_from_formatted"]').val();
                        d.delivery_date_to = $('#search_form input[name="delivery_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[13, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'tracking_number_link', name: 's.tracking_number', class: 'align-middle text-center tracking_number'},
                    {data:'consignee', name: 's.consignee_name', class: 'align-middle text-center consignee'},
                    {data:'address', name: 's.consignee_address', class: 'align-middle text-center address'},
                    {data:'destination', name: 'dc.name', class: 'align-middle text-center destination'},
                    {data:'hub', name: 'hc.name', class: 'align-middle text-center hub'},
                    {data:'account_no', name: 'u.id', class: 'align-middle text-center account_no'},
                    {data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
                    {data:'service_type', name: 'bt.booking_type', class: 'align-middle text-center service_type'},
                    {data:'amount', name: 's.amount', class: 'align-middle text-center amount'},
                    {data:'recovery_status', name: 'ss.name as status', class: 'align-middle text-center recovery_status', orderable: false, searchable: false},
                    {data:'current_status', name: 'ss.name', class: 'align-middle text-center current_status'},
                    {data:'payment_status', name: 'sps.name', class: 'align-middle text-center payment_status'},
                    {data:'operation_status_date', name: 'sod.created_at', class: 'align-middle text-center operation_status_date'},
                    {data:'verification_status_date', name: 'svd.created_at', class: 'align-middle text-center verification_status_date'},
                    {data:'rider_name', name: 'rider.name', class: 'align-middle text-center rider_name'},
                    {data:'remarks', name: 'sj.remarks', class: 'align-middle text-center remarks'},
                    {data:'dncc_link', name: 'delivery_note_shipments.delivery_note_id', class: 'align-middle text-center dncc_link'},
                    {data:'sdn_link', name: 'dnsdn.station_deposit_note_id', class: 'align-middle text-center sdn_link'},
                    {data:'aging', name: 'aging', class: 'align-middle text-center aging', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {

                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            function printDNCC(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.dncc.print') !!}',
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
            $('#datatable tbody').on('click', 'tr td.dncc_link button.print', function() {
                var delivery_note_id = parseInt($(this).parents('tr').data('dncc'));

                printDNCC(delivery_note_id);
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
            $('#datatable tbody').on('click', 'tr td.sdn_link button.print', function() {
                var sdn = parseInt($(this).parents('tr').data('sdn'));

                printSDN(sdn);
            });
        });

    </script>
@endsection