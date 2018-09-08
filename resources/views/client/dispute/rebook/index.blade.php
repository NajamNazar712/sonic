@extends('client.layout.master')

@section('content')
    <h1 class="mb-1">
        Re-book To New Destination
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Booking Date</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{--Rebook modal--}}
    <div class="modal fade text-left" id="RebookModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RebookModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Rebook Shipment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center rebook_body">
                    <form id="updateRebook" action="#" method="post">
                    <div class="row justify-content-center mb-2">
                        <div class="col-6">
                            <span class="form-label">Tracking Number :</span>
                            <input type="text" name="tracking_number" id="tracking_number" readonly class="form-control text-center">
                            <input type="hidden" name="shipment_id" id="shipment_id" readonly class="form-control text-center">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h3>Consignee Information</h3>
                            <div class="form-group">
                                <input type="hidden" id="old_consignee_city">
                                <select name="consignee_city" id="consignee_city" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                <option></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" id="consignee" name="consignee" class="form-control" placeholder="Consignee Name*" data-rule-required="true" data-msg-required="This field is required">
                            </div>
                            <div class="form-group">
                                <textarea name="address" class="form-control" id="address" cols="49" rows="5" placeholder="Address*" data-rule-required="true" data-msg-required="This field is required"></textarea>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="phone1" name="phone1" data-rule-required="true" data-msg-required="This field is required" placeholder="phone 1*">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="phone2" name="phone2" placeholder="Phone 2">
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Consignee Email">
                            </div>
                        </div>
                        <div class="col-6">
                            <h3>Payment Information</h3>
                            <div class="form-group">
                                <input type="text" class="form-control" id="amount" name="amount" data-rule-required="true" data-msg-required="This field is required" placeholder="Amount*">
                            </div>
                            <div class="form-group">
                                <select name="payment_mode" id="payment_mode" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                    <option></option>
                                </select>
                            </div>

                        </div>
                        <div class="col justify-content-center">
                            <div class="form-group text-center">
                                    <button id="rebookSubmit" type="submit" class="btn btn-primary">Update</button>

                            </div>
                        </div>

                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Rebook modal--}}

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    {{--    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/ui/perfect-scrollbar.min.css')}}">--}}



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
        .dispute_comments_section{
            max-height: 200px;
            overflow-y:scroll;
            overflow-x:hidden;
            /*overflow:hidden;*/
            /*position: absolute;*/
            padding: 10px;
        }
        p.comment{
            text-align: left;
        }
        .description-div p.border{
            padding:10px;
        }
        .comment-post{
            padding-top: 10px;
        }
        .comment-date{
            float:right;
            font-size: 13px;
            border-bottom: 1px solid #606060;
        }
        .selectize-control {
            width: 100%;
        }

        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script>--}}


    <script type="text/javascript">
        $(document).ready(function () {


            var selected_rows = [];
            var table = $('#datatable').DataTable({
                // "scrollX": true,
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: 'Re-book',
                    className: 'btn btn-primary rebook_modal',
                    enabled: false,
                    action: function (e, dt, node, config) {
                            if(selected_rows !== ''){

                                    if(selected_rows.length !== 0){
                                        open_rebook_modal();
                                    }

                            }
                    }
                }],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('cod.dispute.rebook.list') }}',
                rowId: 'shipment_id',
                order: [[1, 'asc']],
                columns: [
                    {data: 'shipment_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'booking_type', name: 'bt.booking_type', class: 'align-middle booking_type'},
                    {data: 'status', name: 'ss.name', class: 'align-middle status'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee', name: 'shipments.consignee_name', class: 'align-middle consignee'},
                    {data: 'phone', name: 'shipments.consignee_phone_number_1', class: 'align-middle phone'},
                    {data: 'address', name: 'shipments.consignee_address', class: 'align-middle address'},
                    {data: 'product_name', name: 'products.product_name', class: 'align-middle product_name'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {
                        data: 'action',
                        name: 'action',
                        class: 'text-center align-middle action p-1',
                        orderable: false,
                        searchable: false
                    }

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.select') || $(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                }
            });
            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {

                var id = parseInt($(this).parent('tr').attr('id'));
                    var index = $.inArray(id, selected_rows);

                    if (index === -1) {
                        selected_rows.push(id);
                    }
                    else {
                        selected_rows.splice(index, 1);
                    }

                    if (selected_rows.length > 0) {
                        table.button(0).enable();

                    }
                    else {
                        table.button(0).disable();

                    }


            });

            $("#phone1, #phone2").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $('body').on('change','#RebookModal input,#RebookModal textarea',function() {
                $(this).val($(this).val().trim());
            });
            $('body').on('change','#updateRebook input,#updateRebook textarea',function() {
                $(this).val($(this).val().trim());
            });
            $("#updateRebook").validate({
                ignore: [],
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    // $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    var postObject = new Object;
                    var shipment_id = $.trim($('#shipment_id').val());
                    var consignee_city = $.trim($('#consignee_city').val());
                    var old_consignee_city = $.trim($('#old_consignee_city').val());
                    if(consignee_city !== old_consignee_city) {
                        var consignee = $.trim($('#consignee').val());
                        var address = $.trim($('#address').val());
                        var phone1 = $.trim($('#phone1').val());
                        var phone2 = $.trim($('#phone2').val());
                        var email = $.trim($('#email').val());
                        var amount = $.trim($('#amount').val());
                        var mode = $.trim($('#payment_mode').val());
                        postObject.shipment_id = shipment_id;
                        postObject.consignee_city_id = consignee_city;
                        postObject.consignee = consignee;
                        postObject.address = address;
                        postObject.phone1 = phone1;
                        postObject.phone2 = phone2;
                        postObject.email = email;
                        postObject.amount = amount;
                        postObject.mode = mode;
                        postObject._token = '{{ csrf_token() }}';
                        var errors = 0;
                        if (postObject.email !== '') {
                            if (!validateEmail(postObject.email)) {
                                errors = 1;
                            }
                        }
                        if (errors === 0) {
                            $.ajax({
                                url: "{{route('cod.dispute.rebook.shipment.update')}}",
                                method: 'POST',
                                data: postObject,
                            }).done(function (data) {
                                if (data.status === 1) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                    selected_rows.splice($.inArray(selected_rows[0], selected_rows), 1);

                                    if (selected_rows.length !== 0) {
                                        $('#consignee_city').empty().trigger('change');
                                        $('#payment_mode').empty().trigger('change');
                                        console.log('Submitted id:'+shipment_id);
                                        console.log('After submit: ' + selected_rows);
                                        // open_rebook_modal();
                                        get_shipment_info(selected_rows[0]);
                                    }else{
                                        $('#RebookModal').modal('hide');
                                        table.draw('false');
                                    }

                                } else {
                                    $('#RebookModal').modal('hide');
                                    selected_rows = [];
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                    if (selected_rows.length !== 0) {
                                        console.log('After error submit: ' + selected_rows);
                                        open_rebook_modal();
                                    }
                                }
                            });
                        }
                    }else{
                        var err = 'Select different city!';
                        toastr.error(err, 'Error!', {
                            positionClass: 'toast-bottom-center',
                            containerId: 'toast-bottom-center'
                        });
                    }
                }


            });
            function validateEmail(email) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            }
            // $('.rebook_modal').on('click', function () {
            //     // $('#RebookModal').modal('show');
            //     console.log(selected_rows)
            // });


            $('body').on('click','.rebook',function () {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                    $('#RebookModal').modal('show');
                    get_shipment_info(shipment_id);
            });
            function get_shipment_info(shipment_id) {
                $('#shipment_id').val(shipment_id);
                if(shipment_id != null){
                    $.ajax({
                        url:"{{route('cod.dispute.rebook.shipment.info')}}",
                        method:'POST',
                        data:{
                            'shipment_id':shipment_id,
                            '_token':'{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 1){
                            // var city = data.cities;
                            if(!$('#consignee_city').hasClass('select2-hidden-accessible')){
                                $('#consignee_city').select2({
                                    placeholder: 'Select a city',
                                    dropdownParent: $('#RebookModal')
                                });
                            }
                            if(!$('#payment_mode').hasClass('select2-hidden-accessible')){
                                $('#payment_mode').select2({
                                    placeholder: 'Select a payment mode',
                                    dropdownParent: $('#RebookModal')
                                });
                            }

                            $('#tracking_number').val(data.data.tracking_number);
                            $('#consignee').val(data.data.consignee_name);
                            $('#address').val(data.data.consignee_address);
                            $('#phone1').val(data.data.consignee_phone1);
                            $('#phone2').val(data.data.consignee_phone2);
                            $('#email').val(data.data.consignee_email);
                            $('#amount').val(data.data.amount);
                            $('#old_consignee_city').val(data.data.consignee_city_id);
                            // $('#payment_mode').val(data.data.amount).trigger('change');
                            $.each(data.cities,function(key,value){
                                var newOption = new Option(value.name, value.id, false, false);
                                $('#consignee_city').append(newOption).trigger('select');
                            });
                            $('#consignee_city').val(data.data.consignee_city_id).trigger('change');
                            $.each(data.payment,function(key,value) {
                                var payment = new Option(value.mode, value.id, false, false);
                                $('#payment_mode').append(payment).trigger('select');
                            });
                            $('#payment_mode').val(data.data.payment_mode).trigger('change');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }

                    });
                }
            }
            $('#RebookModal').on('hidden.bs.modal',function () {
               $('#consignee_city').empty().trigger('change');
               $('#payment_mode').empty().trigger('change');
               table.draw('false');
            });


            function open_rebook_modal() {
                if(selected_rows.length !== 0){
                    // setTimeout(function () {
                        $('#RebookModal').modal('show');
                        get_shipment_info(selected_rows[0]);
                    // },1000);

                }
            }
            function print(id) {
                $.ajax({
                    url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                    method: 'POST',
                    data: {
                        'ids[]': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            $('body').on('click', '.print_airway', function() {
                id = parseInt($(this).parents('tr').attr('id'));

                print(id);
            });
        });

    </script>
@endsection