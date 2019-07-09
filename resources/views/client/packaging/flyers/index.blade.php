@extends('client.layout.master')

@section('title', 'Packaging Material Request')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <h1 class="mb-1">
                        Packaging Material Requests
                    </h1>

                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('client.inc.messages')

                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">

                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Request Date/Time</th>
                                        <th class="border-primary border-darken-1">Requested Address</th>
                                        <th class="border-primary border-darken-1">City</th>
                                        {{--<th class="border-primary border-darken-1">Packaging Type</th>--}}
                                        {{--<th class="border-primary border-darken-1">Size</th>--}}
                                        {{--<th class="border-primary border-darken-1">Qty</th>--}}
                                        <th class="border-primary border-darken-1">Amount</th>
                                        <th class="border-primary border-darken-1">Payment Mode</th>
                                        <th class="border-primary border-darken-1">Tracking Number</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Aging</th>
                                        <th class="border-primary border-darken-1"></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Request Packaging Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('cod.packaging.requests.submit')}}" id="material_request_form" method="post">
                        @csrf
                        <div class="row justify-content-md-center">
                            <div class="col-12">
                                <div class="form-body">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12 col-lg-6">
                                            <div class="form-group">
                                                <select name="address_select" id="address_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="Pickup address is required">
                                                    <option value="0">New</option>
                                                    @foreach($address as $pickup)
                                                        <option value="{{$pickup->id}}">{{$pickup->pickup_address}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-md-12 col-lg-6">
                                            <div id="new_pickup_address" class="d-none">
                                                <div class="form-group">
                                                    <textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required">
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" name="new_pickup_phone_number" id="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                                </div>
                                                <div class="form-group">
                                                    <select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">
                                                        @foreach($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="packaging_type_ids" name="packaging_type_ids">
                                    <input type="hidden" id="packaging_size_ids" name="packaging_size_ids">
                                    <input type="hidden" id="packaging_quantities" name="packaging_quantities">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12 col-lg-6">
                                            <div class="form-group">
                                                <select name="mode_of_payment" class="select2" id="mode_of_payment" data-rule-required="true" data-msg-required="Payment mode is required">
                                                    <option></option>
                                                    @foreach($payment_mode as $mode)
                                                        <option value="{{$mode->id}}">{{$mode->mode}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                {{--<label for="sm_flyer">Packaging Material Type</label>--}}
                                                <select name="packaging_material_type" class="select2" id="packaging_material_type">
                                                    @foreach($packaging_types as $packaging_type)
                                                        <option value="{{ $packaging_type->id }}">{{ $packaging_type->type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <select name="packaging_material_size" class="select2" id="packaging_material_size"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-2">
                                            <div class="form-group">
                                                <input name="packaging_material_quantity" class="form-control quantity" id="packaging_material_quantity" placeholder="Quantity here"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-2">
                                            <div class="form-group">
                                                <button class="btn btn-primary btn-block" type="button" id="add_packaging_material_btn"> Add</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <table class="table table-bordered packaging_type_datatable" id="packaging_type_datatable" style="z-index: 3;">
                                            <thead>
                                            <tr role="row" class="bg-primary white">

                                                <th class="border-primary border-darken-1">S. No.</th>
                                                <th class="border-primary border-darken-1">Packaging Type</th>
                                                <th class="border-primary border-darken-1">Size</th>
                                                <th class="border-primary border-darken-1">Quantity</th>
                                                <th class="border-primary border-darken-1"></th>

                                            </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-12 col-lg-6">
                                            <button id="RequestMaterialBtn" type="submit" class="btn btn-primary btn-block" disabled>Request Material</button>
                                        </div>
                                    </div>

                            </div>
                        </div>
                        </div>
                    </form>
                </div>
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" class="btn btn-info" data-dismiss="modal">Close</button>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>



    <div class="modal fade text-left" id="DetailsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DetailsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Details</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $('document').ready(function(){
            var already_selected_size = [];
            $('body').on('change','#material_request_form input,#material_request_form textarea',function() {
                $(this).val($(this).val().trim());
            });
            $("#new_pickup_phone_number").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': 10000
            });

            $('#mode_of_payment').select2({
                width: '100%',
                placeholder: 'Select Payment Mode'
            });
            $('#packaging_material_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Packaging Material Type'
            }).bind('select2:select', function () {
                var type_id = $(this).val();
                if(type_id){
                    $.ajax({
                        url: '{!! route('cod.packaging.requests.sizes') !!}',
                        method: 'POST',
                        data: {
                            'id': type_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            $('#packaging_material_size').empty();

                            $.each(data.sizes,function (key,value) {
                                var type_size = parseInt(type_id+value.id);

                                var index = $.inArray(type_size, already_selected_size);

                                if(index === -1){
                                    var newOption = new Option(value.size, value.id, false, false);
                                    $('#packaging_material_size').append(newOption).trigger('change');
                                    $('#packaging_material_size').val('').trigger('change');
                                }

                            });

                        }else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });

                }
            });



            $('#packaging_material_size').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Packaging Material Size'
            });


            $('#address_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Send To*'
            }).bind('change', function() {
                $(this).valid();

                if (this.value == 0) {
                    $('#new_pickup_address').removeClass('d-none');
                }
                else {
                    $('#new_pickup_address').addClass('d-none');
                }

                // var pickup_city = $(this).find(':selected').data('city-id');
                // var consignee_city = $('#consignee_city').val();

                // shipping_mode_same_day(pickup_city, consignee_city);
            });
            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                $(this).valid();
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('cod.packaging.requests.list') }}',
                        success: function (result) {
                            head = [];

                            head.push('S No.');
                            head.push('Request Date/Time');
                            head.push('Requested Address');
                            head.push('City');
                            head.push('Amount');
                            head.push('Payment Mode');
                            head.push('Tracking No.');
                            head.push('Status');
                            head.push('Aging');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.created_at);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.amount);
                                row.push(values.mode);
                                row.push(values.tracking_number);
                                row.push(values.request_status);
                                row.push(values.aging);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        title: 'Add Request',
                        className: 'btn btn-primary',
                        text: '<i class="la la-plus"></i> Add Request',
                        action:function (e) {
                            $('#AddRequestModal').modal('show');

                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('cod.packaging.requests.list') }}',
                rowId: 'request_id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'created_at', name: 'packaging_material_requests.created_at', class: 'align-middle created_at'},
                    {data: 'address', name: 'packaging_material_requests.address', class: 'align-middle address'},
                    {data: 'city', name: 'ct.name', class: 'align-middle city'},
                    {data: 'amount', name: 'ct.name', class: 'align-middle amount'},
                    {data: 'mode', name: 'packaging_material_requests.packaging_payment_mode_id', class: 'align-middle mode'},
                    {data: 'tracking_number_link', name: 'packaging_material_requests.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'request_status', name: 'packaging_material_requests.status_id', class: 'align-middle request_status'},
                    {data: 'aging', name: 'aging', class: 'align-middle aging'},
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var payment_mode_select = '<select name="payment_mode_select" id="payment_mode_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.aging')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.request_status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.mode')){
                            $(payment_mode_select).appendTo($(search))
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

                    var data2 = $.map({!! $packaging_request_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data2 = $.map({!! $packaging_request_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data: data2,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $payment_mode !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $payment_mode !!}, function (obj) {
                        obj.text = obj.mode;

                        return obj;
                    });

                    $("#payment_mode_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });



                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click', 'a.details', function () {
                var id = $(this).parents('tr').attr('id');
               if(id){
                   $.ajax({
                       url: '{!! route('cod.packaging.requests.details') !!}',
                       method: 'POST',
                       data: {
                           'id': id,
                           '_token': '{{ csrf_token() }}'
                       }
                   }).done(function (data) {
                        if(data.status == 0){
                            var html = '';

                            html += '<table class="table table-sm datatable text-center">';
                            html += '<thead>';
                            html += '<tr role="row">';
                            html += '<th><strong>Type</strong></th>';
                            html += '<th><strong>Size</strong></th>';
                            html += '<th><strong>Quantity</strong></th>';

                            html += '</tr>';
                            html += '</thead>';
                            html += '<tbody>';

                            $.each(data.details, function(index, value){
                                html += '<tr>';
                                html += '<td>' + value.packaging_type.type + '</td>';
                                html += '<td>' + value.packaging_type_size.size + '</td>';
                                html += '<td>' + value.quantity + '</td>';
                                html += '</tr>';
                            });


                            html += '</tbody>';
                            html += '</table>';

                            $('#DetailsModal').modal('show');
                            $('#DetailsModal .modal-body').html(html);
                        }else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                   });

               }
            });
            $('input.quantity').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
            });




            $("#AddRequestModal").on('shown.bs.modal', function(){
                initDatatable();
            });
            $("#AddRequestModal").on('hidden.bs.modal', function(){
                destroyDatatable();
                $('#RequestMaterialBtn').attr('disabled', false);
            });

            function destroyDatatable() {
                ptable.clear();
                ptable.destroy();
            }
            var ptable;
            function initDatatable() {
                ptable = $('#packaging_type_datatable').DataTable({
                    dom: 'ltipr',
                    paging:false,
                    ordering:[0, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                        {name: 'packaging_type', class: 'align-middle packaging_type', orderable: false},
                        {name: 'size', class: 'align-middle size', orderable: false},
                        {name: 'quantity', class: 'align-middle quantity', orderable: false},
                        {name: 'action', class: 'align-middle action', orderable: false}
                    ],
                    rowCallback: function(row, data, index) {

                    },
                    initComplete: function() {

                    }
                });
            }

            var packaging_types_array = [];
            var packaging_size_array = [];
            var packaging_quantity_array = [];
            var packaging_index_array = [];
            var rid = 100;
            $('#add_packaging_material_btn').on('click', function () {
                var type = $('#packaging_material_type').val();
                var type_name = $('#packaging_material_type option:selected').text();
                var size = $('#packaging_material_size').val();
                var size_name = $('#packaging_material_size option:selected').text();
                var quantity = $('#packaging_material_quantity').val();
                var flag = false;
                if(type == ''){
                    flag = true;
                    var error = "<p id='type_error' class='danger'>Type is required</p>";
                    if($('#packaging_material_type').parent('div').find('p#type_error').length == 0){
                        $('#packaging_material_type').parent('div').append(error);
                    }
                }else{
                    flag = false;
                    $('#type_error').remove();
                }

                if(size == ''){
                    flag = true;
                    var error = "<p id='size_error' class='danger'>Size is required</p>";
                    if($('#packaging_material_size').parent('div').find('p#size_error').length == 0){
                        $('#packaging_material_size').parent('div').append(error);
                    }
                }else{
                    $('#size_error').remove();
                }

                if(quantity == ''){
                    flag = true;
                    var error = "<p id='quantity_error' class='danger'>Quantity is required</p>";
                    if($('#packaging_material_quantity').parent('div').find('p#quantity_error').length == 0){
                        $('#packaging_material_quantity').parent('div').append(error);
                    }
                }else{
                    $('#quantity_error').remove();
                }

                if(flag == false){
                    var rowNo = ptable.rows().count();

                    var remove = '<a href="javascript:void(0);" class="btn btn-sm btn-danger remove"><i class="la la-close"></i></a>';
                    ptable.row.add([rowNo+1,type_name,size_name,quantity, remove]).node().id = rid;
                    ptable.draw(false);
                    already_selected_size.push(parseInt(type+size));
                    packaging_index_array.push(rid);
                    rid++;
                    packaging_types_array.push(type);
                    packaging_size_array.push(size);
                    packaging_quantity_array.push(quantity);
                    $('#RequestMaterialBtn').attr('disabled', false);
                    $('#packaging_material_type').val('').trigger('change');
                    $('#packaging_material_size').empty();
                    $('#packaging_material_quantity').val('');
                }

            });

            $('#packaging_type_datatable').on('click', 'a.remove', function(){
               var rowId = parseInt($(this).parents('tr').attr('id'));

                var index = $.inArray(rowId, packaging_index_array);

                if (index !== -1) {
                    already_selected_size.splice(index, 1);
                    packaging_index_array.splice(index, 1);
                    packaging_types_array.splice(index, 1);
                    packaging_size_array.splice(index, 1);
                    packaging_quantity_array.splice(index, 1);
                }
                ptable.row( $(this).parents('tr') ).remove().draw();
                if(ptable.rows().count() == 0){
                    $('#RequestMaterialBtn').attr('disabled', true);
                }
            });


            $('#material_request_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    if(ptable.rows().count() > 0){
                        $('#packaging_type_ids').val(packaging_types_array);
                        $('#packaging_size_ids').val(packaging_size_array);
                        $('#packaging_quantities').val(packaging_quantity_array);
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Your request is being submitted!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }else{

                        toastr.error("Please Select atleast one packaging type!", 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        return false;
                    }

                }
            });

        });
    </script>

@endsection