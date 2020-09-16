@extends('admin.layout.master')

@section('title', 'Supply Chain Management')

@section('content')

    <h1 class="mb-1">
        Supply Chain Management
    </h1>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="col mt-2">
                    <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="col-4">
                            <div class="form-group">
                                <select name="shipment_status" id="shipment_status" class="form-control select2 dt_search" multiple="multiple" >
                                    @foreach($shipment_status as $status)
                                        <option value="{{$status->id}}">{{$status->name}}</option>
                                    @endforeach
                                    Shipper
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <button id="datatable_filter_btn" type="submit" class=" btn btn-outline-primary btn-min-width" disabled><i
                                            class="la la-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Weights</th>
                        <th class="border-primary border-darken-1">Quantity</th>
                        <th class="border-primary border-darken-1">Pieces</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Booking Date</th>
                        <th class="border-primary border-darken-1">Status Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style type="text/css">

        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .selectize-control {
            width: 300px !important;
        }

        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {


            $('#shipment_status').select2({
                placeholder:'Search Shipment Status',
                width:'100%',
                allowClear:true
            }).bind('select2:select', function () {
                $('#datatable_filter_btn').attr('disabled', false);
            });
            /*$('#shipment_status').on("select2:unselect", function(e) {
                if($('#shipment_status').val() == '' && $('input[name="tracking_numbers"]').val() == ''){
                    $('#datatable_filter_btn').attr('disabled', true);
                }
            });*/


            /*$('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#request_service').addClass('d-none');
                    $('#request_complaints').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }else if(id === 2){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
                else if(id === 3){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
                else if(id === 4){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#request_claims').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#AddNewRequest').addClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
            });
            $('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Claim Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 26){
                    $('#claim_product_cost_div').addClass('d-none');
                    $('#claim_product_picture_div').addClass('d-none');
                    $('#claim_invoice_picture_div').addClass('d-none');
                }
                else{
                    $('#claim_product_cost_div').removeClass('d-none');
                    $('#claim_product_picture_div').removeClass('d-none');
                    $('#claim_invoice_picture_div').removeClass('d-none');
                }
            });
            $('#claim_channel').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#complaint_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Request Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#request_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#feedback_channel').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_feedback_form')
            });
            $('#feedback_channel_request').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });*/



            var selected_rows = [];
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pending Deliveries',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[10, 50], [10, 50]],
                pageLength: 10,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.supply_chain.supply_chain_list') }}',
                    data: function (d) {

                       /* d.shipment_status_select = $('#shipment_status').val();*/
                    }
                },
                deferLoading: 0,
                rowId: 'shipment_id',
                order: [[12, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle vendor'},
                    {data: 'weight', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'quantity', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'pieces', name: 'dc.name', class: 'align-middle vendor'},
                    {data: 'service_type', name: 'service_type', class: 'align-middle service_type'},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
                    {data: 'status_date', name: 'status', class: 'align-middle status'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
/*

                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
*/

                ],
                rowCallback: function(row, data, index) {
                    if (data.shipper_status_id != 17) {
                        $('td:eq(0)', row).addClass('select-checkbox');

                        if ($.inArray(data.shipment_id, selected_rows) !== -1) {
                            table.row(row).select();
                        }
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    var payment_select = '<select name="payment_select" id="payment_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.payment_status')){
                            $(payment_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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



                    $("#payment_select").prepend('<option value="" selected></option>').select2({
                        data:data4,
                        placeholder: "Select Payment",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            $('.datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.shipper_recall').enable();
                    table.button('.print').enable();
                }
                else {
                    table.button('.shipper_recall').disable();
                    table.button('.print').disable();
                }
            });



            $('body').on('click','.view_charges',function () {
                var shipment_id = $(this).parents('tr').attr('id');
                $('#ShipmentChargesModal').modal('show');
                $('#shipment_charges_modal_id').val(shipment_id);
                $.ajax({
                    url:'{!! route("admin.orders.charges") !!}',
                    method: 'POST',
                    data: {
                        'shipment_id': shipment_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#shipment_charges_body').html(data);
                    $('#shipment_charges_modal_heading span').text(shipment_id);
                })
            });

            //Selectize
            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
                onChange: function (value) {
                   /* if(value.length == 0 && $('#shipment_status').val() == ''){
                        $('#datatable_filter_btn').attr('disabled', true);
                    }else{
                        $('#datatable_filter_btn').attr('disabled', false);
                    }
*/
                },
            });

            $('#track_form').bind('submit',function (e) {
                e.preventDefault();

               /* var shipment_status = $('#track_form #shipment_status').val();*/


            });


            var max_char = 245;
            $('#feedback_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('#service_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('#complaint_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('body').on('change','#add_request_form textarea, #add_feedback_form textarea',function() {
                $(this).val($(this).val().trim());
            });
            $( "#add_request_form" ).bind('submit', function (e) {
                e.preventDefault();
                var case_nature_id = parseInt($('#case_nature_select').val());
                if(case_nature_id === 1){
                    var nature_flag = true;
                    var case_nature_complaint_id = $('#case_nature_complaints').val();
                    var case_nature_channel_id = $('#complaint_channels').val();
                    var complaint_description = $('#complaint_description').val();
                    if(!case_nature_complaint_id){
                        nature_flag = false;
                        var error = "Please select Complaint type!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(!case_nature_channel_id){
                        nature_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(!complaint_description){
                        nature_flag = false;
                        var error = "Please select Description!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(selected_rows.length == 0){
                        nature_flag = false;
                        var error = "Please select atleast one tracking number!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(nature_flag){
                        $('#AddNewRequest').attr('disabled',true);
                        $.ajax({
                            url: '{!! route('admin.crm.request.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_ids': selected_rows,
                                'case_nature_id' : case_nature_id,
                                'complaint_id' : case_nature_complaint_id,
                                'channel_id': case_nature_channel_id,
                                'description' : complaint_description
                            }
                        })
                            .done(function(data) {
                                if (data.status) {
                                    if(data.flag){
                                        var html = '';

                                        $.each(data.already_existed_shipments, function(index, tracking_number) {
                                            html += tracking_number + '<br/>';
                                        });

                                        html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                        content = document.createElement('div');
                                        content.innerHTML = html;

                                        swal({
                                            title: 'Request / Complaint Already Lodged!',
                                            content: content,
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });
                                    }else{
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }

                                table.button('.shipper_recall').disable();
                                table.button('.print').disable();

                                selected_rows = [];

                                table.rows().deselect();

                                table.draw('false');

                                $('#AddRequestModal').modal('hide');
                                $('#AddNewRequest').attr('disabled',false);
                            });
                    }

                }else if(case_nature_id == 2){
                    var nature_flag = true;
                    var case_nature_complaint_id = $('#case_nature_requests').val();
                    var case_nature_channel_id = $('#request_channels').val();
                    var service_description = $('#service_description').val();
                    if(!case_nature_complaint_id){
                        nature_flag = false;
                        var error = "Please select Complaint type!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(!case_nature_channel_id){
                        nature_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(!service_description){
                        nature_flag = false;
                        var error = "Please select Description!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(selected_rows.length == 0){
                        nature_flag = false;
                        var error = "Please select atleast one tracking number!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(nature_flag){
                        $('#AddNewRequest').attr('disabled',true);
                        $.ajax({
                            url: '{!! route('admin.crm.request.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_ids': selected_rows,
                                'case_nature_id' : case_nature_id,
                                'complaint_id' : case_nature_complaint_id,
                                'channel_id': case_nature_channel_id,
                                'description' : service_description
                            }
                        })
                            .done(function(data) {
                                if (data.status) {
                                    if(data.flag){
                                        var html = '';

                                        $.each(data.already_existed_shipments, function(index, tracking_number) {
                                            html += tracking_number + '<br/>';
                                        });

                                        html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                        content = document.createElement('div');
                                        content.innerHTML = html;

                                        swal({
                                            title: 'Request / Complaint Already Lodged!',
                                            content: content,
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });
                                    }else{
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }

                                table.button('.shipper_recall').disable();
                                table.button('.print').disable();

                                selected_rows = [];

                                table.rows().deselect();

                                table.draw('false');


                                $('#AddRequestModal').modal('hide');
                                $('#AddNewRequest').attr('disabled',false);
                            });
                    }
                }else if(case_nature_id == 3) {
                    var feedback_flag = true;
                    var feedback_channel = $('#feedback_channel_request').val();
                    var feedback_description = $('#feedback_description_request').val();
                    if (!feedback_description) {
                        feedback_flag = false;
                        var error = "Please select Description!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!feedback_channel) {
                        feedback_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (selected_rows.length == 0) {
                        nature_flag = false;
                        var error = "Please select at least one tracking number!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (feedback_flag) {
                        $('#AddNewRequest').attr('disabled',true);
                        $.ajax({
                            url: '{!! route('admin.crm.feedback.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'channel_id': $('#feedback_channel_request').val(),
                                'shipment_ids': selected_rows,
                                'description': feedback_description
                            }
                        })
                            .done(function (data) {
                                if (data.status) {
                                    if(data.flag){
                                        var html = '';

                                        $.each(data.already_existed_shipments, function(index, tracking_number) {
                                            html += tracking_number + '<br/>';
                                        });

                                        html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                        content = document.createElement('div');
                                        content.innerHTML = html;

                                        swal({
                                            title: 'Request / Complaint Already Lodged!',
                                            content: content,
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });
                                    }else{
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                table.button('.shipper_recall').disable();
                                table.button('.print').disable();

                                selected_rows = [];

                                table.rows().deselect();

                                table.draw('false');

                                $('#AddRequestModal').modal('hide');
                                $('#AddNewRequest').attr('disabled',false);
                            });
                    }
                }
                else if(case_nature_id === 4){
                    var nature_flag = true;
                    var case_nature_claim_id = $('#case_nature_claim').val();
                    var case_nature_channel_id = $('#claim_channel').val();
                    var product_cost = $('#claim_product_cost').val();
                    var check_product_picture = $('#product_picture').val();
                    var check_invoice_picture = $('#invoice_picture').val();
                    $('#shipment_ids').val(selected_rows);
                    $('#case_nature_id').val(case_nature_id);
                    $('#channel_id').val(case_nature_channel_id);
                    $('#complaint_id').val(case_nature_claim_id);
                    var formData = new FormData($('#add_request_form')[0]);
                    if(!case_nature_claim_id){
                        nature_flag = false;
                        var error = "Please select Claim type!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(!case_nature_channel_id){
                        nature_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(case_nature_claim_id !== "26"){
                        if(!check_product_picture){
                            nature_flag = false;
                            var error = "Please attach Product Picture!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!product_cost){
                            nature_flag = false;
                            var error = "Please enter Product Cost!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!check_invoice_picture){
                            nature_flag = false;
                            var error = "Please attach Invoice Picture!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                    if(selected_rows.length == 0){
                        nature_flag = false;
                        var error = "Please select atleast one tracking number!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(nature_flag){
                        $('#AddNewRequest').attr('disabled',true);
                        $.ajax({
                            url: '{!! route('admin.crm.request.add') !!}',
                            method: 'POST',
                            enctype: 'multipart/form-data',
                            data: formData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                        })
                            .done(function(data) {
                                if (data.status) {
                                    if(data.flag){
                                        var html = '';

                                        $.each(data.already_existed_shipments, function(index, tracking_number) {
                                            html += tracking_number + '<br/>';
                                        });

                                        html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                        content = document.createElement('div');
                                        content.innerHTML = html;

                                        swal({
                                            title: 'Request / Complaint Already Lodged!',
                                            content: content,
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });
                                    }else{
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }

                                table.button('.shipper_recall').disable();
                                table.button('.print').disable();

                                selected_rows = [];

                                table.rows().deselect();

                                table.draw('false');

                                $('#AddRequestModal').modal('hide');
                                $('#AddNewRequest').attr('disabled',false);
                            });
                    }

                }
            });
            $('#AddRequestModal').on('hide.bs.modal', function (e) {
                $('#add_request_form')[0].reset();
                $('#case_nature_complaints').val('').trigger('change');
                $('#case_nature_select').val('').trigger('change');
                $('#case_nature_requests').val('').trigger('change');
                $('#complaint_channels').val('').trigger('change');
                $('#request_channels').val('').trigger('change');
                $('#feedback_channel_request').val('').trigger('change');
                $('#complaint_description').val('');
                $('#service_description').val('');
                $('#feedback_description_request').val('');
                $('#request_claims').addClass('d-none');
                $('#case_nature_claim').val('').trigger('change');
                $('#claim_channel').val('').trigger('change');
                $('#claim_product_cost').val('');

            });

            $('#add_feedback_form').bind('submit', function (e) {
                e.preventDefault();
                var feedback_flag = true;
                var feedback_chennel = $('#feedback_channel').val();
                var feedback_description = $('#feedback_description').val();
                if(!feedback_description){
                    feedback_flag = false;
                    var error = "Please Enter Description!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(!feedback_chennel){
                    feedback_flag = false;
                    var error = "Please select Channel!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(feedback_flag){
                    $('#AddNewFeedback').attr('disabled',true);
                    $.ajax({
                        url: '{!! route('admin.crm.feedback.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'channel_id': feedback_chennel,
                            'description' : feedback_description
                        }
                    })
                        .done(function(data) {
                            if (data.status) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#AddFeedbackModal').modal('hide');
                            $('#AddNewFeedback').attr('disabled',false);
                        });
                }

            });
            $('#AddFeedbackModal').on('hide.bs.modal', function (e) {
                $('#feedback_channel').val('').trigger('change');
                $('#feedback_description').val('');
            });


        });
    </script>
@endsection