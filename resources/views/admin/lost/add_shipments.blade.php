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
                <div class="alert alert-danger d-none">
                    
                </div>


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

               
                @if( session('role_id') == 1|| in_array(890, session('permissions')))
                <form id="excel_upload_form" class="form-horizontal" method="POST"  novalidate="novalidate" enctype="multipart/form-data">

                    <div class="row align-items-center justify-content-center">
                        <div class="col">
                            <div class="form-group mb-0">
                                <input type="file" name="excel" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB)." id="excel">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group pt-2 text-left">
                                <button type="submit" name="upload" id="upload" class="btn btn-primary" value="upload" disabled>Upload</button>
                            </div>
                        </div>

                        <div style="padding-right: 300px;">
                            <fieldset class="form-group">
                                <select name="employee_excel" class="employee_excel" id="employee_excel" class="form-control select2 dynamic" data-dependent="from"
                                        required>
                                    @foreach($employees as $employee)
                                        <option value="{{$employee->trax_id}}">{{$employee->name . ' - ' . $employee->trax_id}}</option>
                                    @endforeach
                                </select>
                                <div class="danger" id="hub_error" style="display:none;">This field is required</div>
                            </fieldset>
                        </div>
                        <div class="col ml-auto">
                            <div class="form-group text-right">
                                <a href="{{ asset('file/Bulk Lost Shipments Template.xlsx') }}?v=14_07_2023" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                            </div>
                        </div>
                    </div>
                </form>
               @endif

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
                            <th class="border-primary border-darken-1">Employee ID</th>
                            <th class="border-primary border-darken-1">Employee Name</th>
                            <th class="border-primary border-darken-1">Employee Type</th>
                            <th class="border-primary border-darken-1">Shipping Mode</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1"></th>
                        </tr>
                        </thead>
                    </table>
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <input type="hidden" name="trax_id" id="trax_id">

                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary btn-block" disabled id="update_lost_form_submit">Submit</button>

                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="excel_upload_error" role="dialog" aria-labelledby="excel_upload_error_title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="rider_information_title">Error In Excel File</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

<style>
    .error-text {
    color: red;
}
label.error {
    display: block;
    margin-top: 0;
    padding-top: 0;
}
</style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var excel_employee_value;
            $('#employee_excel').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Employee*',
                allowClear: true,
            }).on('change', function(){
                excel_employee_value = $(this).val();
                if(excel_employee_value){
                    $('#excel_upload_form #upload').removeAttr('disabled');
                    trax_id.push(excel_employee_value)
                    $('#update_lost_form input#trax_id').val(trax_id);
                }else{
                    $('#excel_upload_form #upload').attr('disabled', true)
                }
            });
            var employees = @json($employees); // Assuming $admins is a PHP variable containing admin data
            var shipment_ids = [];
            var trax_id = [];

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
                    {
                        name: 'employee_id',
                        class: 'align-middle admin_select',
                        orderable: false,
                    },                    
                    {name: 'employee_name', class: 'align-middle employee_name', orderable: false},
                    {name: 'employee_type', class: 'align-middle employee_type', orderable: false},
                    {name: 'mode', class: 'align-middle mode', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'action', class: 'align-middle action', orderable: false},
                ],
                rowCallback: function(row, data, index) {
                
                },
                initComplete: function() {

                    this.api().table().columns.adjust();
                }
            });
            var rowsCount = 0;
            var counter = 0;
            var counter_excel = 0;
            var limit = 19;
            var excluded_shipment = [];
            var included_employee_lost_shipment = {};

            var employeeDropdownHtml = '<select class="form-control employeeDropdownHtml" multiple = "multiple"><option value=""></option>';
            $.each(employees, function (index, value) {
                if(value.trax_id){
                    employeeDropdownHtml += `<option value="${value.trax_id}">${value.trax_id}</option>`;
                }
            });
            employeeDropdownHtml += '</select>';
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
                                if (data.status == 1 && counter <= limit) {
                                    UnblockPagePermanently();
                                    id = data.details.id;
                                    var inputElementName;
                                    var inputElementType;
                                    var index = $.inArray(id, shipment_ids);

                                    if (index === -1) {
                                        var rowNo = table.rows().count();                                 
                                        counter+=1;
                                        var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
                                        table.row.add([rowNo + 1, data.details.tracking_number, data.details.shipper_name, data.details.origin, data.details.destination, data.details.hub, data.details.amount, employeeDropdownHtml, data.details.employee_name, data.details.employee_type, data.details.mode, data.details.service_type, action]).node().id = data.details.id;
                                        table.draw(false);

                                        $('.employeeDropdownHtml').select2({
                                            width: '100%',
                                            placeholder: "Search Here...",
                                            minimumInputLength: 5,
                                            maximumSelectionLength: 3, 
                                        }).on('change', function(e) {
                                            var traxID = $(this).val();
                                            var row = $(this).closest('tr');
                                            var trackingNumber = data.details.tracking_number;
                                            
                                            if (!included_employee_lost_shipment[trackingNumber]) {
                                                included_employee_lost_shipment[trackingNumber] = [];
                                            }
                                            $.each(traxID, function(index, value) {
                                                var existingChangeIndex = included_employee_lost_shipment[trackingNumber].findIndex(function(item) {
                                                    return item.value === value;
                                                });
                                                
                                                if (existingChangeIndex === -1) {
                                                    included_employee_lost_shipment[trackingNumber].push({
                                                        value: value,
                                                    });
                                                }
                                                $('#update_lost_form input#trax_id').val(JSON.stringify(included_employee_lost_shipment));

                                                $.ajax({
                                                    url: '{!! route('admin.human_resource.employee_confirmation.get_employee_info_name_type') !!}',
                                                    method: 'POST',
                                                    data: {
                                                        '_token': '{{ csrf_token() }}',
                                                        'trax_id': value
                                                    },
                                                    success: function(response) {
                                                        if (response.status == 1) {
                                                            inputElementName = row.find('input[name="employee_name[' + data.details.id + index + ']"]');
                                                            inputElementName.val(response.details.name);
                                                            inputElementName.removeClass('d-none');

                                                            inputElementType = row.find('input[name="employee_type[' + data.details.id + index + ']"]');
                                                            inputElementType.val(response.details.type);
                                                            inputElementType.removeClass('d-none');
                                                        } 
                                                    },
                                                    error: function(xhr, status, error) {
                                                        console.error(xhr.responseText);
                                                    }
                                                });
                                            });
                                        }).on('select2:unselecting', function(e) {
                                            var unselectedValue = e.params.args.data.id;
                                            var trackingNumber = data.details.tracking_number;
                                            if (included_employee_lost_shipment[trackingNumber]) {
                                                included_employee_lost_shipment[trackingNumber] = included_employee_lost_shipment[trackingNumber].filter(function(item) {
                                                    return item.value !== unselectedValue;
                                                });
                                            }

                                            var row = $(this).closest('tr');
                                            var inputElementName = row.find('input[name^="employee_name"]');
                                            var inputElementType = row.find('input[name^="employee_type"]');
                                            inputElementName.addClass('d-none');
                                            inputElementType.addClass('d-none');
                                        });
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
                                    toastr.error(data.error ? data.error : 'Only 20 shipments is allowed to select !!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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

            $('#excel_upload_form').validate({
            errorClass: 'danger',
            successClass: 'success', 
           
            
            errorPlacement: function(error, element) {
                error.addClass('w-100 mt-0').appendTo(element.parents('form'));
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
                formData.append('excel_employee_value', excel_employee_value);
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
                                var shipmentData = data.details;
                                var shipmentAdded = false;
                                var shipmentIDs = Object.keys(shipmentData);
                                var shipment;
                                var alreadyAddedShipments = [];
                                id = data.details;
                                
                                $.each(shipmentIDs, function (index, id) {
                                    var tracking_number = shipmentData[id].tracking_number;
                                    if(counter_excel <= limit){
                                        var index = $.inArray(id, shipment_ids);
                                        shipment = shipmentData[id];
                                        if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(shipment.tracking_number)) === -1) {
                                            var rowNo = table.rows().count();
                                            counter_excel+=1
                                            var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
                                            table.row.add([
                                            rowNo + 1,
                                            shipment.tracking_number,
                                            shipment.shipper_name,
                                            shipment.origin,
                                            shipment.destination,
                                            shipment.hub,
                                            shipment.amount,
                                            shipment.employee_trax_id, shipment.employee_name, shipment.employee_type,
                                            shipment.mode,
                                            shipment.service_type,
                                            action
                                        ]).node().id = id;
                                        
                                        $('.employee_excel').on('change', function() {
                                            $.ajax({
                                                url: '{!! route('admin.human_resource.employee_confirmation.get_employee_info_name_type') !!}',
                                                method: 'POST', 
                                                data: {
                                                    '_token': '{{ csrf_token() }}',
                                                    'trax_id': excel_employee_value
                                                },                                 
                                                success: function(response) {
                                                    if(response.status == 1){
                                                        var row = $('tr');
                                                        var inputElementTraxID = row.find('input[name="employee_trax_id[' + id + ']"]');
                                                        var inputElementName = row.find('input[id="employee_name[' + id + ']"]');
                                                        var inputElementType = row.find('input[name="employee_type[' + id + ']"]');
                                                        
                                                        if (trax_id.indexOf(response.details.trax_id) === -1) {
                                                            trax_id.push(response.details.trax_id)
                                                        }
                                                        
                                                        inputElementTraxID.val(response.details.trax_id); 
                                                        inputElementName.val(response.details.name); 
                                                        inputElementType.val(response.details.type); 
                                                    }
                                                },
                                                error: function(xhr, status, error) {
                                                    // Handle errors
                                                    console.error(xhr.responseText);
                                                }
                                            });
                                        });
                                            table.draw(false);
                                            scan_sound(1);
                                            table.order([0, 'desc']).draw();
                                            shipment_ids.push(id);
                                            shipmentAdded = true;

                                            toastr.success(data.success, 'Success!', { positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center' });
                                        }
                                        else {
                                            alreadyAddedShipments.push(shipment.tracking_number);
                                        }
                                    }else{
                                        excluded_shipment.push(tracking_number);
                                        var excludedString = excluded_shipment.join(', ');
                                        $('.alert-danger').text('Maximum 20 shipments reached, remaining tracking numbers:' + excludedString)
                                        $('.alert-danger').removeClass('d-none')
                                    }
                                });
                               

                               var text = '<div class="col"><table class="table table-sm table-borderless mb-0">';
                                text += '<thead><th>S No.</th><th>Tracking Number</th><th>Error</th></thead>';
                                text += '<tbody>';

                               var error_check = false;
                               var serial = 0;
                               if(data.error_count > 0)
                                {
                                    error_check = true;
                                    $.each(data.error, function(key, value) {
                                        serial++;
                                        text += '<tbody><td>' + serial + '</td><td>' + value.tracking_number + '</td><td><span class="error-text">' + value.error_msg + '</span></td>';
                                    });
                                }

                               if(alreadyAddedShipments.count > 0){
                                   error_check = true;
                                   $.each(alreadyAddedShipments, function(key, value) {
                                       serial++;
                                       text += '<tbody><td>' + serial + '</td><td>' + value + '</td><td><span class="error-text">Already Scanned</span></td>';
                                   });
                               }

                                text += '</tbody></table></div>';

                               if(error_check === true){
                                   $("#excel_upload_error").modal("show");

                                   $("#excel_upload_error .modal-body").html(text);
                               }
                            }
                            else
                            {
                                var errorMessages = data.error;
                                toastr.error(errorMessages, 'Error!', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
                            }

                        $('#lost_shipment_form button.add').prop('disabled', false);

                        $('#update_lost_form_submit').prop('disabled', false);
                        $("#excel").val("");
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
                counter -=1;
                counter_excel-=1
                console.log(counter_excel);
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