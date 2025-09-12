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
                @if( session('role_id') == 1 || in_array(890, session('permissions')) || in_array(944, session('permissions')))
                <form id="excel_upload_form" class="form-horizontal" method="POST"  novalidate="novalidate" enctype="multipart/form-data">
                    

                    <div class="row align-items-center justify-content-center">
                        <div class="col">
                            <div class="form-group mb-0">
                                <input type="file" name="excel" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB)." id="excel">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group pt-2 text-left">
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
                            <th class="border-primary border-darken-1">Parcel Value</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Lost Category</th>
                            <th class="border-primary border-darken-1">Shipping Mode</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Action</th>
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

    <!--lost Responsibl Modal -->
    <div class="addLostResponsibleModal"> </div>
    <!--Deposit Slip Modal -->

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

.readonly-select {
    pointer-events: none;    
    opacity: 0.6;           
    background-color: #f8f9fa;
    cursor: not-allowed;     
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
                    {name: 'parcel_value', class: 'align-middle parcel_value', orderable: false},
                    {name: 'remarks', class: 'align-middle remarks', orderable: false},
                    {name: 'mode', class: 'align-middle mode', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'action_button', class: 'align-middle action_button', orderable: false},
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
                                        var remarkCell = renderRemarkSelect(data.details.id, ''); 
                                        var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow" data-shipment_id="' + data.details.id + '"><i class="la la-close"></i></a>';
                                        table.row.add([
                                            rowNo + 1, data.details.tracking_number, 
                                            data.details.shipper_name, 
                                            data.details.origin, 
                                            data.details.destination, 
                                            data.details.hub, 
                                            data.details.amount, 
                                            data.details.parcel_value,
                                            data.details.remarks,
                                            remarkCell,
                                            data.details.mode,
                                            data.details.service_type, 
                                            data.details.action_button,
                                            action
                                        ]).node().id = data.details.id;
                                        table.draw(false);
                                        scan_sound(1);
                                        table.order([0, 'desc']).draw();

                                        shipment_ids.push(data.details.id);
                                        validateRows(shipment_ids, change);                                        
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
                                    // $.each(shipmentData, function(id, shipment2){
                                        var index = $.inArray(id, shipment_ids);
                                        shipment = shipmentData[id];
                                        
                                        if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(shipment.tracking_number)) === -1) {
                                            var rowNo = table.rows().count();
                                            var remarkCell = renderRemarkSelect(id, ''); 
                                           var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
                                           table.row.add([
                                           rowNo + 1,
                                           shipment.tracking_number,
                                           shipment.shipper_name,
                                           shipment.origin,
                                           shipment.destination,
                                           shipment.hub,
                                           shipment.amount,
                                           shipment.parcel_value,
                                           shipment.remarks,
                                            remarkCell, 
                                           shipment.mode,
                                           shipment.service_type, 
                                           shipment.action_button,
                                           action
                                       ]).node().id = id;
                                       console.log(shipment);
                                       
       
                                        table.draw(false);
                                        scan_sound(1);
                                        table.order([0, 'desc']).draw();

                                        shipment_ids.push(shipment.id);
                                        shipmentAdded = true;
                                        validateRows(shipment_ids, change);                                        
                                        toastr.success(data.success, 'Success!', { positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center' });
                                    }
                                    else {
                                        alreadyAddedShipments.push(shipment.tracking_number);
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

        function findShipmentsWithMissingRemarks(shipmentIds, changeMap) {
            var missing = [];
            $.each(shipmentIds, function(_, sid) {
                var id = String(sid);
                if ($.inArray(id, Object.keys(changeMap)) !== -1) {
                return;
                }
                var $sel = $(`select[name="remarks_category[${id}]"]`);
                var v = $sel.val();
                if (v !== '1' && v !== '2') {
                missing.push(id);
                }
            });
            return missing;
        }
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
                var missing = findShipmentsWithMissingRemarks(shipment_ids, change);
                if (missing.length > 0) {
                toastr.error(
                    'Please select a Remark (1 = Transit Lost / 2 = Snatching/Theft/Stolen) for shipments: ' + missing.join(', '),
                    'Error!',
                    { positionClass: 'toast-top-center', containerId: 'toast-top-center' }
                );
                    return false; 
                }
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to add to Lost Shipments!',
                    icon: 'warning',
                    buttons: {
                    cancel: { text: 'No', value: null, visible: true, closeModal: true },
                    confirm: { text: 'Yes', value: true, visible: true, closeModal: true }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function(confirm) {
                    if (confirm) {
                        blockPagePermanently();
                        $('#update_lost_form button[type="submit"]').attr('disabled', 'disabled');
                        $('#update_lost_form input#shipment_ids').val(shipment_ids); 
                        form.submit(); 
                    }
                });
            
            }
        });

        function validateRows(shipmentIds, change) {
            var emptyFields = []; 
            
            $.each(shipmentIds, function(index, shipmentId) {
                shipmentId = shipmentId.toString();
                if ($.inArray(shipmentId, Object.keys(change)) == -1) {
                    var field = $('[name="remarks[' + shipmentId + ']"]');
                    $('[name="remarks[' + shipmentId + ']"]').rules('add', {
                        required: true,
                        messages: {
                            required: "Field in row for shipment " + shipmentId + " is required"
                        }
                    });

                    if (field.val() === '') {
                        emptyFields.push(shipmentId); 
                    }
                } else {
                    // console.log(Object.keys(change).length);
                    // Remove validation rule if shipmentId is in change array
                    $('[name="remarks[' + shipmentId + ']"]').rules('remove', 'required');
                }
            });

            return emptyFields; 
        }

        var flag_new = false

        $('#update_lost_form_submit').click(function() {
            var emptyFields = validateRows(shipment_ids, change);
            if (emptyFields.length > 0) {
                var shipment_ids_string = emptyFields.join(',');
                toastr.error('Either Remarks Or Responsible Will Be Required For These Shipments: ' + shipment_ids_string, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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
            var shipment_id_remove = $(this).attr('data-shipment_id');
            var existing_table = $('#addLostResponsibleTable_' + shipment_id_remove).DataTable();
            existing_table.clear().draw();

            delete change[shipment_id_remove];

        });

        var shipment_id;
        $('body').on('click', '.add_lost_responsible', function () {
            shipment_id = $(this).attr('data-id');
            var modalId = 'addLostResponsibleModal_' + shipment_id;
            var modalButton = 'addLostResponsibleModalBtn_' + shipment_id;
            var closeModalButton = 'addLostResponsibleCloseModalBtn';

            var modalContent = '<div class="modal fade text-left addLostResponsible" id="' + modalId + '" data-backdrop="static" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-xl" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header bg-primary white">' +
                '<h4 class="modal-title white">Add Lost Responsible for Shipment ID: ' + shipment_id + '</h4>' +
                '<button type="button" class="close" aria-label="Close" id = "' + closeModalButton + '">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body text-center">' +
                '<form id="addLostResponsibleForm_' + shipment_id + '" class="form" method="post" enctype="multipart/form-data">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '<input type="hidden" id="shipment_id" name="shipment_id" value="' + shipment_id + '">' +
                '<table class="table table-bordered datatable" id="addLostResponsibleTable_' + shipment_id + '">' +
                '<thead>' +
                '<tr role="row" class="bg-primary white">' +
                '<th class="border-primary border-darken-1">S. No.</th>' +
                '<th class="border-primary border-darken-1">Employee ID</th>' +
                '<th class="border-primary border-darken-1">Employee Name</th>' +
                '<th class="border-primary border-darken-1">Employee Type</th>' +
                '<th class="border-primary border-darken-1">Employee Status</th>' +
                '<th class="border-primary border-darken-1"></th>' +
                '</tr>' +
                '</thead>' +
                '</table>' +              
                '</form>' +
                '<div class="row justify-content-center mt-3">'+
                        '<div class="col-">'+
                            '<button class="btn btn-info btn-block" class="close close_btn" id="' + modalButton + '" data-dismiss="modal" aria-label="Close" disabled>Save</button>'+
                        '</div>'+
                '</div>'+
              
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';


            $('.addLostResponsibleModal').append(modalContent);
            $('#' + modalId).modal('show');
            $('#' + modalId).on('shown.bs.modal', function (event) {
                if (!$.fn.DataTable.isDataTable('#addLostResponsibleTable_' + shipment_id)) { 
                    addLostResponsible = $('#addLostResponsibleTable_' + shipment_id).DataTable({
                        dom: '<"d-inline-block"l><"pull-right"B>tipr',
                        buttons: [{
                            title: 'Add Row',
                            className: 'btn btn-primary mb-1 add_row',
                            text: '<i class="la la-plus"></i> Add Row',
                            action: function (e) {
                                add_row(shipment_id);                                
                                
                                if(flag_new || addLostResponsible.length === 0){
                                    addLostResponsible.button(0).disable();
                                }else{
                                    addLostResponsible.button(0).enable();
                                    flag_new = true;
                                }
                            }
                        }],
                        ordering: false,
                        paging: false,
                        columns: [
                            { orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return ''; }},
                            { name: 'user', class: 'align-middle user form-group', width: '40%' },
                            { name: 'user_name', class: 'align-middle user_name form-group', width: '20%' },
                            { name: 'user_type', class: 'align-middle user_type form-group', width: '20%' },
                            { name: 'user_status', class: 'align-middle user_type form-group', width: '20%' },
                            {name: 'action', class: 'align-middle action'},
                        ],
                        rowCallback: function (row, data, index) {
                            var info = addLostResponsible.page.info();
                            $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                        },
                        initComplete: function () {
                            // this.api().table().columns.adjust();
                        }
                    });
                }
            });
           
        });
        var rows_count = 0;
        var rows_count_1 = 0;
        var selected_rows = [];
        var change = {};
        var new_array = [];
        var selected_trax_id = [];
        var trax_index;
        function add_row(shipment_id) {
            rows_count++;
            rows_count_1++;
            var user_input = '<input class="form-control user-input" placeholder="Enter TraxID (s)" data-shipment_id="' + shipment_id + '" data-row="' + rows_count + '">';
            var user_name = '<input class="form-control user-name" data-shipment_id="' + shipment_id + '" data-row="' + rows_count + '" readonly>';
            var user_type = '<input class="form-control user-type" data-shipment_id="' + shipment_id + '" data-row="' + rows_count + '" readonly>';
            var user_status = '<input class="form-control user-status" data-shipment_id="' + shipment_id + '" data-row="' + rows_count + '" readonly>';

            var remove = '<a href="javascript:void(0);" data-shipment_id="' + shipment_id + '" class="btn btn-icon btn-sm btn-danger remove_row ' + rows_count + '" data-trax_id=""><i class="la la-close"></i></a>';
        
            addLostResponsible.row.add([0, user_input, user_name, user_type, user_status, remove]).node().id = rows_count;
            addLostResponsible.draw(true);
            selected_rows.push(rows_count);

            if (!new_array[shipment_id]) {
                new_array[shipment_id] = [];
            }   
            new_array[shipment_id].push({
                value: rows_count_1,
            });
            $('.user-input[data-shipment_id="' + shipment_id + '"][data-row="' + rows_count + '"]').on('keypress', function(event) {
                if (event.which === 13 || event.keyCode === 13) {
                    var inputValue = $(this).val();
                    
                    if(change[shipment_id]){
                        trax_index = change[shipment_id].findIndex(function(item) {
                            return item.value === inputValue;
                        });
                    }

                    console.log(trax_index)
                    if (trax_index == -1 || trax_index === undefined || change.length === 0 ) {
                        $('.remove_row.' + rows_count).attr('data-trax_id', inputValue);
                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_confirmation.get_employee_info_name_type') !!}',
                            method: 'POST',
                            data: 
                            {   trax_id: inputValue,
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if(response.status == 1){
                                    flag_new = true;
                                    addLostResponsible.button(0).enable();

                                    $('.user-name[data-shipment_id="' + shipment_id + '"][data-row="' + rows_count + '"]').val(response.details.name)
                                    $('.user-type[data-shipment_id="' + shipment_id + '"][data-row="' + rows_count + '"]').val(response.details.type)
                                    $('.user-status[data-shipment_id="' + shipment_id + '"][data-row="' + rows_count + '"]').val(response.details.status)

                                    if (!change[shipment_id]) {
                                        change[shipment_id] = [];
                                    }
                                    var existingChangeIndex = change[shipment_id].findIndex(function(item) {
                                        return item.value === inputValue;
                                    });

                                    if (existingChangeIndex === -1) {
                                        change[shipment_id].push({
                                            value: inputValue,
                                            computedType: response.details.computedType 

                                        });
                                    }

                                    if(change[shipment_id].length > 0){
                                        $('#addLostResponsibleModalBtn_' + shipment_id).prop('disabled', false);
                                    }

                                    $('#update_lost_form input#trax_id').val(JSON.stringify(change));
                                    // $('#addLostResponsibleModalBtn_' + shipment_id).prop('disabled', false);

                                }else{
                                    toastr.error('Employee Doesnt Exists !!', 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }

                            },
                            error: function(xhr, status, error) {
                                toastr.error('Employee Doesnt Exists !!', 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            }
                        });
                    }else{
                        toastr.error('Already Added !!', 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                }
            });
        }
        $('body').on('click', 'a.remove_row', function () {
            
            var index = $.inArray(rid, selected_rows);
            var rid = parseInt($(this).parents('tr').attr('id'));
            var shipment_id_remove = $(this).attr('data-shipment_id');
            var trax_id = $(this).attr('data-trax_id');

            var saveBtnId = 'addLostResponsibleModalBtn_' + shipment_id_remove
            
            rows_count_1 = rows_count_1;

            if (index !== -1) {
                selected_rows.splice(index, 1);
            }
            addLostResponsible.row($(this).parents('tr')).remove().draw();
            if (change[shipment_id_remove] && change[shipment_id_remove] !== undefined && typeof change[shipment_id_remove] === 'object') {
                change[shipment_id_remove] = change[shipment_id_remove].filter(function(item) {
                    return item.value !== trax_id;
                });

                if (change[shipment_id_remove].length === 0) {
                    $('#' + saveBtnId).prop('disabled', true);
                    change[shipment_id_remove] = [];
                    delete change[shipment_id_remove];
                }

            }

            var indexToRemove = new_array[shipment_id_remove].findIndex(function(item) {
                return item.value !== rows_count_1;
            });

            if (indexToRemove !== -1) {
                new_array[shipment_id_remove].splice(indexToRemove, 1);
            }

            if(addLostResponsible.row().length === 0){
                new_array[shipment_id_remove] = [];
            }

            
            if (new_array[shipment_id] && change[shipment_id]) {
                var truee = new_array[shipment_id].length === change[shipment_id].length;
                var falsee = new_array[shipment_id].length !== change[shipment_id].length;
            } else {
                console.error("One or both of the arrays are undefined for shipment_id:", shipment_id);
            }

            if(addLostResponsible.row().length >= 0 && change[shipment_id_remove] !== undefined){
                if(truee === true){
                    addLostResponsible.button(0).enable();
                }else if(falsee !== false){
                    addLostResponsible.button(0).disable();
                }
            }else if (addLostResponsible.row().length == 0){
                addLostResponsible.button(0).enable();
            }else{
                addLostResponsible.button(0).disable();
            }


        });  

        $(document).on('hide.bs.modal','.addLostResponsible', function (e) {        
            if (!new_array[shipment_id]) {
                new_array[shipment_id] = [];
            }   

            if (!change[shipment_id]) {
                change[shipment_id] = [];
            }
            if(new_array[shipment_id] != undefined || change[shipment_id] != undefined || addLostResponsible.row().length === 0){
                var truee = new_array[shipment_id].length === change[shipment_id].length;
                var falsee = new_array[shipment_id].length !== change[shipment_id].length;

                if(addLostResponsible.row().length === 0){
                    delete change[shipment_id];
                    if ($.fn.DataTable.isDataTable('#addLostResponsibleTable_' + shipment_id)) {
                        $('#addLostResponsibleTable_' + shipment_id).DataTable().destroy(); 
                    }                                       
                    return;
                }else if (truee === true){
                    if ($.fn.DataTable.isDataTable('#addLostResponsibleTable_' + shipment_id)) {
                        $('#addLostResponsibleTable_' + shipment_id).DataTable().destroy(); 
                    }
                    return;
                }else if (falsee !== false){
                    e.preventDefault();
                    toastr.error('Please Fill The Field !!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            }else{                
                e.preventDefault();
                toastr.error('Please Fill The Field !!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }

            
        });

        $(document).on('click', '.addLostResponsible #addLostResponsibleCloseModalBtn', function (e) {                        
            if (change[shipment_id] && change[shipment_id].length != 0 && change[shipment_id].length === new_array[shipment_id].length){
                
                swal({
                    title: 'Are you sure?',
                    text: 'Are you sure you want to cancel? Your changes will not be saved. Or click Save to keep your work.',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            value: null,
                            visible: true,
                            className: "",
                            closeModal: true,
                        },
                        confirm: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "",
                            closeModal: true
                        }
                    }
                }).then((willProceed) => {
                    if (willProceed) {
                        $('.addLostResponsible').modal('hide')
                        var existing_table = $('#addLostResponsibleTable_' + shipment_id).DataTable();

                        if (existing_table) {
                            existing_table.clear().draw();
                            existing_table.destroy();
                            delete change[shipment_id];
                            delete new_array[shipment_id];
                        }
                        $('#addLostResponsibleModalBtn_' + shipment_id).prop('disabled', true);

                        return;
                    }
                      
                });
            }else{
                $('.addLostResponsible').modal('hide')

            }
        });

        $(document).on('click', '[id^="addLostResponsibleModalBtn_"]', function() {
            updateRemarksCategory(shipment_id)
        });
        

        const REMARK_OPTIONS = [
            { id: 1, text: 'Transit Lost' },
            { id: 2, text: 'Snatching/Theft/Stolen' }
        ];

        const AUTO_REMARKS = {
            3: 'Lost by Operation Staff',
            4: 'Lost by Rider',
            5: 'Lost by Rider & Operation Staff'
        };

        function renderRemarkSelect(shipmentId, selectedVal) {
            let opts = REMARK_OPTIONS.map(o =>
                `<option value="${o.id}" ${String(selectedVal)===String(o.id) ? 'selected' : ''}>${o.text}</option>`
            ).join('');

            return `
                <select name="remarks_category[${shipmentId}]" 
                        id="remarks_category_${shipmentId}"
                        class="form-control remark-select" 
                        data-shipment-id="${shipmentId}" required>
                    <option value="">-- Select Remark --</option>
                    ${opts}
                </select>`;
        }
        function updateRemarksCategory(shipment_id) {
            if (manualRemarkMode[shipment_id]) return; // stop auto if manual mode

            let types = change[shipment_id]?.map(item => item.computedType) || [];
            if (types.length === 0) return;

            let allRider = types.every(t => t === 'Rider');
            let allAdmin = types.every(t => t === 'Operation Staff');
            let mixed = !allRider && !allAdmin;

            let categoryId = null;
            if (allAdmin) categoryId = 3;
            else if (allRider) categoryId = 4;
            else if (mixed) categoryId = 5;

            let remarksField = $('#remarks_category_' + shipment_id);
            if (remarksField.length && categoryId) {
                if (!remarksField.find(`option[value="${categoryId}"]`).length) {
                    remarksField.append(`<option value="${categoryId}">${AUTO_REMARKS[categoryId]}</option>`);
                }
                remarksField.val(categoryId).prop('readonly', true).addClass('readonly-select');
            }
        }
        let manualRemarkMode = {}; 

        $(document).on('keypress', '.remarks', function () {
            let shipment_id = $(this).data('shipment-id');
            manualRemarkMode[shipment_id] = true; // Manual mode on

            let remarksField = $('#remarks_category_' + shipment_id);

            // Remove readonly class and unlock
            remarksField.removeClass('readonly-select')
                        .prop('readonly', false);

            // Reset dropdown with only manual options (1,2)
            remarksField.html(`
                <option value="">-- Select Remark --</option>
                ${REMARK_OPTIONS.map(o =>
                    `<option value="${o.id}">${o.text}</option>`
                ).join('')}
            `);
        });


        $(document).off('click', '#' + modalButton).on('click', '#' + modalButton, function () {
           updateRemarksCategory(shipment_id)
        });

    });
    </script>
@endsection