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

    {{--<form action="{{route('cod.packaging.requests.submit')}}" id="material_request_form" method="post">--}}
        {{--@csrf--}}
        {{--<div class="row justify-content-md-center">--}}
            {{--<div class="col-12">--}}
                {{--<div class="form-body">--}}
                    {{--<div class="row justify-content-center">--}}
                        {{--<div class="col-md-12 col-lg-6">--}}
                            {{--<div class="form-group">--}}
                                {{--<select name="address_select" id="address_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">--}}
                                    {{--<option value="0">New</option>--}}
                                    {{--@foreach($address as $pickup)--}}
                                        {{--<option value="{{$pickup->id}}">{{$pickup->pickup_address}}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="row justify-content-center">--}}
                        {{--<div class="col-md-12 col-lg-6">--}}
                            {{--<div id="new_pickup_address" class="d-none">--}}
                                {{--<div class="form-group">--}}
                                    {{--<textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"></textarea>--}}
                                {{--</div>--}}

                                {{--<div class="form-group">--}}
                                    {{--<input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required">--}}
                                {{--</div>--}}

                                {{--<div class="form-group">--}}
                                    {{--<input type="text" name="new_pickup_phone_number" id="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">--}}
                                        {{--@foreach($cities as $city)--}}
                                            {{--<option value="{{ $city->id }}">{{ $city->name }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="row">--}}
                        {{--<div class="col-md-6 col-lg-4">--}}
                            {{--<div class="form-group">--}}
                                {{--<label for="sm_flyer">Small Flyers</label>--}}
                                {{--<input type="text" id="sm_flyer" class="form-control numeric flyer" placeholder="Small Flyers Quantity" name="sm_flyer">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-6 col-lg-4">--}}
                            {{--<div class="form-group">--}}
                                {{--<label for="md_flyer">Medium Flyers</label>--}}
                                {{--<input type="text" id="md_flyer" class="form-control numeric flyer" placeholder="Medium Flyers Quantity" name="md_flyer">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-6 col-lg-4">--}}
                            {{--<div class="form-group">--}}
                                {{--<label for="lg_flyer">Large Flyers</label>--}}
                                {{--<input type="text" id="lg_flyer" class="form-control numeric flyer" placeholder="Large Flyers Quantity" name="lg_flyer">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-6 col-lg-3">--}}
                        {{--<div class="form-group">--}}
                        {{--<label for="boxes">Boxes</label>--}}
                        {{--<input type="text" id="boxes" class="form-control numeric flyer" placeholder="Boxes Quantity" name="boxes">--}}
                        {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="row justify-content-center">--}}
                        {{--<div class="col-md-12 col-lg-6">--}}
                            {{--<div class="form-group">--}}
                                {{--<label for="boxes">Flyer Mode of Payment</label>--}}
                                {{--<select name="mode_of_payment" class="select2" id="mode_of_payment" data-rule-required="true" data-msg-required="Payment mode is required">--}}
                                    {{--<option></option>--}}
                                    {{--@foreach($payment_mode as $mode)--}}
                                        {{--<option value="{{$mode->id}}">{{$mode->mode}}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="row justify-content-center">--}}
                        {{--<div class="col-md-12 col-lg-6">--}}
                            {{--<button id="RequestMaterialBtn" type="submit" class="btn btn-primary btn-block">Request Material</button>--}}

                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</form>--}}


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
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">--}}
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
{{--    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $('document').ready(function(){
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

                // var pickup_city = $(this).val();
                // var consignee_city = $('#consignee_city').val();
                //
                // shipping_mode_same_day(pickup_city, consignee_city);
            });


            $('#material_request_form').validate({
                rules: {
                    sm_flyer: {
                        require_from_group: [1, ".flyer"]
                    },
                    md_flyer: {
                        require_from_group: [1, ".flyer"]
                    },
                    lg_flyer: {
                        require_from_group: [1, ".flyer"]
                    },
                    // boxes: {
                    //     require_from_group: [1, ".flyer"]
                    // }
                },
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
                        text: 'Your request is being submitted!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });


            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
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
                    {data: 'mode', name: 'ppm.mode', class: 'align-middle mode'},
                    {data: 'tracking_number_link', name: 'packaging_material_requests.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'request_status', name: 'prs.name', class: 'align-middle request_status'},
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
                    var status = '<select name="status" id="status" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.status')) {
                            $(status).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
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

                    var data = $.map({!! $status !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $('#status').prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
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
                   $('#DetailsModal').modal('show');
                   $.ajax({
                       url: '{!! route('cod.packaging.requests.details') !!}',
                       method: 'POST',
                       data: {
                           'id': id,
                           '_token': '{{ csrf_token() }}'
                       }
                   }).done(function (data) {

                   });

               }
            });

        });
    </script>

@endsection