@extends('admin.layout.master')

@section('title', 'International Wholesale Excel Booking')

@section('content')

    <h1 class="mb-1">
        International Wholesale Excel Booking
    </h1>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="shipment_excel_form" class="form-horizontal" method="POST" action="{{ route('admin.international.wholesale.excel.store') }}" novalidate="novalidate" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="row align-items-center justify-content-center">
                        <div class="col">
                            <div class="form-group">
                                <input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group text-left">
                                <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                            </div>
                        </div>

                        <div class="col ml-auto">
                            <div class="form-group text-right">
                                <a href="{{ asset('file/International Wholesale Booking Template.xlsx') }}?v=14_09_2022" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">Trax Shipment No.</th>
                        <th class="border-primary border-darken-1">DHL Waybill No.</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Type</th>
                        <th class="border-primary border-darken-1">Weight</th>
                        <th class="border-primary border-darken-1">Pieces</th>
                        <th class="border-primary border-darken-1">Courier Charges</th>
                        <th class="border-primary border-darken-1">Other Charges</th>
                        <th class="border-primary border-darken-1">Bill Amount</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Booked By</th>
                        <th class="border-primary border-darken-1">Booking Date/Time</th>
                        <th class="border-primary border-darken-1">Edit By</th>
                        <th class="border-primary border-darken-1">Edit Date/Time</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditShipmentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditShipmentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Shipment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit_shipment_form" action="{{ route('admin.international.wholesale.excel.edit') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <input type="hidden" name="shipment_id" id="edit_shipment_id">
                    <div class="modal-body p-3">
                        <div class="form-group">
                            <label class="label" for="tracking_number">DHL Waybill No.</label>
                            <input type="text" name="dhl_waybill" id="edit_dhl_waybill" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label class="label" for="tracking_number">Destination</label>
                            <select name="destination_city_id" id="edit_destination_city_id" class="form-control select2" data-rule-required="true"  data-msg-required="Destination City is required">
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}"> {{ $city->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="label" for="type">Type</label>
                            <input type="text" name="type" id="edit_type" class="form-control" data-rule-required="true" data-msg-required="Type is required">
                        </div>
                        <div class="form-group">
                            <label class="label" for="weight">Weight</label>
                            <input type="text" class="form-control weight" name="weight" id="edit_weight" data-rule-required="true" data-msg-required="Shipment weight is required">
                        </div>
                        <div class="form-group">
                            <label class="label" for="pieces">Pieces</label>
                            <input type="text" class="form-control pieces" name="pieces" id="edit_pieces" data-rule-required="true" data-msg-required="Shipment pieces is required">
                        </div>

                        <div class="form-group">
                            <label class="label" for="other charges">Other Charges</label>
                            <input type="text" class="form-control other_charges" name="other_charges" id="edit_other_charges" data-rule-required="true" data-msg-required="Other Charges are required">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="edit_shipment_btn" type="submit" class="btn btn-info">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="shipment_log_modal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="shipment_log_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Shipment Logs</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#edit_pieces').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#edit_weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'min': 0.01,
                'max':100000,
            });

            $('#EditShipmentModal #edit_destination_city_id').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Destination",
                dropdownParent:$('#EditShipmentModal')
            });
            $('body').on('change','#edit_international_tracking_number',function() {
                $(this).val($(this).val().trim());
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.international.wholesale.excel.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Trax Shipment No.');
                            head.push('DHL Waybill No.');
                            head.push('Shipper Name');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Type');
                            head.push('Weight');
                            head.push('Pieces');
                            head.push('Courier Charges');
                            head.push('Other Charges');
                            head.push('Bill Amount');
                            head.push('Status');
                            head.push('Booked By');
                            head.push('Booking Date/Time');
                            head.push('Edit By');
                            head.push('Edit Date/Time');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.shipment_id);
                                row.push(values.dhl_waybill);
                                row.push(values.shipper_name);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.type);
                                row.push(values.weight);
                                row.push(values.pieces);
                                row.push(values.courier_charges);
                                row.push(values.other_charges);
                                row.push(values.bill_amount);
                                row.push(values.shipment_status);
                                row.push(values.booked_by);
                                row.push(values.booking_date);
                                row.push(values.updated_by);
                                row.push(values.updated_date);
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
                buttons:[{
                    extend: 'excel',
                    title: 'International Wholesale Booking',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                ajax: '{{ route('admin.international.wholesale.excel.list') }}',
                rowId: 'shipment_id',
                order: [14, 'desc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipment_id', name: 'wholesale_shipments.id', class: 'align-middle shipment_id'},
                    {data: 'dhl_waybill', name: 'wholesale_shipments.dhl_waybill', class: 'align-middle dhl_waybill'},
                    {data: 'shipper_name', name: 'wu.name', class: 'align-middle shipper_name'},
                    {data: 'origin', name: 'o.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'd.name', class: 'align-middle destination'},
                    {data: 'type', name: 'wholesale_shipments.type', class: 'align-middle type'},
                    {data: 'weight', name: 'wholesale_shipments.weight', class: 'align-middle type'},
                    {data: 'pieces', name: 'wholesale_shipments.pieces', class: 'align-middle pieces'},
                    {data: 'courier_charges', name: 'wholesale_shipments.courier_charges', class: 'align-middle courier_charges'},
                    {data: 'other_charges', name: 'wholesale_shipments.other_charges', class: 'align-middle other_charges'},
                    {data: 'bill_amount', name: 'wholesale_shipments.bill_amount', class: 'align-middle bill_amount'},
                    {data: 'shipment_status', name: 'wss.name', class: 'align-middle shipment_status'},
                    {data: 'booked_by', name: 'cb.name', class: 'align-middle booked_by'},
                    {data: 'booking_date', name: 'wholesale_shipments.created_at', class: 'align-middle booking_date'},
                    {data: 'updated_by', name: 'ub.name', class: 'align-middle updated_by'},
                    {data: 'updated_date', name: 'wholesale_shipments.updated_at', class: 'align-middle updated_date'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

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

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.pod_file')) {
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

            $('#edit_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Your shipment is being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('#datatable tbody').on('click', 'tr td.action button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    if($(this).hasClass('edit')){

                        $.ajax({
                            url: '{!! route('admin.international.wholesale.excel.edit_info') !!}',
                            data: {
                                'shipment_id': id
                            }
                        }).done(function(data) {
                            if(data.status == 0){
                                $('#edit_dhl_waybill').val(data.details.dhl_waybill);
                                $('#edit_destination_city_id').val(data.details.destination_city_id).trigger('change');
                                $('#edit_type').val(data.details.type);
                                $('#edit_weight').val(data.details.weight);
                                $('#edit_pieces').val(data.details.pieces);
                                $('#edit_shipment_id').val(data.details.id);
                                $('#edit_other_charges').val(data.details.other_charges);

                                $('#EditShipmentModal').modal('show');
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                    else if($(this).hasClass('cancel')){

                        swal({
                            text: 'Are you sure, you want to cancel this shipment?',
                            icon: 'warning',
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
                                if(id) {
                                    blockPagePermanently();
                                    $.ajax({
                                        url: '{!! route('admin.international.wholesale.excel.cancel') !!}',
                                        method:'POST',
                                        data: {
                                            '_token':'{{ csrf_token() }}',
                                            'shipment_id': id
                                        }
                                    }).done(function(data) {
                                        if(data.status == 1){

                                            table.draw('false');
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                        }else{

                                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                        }
                                        UnblockPagePermanently();


                                    });
                                }
                            }
                        });
                    }
                }

            });

            $('#shipment_excel_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Shipment are being booked!',
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