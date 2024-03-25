@extends('admin.layout.master')

@section('title', 'SMS Count Report')

@section('content')
    <h1 class="mb-1">
        SMS Count Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                    <div id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">

                        <div class="col-5">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                                </div>
                                <input type="text" name="search_month" class="form-control pickadate bg-primary border-primary white rounded-right height-5-per" id="search_month" placeholder="Search Month" data-rule-required="true" data-msg-required="Month is required">
                            </div>
                        </div>
                        
                        @if(!empty($notifications))
                        <div class="col-5">
                            <fieldset class="form-group">
                                <select name="search_type[]" id="search_type" class="form-control select2" multiple>
                                    @foreach($notifications as $notification)
                                    <option value="{{$notification->id}}">{{$notification->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        @endif
                        <div class="col-2">
                            <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Month</th>
                        <th class="border-primary border-darken-1">Type</th>
                        <th class="border-primary border-darken-1">Count</th>
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
        #month_table , #search_month_table {
            display:none;
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
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_type').select2({
                placeholder:'Select Shipper',
                width:'100%'
            });

            var max = '{{ Carbon\Carbon::now() }}';
            var search_month = $('#search_form #search_month').pickadate({
                firstDay: 1,
                disable:[true,1],
                clear: '',
                today:'Select Current Month',
                max: max,
                format:'mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_month_root').css('top','40px');
                    $('#search_month_root button.picker__button--today').removeAttr('disabled');
                },
                onSet: function(context) {

                    var from_month = $('#search_month_root .picker__select--month').val();
                    var from_year = $('#search_month_root .picker__select--year').val();
                    var month_selected = new Date(from_year,from_month, 1);

                    search_month.pickadate('picker').set('select', month_selected,{muted:true});

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
                        url: '{{ route('admin.reports.sms.list') }}',
                        method:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Type');
                            head.push('Month');
                            head.push('Count');
                            
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.created_year_month);
                                row.push(values.notification_count);
                                
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'SMS Count Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                autoWidth: false,
                ajax: {
                    url: '{{ route('admin.reports.sms.list') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        
                        d.search_month = $('input[name="search_month_formatted"]').val();
                        d.search_type = $('#search_type').val();

                    }
                },
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'created_year_month', name: 'created_year_month', class: 'align-middle created_year_month', orderable: false, searchable: false},
                    {data: 'name', name: 'name', class: 'align-middle name', orderable: false, searchable: false},
                    {data: 'notification_count', name: 'notification_count', class: 'align-middle notification_count', orderable: false, searchable: false},
                    
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