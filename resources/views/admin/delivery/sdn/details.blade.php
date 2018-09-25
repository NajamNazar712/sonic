@extends('admin.layout.master')
@section('title','Station Delivery Note')

@section('content')
    <h1 class="mb-1">
        Station Delivery Note ({{$sdn_id}})
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">DNCC No.</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            <th class="border-primary border-darken-1">Rider</th>
                            <th class="border-primary border-darken-1">Route</th>
                            <th class="border-primary border-darken-1">No. Of Shipments</th>
                            <th class="border-primary border-darken-1">No. Of Shipments Delivered</th>
                            <th class="border-primary border-darken-1">DNCC Amount</th>
                            {{--<th class="border-primary border-darken-1">Net Amount</th>--}}
                            <th class="border-primary border-darken-1">Remarks</th>
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.sdn.ajax',['id'=>$sdn_id]) }}',
                        data: {
                            'page': 'all'
                        },
                        success: function (result) {
                            head = [];
                            head.push('S. No');
                            head.push('DNCC No.');
                            head.push('Hub');
                            head.push('Rider');
                            head.push('Route');
                            head.push('No. Of Shipments');
                            head.push('No. Of Shipments Delivered');
                            head.push('DNCC Amount');
                            // head.push('Net Amount');
                            head.push('Remarks');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.dncc);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.route);
                                row.push(values.shipments_count);
                                row.push(values.delivered_shipments);
                                row.push(values.received_cod_amount);
                                // row.push(values.net_amount);
                                row.push(values.remarks);

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
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'Station Deposit Note',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.delivery.sdn.ajax',['id'=>$sdn_id]) }}',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'dncc' ,name: 'delivery_notes.id', class: 'align-middle text-center dncc'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'route' ,name: 'route', class: 'align-middle route'},
                    { data:'shipments_count' ,name: 'delivery_notes.shipments_count', class: 'align-middle shipments_count'},
                    { data:'delivered_shipments' ,name: 'delivery_notes.delivered_shipments', class: 'align-middle delivered_shipments'},
                    { data:'received_cod_amount' ,name: 'delivery_notes.received_cod_amount', class: 'align-middle received_cod_amount'},
                    // { data:'expense' ,name: 'expense', class: 'align-middle expense'},
                    // { data:'net_amount' ,name: 'delivery_notes.net_amount', class: 'align-middle net_amount'},
                    { data:'remarks' ,name: 'delivery_notes.remarks', class: 'align-middle remarks'},
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

                        if ($(header).is('.serial_number')) {
                            $(td).appendTo($(search));
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
                    this.api().table().columns.adjust();
                }
            });


        });
    </script>
@endsection