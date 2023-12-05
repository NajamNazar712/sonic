@extends('admin.layout.master')

@section('title', 'CSAT Summary')

@section('content')
    <h1 class="mb-1">
        CSAT Summary
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="text-center p-1 bg-info text-white custom_csat">
                    <strong>CSAT = {{ number_format($csat_score) }}%</strong>
                </div>
                <form id="search_form" class="form-inline mb-1 mt-3 justify-content-center" novalidate="novalidate">
                    <div class="col-2">
                        <div class="col-3 mb-1">
                            <select name="agents" class="select2" id="agents">
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="col-3 mb-1">
                            <select id="ratings" class="select2" style="width: 200px;">
                                @foreach ($ratings as $rating)
                                    <option value="{{ $rating->id }}">{{ $rating->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left date_css">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="from_date"
                                class="form-control bg-primary border-primary white rounded-right" id="from_date"
                                placeholder="Date From">
                        </div>
                    </div>
                    <div class="col-3 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left date_css">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="to_date"
                                class="form-control bg-primary border-primary white rounded-right" id="to_date"
                                placeholder="Date To">
                        </div>
                    </div>

                    <div class="col-3 mb-1">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                Search</button>
                        </div>
                    </div>

                    
                </form>


               
                <div id="table">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Agent</th>
                                <th class="border-primary border-darken-1">Complaint ID</th>
                                <th class="border-primary border-darken-1">Complaint Type</th>
                                <th class="border-primary border-darken-1">Shipment Status</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Resolved WithIn</th>
                                <th class="border-primary border-darken-1">Rating</th>
                                
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">

    <style>
        .date_css {
            height: 40px;
            margin: 1px;
        }

        #rating {
            background-color: white;
            border: 1px solid #ccc;
            padding: 5px;
        }

        #rating option img {
            width: 20px;
            height: 20px;
            vertical-align: middle;
            margin-right: 5px;
        }

        .custom_csat {
            width: 180px;
            margin: 0 auto;
            display: table;
        }
        
    </style>
@endsection
@section('js')
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pagination/moment.min.js') }}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            agent_select = $('#agents').prepend('<option value="" selected="selected"></option>').select2({
                width: '200px',
                placeholder: 'Select Agent',
                allowClear: true
            })

            $("#ratings").prepend('<option value="" selected="selected"></option>').select2({
                templateResult: function(idioma) {
                    var stars = '';
                    for (var i = 1; i <= idioma.id; i++) {
                        stars += '<img src="{{ asset('img/star_icon_nobg.png') }}" alt="' + i +
                            ' star" />';
                    }
                    var $span = $("<span>" + stars + "</span>");
                    return $span;
                },
                placeholder: 'Select Rating',
                allowClear: true

            });

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $(
                            '#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var date_limit = '{{ Carbon\Carbon::now()->toDateString() }}';
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format: 'dd mmmm, yyyy',
                max: new Date(date_limit),
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $(
                            '#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form').validate({

                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $('#table').removeClass('d-none');
                    table.draw(true);
                }
            });


            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.csat_report.list') }}',
                        data: params,
                        success: function(result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Agent');
                            head.push('Complaint ID');
                            head.push('Complaint Type');
                            head.push('Shipment Status');
                            head.push('Status');
                            head.push('Resolved WithIn');
                            head.push('Rating');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.agent_id);
                                row.push(values.id);
                                row.push(values.case_nature_type_id);
                                row.push(values.shipment_status);
                                row.push(values.status_id);
                                row.push(values.created_at);
                                row.push(values.rating_id);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {
                        body: body,
                        header: head
                    };
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: false,
                scrollY: '500px',
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-primary',
                    title: 'CSAT Report Summary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }, ],
                lengthMenu: [
                    [50, 100, 500, 1000, -1],
                    [50, 100, 500, 1000, 'All']
                ],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.csat_report.list') }}',

                    data: function(d) {
                        d.agents = $('#agents').val();
                        d.ratings = $('#ratings').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [
                    [2, 'desc']
                ],
                columns: [{
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function(data, type, row) {
                            return '';
                        }
                    },
                    {
                        data: 'agent_id',
                        name: 'crm_requests.agent_id',
                        class: 'align-middle text-center agent_id'
                    },
                    {
                        data: 'id',
                        name: 'crm_requests.id',
                        class: 'align-middle text-center id'
                    },
                    {
                        data: 'case_nature_type_id',
                        name: 'crm_requests.case_nature_type_id',
                        class: 'align-middle text-center case_nature_type_id'
                    },
                    {
                        data: 'shipment_status',
                        name: 'sj.shipper_status_id',
                        class: 'align-middle text-center shipment_status'
                    },
                    {
                        data: 'status_id',
                        name: 'crm_requests.status_id',
                        class: 'align-middle text-center status_id'
                    },
                    {
                        data: 'created_at',
                        name: 'crm_requests.created_at',
                        class: 'align-middle text-center created_at'
                    },
                    {
                        data: 'rating_id',
                        name: 'crmf.rating_id',
                        class: 'align-middle text-center rating_id'
                    },

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });


        });
    </script>
@endsection
