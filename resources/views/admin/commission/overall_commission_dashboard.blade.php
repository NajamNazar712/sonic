@extends('admin.layout.master')

@section('title', 'Overall Commission Dashboard')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    <h1 class="mb-1">
        Overall Commission Dashboard
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row">
                    <div class="col-3">
                        <div class="card pull-up border">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-hourglass font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="" id="booked">{{$stats['booked']}}</h3>
                                            <span>Shipment(s) Booked</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card pull-up border">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-layers font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="" id="received">{{$stats['received']}}</h3>
                                            <span>Shipment(s) Received</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card pull-up border">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="la la-money font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="" id="revenue">{{$stats['revenue']}}</h3>
                                            <span>Revenue</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card pull-up border">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="la la-money font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="" id="commission">{{$stats['commission']}}</h3>
                                            <span>Commission Earned</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-2 mt-1 justify-content-center">
                    <div class="col-12 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                            <div class="col-3">
                                <fieldset class="form-group">
                                    <select name="search_shipper" id="search_shipper" class="form-control select2">
                                        @foreach($shippers as $shipper)
                                            <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-3">
                                <fieldset class="form-group">
                                    <select name="search_admin" id="search_admin" class="form-control select2">
                                        @foreach($admins as $admin)
                                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-3">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" title="Date From" data-value="{{ Carbon\Carbon::today() }}">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" title="Date To" data-value="{{ Carbon\Carbon::today() }}">
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group mt-1">
                                    <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="f_datatable_div">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Account ID</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Shipment(s) Booked</th>
                            <th class="border-primary border-darken-1">Shipment(s) Received</th>
                            <th class="border-primary border-darken-1">Revenue</th>
                            <th class="border-primary border-darken-1">Total Commission %</th>
                            <th class="border-primary border-darken-1">Total Commission Amount</th>
                            @if(!empty($sales_tier))
                                @foreach($sales_tier as $sale_tier)
                                    <th class="border-primary border-darken-1">{{$sale_tier['tier_name']}}</th>
                                    <th class="border-primary border-darken-1">Commission %</th>
                                    <th class="border-primary border-darken-1">Amount</th>
                                @endforeach
                            @endif
                        </tr>
                        </thead>
                    </table>
                </div>

                <div class="d-none" id="s_datatable_div">
                    <table class="table table-bordered datatable" id="s_datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Account ID</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Shipment(s) Booked</th>
                            <th class="border-primary border-darken-1">Shipment(s) Received</th>
                            <th class="border-primary border-darken-1">Revenue</th>
                            <th class="border-primary border-darken-1">Total Commission %</th>
                            <th class="border-primary border-darken-1">Total Commission Amount</th>
                            <th class="border-primary border-darken-1">Sales Person</th>
                            <th class="border-primary border-darken-1">Commission %</th>
                            <th class="border-primary border-darken-1">Amount</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <style type="text/css">
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shipper',
                allowClear:true
            });
            $('#search_form #search_admin').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Admin',
                allowClear:true
            });

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                select: '{{$first_day}}',
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
                select: '{{$last_day}}',
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

            var sales_tier = @json($sales_tier);
            var columns = [];
            var s_columns = [];
            var export_col = [1,2,3,4,5,6,7];
            var s_export_col = [1,2,3,4,5,6,7,8,9,10];
            columns.push({orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}});
            columns.push({data: 'id', name: 'u.id', class: 'align-middle id'});
            columns.push({data: 'shipper_name', name: 'u.name', class: 'align-middle text-center shipper_name'});
            columns.push({data: 'booked', class: 'align-middle text-center booked', orderable: false, searchable: false});
            columns.push({data: 'received', class: 'align-middle text-center received', orderable: false, searchable: false});
            columns.push({data: 'revenue', class: 'align-middle text-center revenue', orderable: false, searchable: false});
            columns.push({data: 'total_commission', class: 'align-middle text-center total_commission', orderable: false, searchable: false});
            columns.push({data: 'total_commission_amount', class: 'align-middle text-center total_commission_amount', orderable: false, searchable: false});
            s_columns.push({orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}});
            s_columns.push({data: 'id', name: 'u.id', class: 'align-middle id'});
            s_columns.push({data: 'shipper_name', name: 'u.name', class: 'align-middle text-center shipper_name'});
            s_columns.push({data: 'booked', class: 'align-middle text-center booked', orderable: false, searchable: false});
            s_columns.push({data: 'received', class: 'align-middle text-center received', orderable: false, searchable: false});
            s_columns.push({data: 'revenue', class: 'align-middle text-center revenue', orderable: false, searchable: false});
            s_columns.push({data: 'total_commission', class: 'align-middle text-center total_commission', orderable: false, searchable: false});
            s_columns.push({data: 'total_commission_amount', class: 'align-middle text-center total_commission_amount', orderable: false, searchable: false});
            $.each(sales_tier, function (index, value) {
                var i;
                i = export_col.length;
                export_col.push(i+1);
                export_col.push(i+2);
                export_col.push(i+3);
                columns.push({data:value.tier_name.replace(/ /g, '').toLowerCase(), class:'align-middle text-center counts', orderable: false, searchable: false});
                columns.push({data:value.tier_name.replace(/ /g, '').toLowerCase() + 'commission', class:'align-middle text-center counts', orderable: false, searchable: false});
                columns.push({data:value.tier_name.replace(/ /g, '').toLowerCase() + 'amount', class:'align-middle text-center counts', orderable: false, searchable: false});
            });$.each(sales_tier, function (index, value) {
                if(value.id == 1){
                    s_columns.push({data:value.tier_name.replace(/ /g, '').toLowerCase(), class:'align-middle text-center counts', orderable: false, searchable: false});
                    s_columns.push({data:value.tier_name.replace(/ /g, '').toLowerCase() + 'commission', class:'align-middle text-center counts', orderable: false, searchable: false});
                    s_columns.push({data:value.tier_name.replace(/ /g, '').toLowerCase() + 'amount', class:'align-middle text-center counts', orderable: false, searchable: false});
                }
            });
            let params  = {
                '_token': "{{csrf_token()}}",
                'excel' : true,
            }
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Overall Commission Dashboard',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: export_col
                        },
                        action: function (e, dt, node, config) {
                            var that = this;
                                $.ajax({
                                    'url': "{{ route('admin.dashboard.overall.commission.list') }}",
                                    data : params,
                                    method: 'post',

                                }).done(function(data) {
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(that,e, dt, node, config);
                                });
                        }
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.dashboard.overall.commission.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.sales_tier = sales_tier;
                        d.search_shipper = $('#search_shipper').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    },
                },
                columns: columns,
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


                        if ($(header).is('.serial_number') || $(header).is('.booked') || $(header).is('.received') || $(header).is('.revenue') || $(header).is('.total_commission') || $(header).is('.total_commission_amount') || $(header).is('.counts')) {
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
            var s_table = $('#s_datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Overall Commission Dashboard',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: s_export_col
                        },
                        action: function (e, dt, node, config) {
                            var that = this;
                            $.ajax({
                                'url': "{{ route('admin.dashboard.overall.commission.list') }}",
                                data : params,
                                method: 'post',

                            }).done(function(data) {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(that,e, dt, node, config);
                            });
                        }
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.dashboard.overall.commission.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.sales_tier = sales_tier;
                        d.search_shipper = $('#search_shipper').val();
                        d.search_admin = $('#search_admin').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    },
                },
                columns: s_columns,
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


                        if ($(header).is('.serial_number') || $(header).is('.booked') || $(header).is('.received') || $(header).is('.revenue') || $(header).is('.total_commission') || $(header).is('.total_commission_amount') || $(header).is('.counts')) {
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

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    var from_date = $('#search_form input[name="from_date_formatted"]').val();
                    var to_date = $('#search_form input[name="to_date_formatted"]').val();
                    var shipper = $('#search_shipper').val();
                    var admin = $('#search_admin').val();
                    $.ajax({
                        url: '{!! route('admin.dashboard.overall.commission.data') !!}',
                        method: 'post',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'search_date_from': from_date,
                            'search_date_to': to_date,
                            'search_shipper': shipper,
                            'search_admin': admin,
                        }
                    }).done(function (data) {
                        if(data.status){
                            $('#booked').text(data.stats.booked);
                            $('#received').text(data.stats.received);
                            $('#revenue').text(data.stats.revenue);
                            $('#commission').text(data.stats.commission);
                            table.draw();

                        }else{
                            $('#booked').text(0);
                            $('#received').text(0);
                            $('#revenue').text(0);
                            $('#commission').text(0);
                            table.draw();
                        }
                        UnblockPagePermanently();
                    });
                    if(admin == null || admin == ''){
                        $('#s_datatable_div').addClass('d-none');
                        $('#f_datatable_div').removeClass('d-none');
                        table.draw(true);
                    }
                    else{
                        $('#f_datatable_div').addClass('d-none');
                        $('#s_datatable_div').removeClass('d-none');
                        s_table.draw(true);
                    }
                }
            });
        });

    </script>
@endsection