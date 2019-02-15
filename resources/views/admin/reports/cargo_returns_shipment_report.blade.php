@extends('admin.layout.master')

@section('title', 'Cargo Returns Shipment Report')

@section('content')
    <h1 class="mb-1">
        Cargo Returns Shipment Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-3 ">

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)">
                        </div>

                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Return Confirm Date</th>
                        <th class="border-primary border-darken-1">Return Confirm Aging</th>
                        <th class="border-primary border-darken-1">Cargo No.</th>
                        <th class="border-primary border-darken-1">Cargo Creation Date</th>
                        <th class="border-primary border-darken-1">Dispatching Aging</th>
                        <th class="border-primary border-darken-1">Origin City</th>
                        <th class="border-primary border-darken-1">Destination City</th>
                        <th class="border-primary border-darken-1">Origin Hub</th>
                        <th class="border-primary border-darken-1">Destination Hub</th>
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.reports.cargo_returns_shipment.print') !!}',
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

            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Origin',
                width: '100%',
                allowClear: true
            });

            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Destination',
                width: '100%',
                allowClear: true
            });

            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.cargo_returns_shipment.list') }}',
                        data: {
                            'page': 'all',
                            'search_origin': $('#search_origin').val(),
                            'search_destination': $('#search_destination').val(),
                            'search_date_from': $('input[name="search_date_from_formatted"]').val(),
                            'search_date_to': $('input[name="search_date_to_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Tracking Number');
                            head.push('Status');
                            head.push('Return Confirm Date');
                            head.push('Return Confirm Aging');
                            head.push('Cargo No.');
                            head.push('Cargo Creation Date');
                            head.push('Dispatching Aging');
                            head.push('Origin City');
                            head.push('Destination City');
                            head.push('Origin Hub');
                            head.push('Destination Hub');
                            $.each(result.data, function (index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.status);
                                row.push(values.return_confirm_date);
                                row.push(values.return_confirm_aging);
                                row.push(values.cargo_no);
                                row.push(values.cargo_creation_date);
                                row.push(values.dispatching_aging);
                                row.push(values.origin_city);
                                row.push(values.destination_city);
                                row.push(values.origin_hub);
                                row.push(values.destination_hub);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Cargo Returns Shipment Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.cargo_returns_shipment.list') }}',
                    data: function (d) {
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[3, 'desc']],
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'status', name: 'ss.id', class: 'align-middle status'},
                    {data: 'return_confirm_date', name: 'sj.created_at', class: 'align-middle return_confirm_date'},
                    {data: 'return_confirm_aging', name: 'sj.created_at', class: 'align-middle return_confirm_aging'},
                    {data: 'cargo_id_padded_link', name: 'cc.id', class: 'align-middle cargo_number'},
                    {data: 'cargo_creation_date', name: 'cc.created_at', class: 'align-middle cargo_creation_date'},
                    {data: 'dispatching_aging', name: 'sjc.created_at', class: 'align-middle dispatching_aging'},
                    {data: 'origin_city', name: 'cori.name', class: 'align-middle origin_city'},
                    {data: 'destination_city', name: 'cdri.name', class: 'align-middle destination_city'},
                    {data: 'origin_hub', name: 'co.name', class: 'align-middle origin_hub'},
                    {data: 'destination_hub', name: 'cd.name', class: 'align-middle destination_hub'},
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click', function () {
                table.draw();
            });

            $('#datatable tbody').on('click','tr td.cargo_number button.print',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                console.log(id);
                print(id);

            });
        });

    </script>
@endsection