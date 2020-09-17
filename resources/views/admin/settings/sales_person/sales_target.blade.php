@extends('admin.layout.master')

@section('title', 'Sales Person Targets')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Sales Person Targets
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.sales.targets.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <input id="daterange" type="text" name="daterange" class="form-control" data-msg-required="Date Range is required" data-rule-require="true" required="required" value="" />
                                            <input type="hidden" name="start_date" id="start_date">
                                            <input type="hidden" name="end_date" id="end_date">
                                        </div>
                                        

                                        <div class="form-group">
                                            <select name="sales_person[]" id="sales_person_select" class="form-control select2" multiple="multiple" data-msg-required="Atleast one Sales Person is required" data-rule-require="true" required="required">
                                                @foreach($sales_person as $person)
                                                    <option value="{{$person->id}}">{{$person->name}}</option>
                                                @endforeach
                                                </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Target Shipments/Day</span>
                                                </div>
                                                <input type="text" name="target_shipment_days" id="target_shipment_days" class="form-control class" placeholder="Target Shipments/Day*" data-rule-required="true" data-msg-required="Target Shipments/Day is required" value="">
                                                
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Target Shipments/Week</span>
                                                </div>
                                                <input type="text" name="target_shipment_week" id="target_shipment_week" class="form-control class" placeholder="Target Shipments/Week*" data-rule-required="true" data-msg-required="Target Shipments/Week is required" value="">
                                                
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Average Revenue</span>
                                                </div>
                                                <input type="text" name="average_revenue" class="form-control class" placeholder="Average Revenue*" data-rule-required="true" data-msg-required="Average Revenue is required" value="">
                                                
                                            </div>
                                        </div>
                                        

                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(count($targets) > 0)
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                    
                            <div class="row justify-content-center">
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Sales Person</th>
                                        <th class="border-primary border-darken-1">Start Date</th>
                                        <th class="border-primary border-darken-1">End Date</th>
                                        <th class="border-primary border-darken-1">Target Shipments/Day</th>
                                        <th class="border-primary border-darken-1">Target Shipments/Week</th>
                                        <th class="border-primary border-darken-1">Average Revenue</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#daterange').daterangepicker({
                opens: 'left',
                timePicker:false,
                todayHighlight: true,
                dateLimit:{days:7},
              }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                $('#start_date').val(start.format('YYYY-MM-DD'));
                $('#end_date').val(end.format('YYYY-MM-DD'));
            });
            
            
            $('#sales_person_select').select2({
                placeholder:'Sales Person Select',
                width:'100%'
            });
            $('#settings_form .class').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });
            $('#target_shipment_days').on('change', function(){
                var days = $(this).val();

                $('#target_shipment_week').val(days * 6);
            });
        });

        @if(count($targets) > 0)
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.sales.targets.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Sales Person');
                            head.push('Start Date');
                            head.push('End Date');
                            head.push('Target Shipments/Day');
                            head.push('Target Shipments/Week');
                            head.push('Average Revenue');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.sales_person);
                                row.push(values.start_date);
                                row.push(values.end_date);
                                row.push(values.target_days);
                                row.push(values.target_week);
                                
                                row.push(values.average_revenue);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Sales Person Targets',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset',
                ],
                scrollX: true,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.settings.sales.targets.list') }}',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'sales_person', name:'a.name', class: 'align-middle sales_person'},
                    {data: 'start_date', name: 'sale_person_targets.start_date', class: 'align-middle start_date'},
                    {data: 'end_date', name: 'sale_person_targets.end_date', class: 'align-middle end_date'},
                    {data: 'target_days', name: 'sale_person_targets.target_days', class: 'align-middle target_days'},
                    {data: 'target_week', name: 'sale_person_targets.target_week', class: 'align-middle target_week'},
                    {data: 'average_revenue', name: 'sale_person_targets.average_revenue', class: 'align-middle average_revenue'}

                ],
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


                        if ($(header).is('.serial_number')) {
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

        @endif


    </script>
@endsection