@extends('admin.layout.master')

@section('title', 'DWS Report')

@section('content')
    <h1 class="mb-1">
        DWS Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">

                   {{-- <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>--}}
                  

                    <div class="col-4 mb-1">

                        <fieldset class="form-group">
                             <input name="tracking_number" id="tracking_number" class="form-control tracking_number" required data-rule-required="true" data-msg-required="This field is required"  placeholder="Tracking Number(s)">
                        </fieldset>
                    </div>
                    
                    <div class="col-4 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::today()->subMonths(1)  }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::today()}}">
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
                        <th class="border-primary border-darken-1">CN</th>
                        <th class="border-primary border-darken-1">Weight Type</th>
                        <th class="border-primary border-darken-1">Dense Weight</th>
                        <th class="border-primary border-darken-1">Length (Volumetric Weight)</th>
                        <th class="border-primary border-darken-1">Breadth (Volumetric Weight)</th>
                        <th class="border-primary border-darken-1">Height (Volumetric Weight)</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">DWS Image</th>
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            /*$('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Shipper',
                width:'100%',
                allowClear:true
            });*/
            var select = $('.tracking_number').selectize({
                placeholder: 'Tracking Number(s)',
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
                },
            });

            var today = '{{ Carbon\Carbon::today() }}';
            var next_month = '{{ Carbon\Carbon::today()->addMonths(1) }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                // min: new Date(thirtydays),
                max : new Date(today),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    var old_date_formatted = $('input[name="from_date_formatted"]').val();
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(29, 'days');
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    to_date.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    to_date.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max : new Date(next_month),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    // var current_date_formatted = $('input[name="to_date_formatted"]').val();
                    // from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
                }
            });



            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();

                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.dws_report.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            
                            head.push('S.No');
                            head.push('CN');
                            head.push('Weight Type');
                            head.push('Dense Weight');
                            head.push('Length (Volumetric Weight)');
                            head.push('Breadth (Volumetric Weight)');
                            head.push('Height (Volumetric Weight)');
                            head.push('Date');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.weight_type);
                                row.push(values.dense_weight);
                                row.push(values.length);
                                row.push(values.width);
                                row.push(values.height);
                                row.push(values.date);

                                body.push(row);
                            });
                        },
                        async: false
                    });


                    return {body: body, header: head};
                }
            } );

            var index_column = [];
            var flag = false;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: false  , scrollY: false,
                buttons: [
                    {
                        extend: 'excel',
                        title: 'DWS Report',
                        className: 'btn btn-primary excel',
                        enabled:false,
                        text: '<i class="la la-file-excel-o"></i> Excel',
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
                deferLoading: [50, 0],
                ajax: {
                    url: '{{ route('admin.reports.dws_report.list') }}',
                    data: function (d) {
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                        d.tracking_number = $('#tracking_number').val();
                        
                    }
                },
                rowId: 'shId',
                order: [[7, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link', searchable: false},
                    {data: 'weight_type', name: 'weight_type', class: 'align-middle weight_type', searchable: false},
                    {data: 'dense_weight', name: 'dense_weight', class: 'align-middle dense_weight'},
                    {data: 'length', name: 'length', class: 'align-middle length'},
                    {data: 'width', name: 'width', class: 'align-middle width'},
                    {data: 'height', name: 'height', class: 'align-middle height'},
                    {data: 'date', name: 'date', class: 'align-middle date'},
                    {data: 'dws_image', name: 'dws_image', class: 'align-middle dws_image'},

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
               table.button('.excel').enable();
               table.draw();
            });

            $('body').on('click','.dws_image button',function () {
                var link = $(this).attr('data-link');
                window.open(link, "_blank")
            });


        });

    </script>
@endsection