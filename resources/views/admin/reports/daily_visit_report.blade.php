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
                <form id="search_form" class="form-inline mb-2 justify-content-center" novalidate="novalidate">
                    <div class="row justify-content-center">
                        <div class="col-6 mb-1">
                            <fieldset class="form-group">
                                <select name="team_member" id="team_member" class="form-control select2">
                                    @foreach($admins as $admin)
                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-6 mb-1">
                            <fieldset class="form-group">
                                <select name="rating" id="rating" class="form-control select2">
                                    @foreach($ratings as $rating)
                                        <option value="{{$rating->id}}">{{$rating->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <!--  Date wise Div  -->
                        <div class="col-5 mb-1">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                                </div>
                                <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required">
                            </div>
                        </div>

                        <div class="col-5 mb-1">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                                </div>
                                <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required">
                            </div>
                        </div>

                        <!--    Search Button  -->
                        <div class="col-2 justify-content-center">
                            <div class="form-group ">
                                <button type="button" class="btn btn-outline-info btn-min-width search_filter_btn" id="search_filter_btn"><i class="la la-search"></i>Search
                                </button>
                            </div>
                        </div>
                        <!--   end  -->
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Admin User</th>
                        <th class="border-primary border-darken-1">Admin Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Visit Date/Time</th>
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
                        <th class="border-primary border-darken-1">Shipper Rating</th>
                        <th class="border-primary border-darken-1">Shipper Feedback</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
        td.rating_code {
            font-size: 2em !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {

            /*   --- dropdown  ----    */
            $('#team_member').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Team Member',
                width:'100%',
                allowClear:true
            });

            $('#rating').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Rating',
                width:'100%',
                allowClear:true
            });

            $(document).on('click', '.dropdown-item', function() {
                var visit_status = $(this).data('visit-status');
                var daily_visit_id = parseInt($(this).parents('tr').attr('id'));

                $.ajax({
                    type: 'POST',
            
                    url: '{{ route('admin.reports.daily_visit.set_visit_status') }}',
                    data: {
                        daily_visit_id: daily_visit_id,
                        visit_status: visit_status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        
                        if (data.status == 'success') {
                            
                            toastr.success(data.message, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                            location.reload();

                        } else {
                            toastr.error(data.message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                });
            });

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            /*    end   */

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.daily_visit.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Admin User');
                            head.push('Admin Hub');
                            head.push('Zone');
                            head.push('Visit Date/Time');
                            head.push('Company Name');
                            head.push('Customer Name');
                            head.push('Customer Address');
                            head.push('Phone Number');
                            head.push('Email Address');
                            head.push('Lead Status');
                            head.push('Meeting Feedback');
                            head.push('Shipper Rating');
                            head.push('Shipper Feedback');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.admin);
                                row.push(values.city);
                                row.push(values.zone);
                                row.push(values.created_at);
                                row.push(values.company_name);
                                row.push(values.customer_name);
                                row.push(values.customer_address);
                                row.push(values.phone_no);
                                row.push(values.email);
                                row.push(values.lead_status);
                                row.push(values.feedback);
                                row.push(values.rating_text);
                                row.push(values.rating_comment);
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
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.daily_visit.list') }}',
                    data:function (d){
                        d.team_member = $('#team_member').val();
                        d.rating = $('#rating').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[4, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'admin' ,name: 'a.name', class: 'align-middle admin'},
                    { data:'city' ,name: 'c.name', class: 'align-middle city'},
                    { data:'zone' ,name: 'z.name', class: 'align-middle zone'},
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
                    { data:'rating' ,name: 'rate.name', class: 'align-middle rating_code'},
                    { data:'rating_comment' ,name: 'daily_visits.comment', class: 'align-middle rating_comment'},
                    { data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
                table.draw(true);
            });
        });
    </script>
@endsection