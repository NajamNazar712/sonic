@extends('admin.layout.master')

@section('title', 'Rider Consignments No list')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Rider Consignments No list
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th  class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Employee ID</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1">Consignment No</th>
{{--                                    <th class="border-primary border-darken-1">Barcode</th>--}}
                                    <th class="border-primary border-darken-1">Status</th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
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

        .small-calender-icon {
            font-size: 17px !important;
        }

        .bg-gradient-directional-booked_shipments {
            background-image: linear-gradient(45deg, #5e187b, #ed86ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-arrived_shipments {
            background-image: linear-gradient(45deg, #074077, #2fbef5);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-in_transit {
            background-image: linear-gradient(45deg, #535BE2, #9ea5ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-destination {
            background-image: linear-gradient(45deg, #027d8a, #01e4e4);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-out_for_delivery {
            background-image: linear-gradient(45deg, #ff9819, #fff824);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_shipments {
            background-image: linear-gradient(45deg, #39546d, #90929a);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_confirmation {
            background-image: linear-gradient(45deg, #6a1fa2, #ff4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-delivered {
            background-image: linear-gradient(45deg, #076500, #11f118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_confirm {
            background-image: linear-gradient(45deg, #ff0c0c, #ff9191);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_return {
            background-image: linear-gradient(45deg, #7d491c, #e0b668de);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_delivered {
            background-image: linear-gradient(45deg, #02c123, #99ff12d1);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-cancelled_shipments {
            background-image: linear-gradient(45deg, #ff6a00, #ffb74c);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_launched {
            background-image: linear-gradient(45deg, #074077, #2FBEF5);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_in_process {
            background-image: linear-gradient(45deg, #6A1FA2, #FF4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_closed {
            background-image: linear-gradient(45deg, #076500, #11F118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_rejected {
            background-image: linear-gradient(45deg, #FF0C0C, #FF9191);
            background-repeat: repeat-x;
        }

        .selectize-control {
            width: 300px !important;
        }

        .div_border {
            border-style: double;
        }

        .statusBooked {
            background-color: #5DADE2;
        }

        .statusOrigin {
            background-color: #E67E22;
        }

        .statusIntransit {
            background-color: #7F8C8D;
        }

        .statusDestination {
            background-color: #F1C40F;
        }

        .statusNotattempted {
            background-color: #1F618D;
        }

        .statusDeliveryunsuccessful {
            background-color: #28B463;
        }

        .statusOnhold {
            background-color: #154360;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {


            // var selected_rows = [];

            {{--jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {--}}
            {{--    if (this.context.length) {--}}
            {{--        body = [];--}}
            {{--        var params = table.ajax.params();--}}
            {{--        params.start = 0;--}}
            {{--        params.length = -1;--}}
            {{--        params.excel = true;--}}
            {{--        var jsonResult = $.ajax({--}}
            {{--            url: '{{ route('admin.leads.list') }}',--}}
            {{--            data: params,--}}
            {{--            success: function (result) {--}}
            {{--                head = [];--}}
            {{--                head.push('S.No');--}}
            {{--                head.push('Lead ID');--}}
            {{--                head.push('Contact Person');--}}
            {{--                head.push('City');--}}
            {{--                head.push('Territory');--}}
            {{--                head.push('Area');--}}
            {{--                head.push('Phone No');--}}
            {{--                head.push('Email Address');--}}
            {{--                head.push('Service');--}}
            {{--                head.push('Brand');--}}
            {{--                head.push('Company');--}}
            {{--                head.push('Lead Reference');--}}
            {{--                head.push('Requested Date/Time');--}}
            {{--                head.push('Aging');--}}
            {{--                head.push('Sale Person Tagged');--}}
            {{--                head.push('Sale Person Tagged At');--}}
            {{--                head.push('Sale Person Tagged Aging');--}}
            {{--                head.push('Reference Person');--}}
            {{--                head.push('Lead Status');--}}
            {{--                head.push('Reason');--}}
            {{--                head.push('Call Status');--}}
            {{--                head.push('Updated By');--}}
            {{--                head.push('Updated At');--}}

            {{--                $.each(result.data, function (index, values) {--}}
            {{--                    row = [];--}}


            {{--                    row.push(index + 1);--}}
            {{--                    row.push(values.lead_id);--}}
            {{--                    row.push(values.contact_person);--}}
            {{--                    row.push(values.city);--}}
            {{--                    row.push(values.territory);--}}
            {{--                    row.push(values.area);--}}
            {{--                    row.push(values.phone_number);--}}
            {{--                    row.push(values.email_address);--}}
            {{--                    row.push(values.service);--}}
            {{--                    row.push(values.brand);--}}
            {{--                    row.push(values.company);--}}
            {{--                    row.push(values.lead_reference);--}}
            {{--                    row.push(values.requested_date);--}}
            {{--                    row.push(values.aging);--}}
            {{--                    row.push(values.sale_person);--}}
            {{--                    row.push(values.sale_person_updated_at);--}}
            {{--                    row.push(values.sale_person_tagged_aging);--}}
            {{--                    row.push(values.reference_person);--}}
            {{--                    row.push(values.status);--}}
            {{--                    row.push(values.reason_id);--}}
            {{--                    row.push(values.call_status);--}}
            {{--                    row.push(values.updated_by);--}}
            {{--                    row.push(values.updated_at);--}}

            {{--                    body.push(row);--}}
            {{--                });--}}
            {{--            },--}}
            {{--            async: false--}}
            {{--        });--}}

            {{--        return {body: body, header: head};--}}
            {{--    }--}}
            {{--});--}}
            var selected_rows = [];
            var issue_id = {!! isset($issue_id) ? $issue_id : 0 !!};
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
{{--                @if (session('role_id') == 1 || count(array_intersect([964], session('permissions'))) !== 0)--}}

{{--                buttons: [--}}
{{--                    // {--}}
{{--                    //     text: '<i class="la la-print"></i> Print',--}}
{{--                    //     className: 'btn btn-primary print',--}}
{{--                    //     enabled: false,--}}
{{--                    //     action: function (e, dt, node, config) {--}}
{{--                    //         table.button(1).disable();--}}
{{--                    //         print(selected_rows);--}}
{{--                    //         table.rows().deselect();--}}
{{--                    //         selected_rows = [];--}}
{{--                    //     }--}}
{{--                    // },--}}

{{--                    {--}}
{{--                        extend: 'selectAll',--}}
{{--                        text: 'Select All',--}}
{{--                        className: 'select_all',--}}
{{--                        action: function (e) {--}}
{{--                            e.preventDefault();--}}

{{--                            table.rows().nodes().each(function (index) {--}}
{{--                                var row = table.row(index);--}}

{{--                                if ($(row.node().firstChild).hasClass('select-checkbox')) {--}}
{{--                                    row.select();--}}

{{--                                    id = parseInt(row.id());--}}

{{--                                    var index = $.inArray(id, selected_rows);--}}

{{--                                    if (index === -1) {--}}
{{--                                        selected_rows.push(id);--}}
{{--                                    }--}}

{{--                                    table.button('.print').enable();--}}
{{--                                }--}}
{{--                            });--}}


{{--                        }--}}
{{--                    },--}}
{{--                    {--}}
{{--                        extend: 'selectNone',--}}
{{--                        text: 'Select None',--}}
{{--                        className: 'select_none',--}}
{{--                        action: function (e) {--}}
{{--                            e.preventDefault();--}}

{{--                            table.rows().nodes().each(function (index) {--}}
{{--                                var row = table.row(index);--}}

{{--                                if ($(row.node().firstChild).hasClass('select-checkbox')) {--}}
{{--                                    row.deselect();--}}

{{--                                    id = parseInt(row.id());--}}

{{--                                    var index = $.inArray(id, selected_rows);--}}

{{--                                    if (index !== -1) {--}}
{{--                                        selected_rows.splice(index, 1);--}}
{{--                                    }--}}

{{--                                    if (selected_rows.length == 0) {--}}
{{--                                        table.button('.print').disable();--}}
{{--                                    }--}}
{{--                                }--}}
{{--                            });--}}
{{--                        }--}}
{{--                    },--}}
{{--                    'reset'--}}
{{--                ],--}}
{{--                @else--}}
{{--                buttons: [--}}
{{--                    'reset'--}}
{{--                ],--}}
{{--                @endif--}}
                buttons: [
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.logistic.cn.issue_to_rider.cn_list", ["rider_issue_id" =>':id']) }}'.replace(':id',issue_id),
                },
                rowId: 'id',
                // order: [[13, 'desc']],
                columns: [

                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        class: 'text-center align-middle select p-1',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'id',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'trax_id', name: 'r.trax_id', class: 'align-middle trax_id'},
                    {data: 'rider_name', name: 'r.name', class: 'align-middle rider_name'},
                    {data: 'cn_number', name: 'trax_rider_cn_details.cn_number', class: 'align-middle cn_number'},
                    // {data: 'barcode', name: 'barcode', class: 'align-middle barcode', orderable: false},
                    {data: 'is_used', name: 'trax_rider_cn_details.is_used', class: 'align-middle is_used'},
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    $('td:eq(0)', row).addClass('select-checkbox');
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        // if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.aging') || $(header).is('.reason_id') || $(header).is('.sale_person_tagged_aging')) {
                        //     $(td).appendTo($(search) || $(header).is('.serial_number'));
                        // } else {
                        //     var current = $(input).appendTo($(search)).on('change', function () {
                        //         column.search($(this).val(), false, false, true).draw();
                        //     }).wrap(td).after(icon);
                        //
                        //     if (column.search()) {
                        //         current.val(column.search());
                        //     }
                        // }
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('.datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.print').enable();
                }
                else {
                    table.button('.print').disable();
                }
            });

            function print(selected_rows) {
                $.ajax({
                    url: '{!! route('admin.logistic.cn.issue_to_rider.barcodes_print') !!}',
                    method: 'POST',
                    data: {
                        'ids[]': selected_rows,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {

                        var tab = window.open('', '_blank');

                        if (!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        } else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }



        });

    </script>
@endsection