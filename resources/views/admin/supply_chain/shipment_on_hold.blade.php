@extends('admin.layout.master')

@section('title', 'Supply Chain Shipment On Hold')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Supply Chain Shipment On Hold
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="form-group">
                                    <input type="text" name="tracking_numbers" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="search" class="btn btn-primary search" value="Search">Search</button>
                                </div>
                            </form>

                            <form id="on_hold_form" class="form mb-1 justify-content-center mt-2" method="POST" action="{{ route('admin.cargo.supply_chain.shipment_on_hold.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <input type="hidden" name="shipment_ids" id="shipment_ids" class="shipment_ids">
                                <div class="row justify-content-center mb-1">
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                            </div>
                                            <input type="text" name="dispatch_date" class="form-control bg-primary border-primary white rounded-right" id="dispatch_date" placeholder="Dispatch Date*" data-rule-required="true" data-msg-required="Dispatch Date is required">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="delivery_date" class="form-control bg-primary border-primary white rounded-right" id="delivery_date" placeholder="Delivery Date*" data-rule-required="true" data-msg-required="Delivery Date is required">
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Tracking No.</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Consignee Name</th>
                                        <th class="border-primary border-darken-1">Phone</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Collection Amount</th>
                                        <th class="border-primary border-darken-1">Service Type</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1"></th>
                                    </tr>
                                    </thead>
                                </table>

                                <div class="form-group text-center">
                                    <button type="submit" name="on_hold" id="on_hold" class="btn btn-primary change" value="on_hold" disabled>On-Hold</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        table.table.table-sm td {
            border: 1px solid #626E82 !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var today = '{{\Carbon\Carbon::now()->toDateString()}}';
            var dispatch_date = $('#dispatch_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                min: today,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#dispatch_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#delivery_date').pickadate('picker').set('min', $('#dispatch_date').pickadate('picker').get('select'));
                    }
                }
            });
            var delivery_date = $('#delivery_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                min: today,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#delivery_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#dispatch_date').pickadate('picker').set('max', $('#delivery_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                paging:false,
                autoWidth: false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false},
                    {name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
                    {name: 'phone', class: 'align-middle phone', orderable: false},
                    {name: 'address', class: 'align-middle address', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'status', class: 'align-middle status', orderable: false},
                    {name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            var rowNo = 0;
            var shipment_ids = [];
            var tracking_ids = [];
            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $(form).find('button.search').prop('disabled', true);

                    $('#shipment').html('');

                    $('#on_hold_form input.tracking_number').val('');

                    var tracking_number = $(form).find('input.tracking_number').val();

                    form.reset();

                    var is_indexed = $.inArray(tracking_number, tracking_ids);
                    if(is_indexed === -1) {
                        $.ajax({
                            url: '{!! route('admin.cargo.supply_chain.shipment_on_hold.shipment_details') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'tracking_number': tracking_number
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    rowNo++;
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove_row"><i class="la la-close"></i></a>';
                                    var row = table.row.add([rowNo, data.tracking_number, data.destination, data.consignee_name, data.phone, data.address, data.amount, data.service_type, data.shipment_status, remove]).node().id = data.shId;
                                    shipment_ids.push(data.shId);
                                    tracking_ids.push(tracking_number);
                                    table.draw();
console.log(shipment_ids.length);
                                    if(shipment_ids.length > 0){
                                        $('#on_hold').prop('disabled', false);
                                    }

                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {

                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                $(form).find('button.search').prop('disabled', false);
                            });
                    }
                    else{
                        var error = 'Shipment already scanned';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        $(form).find('button.search').prop('disabled', false);
                    }

                    return false;
                }
            });
            $('body').on('click','a.remove_row',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, shipment_ids);

                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                    tracking_ids.splice(index, 1);
                    rowNo -= 1;
                    if(shipment_ids.length <= 0){
                        $('#on_hold').prop('disabled', true);
                    }
                }

                table.row( $(this).parents('tr') ).remove().draw();
            });

            $('#on_hold_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#shipment_ids').val(shipment_ids);
                    form.submit();
                }
            });
        });
    </script>
@endsection