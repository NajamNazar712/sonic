@extends('admin.layout.master')

@section('title', 'Bulk Claim Logging')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Bulk Claim Logging
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            
                            <form id="add_shipment_form" class="mb-1 justify-content-center" novalidate="novalidate">

                                <div class="row text-center justify-content-center align-items-center">
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                                        </div>
                                    </div>
                                     <div class="col-auto">
                                         <div class="form-group ml-1">
                                             <button type="submit" name="add" class="btn btn-primary add" id="add" value="Add">Add</button>
                                         </div>
                                     </div>
                                 </div>
                            </form>
                            <form id="bulk_claim_submit" class="form-horizontal text-center" method="POST" action="{{ route('admin.crm.bulk_claim.submit') }}" novalidate="novalidate" enctype="multipart/form-data">

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Claim Type</th>
                                    <th class="border-primary border-darken-1">Channel</th>
                                    <th class="border-primary border-darken-1">Product Cost</th>
                                    <th class="border-primary border-darken-1">Receiving Sheet</th>
                                    <th class="border-primary border-darken-1">Product Picture</th>
                                    <th class="border-primary border-darken-1">Invoice Picture</th>
                                    <th class="border-primary border-darken-1">Description</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>

                                {{ csrf_field() }}

                                <input type="hidden" name="shipment_ids" class="shipment_ids">
                                <div class="form-group ml-1">
                                    <button type="submit" name="confirm" class="btn btn-primary confirm" value="Submit" disabled="disabled">Submit</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    {{-- <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script> --}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {


            var shipment_ids = [];


            $('#add_shipment_form input.tracking_number').focus();

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                paging:false,
                autoWidth:false,
                columns: [
                    {name: 'serial_number', class: 'align-middle serial_number', orderable: false, searchable: false},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'claim_type', class: 'align-middle claim_type', orderable: false, searchable: false},
                    {name: 'channel', class: 'align-middle channel', orderable: false, searchable: false},
                    {name: 'product_cost', class: 'align-middle product_cost', orderable: false, searchable: false},
                    {name: 'receiving_sheet', class: 'align-middle receiving_sheet', orderable: false, searchable: false},
                    {name: 'product_picture', class: 'align-middle product_picture', orderable: false, searchable: false},
                    {name: 'invoice_picture', class: 'align-middle invoice_picture', orderable: false, searchable: false},
                    {name: 'description', class: 'align-middle description', orderable: false, searchable: false},
                    {name: 'remove', class: 'align-middle remove', sortable: false, orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                },
                initComplete: function() {
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();
                    });

                    this.api().table().columns.adjust();
                }
            });

            

            $('#add_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            

            $('#add_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                /*errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },*/
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#add_shipment_form button.add').prop('disabled', true);

                    var tracking_number = $(form).find('input.tracking_number').val();
                   
                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        $.ajax({
                            url: '{!! route('admin.crm.bulk_claim.shipment_details') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                '_token': '{{ csrf_token() }}'
                            },
                            timeout: 5000,
                            error: function (data) {
                                form.reset();

                                $('#add_shipment_form input.tracking_number').val('').focus();

                                $('#add_shipment_form button.add').prop('disabled', false);
                                scan_sound(2);
                                toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            },
                            success: function(data) {
                                form.reset();

                                $('#add_shipment_form input.tracking_number').val('').focus();

                                remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';

                                if (data.status == 0) {
                                    id = data.details.id;

                                    var index = $.inArray(id, shipment_ids);

                                    if (index === -1) {
                                        var rowNo = table.rows().count();
                                        
                                        var case_nature_type = '<div class="form-group"><select name="case_nature_type_id['+id+']" id="case_nature_type_id_'+id+'" class="case_nature_type_id select2 form-control" data-rule-required="true" data-msg-required="This field is required"></select></div>';
                                        var receiving_sheet = '<div class="form-group"><select name="receiving_sheet_id['+id+']" id="receiving_sheet_id_'+id+'" class="receiving_sheet_id select2 form-control" ></select></div>';
                                        

                                        var channel = '<div class="form-group"> <select name="channel_id['+id+']" id="channel_id_'+id+'" class="channel_id select2 form-control" data-rule-required="true" data-msg-required="This field is required"></select></div>';
                                       

                                        table.row.add([rowNo + 1, data.details.tracking_number, case_nature_type, channel, '<div class="form-group"> <input type="text" class="form-control form-control-sm claim_product_cost" name="claim_product_cost['+id+']" placeholder="Enter Product Cost" data-rule-required="true" data-msg-required="This field is required"></div>',receiving_sheet, '<div class="form-group"><input type="file" class="form-control form-control-sm" name="product_picture['+id+']" id="product_picture_'+id+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="File is required"></div>','<div class="form-group"><input type="file" class="form-control form-control-sm" name="invoice_picture['+id+']" id="invoice_picture_'+id+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="File is required"></div>','<textarea class="form-control form-control-sm" name="description["'+id+'"]" id="claim_description" rows="5" placeholder="Enter Description Here..."></textarea>', remove_button]).node().id = data.details.id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        var case_nature_type_data = $.map({!! $case_nature_type !!}, function (obj) {
                                            obj.id = obj.id;
                                            obj.text = obj.type;

                                            return obj;
                                        });
                                        $(".receiving_sheet_id").prepend('<option value="" selected></option>').select2({
                                            placeholder: "Select Receiving Sheet ID",
                                            width: '100%',
                                            containerCssClass: 'select-xs',
                                            dropdownCssClass: 'form-control-sm p-0'
                                        });
                                        $(".case_nature_type_id").prepend('<option value="" selected></option>').select2({
                                            data:case_nature_type_data,
                                            placeholder: "Select Claim Type",
                                            width: '100%',
                                            containerCssClass: 'select-xs',
                                            dropdownCssClass: 'form-control-sm p-0'
                                        }).bind('change', function () {
                                            var id = parseInt($(this).val());
                                            var shipment_id = this.id.slice(20);
                                            // console.log('changed');
                                            // console.log($('#receiving_sheet_id_'+shipment_id));
                                            $('#receiving_sheet_id_'+shipment_id).empty().trigger('change');
                                            if(id == 26){
                                                $('#invoice_picture_'+shipment_id).data('rule-required',false);
                                                $('#product_picture_'+shipment_id).data('rule-required',false);
                                                
                                            }else{
                                                if(id == 23){

                                                    $.ajax({
                                                        url: '{!! route('admin.crm.request.lost.claim') !!}',
                                                        method: 'POST',
                                                        data: {
                                                            '_token': '{{ csrf_token() }}',
                                                            'shipment_id': shipment_id,
                                                        }
                                                    }).done(function (data) {
                                                        if(data.status==1){
                                                            var newOption = new Option(data.receiving_sheet_id, data.receiving_sheet_id, false, false);
                                                            $('#receiving_sheet_id_'+shipment_id).append(newOption).trigger('change');
                                                        }
                                                    });
                                                    
                                                }
                                                $('#invoice_picture_'+shipment_id).data('rule-required',true);
                                                $('#product_picture_'+shipment_id).data('rule-required',true);
                                            }
                                        });
                                        var channel_data = $.map({!! $channels !!}, function (obj) {
                                            obj.id = obj.id;
                                            obj.text = obj.channel;

                                            return obj;
                                        });
                                        $(".channel_id").prepend('<option value="" selected></option>').select2({
                                            data:channel_data,
                                            placeholder: "Select Channel",
                                            width: '100%',
                                            containerCssClass: 'select-xs',
                                            dropdownCssClass: 'form-control-sm p-0'
                                        });
                                        $('.claim_product_cost').inputmask({
                                            'alias': 'integer',
                                            'allowMinus': false,
                                            'allowPlus': false
                                        });
                                        table.columns.adjust();
                                        scan_sound(1);
                                        shipment_ids.push(data.details.id);

                                        $('#add_shipment_form button.add').prop('disabled', false);
                                        console.log('enabled');
                                        $('#bulk_claim_submit button.confirm').prop('disabled', false);

                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }else {
                                    $('#add_shipment_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        });
                    }
                    else {
                        $('#add_shipment_form button.add').prop('disabled', false);
                        scan_sound(2);
                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            $('#bulk_claim_submit').validate({
                errorClass: 'danger',
                successClass: 'success',
                /*errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },*/
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#bulk_claim_submit input.shipment_ids').val(shipment_ids);
                    swal({
                        text: 'Are you sure, you want to Mark Claim Request?',
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
                            swal({
                                title: 'Please Wait!',
                                text: 'Request being marked!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            blockPagePermanently();
                            form.submit();
                        }
                    });

                }
            });
            


            $('#datatable tbody').on('click', 'tr td.remove button', function() {
                var parent = $(this).parents('tr');
                var id = parseInt(parent.attr('id'));
                table.row(parent).remove();
                table.draw(false);
                var index = $.inArray(id, shipment_ids);
                if (index !== -1) {
                    shipment_ids.splice(index, 1);

                    if (shipment_ids.length == 0) {
                        $('#bulk_claim_submit button.confirm').prop('disabled', true);
                    }
                }

                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
            });


        });

       



    </script>
@endsection