@extends('admin.layout.master')

@section('title', 'International Tracking Upload')

@section('content')

    <h1 class="mb-1">
        International Tracking Upload
    </h1>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                @include('admin.inc.messages')

                <!-- <div class="row justify-content-end">
                    <div class="col-5">
                        <div class="card">
                            <div class="card-header">
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li class="primary border-primary round"><a data-action="collapse">Service Providers <i class="ft-plus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-body p-1">
                                    <h4 class=" info">Service Providers</h4>
                                    <table class="table table-sm table-bordered border mb-0 text-center">
                                        <thead>
                                        <tr>
                                            <td class="border-primary border-darken-1">ID(s)</td>
                                            <td class="border-primary border-darken-1">Name(s)</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($service_providers as $service_provider)
                                                <tr>
                                                    <td class="align-middle">{{ $service_provider->id }}</td>
                                                    <td class="align-middle">{{ $service_provider->name }}</td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

               <form id="tracking_form" class="form-horizontal" method="POST" action="{{ route('admin.international.tracking_upload.store') }}" novalidate="novalidate" enctype="multipart/form-data">
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
                                <a href="{{ asset('file/International Tracking Upload Template.xlsx') }}?v=28_10_2020" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                            </div>
                        </div>
                    </div>
                </form>-->

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Tracking No. Booking Date</th>
                        <th class="border-primary border-darken-1">3PL Tracking Number</th>
                        <th class="border-primary border-darken-1">3PL Service Provider</th>
                        <th class="border-primary border-darken-1">Seal Number</th>
                        <th class="border-primary border-darken-1">Postal Code</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">POD File</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditTrackingModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditTrackingModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit International Tracking</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit_tracking_form" action="{{ route('admin.international.tracking_upload.edit') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <input type="hidden" name="international_shipment_id" id="edit_shipment_id">
                    <div class="modal-body p-3">
                        <div class="form-group">
                            <label class="label" for="tracking_number">Tracking Number</label>
                            <input type="text" name="tracking_number" id="edit_tracking_number" class="form-control" data-rule-required="true" data-msg-required="Tracking No. is required">
                        </div>
                        <div class="form-group">
                            <label class="label" for="tracking_number">International Tracking Number</label>
                            <input type="text"  class="form-control" name="international_tracking_number" id="edit_international_tracking_number" data-rule-required="true" data-msg-required="Internatioanl Tracking No. is required">
                        </div>
                        <div class="form-group">
                            <label class="label" for="actual_weight">Actual Weight</label>
                            <input type="text" class="form-control" name="actual_weight" id="edit_actual_weight">
                        </div>

                        <div class="form-group">
                            <label for="service_provider" class="label">Service Provider</label>
                            <select name="service_provider" id="service_provider" class="form-control select2" data-rule-required="true" data-msg-required="Service provider is required">
                                @foreach($service_providers as $service_provider)
                                    <option value="{{$service_provider->id}}">{{$service_provider->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="edit_tracking_btn" type="submit" class="btn btn-info">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="upload_pod_modal" data-backdrop="static" role="dialog" aria-labelledby="upload_pod_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form id="upload_pod_form" method="post" action="{{route('admin.delivery.receive.upload_pod')}}" enctype="multipart/form-data">
                    @method('POST')
                        @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="bookings_modal_title">Upload POD</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                        
                        <input type="hidden" name="shipment_id" id="pod_shipment" >
                        <div class="form-group">
                            <label for="pod_file">
                                POD File: 
                            </label><br>
                            <input class="form-control form-control-sm" type="file" name="pod_file" id="pod_file" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                        </div>
                    

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
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
            $('#edit_tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#edit_actual_weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'min': 0.01,
                'max':100000,
            });

            $('#EditTrackingModal #service_provider').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Service Provider",
                dropdownParent:$('#EditTrackingModal')
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
                        url: '{{ route('admin.international.tracking_upload.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Tracking No. Booking Date');
                            head.push('3PL Tracking Number');
                            head.push('3PL Service Provider');
                            head.push('Seal Number');
                            head.push('Postal Code');
                            head.push('Actual Weight');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.booking_date);
                                row.push(values.international_tracking_number);
                                row.push(values.provider);
                                row.push(values.seal_number);
                                row.push(values.postal_code);
                                row.push(values.actual_weight);
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
                    title: 'International Shipments Tracking',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                scrollY:'500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                ajax: '{{ route('admin.international.tracking_upload.list') }}',
                rowId: 'shipment_id',
                order: [2, 'desc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
                    {data: 'international_tracking_number', name: 'international_shipments.international_tracking_number', class: 'align-middle international_tracking_number'},
                    {data: 'provider', name: 'issp.name', class: 'align-middle provider'},
                    {data: 'seal_number', name: 'international_shipments.seal_number', class: 'align-middle seal_number'},
                    {data: 'postal_code', name: 'international_shipments.postal_code', class: 'align-middle postal_code'},
                    {data: 'actual_weight', name: 'international_shipments.actual_weight', class: 'align-middle actual_weight'},
                    {data: 'pod_file', name: 'international_shipments.pod_file', class: 'align-middle pod_file', orderable: false, searchable: false},
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

            $('#tracking_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Your shipment(s) are being updated!',
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
                    if($(this).hasClass('remove')){

                        $.ajax({
                            url: '{!! route('admin.international.tracking_upload.edit') !!}',
                            data: {
                                'shipment_id': id
                            }
                        }).done(function(data) {
                            if(data.status == 0){
                                $('#edit_tracking_number').val(data.details.tracking_number);
                                $('#edit_actual_weight').val(data.details.actual_weight);
                                $('#edit_international_tracking_number').val(data.details.international_tracking_number);
                                $('#edit_shipment_id').val(data.details.id);
                                $('#actual_weight').val(data.details.actual_weight);

                                if(data.details.shipment_status == 1){
                                    $("#edit_actual_weight").prop("readonly", true);
                                }
                                $('#EditTrackingModal').modal('show');
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                }

            });

            $('#edit_tracking_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Tracking Number is being edited!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#upload_pod_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Uploading POD File!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            

            $('body').on('click', 'button.upload_pod',  function(){
                var id = $(this).parents('tr').attr('id');
                $('#pod_shipment').val(id);

                $('#upload_pod_modal').modal('show');
                // if(id){
                //     $('#corporate_rate_type_modal').modal('show');
                //     $('#corporate_rate_type_shipper_id').val(id);
                // }
            });
            $('body').on('click', 'button.replace_pod',  function(){
                var id = $(this).parents('tr').attr('id');
                $('#pod_shipment').val(id);

                $('#upload_pod_modal').modal('show');
                // if(id){
                //     $('#corporate_rate_type_modal').modal('show');
                //     $('#corporate_rate_type_shipper_id').val(id);
                // }
            });
        });
    </script>
@endsection