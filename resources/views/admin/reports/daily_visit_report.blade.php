@extends('admin.layout.master')

@section('title', 'Daily Visit Report')

@section('content')
    <h1 class="mb-1">
        Daily Visit Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Admin User</th>
                        <th class="border-primary border-darken-1">Visit Data/Time</th>
                        <th class="border-primary border-darken-1">Company Name</th>
                        <th class="border-primary border-darken-1">Customer Name</th>
                        <th class="border-primary border-darken-1">Customer Address</th>
                        <th class="border-primary border-darken-1">Phone Number</th>
                        <th class="border-primary border-darken-1">Email Address</th>
                        <th class="border-primary border-darken-1">Lead Status</th>
                        <th class="border-primary border-darken-1">Meeting Feedback</th>
                        <th class="border-primary border-darken-1">Location</th>
                        <th class="border-primary border-darken-1">Photo of Location</th>
                        <th class="border-primary border-darken-1">Photo of Business Card</th>
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.daily_visit.list') }}',
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Admin User');
                            head.push('Visit Date/Time');
                            head.push('Company Name');
                            head.push('Customer Name');
                            head.push('Customer Address');
                            head.push('Phone Number');
                            head.push('Email Address');
                            head.push('Lead Status');
                            head.push('Meeting Feedback');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.admin);
                                row.push(values.created_at);
                                row.push(values.company_name);
                                row.push(values.customer_name);
                                row.push(values.customer_address);
                                row.push(values.phone_no);
                                row.push(values.email);
                                row.push(values.lead_status);
                                row.push(values.feedback);
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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Daily Visit Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                "autoWidth": false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,ajax: {
                    url: '{{ route('admin.reports.daily_visit.list') }}',
                },
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'admin' ,name: 'a.name', class: 'align-middle admin'},
                    { data:'created_at' ,name: 'daily_visits.created_at', class: 'align-middle created_at'},
                    { data:'company_name' ,name: 'daily_visits.company_name', class: 'align-middle company_name'},
                    { data:'customer_name' ,name: 'daily_visits.customer_name', class: 'align-middle customer_name'},
                    { data:'customer_address' ,name: 'daily_visits.customer_address', class: 'align-middle customer_address'},
                    { data:'phone_no' ,name: 'daily_visits.phone_no', class: 'align-middle phone_no'},
                    { data:'email' ,name: 'daily_visits.email', class: 'align-middle email'},
                    { data:'lead_status' ,name: 'dvls.name', class: 'align-middle lead_status'},
                    { data:'feedback' ,name: 'daily_visits.feedback', class: 'align-middle feedback'},
                    { data:'location' ,name: 'location', class: 'align-middle location', sortable: false, orderable: false, searchable: false},
                    { data:'l_photo' ,name: 'l_photo', class: 'align-middle l_photo', sortable: false, orderable: false, searchable: false},
                    { data:'b_c_photo' ,name: 'b_c_photo', class: 'align-middle b_c_photo', sortable: false, orderable: false, searchable: false},

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

        });
    </script>
@endsection