@extends('admin.layout.master')

@section('title', 'International Rates')

@section('content')
    <h1>International Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="font-large-1">{{$shipper->name}}
                            @if($shipper->account_type_id == 1)
                                <div class="badge badge-success pull-right">Reimbursement Account</div>
                            @else
                                <div class="badge badge-success pull-right">Corporate Invoicing Account</div>
                            @endif
                        </h2>
                        @include('admin.inc.messages')
                    </div>
                    <div class="card-body">
                        <form id="update_rates_form" class="row mb-1" novalidate="novalidate" action="#" method="post">
                            @csrf
                            @method('post')
                            <input type="hidden" name="shipper_id" value="{{ $shipper->id }}">

                            <div class="col form-group">
                                <label><strong>Fuel Surcharge</strong></label>
                                <div class="input-group">
                                    <input type="text" name="fuel_surcharge" class="form-control fuel_surcharge" placeholder="Fuel Surcharge" data-rule-required="true" data-msg-required="Fuel Surcharge is required" disabled value="{{$fuel_surcharge}}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col form-group">
                                <label><strong>Exchange Rate</strong></label>
                                <div class="input-group">
                                    <input type="text" name="exchange_rate" value="{{$exchange_charges}}" class="form-control amount"  placeholder="Exchange Rate*" data-rule-required="true" data-msg-required="Exchange Rate is required" disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text">PKR</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col form-group">
                                <label><strong>GST</strong></label>
                                <div class="input-group">
                                    <input type="text" name="gst" class="form-control gst decimal" placeholder="GST*" data-rule-required="true" data-msg-required="GST is required" value="{{ $gst }}" disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>


                        </form>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">Range Up</th>
                                    <th class="border-primary border-darken-1">Range Down</th>
                                    <th class="border-primary border-darken-1">Zone 1 </th>
                                    <th class="border-primary border-darken-1">Zone 2 </th>
                                    <th class="border-primary border-darken-1">Zone 3 </th>
                                    <th class="border-primary border-darken-1">Zone 4 </th>
                                    <th class="border-primary border-darken-1">Zone 5 </th>
                                    <th class="border-primary border-darken-1">Zone 6 </th>
                                    <th class="border-primary border-darken-1">Zone 7 </th>
                                    <th class="border-primary border-darken-1">Zone 8 </th>
                                    <th class="border-primary border-darken-1">Zone 9 </th>
                                    <th class="border-primary border-darken-1">Zone 10 </th>
                                    <th class="border-primary border-darken-1">Zone 11 </th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            $('input.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.international.rates.update.list', ['id' => $shipper->id]) }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Range Up');
                            head.push('Range Down');
                            head.push('Zone 1');
                            head.push('Zone 2');
                            head.push('Zone 3');
                            head.push('Zone 4');
                            head.push('Zone 5');
                            head.push('Zone 6');
                            head.push('Zone 7');
                            head.push('Zone 8');
                            head.push('Zone 9');
                            head.push('Zone 10');
                            head.push('Zone 11');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.range_up);
                                row.push(values.range_down);
                                row.push(values.zone_1);
                                row.push(values.zone_2);
                                row.push(values.zone_3);
                                row.push(values.zone_4);
                                row.push(values.zone_5);
                                row.push(values.zone_6);
                                row.push(values.zone_7);
                                row.push(values.zone_8);
                                row.push(values.zone_9);
                                row.push(values.zone_10);
                                row.push(values.zone_11);

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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'International Rates',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },

                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                rowId: 'id',
                order: [[1, 'asc']],
                ajax: '{{ route('admin.international.rates.update.list', ['id' => $shipper->id]) }}',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'range_up', name: 'range_up', class: 'align-middle range_up'},
                    {data: 'range_down', name: 'range_down', class: 'align-middle range_down'},
                    {data: 'zone_1', name: 'zone_1', class: 'align-middle zone_1'},
                    {data: 'zone_2', name: 'zone_2', class: 'align-middle zone_2'},
                    {data: 'zone_3', name: 'zone_3', class: 'align-middle zone_3'},
                    {data: 'zone_4', name: 'zone_4', class: 'align-middle zone_4'},
                    {data: 'zone_5', name: 'zone_5', class: 'align-middle zone_5'},
                    {data: 'zone_6', name: 'zone_6', class: 'align-middle zone_6'},
                    {data: 'zone_7', name: 'zone_7', class: 'align-middle zone_7'},
                    {data: 'zone_8', name: 'zone_8', class: 'align-middle zone_8'},
                    {data: 'zone_9', name: 'zone_9', class: 'align-middle zone_9'},
                    {data: 'zone_10', name: 'zone_10', class: 'align-middle zone_10'},
                    {data: 'zone_11', name: 'zone_11', class: 'align-middle zone_11'}
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
            $('#update_rates_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value).replace(/,/g, '');
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Rates are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

        });

    </script>

@endsection

