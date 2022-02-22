@extends('admin.layout.master')

@section('title', 'Rush & Saver Plus Cargo Origin List')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Rush & Saver Plus Cargo
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="settings_form" class="form-horizontal" method="POST" action="{{ route('admin.settings.overnight_overland_cargo_report.update') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <h4 class="input-group form-section mb-2 justify-content-center"><b>RAD TAT</b></h4>
                                <div class="row justify-content-center">
                                    <div class="form-group mr-1">
                                        <input class="form-control numeric" name="overnight" id="overnight" value="{{$overnight->setting_value}}" placeholder="Overnight*" data-rule-required="true" data-msg-required="Overnight RAD TAT is required">
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control numeric" name="overland" id="overland" value="{{$overland->setting_value}}" placeholder="Overland*" data-rule-required="true" data-msg-required="Overland RAD TAT is required">
                                    </div>
                                </div>
                                <div class="input-group mb-2 justify-content-center">
                                    <button type="submit" class="btn btn-primary" style="width: 200px">Update</button>
                                </div>
                                <h4 class="input-group form-section mb-2 justify-content-center"><b>Origin List</b></h4>
                            </form>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Cut-Off Time</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.overnight_overland_cargo_report.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Origin');
                            head.push('Cut-Off Time');
                            head.push('Updated At');
                            head.push('Updated By');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.origin);
                                row.push(values.cut_off_time);
                                row.push(values.updated_at);
                                row.push(values.updated_by);

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
                        title: 'Overnight & Overland Cargo Origin List',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset',
                ],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                autoWidth: false,
                serverSide: true,
                ajax: '{{ route('admin.settings.overnight_overland_cargo_report.list') }}',
                order: [[3 , 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'origin', name:'cities.name', class: 'align-middle origin'},
                    {data: 'cut_off_time', name: 'cities.cut_off_time', class: 'align-middle cut_off_time'},
                    {data: 'updated_at', name: 'cities.cut_off_time_updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
                    {data: 'action', class: 'align-middle action', orderable: false, searchable: false}

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


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
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
            $('body').on('click','.edit',function (e) {
                var id = $(this).data('target-id');
                if(id) {
                    var route = '{!!route('admin.settings.overnight_overland_cargo_report.edit', 'id')!!}';
                    url = route.replace('id', id);
                    window.location.href = url;
                }
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });

        });
    </script>
@endsection