@extends('admin.layout.master')
@section('title','Add Lost Shipments')

@section('content')
    <h1 class="mb-1">
        Add Lost Shipments
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="lost_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="row align-items-center justify-content-center">
                        <div class="form-group">
                            <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                        </div>
    
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                        
                    </div>
                </form>

                <form id="excel_upload_form" class="form-horizontal" method="POST"  novalidate="novalidate" enctype="multipart/form-data">
                    

                    <div class="row align-items-center justify-content-center">
                        <div class="col">
                            <div class="form-group">
                                <input type="file" name="excel" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB)." id="excel">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group text-left">
                                <button type="submit" name="upload" class="btn btn-primary" value="upload">Upload</button>
                            </div>
                        </div>

                        <div class="col ml-auto">
                            <div class="form-group text-right">
                                <a href="{{ asset('file/Bulk Lost Shipments Template.xlsx') }}?v=14_07_2023" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                            </div>
                        </div>
                    </div>
                </form>
               

                <form id="update_lost_form" action="{{route('admin.delivery.lost.add.shipments.store')}}" class="form-horizontal" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            <th class="border-primary border-darken-1">Amount</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Shipping Mode</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1"></th>
                        </tr>
                        </thead>
                    </table>
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary btn-block" disabled id="update_lost_form_submit">Submit</button>

                        </div>

                    </div>
                </form>

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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var shipment_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                "autoWidth": false,
                paging:false,

                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'shipper_name', class: 'align-middle shipper_name form-group', orderable: false},
                    {name: 'origin', class: 'align-middle origin form-group', orderable: false},
                    {name: 'destination', class: 'align-middle destination form-group', orderable: false},
                    {name: 'hub', class: 'align-middle hub form-group', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                    {name: 'remarks', class: 'align-middle remarks', orderable: false},
                    {name: 'mode', class: 'align-middle mode', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'action', class: 'align-middle action', orderable: false},
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();
                    //
                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {

                    this.api().table().columns.adjust();
                }
            });
            var rowsCount = 0;

            $('#lost_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            {{--var cities_array = @json($cities);--}}
            $('#lost_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#lost_shipment_form button.add').prop('disabled', true);

                    var tracking_number = $(form).find('input.tracking_number').val();

                    form.reset();

                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        blockPagePermanently();
                        $.ajax({
                            url: '{!! route('admin.delivery.lost.add.shipment.info') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 1) {
                                    UnblockPagePermanently();
                                    id = data.details.id;

                                    var index = $.inArray(id, shipment_ids);

                                    if (index === -1) {
                                        var rowNo = table.rows().count();

                                        var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
                                        table.row.add([rowNo + 1, data.details.tracking_number, data.details.shipper_name, data.details.origin, data.details.destination, data.details.hub, data.details.amount, data.details.remarks,data.details.mode,data.details.service_type, action]).node().id = data.details.id;
                                        table.draw(false);
                                        scan_sound(1);
                                        table.order([0, 'desc']).draw();

                                        shipment_ids.push(data.details.id);

                                        $('#lost_shipment_form button.add').prop('disabled', false);

                                        $('#update_lost_form_submit').prop('disabled', false);

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else {
                                    UnblockPagePermanently();
                                    $('#lost_shipment_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                    else {
                        $('#lost_shipment_form button.add').prop('disabled', false);
                        scan_sound(2);
                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            // function validateEmail(email) {
            //     var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            //     return re.test(email);
            // }
            
            // $('#excel_upload_form').validate({
            //     errorClass: 'danger',
            //     successClass: 'success',
            //     errorPlacement: function(error, element) {
            //         error.addClass('w-100').appendTo(element.parents('form'));
            //     },
            //     submitHandler: function(form) {
            //         $('#excel_upload_form button.upload').prop('disabled', true);
                    
            //         var fileInput = $(form).find('#excel').val();
                    
            //         var file = fileInput.files && fileInput.files.length > 0 ? fileInput.files[0] : null;
            //         var tracking_number = file ? file.name : "";
            //         alert(tracking_number);
            //         // var file = fileInput.files[0];
            //         // alert(file);
            //         // var tracking_number = file ? file.name : "";
                   

            //         // var fileInput = document.getElementById('tracking_file');
                    
            //         form.reset();
            //         if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
            //             blockPagePermanently();
            //             $.ajax({
            //                 url: '{!! route('admin.delivery.lost.add.bulk.lost') !!}',
            //                 method: 'POST',
            //                 data: {
            //                     'tracking_number': tracking_number,
            //                     '_token': '{{ csrf_token() }}'
            //                 }
            //             })
            //                 .done(function(data) {
            //                     if (data.status == 1) {
            //                         UnblockPagePermanently();
            //                         id = data.details.id;

            //                         var index = $.inArray(id, shipment_ids);

            //                         if (index === -1) {
            //                             var rowNo = table.rows().count();

            //                             var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
            //                             table.row.add([rowNo + 1, data.details.tracking_number, data.details.shipper_name, data.details.origin, data.details.destination, data.details.hub, data.details.amount, data.details.remarks,data.details.mode,data.details.service_type, action]).node().id = data.details.id;
            //                             table.draw(false);
            //                             scan_sound(1);
            //                             table.order([0, 'desc']).draw();

            //                             shipment_ids.push(data.details.id);

            //                             $('#lost_shipment_form button.add').prop('disabled', false);

            //                             $('#update_lost_form_submit').prop('disabled', false);

            //                             toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
            //                         }
            //                     }
            //                     else {
            //                         UnblockPagePermanently();
            //                         $('#lost_shipment_form button.add').prop('disabled', false);
            //                         scan_sound(2);
            //                         toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            //                     }
            //                 });
            //         }
            //         else {
            //             $('#lost_shipment_form button.add').prop('disabled', false);
            //             scan_sound(2);
            //             toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            //         }

            //         return false;
            //     }
            // });
            $('#excel_upload_form').validate({
            errorClass: 'danger',
            successClass: 'success',
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('form'));
            },

            submitHandler: function(form) {
                // Disable the submit button to prevent multiple submissions
                $('#excel_upload_form button.upload').prop('disabled', true);

                // Get the file input element and selected file
                var fileInput = $(form).find('#excel')[0];
                var file = fileInput.files && fileInput.files.length > 0 ? fileInput.files[0] : null;

                // Create a new FormData object
                var formData = new FormData();
                formData.append('excel', file);

                // Make the AJAX request
                $.ajax({
                    url: '{!! route('admin.delivery.lost.add.bulk.lost') !!}',
                    method: 'POST',
                    data: formData,
                    processData: false, // Prevent automatic processing of data
                    contentType: false, // Prevent automatic content-type header
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.status == 1) {
                                UnblockPagePermanently();
                                id = data.details.id;

                                var index = $.inArray(id, shipment_ids);

                                if (index === -1) {
                                    var rowNo = table.rows().count();

                                    var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
                                    table.row.add([rowNo + 1, data.details.tracking_number, data.details.shipper_name, data.details.origin, data.details.destination, data.details.hub, data.details.amount, data.details.remarks,data.details.mode,data.details.service_type, action]).node().id = data.details.id;
                                    table.draw(false);
                                    scan_sound(1);
                                    table.order([0, 'desc']).draw();

                                    shipment_ids.push(data.details.id);

                                    $('#lost_shipment_form button.add').prop('disabled', false);

                                    $('#update_lost_form_submit').prop('disabled', false);

                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            }
                            else{
                                // Handle error response and display errors
                                var errorMessages = data.errors.join('\n');
                                toastr.error(errorMessages, 'Error!', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
                                // ...
                            }
                        // else {
                        //     alert(data.error);
                        //     UnblockPagePermanently();
                        //     $('#excel_upload_form button.upload').prop('disabled', false);
                        //     scan_sound(2);
                        //     toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        // }
                    },
                    error: function(xhr, status, error) {
                        
                    },
                    complete: function() {
                        // Enable the submit button after request completes
                        $('#excel_upload_form button.upload').prop('disabled', false);
                    }
                });

                return false;
            }
        });


            $('#update_lost_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    // $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to add to Lost Shipments!',
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
                    }).then(function (confirm) {
                        if(confirm){
                            blockPagePermanently();
                            $('#update_lost_form button[type="submit"]').attr('disabled', 'disabled');
                            $('#update_lost_form input#shipment_ids').val(shipment_ids);
                            form.submit();
                        }
                    });

                    // form.submit();
                }
            });


            $('body').on('click','.action a.removerow',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, shipment_ids);

                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                }
                table.row( $(this).parents('tr') ).remove().draw();
                if(shipment_ids.length == 0){
                    $('#update_lost_form button[type="submit"]').attr('disabled', 'disabled');
                }
            });

        });
    </script>
@endsection