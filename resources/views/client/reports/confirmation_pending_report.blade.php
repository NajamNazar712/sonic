@extends('client.layout.master')

@section('title', 'Confirmation Pending Shipments Report')

@section('content')
    <h1 class="mb-1">
        Confirmation Pending Shipments Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="col-4">
                     <div class="form-group">
                            <input type="text" name="tracking_numbers" class="dt_search tracking_numbers"
                                placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">
                        </div>
                    </div>
                        <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                                    </div>

                                    <input type="text" name="dr_search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="dr_search_date_from" placeholder="Returned Shipments Date (From)">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                                    </div>

                                    <input type="text" name="dr_search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="dr_search_date_to" placeholder="Returned Shipments Date (To)">
                                </div>
                            </div>
                    <div class="form-group justify-content-center">
                        <button id="search_filter_btn" type="submit" class="ml-1 btn btn-outline-primary btn-min-width mt-2"><i
                                    class="la la-search"></i> Search
                        </button>
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">First Returned Confirmation Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Reason</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}"><style>

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
        .selectize-control {
			width: 460px !important;
            position: absolute !important;
		}
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#tracking_numbers').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false
                });
            $('#dr_search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#dr_search_date_to').pickadate('picker').set('min', $('#dr_search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            $('#dr_search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#dr_search_date_from').pickadate('picker').set('min', $('#dr_search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            // var from_date = $('#dr_search_date_from').pickadate({
            //     firstDay: 1,
            //     clear: '',
            //     selectYears: true,
            //     selectMonths: true,
            //     formatSubmit: 'yyyy-mm-dd 00:00:00',
            //     hiddenSuffix: '_formatted',
            //     onSet: function(context) {
            //         if (context.select) {
            //             $('#dr_search_date_to').pickadate('picker').set('min', $('#dr_search_date_from').pickadate('picker').get('select'));
            //         }
            //     }
            // });

            // var to_date = $('#dr_search_date_to').pickadate({
            //     firstDay: 1,
            //     clear: '',
            //     selectYears: true,
            //     selectMonths: true,
            //     formatSubmit: 'yyyy-mm-dd 23:59:59',
            //     hiddenSuffix: '_formatted',
            //     onSet: function(context) {
            //         if (context.select) {
            //             $('#dr_search_date_from').pickadate('picker').set('max', $('#dr_search_date_to').pickadate('picker').get('select'));
            //         }
            //     }
            // });


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    if(params !== undefined){
                        params.start = 0;
                        params.length = -1;
                    }

                    var jsonResult = $.ajax({
                        url: '{{ route('cod.reports.confirmation_pending_report.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No.');
                            head.push('First Returned Confirmation Date');
                            head.push('Status');
                            head.push('Remarks');
                            head.push('Reason');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.status_date);
                                row.push(values.status);
                                row.push(values.remarks);
                                row.push(values.reason);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: false, scrollY: '500px',
                // deferLoading: [50, 0],
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Confirmation Pending Shipments Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                deferLoading: 0,
                ajax:{
                    url: '{{ route('cod.reports.confirmation_pending_report.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.dr_search_date_from = $('input[name="dr_search_date_from_formatted"]').val();
                        d.dr_search_date_to = $('input[name="dr_search_date_to_formatted"]').val();
                    }
                },
                rowId: 'shId',
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'r', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking'},
                    {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'remarks', name: 'sret.remarks', class: 'align-middle remarks'},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });


            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                var tracking_numbers = $('#track_form .tracking_numbers').val();

                if (tracking_numbers != '') {
                    table.draw();
                }

            });


            $('.decimal').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 100000
            });


            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });
    </script>
@endsection