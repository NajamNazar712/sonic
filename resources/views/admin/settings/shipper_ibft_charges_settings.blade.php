@extends('admin.layout.master')

@section('title', 'Shipper IBFT Charges Settings')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Shipper IBFT Charges Settings
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.sales.projection.shipments.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <select name="shippers[]" id="shipper_select" class="form-control select2" multiple="multiple" data-msg-required="Atleast one Shipper is required" data-rule-required="true" required="required">
                                                @foreach($shippers as $person)
                                                    <option value="{{$person->id}}">{{$person->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group text-left">
                                            <fieldset>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="customCheck" id="customCheck1">
                                                    <label class="custom-control-label" for="customCheck1">Select All</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Ibft Charges</span>
                                                </div>
                                                <input type="text" name="Ibft_charges" class="form-control class" placeholder="Ibft Charges*" data-rule-required="true" data-msg-required="Ibft Charges is required" value="">

                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(count($shipments) > 0)
                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">

                                <div class="row justify-content-center">
                                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                        <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">Shipper</th>
                                            <th class="border-primary border-darken-1">Ibft Charges</th>

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
            $('#shipper_select').select2({
                placeholder:'Shipper Select',
                width:'100%'
            });
            $("#customCheck1").click(function(){
                if($("#customCheck1").is(':checked') ){
                    $("#shipper_select > option").prop("selected","selected");
                    $("#shipper_select").trigger("change");
                }else{
                    $("#shipper_select > option").removeAttr("selected");
                    $("#shipper_select").val(null).trigger("change");
                }
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

            @if(count($shipments) > 0)
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.sales.projection.shipments.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Shipper Name');
                            head.push('Ibft Charges');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.shipment);

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
                        title: 'Business Projected Shipments',
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
                ajax: '{{ route('admin.settings.sales.projection.shipments.list') }}',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipper', name:'u.name', class: 'align-middle shipper'},
                    {data: 'shipment', name: 'business_projection_shipments.shipment', class: 'align-middle shipment'},

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

        });


    </script>
@endsection