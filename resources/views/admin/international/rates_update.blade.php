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
                        <form id="update_rates_form" class="row mb-1" novalidate="novalidate" action="{{ route('admin.international.rates.update.submit') }}" method="post">
                            @csrf
                            @method('post')

                            <input type="hidden" id="shipper_id" name="shipper_id" value="{{ $shipper->id }}">

                            <div class="col-4 form-group">
                                <label><strong>Fuel Surcharge</strong></label>
                                <div class="input-group">
                                    <input type="text" name="fuel_surcharge" class="form-control fuel_surcharge" placeholder="Fuel Surcharge" data-rule-required="true" data-msg-required="Fuel Surcharge is required" disabled value="{{$fuel_surcharge}}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 form-group">
                                <label><strong>Exchange Rate</strong></label>
                                <div class="input-group">
                                    <input type="text" name="exchange_rate" value="{{$exchange_charges}}" class="form-control amount"  placeholder="Exchange Rate*" data-rule-required="true" data-msg-required="Exchange Rate is required" disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text">PKR</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 form-group">
                                <label><strong>GST</strong></label>
                                <div class="input-group">
                                    <input type="text" name="gst" class="form-control gst decimal" placeholder="GST*" data-rule-required="true" data-msg-required="GST is required" value="{{ $gst }}" disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                           @foreach($marginZoneColumn['zoneColumnArray'] as $index => $zone)
                                @php
                                    $marginKey = $marginZoneColumn['marginColumn'][$index] ?? null;
                                @endphp
                                @if($marginKey)
                                    <div class="col-2 form-group">
                                        <label><strong>Margin For {{ strtoupper(str_replace('_', ' ', $zone)) }}</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="{{ $marginKey }}" class="form-control margin decimal"
                                                placeholder="Margin {{ ucfirst(str_replace('_', ' ', $zone)) }}*" 
                                                data-rule-required="true" 
                                                data-msg-required="Margin is required" 
                                                value="{{ $margin[$marginKey] ?? '' }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                           
                            <div class="col-12 form-group text-center">
                                {{--                                <button type="submit" name="submit" class="btn btn-primary" value="submit">Submit</button>--}}

                                <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Update Rates</button>
                                <input type="hidden" name="authorize" id="authorize">
                                <input type="hidden" name="approve" id="approve">
                                @if($user_information != null)
                                    @if ($user_information->status == 4 && (session('role_id') == 1 || in_array(8, session('permissions'))))
                                        <button id="accountActiveSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Approve</button>
                                        <button id="AuthorizeaccountRejectActiveSubmit" type="button" class="btn btn-outline-danger round btn-min-width mr-1 mb-1">Reject Rates</button>
                                    @endif
                                    @if ($user_information->status == 2 && (session('role_id') == 1 || in_array(140, session('permissions'))))
                                        <button id="accountApproveActiveSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Approve</button>
                                        <button id="accountRejectActiveSubmit" type="button" class="btn btn-outline-danger round btn-min-width mr-1 mb-1">Reject Rates</button>
                                    @endif
                                @endif
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
                                     @foreach ($marginZoneColumn['zoneColumnArray'] as $key => $value) 
                                    <th><?php echo ucfirst(str_replace('_', ' ', $value)) ?></th>
                                    @endforeach

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="RejectRatesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RejectRatesModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Write a reason to reject rates!</h4>
                </div>
                <div class="modal-body">
                    <textarea id="reject_reason" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" id="RejectRatesSubmit">Yes</button>
                </div>
            </div>
        </div>
    </div>
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
         var zoneColumns = @json($marginZoneColumn['zoneColumnArray']);
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
             jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                    if (this.context.length) {
                        let body = [];
                        let head = ['S.No', 'Range Up', 'Range Down']; // Default columns

                        var params = table.ajax.params();
                        params.start = 0;
                        params.length = -1;

                        var jsonResult = $.ajax({
                            url: '{{ route('admin.international.rates.update.list', ['id' => $shipper->id]) }}',
                            data: params,
                            async: false, // ✅ Corrected placement of async
                            success: function (result) {
                                if (result.data.length > 0) {
                                    // Dynamically extract zone columns from the first result row
                                    let firstRow = result.data[0];
                                    let zoneColumns = Object.keys(firstRow).filter(key => key.startsWith("zone_"));

                                    // Add dynamically found zone columns to the header
                                    head.push(...zoneColumns.map(zone => zone.replace('_', ' ').toUpperCase()));

                                    // Process data rows
                                    $.each(result.data, function (index, values) {
                                        let row = [];

                                        row.push(index + 1); // Serial number
                                        row.push(values.range_up);
                                        row.push(values.range_down);

                                        // Add dynamic zone values
                                        zoneColumns.forEach(zone => {
                                            row.push(values[zone]);
                                        });

                                        body.push(row);
                                    });
                                }
                            }
                        });

                        return { body: body, header: head };
                    }
            });


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
                ].concat(zoneColumns.map(zone => ({
                    data: zone,
                    name: zone,
                    class: 'align-middle ' + zone
                }))),
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
            var auth_reject = 0;
            $('#accountRejectActiveSubmit').click(function() {
                $('#RejectRatesModal').modal('show');
                auth_reject = 0;
            });
            $('#AuthorizeaccountRejectActiveSubmit').click(function() {
                $('#RejectRatesModal').modal('show');
                auth_reject = 1;
            });
            $('#RejectRatesSubmit').on('click',function () {
                var shipper = $('#shipper_id').val();
                var reject_reason = document.getElementById('reject_reason').value;
                if(reject_reason){
                    $.ajax({
                        url: '{!! route('admin.international.rates.update.reject') !!}',
                        method: 'POST',
                        data: {
                            'rejected_reason': reject_reason,
                            'shipper_id':shipper,
                            'authorization':auth_reject,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            $('#RejectRatesModal').modal('hide');
                            window.setTimeout(function () {window.location.reload()}, 3000);
                        });
                }else{
                    var error = "You have not selected any reason!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
            $('#accountActiveSubmit').on('click',function(){
                $('#authorize').val(1);
            });
            $('#accountApproveActiveSubmit').on('click',function(){
                $('#approve').val(1);
                // console.log('ddd');
            });
            $('#update_rates_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value).replace(/,/g, '');
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    var msg = "";
                    if($('#authorize').val() == 1 || $('#approve').val() == 1){
                        msg = "Rates are being approved!"
                    }else{
                        msg = 'Rates are being updated!';
                    }
                    swal({
                        title: 'Please Wait!',
                        text: msg,
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

