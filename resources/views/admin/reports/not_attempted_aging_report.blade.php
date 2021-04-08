@extends('admin.layout.master')

@section('title', 'Not Attempted Aging Report')

@section('content')
    <h1 class="mb-1">
        Not Attempted Aging Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="col">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>
                                <input type="text" name="date" class="form-control bg-primary border-primary white rounded-right" id="date" placeholder="Date*" data-value="{{ Carbon\Carbon::today() }}">
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" id="search_filter_btn" class="btn btn-primary">Search</button>
                        </div>
                    </form>

                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">0</th>
                        <th class="border-primary border-darken-1">1</th>
                        <th class="border-primary border-darken-1">2</th>
                        <th class="border-primary border-darken-1">3</th>
                        <th class="border-primary border-darken-1">4</th>
                        <th class="border-primary border-darken-1">5</th>
                        <th class="border-primary border-darken-1">6+</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var min = '{{ Carbon\Carbon::now()->subDays(30)->toDateString() }}';
            var max = '{{ Carbon\Carbon::now() }}';
            var date = $('#date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                min: new Date(min),
                max: new Date(max),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#date_root').css('top', '40px');
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.not_attempted_aging.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Hub');
                            head.push('0');
                            head.push('1');
                            head.push('2');
                            head.push('3');
                            head.push('4');
                            head.push('5');
                            head.push('6+');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.hub);
                                row.push(values.zero);
                                row.push(values.one);
                                row.push(values.two);
                                row.push(values.three);
                                row.push(values.four);
                                row.push(values.five);
                                row.push(values.six_plus);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Not Attempted Aging Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.not_attempted_aging.list') }}',
                    data: function (d) {
                        d.date = $('input[name="date_formatted"]').val();
                    }
                },
                order: [[2, 'desc']],
                rowId: 'payment_id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'zero', name: 'not_attempted_shipment_agings.zero', class: 'align-middle zero'},
                    {data: 'one', name: 'not_attempted_shipment_agings.one', class: 'align-middle one'},
                    {data: 'two', name: 'not_attempted_shipment_agings.two', class: 'align-middle two'},
                    {data: 'three', name: 'not_attempted_shipment_agings.three', class: 'align-middle three'},
                    {data: 'four', name: 'not_attempted_shipment_agings.four', class: 'align-middle four'},
                    {data: 'five', name: 'not_attempted_shipment_agings.five', class: 'align-middle five'},
                    {data: 'six_plus', name: 'not_attempted_shipment_agings.return_charges', class: 'align-middle six_plus'}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_form').on('submit',function (e) {
                e.preventDefault();
                table.draw();
            });
        });



    </script>
@endsection