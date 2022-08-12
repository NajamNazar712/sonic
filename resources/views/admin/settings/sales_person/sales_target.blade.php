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
                                <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.sales.targets.update') }}" novalidate="novalidate">
                                    {{ csrf_field() }}
                                    <div class="row justify-content-center">
                                        <div class="col-5">
                                            <div class="col">
                                                <div class="form-group">
                                                    <input type="text" name="search_date_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_from" placeholder="Select Date">
                                                </div>
                                            </div>


                                            <div class="col">
                                                <div class="form-group">
                                                    <select name="sales_person[]" id="sales_person_select" class="form-control select2" multiple="multiple" data-msg-required="Atleast one Sales Person is required" data-rule-required="true" required="required">
                                                        @foreach($sales_person as $person)
                                                            <option value="{{$person->id}}">{{$person->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Target Shipments/Day</span>
                                                        </div>
                                                        <input type="text" name="target_shipment_days" id="target_shipment_days" class="form-control class" placeholder="Target Shipments/Day*" data-rule-required="true" data-msg-required="Target Shipments/Day is required" value="">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Target Shipments/Month</span>
                                                        </div>
                                                        <input type="text" name="target_shipment_month" id="target_shipment_month" class="form-control class" placeholder="Target Shipments/Month*" data-rule-required="true" data-msg-required="Target Shipments/Month is required" value="">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Average Revenue</span>
                                                        </div>
                                                        <input type="text" name="average_revenue" class="form-control class" placeholder="Average Revenue*" data-rule-required="true" data-msg-required="Average Revenue is required" value="">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <div class="input-group justify-content-center">
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Sales Person</th>
                                        <th class="border-primary border-darken-1">Start Date</th>
                                        <th class="border-primary border-darken-1">End Date</th>
                                        <th class="border-primary border-darken-1">Target Shipments/Day</th>
                                        <th class="border-primary border-darken-1">Revenue Target/Day</th>
                                        <th class="border-primary border-darken-1">Target Shipments/Month</th>
                                        <th class="border-primary border-darken-1">Revenue Target/Month</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {

            var search_date_from = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_date_root').css('bottom','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                    }
                }
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

                $('#target_shipment_month').val(days * 30);
            });
        });

        @if(count($targets) > 0)
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
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
                            head.push('Revenue Target/Day');
                            head.push('Target Shipments/Month');
                            head.push('Revenue Shipments/Month');
                            head.push('Average Revenue');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.sales_person);
                                row.push(values.start_date);
                                row.push(values.end_date);
                                row.push(values.target_days);
                                row.push(values.per_day_revenue_target);
                                row.push(values.target_month);
                                row.push(values.per_month_revenue_target);
                                row.push(values.average_revenue);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    @if (session('role_id') == 44 || session('role_id') == 1 )

                    {
                        text: 'Delete',
                        className: 'btn btn-primary delete_sale_person',
                        enabled:false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){

                                swal({
                                    text: 'Are you sure, you want to Delete?',
                                    icon: 'info',
                                    buttons: {
                                        cancel: {
                                            text: 'No',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Yes',
                                            value: true,
                                            visible: true,
                                            closeModal: true
                                        }
                                    },
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true
                                }).then(function(confirm) {
                                    if (confirm) {

                                        $.ajax({
                                            url: '{!! route('admin.settings.sales.targets.delete') !!}',
                                            method: 'POST',
                                            data: {
                                                'sale_person_ids[]': selected_rows,
                                                '_token': '{{ csrf_token() }}'
                                            }
                                        })
                                        .done(function (data) {
                                            if (data.status === 0) {
                                                toastr.error(data.error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                            selected_rows = [];
                                            table.rows().deselect();
                                            table.draw(true);
                                            table.button('.delete_sale_person').disable();

                                        });
                                    }
                                });

                            }else{
                                var error = "Not selected any Sale Person!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },

                    @endif
                    {
                        extend: 'excel',
                        title: 'Sales Person Targets',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset',
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                rowId: 'id',
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
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'sales_person', name:'a.name', class: 'align-middle sales_person'},
                    {data: 'start_date', name: 'sale_person_targets.start_date', class: 'align-middle start_date'},
                    {data: 'end_date', name: 'sale_person_targets.end_date', class: 'align-middle end_date'},
                    {data: 'target_days', name: 'sale_person_targets.target_days', class: 'align-middle target_days'},
                    {data: 'per_day_revenue_target', name: 'per_day_revenue_target', class: 'align-middle per_day_revenue_target'},
                    {data: 'target_month', name: 'sale_person_targets.target_month', class: 'align-middle target_month'},
                    {data: 'per_month_revenue_target', name: 'per_month_revenue_target', class: 'align-middle per_month_revenue_target'},
                    {data: 'average_revenue', name: 'sale_person_targets.average_revenue', class: 'align-middle average_revenue'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }

                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.per_day_revenue_target') || $(header).is('.per_month_revenue_target')) {
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

        $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
            var id = parseInt($(this).parent('tr').attr('id'));
            var index = $.inArray(id, selected_rows);
            if (index === -1) {
                selected_rows.push(id);
            }
            else {
                selected_rows.splice(index, 1);
            }
            if (selected_rows.length > 0) {
                table.button('.delete_sale_person').enable();
            }
            else {
                table.button('.delete_sale_person').disable();
            }
        });

        @endif


    </script>
@endsection