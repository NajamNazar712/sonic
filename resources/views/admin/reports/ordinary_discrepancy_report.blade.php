@extends('admin.layout.master')
@section('title','Ordinary Discrepancy Report')
@section('content')
    <h1 class="mb-1">
        Ordinary Discrepancy Report
    </h1> 

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                            </div>
                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)">
                        </div>
                    </div>
                    
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                

                <form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" id ="tracking_number" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>

                    <div class="form-group ml-1">
                        <button type="submit" name="add" class="btn btn-primary add" value="Add">Track</button>
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking no.</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Shipment Status</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Quantity - By Shipper</th>
                            <th class="border-primary border-darken-1">COD Value</th>
                            <th class="border-primary border-darken-1">Shipment Content - By Shipper</th>
                            <th class="border-primary border-darken-1">Shipment Content - By Admin</th>
                            <th class="border-primary border-darken-1">Image</th>
                            <th class="border-primary border-darken-1">Quantity - By Admin</th>
                            <th class="border-primary border-darken-1">Comment or Remarks</th>
                            <th class="border-primary border-darken-1">Created At</th>
                            <th class="border-primary border-darken-1">Created By</th>
                            <th class="border-primary border-darken-1">Admin Hub</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <!-- Shipment Tracking Modal -->
    <div class="modal fade" id="shipment_tracking_modal" data-backdrop="static" role="dialog" aria-labelledby="shipment_tracking_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 900px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipment_tracking_modal_title">Shipment Tracking</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                
                            <form id="update_shipment_tracking_form" method="post" accept-charset="utf-8" novalidate="novalidate">
                                @csrf
                                <input type="hidden" id="tracking_number">
                                <div class="modal-body" style="padding: 5px;">
                                        <div class="row p-1">
                                            <div class="col">
                                                <label>Origin</label>
                                                <input class="form-control" id="origin" name="origin" type="text" value="origin" readonly/>
                                            </div>
                                            <div class="col">
                                                <label>Destination</label>
                                                <input class="form-control" id="destination" name="destination" type="text" value="destination" readonly/>
                                            </div>
                                            <div class="col">
                                                <label>Shipment Status</label>
                                                <input class="form-control" id="shipment_status" name="shipment_status" type="text" value="shipment_status" readonly/>
                                            </div>
                                        </div>
                                        <div class="row p-1">
                                            <div class="col">
                                                <label>Arrival Date</label>
                                                <input class="form-control" id="arrival_date" name="arrival_date" type="text" value="arrival_date" readonly/>
                                            </div>
                                            <div class="col">
                                                <label>Cod Amount</label>
                                                <input class="form-control" id="cod_value" name="cod_value" type="text" value="cod_value" readonly/>
                                            </div>
                                            <div class="col">
                                                <label>Shipper Name</label>
                                                <input class="form-control" id="shipper_name" name="shipper_name" type="text" value="shipper_name" readonly/>
                                            </div>
                                        </div>
                                        <div class="row p-1">
                                            <div class="col">
                                                <label>Shipment Content By Shipper</label>
                                                <input class="form-control" id="shipment_content_shipper" name="shipment_content_shipper" type="text" value="shipment_content_shipper" readonly/>
                                            </div>
                                            <div class="col">
                                                <label>Quantity<span class="text-danger">*</span></label>
                                                <input type="text" name="quantity" id="quantity" class="form-control quantity" placeholder="Quantity" data-rule-required="true" data-msg-required="Quantity is required">
                                            </div>
                                            <div class="col">
                                                <label>Remarks<span class="text-danger">*</span></label>
                                                <input type="text" name="remarks" id="remarks" class="w-100 p-1 border-primary" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks is required">
                                            </div>
                                        </div>
                                        <div class="row p-1">
                                            <div class="col">
                                                <label>Product Content By Admin (Description)<span class="text-danger">*</span></label>
                                                <input type="text" name="shipment_content_admin" id="shipment_content_admin" class="form-control shipment_content_admin" placeholder="Product Content" data-rule-required="true" data-msg-required="Product Content is required">
                                            </div>
                                            <div class="col">
                                                <label>Picture<span class="text-danger">*</span></label>
                                                <input type="file" name="picture_attached" id="picture_attached" class="form-control picture_attached" title="Select File" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-maxsize="5242880" data-rule-required="true" data-msg-required="Picture is required">
                                            </div>
                                        </div>
                                        <div class="form-group ml-1 ">
                                            <button type="submit" name="update_shipment" id="update_shipment"
                                                class="btn btn-primary update_shipment" value="Add">Update Shipment</button>
                                        </div>
                                    </div>
                                </form>
                            

                </div>
            </div>
        </div>
    </div>
    <!-- Shipment Tracking Modal -->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style>
        
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }

        #quantity::placeholder {
            text-align: left;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#quantity-error').prop('disabled', true);

            $('#add_shipment_form input.tracking_number').focus();

            $('#add_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#update_shipment_tracking_form input.quantity').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            // fetching data of from shipment tracking number and display values in modal
            $('#add_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    var data = 
                    {
                    tracking_number: $('#tracking_number').val(),
                    '_token': '{{ csrf_token() }}'
                    };
                    // AJAX request to fetch data of tracking and display values in modal 
                    $.ajax({
                        url: "{{ route('admin.reports.ordinary_discrepancy_report.tracking_data') }}",
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.status == 1) 
                            {
                                $('#origin').val(response.data.origin);
                                $('#destination').val(response.data.destination);
                                $('#shipment_status').val(response.data.shipment_status);
                                $('#arrival_date').val(response.data.arrival_date);
                                $('#cod_value').val(response.data.cod_value);
                                $('#shipper_name').val(response.data.shipper_name);
                                $('#shipment_content_shipper').val(response.data.shipment_content_shipper);
                                $('#shipment_tracking_modal').modal('show');
                            }
                            else{
                                swal({
                                    title: 'Something Went Wrong!',
                                    text: 'No Tracking Number Found',
                                    icon: 'error',
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });
                            }
                            table.draw();
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Form submission failed:', textStatus, errorThrown);
                        }
                    });
                }
            });

            // submit tracking
            $('#update_shipment_tracking_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.appendTo(element.parent());
                },
                submitHandler: function(form) {
                    
                    var formData = new FormData();
                    formData.append('tracking_number', $('#tracking_number').val());
                    formData.append('origin', $('#origin').val());
                    formData.append('destination', $('#destination').val());
                    formData.append('shipment_status', $('#shipment_status').val());
                    formData.append('arrival_date', $('#arrival_date').val());
                    formData.append('cod_value', $('#cod_value').val());
                    formData.append('shipper_name', $('#shipper_name').val());
                    formData.append('shipment_content_shipper', $('#shipment_content_shipper').val());
                    formData.append('quantity', $('#quantity').val());
                    formData.append('remarks', $('#remarks').val());
                    formData.append('shipment_content_admin', $('#shipment_content_admin').val());
                    formData.append('picture_attached', $('#picture_attached')[0].files[0]);

                    formData.append('_token', '{{ csrf_token() }}');
                    // AJAX request
                    $.ajax({
                        url: "{{ route('admin.reports.ordinary_discrepancy_report.submit_tracking') }}",
                        type: 'POST',
                        data: formData,
                        contentType: false, 
                        processData: false,
                        success: function(response) {
                            if (response.status == 1) 
                            {
                                swal({
                                    text: response.message,
                                    icon: 'success',
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });
                            $('#shipment_tracking_modal').modal('hide');
                            }
                            else{
                                swal({
                                    text: response.message,
                                    icon: 'error',
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });
                            }
                            table.draw();
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Form submission failed:', textStatus, errorThrown);
                        }
                    });
                }
            });

            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                }
            });

            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                }
            });

            //Excel
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.ordinary_discrepancy_report.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Arrival Date');
                            head.push('Shipper Status');
                            head.push('Shipper Name');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Quantity by Shipper');
                            head.push('Cod Value');
                            head.push('Shipment content by Shipper');
                            head.push('Shipment content by Admin');
                            head.push('Quantity by Admin');
                            head.push('Remarks by Admin');
                            head.push('Created At');
                            head.push('Updated By');
                            head.push('Admin Hub');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.arrival_date);
                                row.push(values.shipment_status);
                                row.push(values.shipper_name);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.quantity_by_shipper);
                                row.push(values.cod_value);
                                row.push(values.shipment_content_by_shipper);
                                row.push(values.shipment_content_by_admin);
                                row.push(values.quantity_by_admin);
                                row.push(values.remarks_by_admin);
                                row.push(values.created_at);
                                row.push(values.updated_by);
                                row.push(values.admin_hub);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    extend: 'excel',
                    title: 'Ordinary Discrepancy Report',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.ordinary_discrepancy_report.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'tracking_number',
                order: [[2, 'desc']],
                columns: [
                    {orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function(data, type, row) {
                            return '';
                        }},
                    { data:'tracking_number' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'arrival_date' ,name: 'sj.created_at', class: 'align-middle arrival_date', orderable:false},
                    { data:'shipment_status' ,name: 'ss.name', class: 'align-middle shipment_status'},
                    { data:'shipper_name' ,name: 'users.name', class: 'align-middle shipper_name'},
                    { data:'origin' ,name: 'citi.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'c.name', class: 'align-middle destination'},
                    { data:'quantity_by_shipper' ,name: 'shipment.pieces', class: 'align-middle quantity_by_shipper'},
                    { data:'cod_value' ,name: 'shipments.amount', class: 'align-middle cod_value'},
                    { data:'shipment_content_by_shipper', name: 'si.description', class: 'align-middle shipment_content_by_shipper'},
                    { data:'shipment_content_by_admin' ,name: 'ordinary_discrepancy_reports.product_content', class: 'align-middle shipment_content_by_admin'},
                    { data:'images' ,name: 'rdinary_discrepancy_reports.picture_path', class: 'align-middle images'},
                    { data:'quantity_by_admin' ,name: 'ordinary_discrepancy_reports.quantity', class: 'align-middle quantity_by_admin text-center'},
                    { data:'remarks_by_admin' ,name: 'ordinary_discrepancy_reports.remarks', class: 'align-middle remarks_by_admin'},
                    { data:'created_at' ,name: 'ordinary_discrepancy_reports.created_at', class: 'align-middle created_at'},
                    { data:'updated_by' ,name: 'admins.name', class: 'align-middle updated_by'},
                    { data:'admin_hub' ,name: 'ch.name', class: 'align-middle admin_hub'},
                    
                ],
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });

    </script>
@endsection