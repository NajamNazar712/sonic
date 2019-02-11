@extends('admin.layout.master')
@section('title','Update Misrouted')

@section('content')
    <h1 class="mb-1">
        Update Misrouted
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="misroute_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>

                    <div class="form-group ml-1">
                        <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                    </div>
                </form>

                <form id="update_misroute_form" action="{{route('admin.delivery.misroute.update.store')}}" class="form-horizontal" method="POST">
                    {{ csrf_field() }}
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Phone2</th>
                        <th class="border-primary border-darken-1">Email</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary btn-block" disabled id="update_misroute_form_submit">Submit &amp; Print</button>

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
            @if(session('excel'))
            function excel() {
                window.open('{!! route('admin.delivery.misroute.update.excel') !!}?ids=' + '{{ implode(',', session('shipments')) }}', '_blank');
            }
            excel();
            @endif
            var shipment_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                autoWidth : false,
                paging:false,
                // ordering:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'destination', class: 'align-middle destination form-group', orderable: false},
                    {name: 'consignee_name', class: 'align-middle consignee_name form-group', orderable: false},
                    {name: 'address', class: 'align-middle address form-group', orderable: false},
                    {name: 'phone', class: 'align-middle phone form-group', orderable: false},
                    {name: 'phone2', class: 'align-middle phone2', orderable: false},
                    {name: 'email', class: 'align-middle email', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                    {name: 'action', class: 'align-middle action',searchable: false , orderable: false}
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

            $('#misroute_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            var cities_array = @json($cities);
            $('#misroute_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#misroute_shipment_form button.add').prop('disabled', true);

                    var tracking_number = $(form).find('input.tracking_number').val();

                    form.reset();

                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
                        $.ajax({
                            url: '{!! route('admin.delivery.misroute.update.shipment.info') !!}',
                            method: 'POST',
                            data: {
                                'tracking_number': tracking_number,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 1) {
                                    id = data.details.id;

                                    var index = $.inArray(id, shipment_ids);

                                    if (index === -1) {
                                        var name = '<input class="form-control" name="consignee_name['+data.details.id+']" data-rule-required="true" data-msg-required="Name is required" value="'+data.details.consignee_name+'">';
                                        var address = '<input class="form-control" name="consignee_address['+data.details.id+']" data-rule-required="true" data-msg-required="Address is required" value="'+data.details.consignee_address+'">';
                                        var phone1 = '<input class="form-control phone1" name="consignee_phone1['+data.details.id+']" data-rule-required="true" data-msg-required="Phone 1 is required" value="'+data.details.consignee_phone1+'">';
                                        var phone2 = '<input class="form-control phone2" name="consignee_phone2['+data.details.id+']" value="'+data.details.consignee_phone2+'" placeholder="Enter Phone 2">';
                                        var email = '<input class="form-control" name="consignee_email['+data.details.id+']" value="'+data.details.consignee_email+'" placeholder="Enter email">';
                                        var city = '<select class="select2 form-control consignee_city_select_'+data.details.id+'" name="consignee_city['+data.details.id+']"></select>';
                                        var action = '<a href="javascript:void(0);" class="btn btn-icon btn-danger removerow"><i class="la la-close"></i></a>';
                                        var rowNo = table.rows().count();

                                        table.row.add([rowNo + 1, data.details.tracking_number, city, name, address, phone1, phone2, email, data.details.amount,action]).node().id = data.details.id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();

                                        shipment_ids.push(data.details.id);

                                        $('#misroute_shipment_form button.add').prop('disabled', false);

                                        $('#update_misroute_form_submit').prop('disabled', false);

                                        var city_select = $('.consignee_city_select_'+data.details.id).select2({
                                            data: cities_array,
                                            placeholder:'Select Destination*',
                                        });

                                        var route = data.details.consignee_city_id;
                                        city_select.val(route).trigger('change');
                                        $("input.phone1, input.phone2").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else {
                                    $('#misroute_shipment_form button.add').prop('disabled', false);

                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                    else {
                        $('#misroute_shipment_form button.add').prop('disabled', false);

                        toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            // function validateEmail(email) {
            //     var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            //     return re.test(email);
            // }

            $('#update_misroute_form').validate({
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
                        text: 'Select Yes to Update Misrouted Shipments!',
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
                            $('#update_misroute_form button[type="submit"]').attr('disabled', 'disabled');
                            $('#update_misroute_form input#shipment_ids').val(shipment_ids);
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
                    $('#update_misroute_form button[type="submit"]').attr('disabled', 'disabled');
                }
            });

        });
    </script>
@endsection