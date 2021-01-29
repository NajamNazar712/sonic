@extends('admin.layout.master')
@section('title','Month Closing Report-Pivot')


@section('content')
    <h1 class="mb-1">
        Month Closing Report-Pivot
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Category</th>
                        <th class="border-primary border-darken-1">Responsible Person</th>
                        <th class="border-primary border-darken-1">Tracking No(s).</th>
                        <th class="border-primary border-darken-1">Current Status</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Number</th>
                        <th class="border-primary border-darken-1">Claim ID </th>
                        <th class="border-primary border-darken-1">Claim Type</th>
                        <th class="border-primary border-darken-1">Closing Type</th>
                        <th class="border-primary border-darken-1">Consignee Address</th>
                        <th class="border-primary border-darken-1">Comments</th>
                        <th class="border-primary border-darken-1">Closing Status</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.month_closing.pivot.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Category');
                            head.push('Responsible Person');
                            head.push('Tracking No(s).');
                            head.push('Current Status');
                            head.push('COD Amount');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Shipper');
                            head.push('Consignee Name');
                            head.push('Number');
                            head.push('Claim ID');
                            head.push('Claim Type');
                            head.push('Closing Type');
                            head.push('Consignee Address');
                            head.push('Comments');
                            head.push('Closing Status');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.category);
                                row.push(values.responsible_person);
                                row.push(values.shipment_count);
                                row.push(values.current_status);
                                row.push(values.cod_amount);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.shipper);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
                                row.push(values.claim_id);
                                row.push(values.claim_type);
                                row.push(values.closing_type);
                                row.push(values.consignee_address);
                                row.push(values.remarks);
                                row.push(values.closing_status);


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

                buttons:[{
                    extend: 'excel',
                    title: 'Month Closing Report-Individual',
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
                serverSide: true,
                ajax: '{{ route('admin.reports.month_closing.pivot.list') }}',
                rowId: 'responsible_person_id',
                order: [[2, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'category', name: 'mcr.admin', class: 'align-middle category'},
                    {data: 'responsible_person', name: 'rp.name', class: 'align-middle responsible_person'},
                    {data: 'shipment_count', name: 'shipment_count', class: 'align-middle shipment_count'},
                    {data: 'current_status', name: 'ss.name', class: 'align-middle current_status'},
                    {data: 'cod_amount', name: 'shipments.amount', class: 'align-middle cod_amount'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 'consignee_phone', class: 'align-middle consignee_phone'},
                    {data: 'claim_id_link', name: 'cr.id', class: 'align-middle claim_id_link'},
                    {data: 'claim_type', name: 'crn.type', class: 'align-middle claim_type'},
                    {data: 'closing_type', name: 'mct.name', class: 'align-middle closing_type'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'remarks', name: 'mc.remarks', class: 'align-middle remarks'},
                    {data: 'closing_status', name: 'mcs.name', class: 'align-middle closing_status'},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

           /* $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                if(shipment_id){
                    if ($(this).hasClass('shipment_count_popup')) {

                    }
                }
            });*/

        });
    </script>
@endsection
