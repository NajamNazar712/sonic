@extends('client.layout.master')

@section('title', 'Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
      </div>
      <div class="content-body">
          <div class="row">
              <div class="card">
                  <div class="card-content">
                    <div class="card-body">
                        <h2>Order Details</h2>

                        <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                          <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1"></th>
                                <th class="border-primary border-darken-1">S No.</th>
                                <th class="border-primary border-darken-1">Tracking No.</th>
                                <th class="border-primary border-darken-1">Order ID</th>
                                <th class="border-primary border-darken-1">Service Type</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Payment Status</th>
                                <th class="border-primary border-darken-1">Origin</th>
                                <th class="border-primary border-darken-1">Destination</th>
                                <th class="border-primary border-darken-1">Consignee Name</th>
                                <th class="border-primary border-darken-1">Consignee Contact</th>
                                <th class="border-primary border-darken-1">Consignee Address</th>
                                <th class="border-primary border-darken-1">Collection Amount</th>
                                <th class="border-primary border-darken-1">Product Type</th>
                                <th class="border-primary border-darken-1">Booking Date</th>
                                <th class="border-primary border-darken-1">Instructions</th>
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
  </div>
<!--Shipment Charges Modal -->
<div class="modal fade text-left" id="ShipmentChargesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShipmentChargesModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="shipment_charges_modal_heading">Shipment Charges of #<span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="hidden" id="shipment_charges_modal_id">
            <div class="modal-body shipment_charges_body text-center" id="shipment_charges_body">
            </div>
        </div>
    </div>
</div>
<!--Shipment Charges Modal -->
<!--Dispute Modal -->
<div class="modal fade text-left" id="DisputeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DisputeModal"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Launch Dispute</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body  text-center">
                <form id="dispute_form" action="" method="post">
                    <input type="hidden" id="dispute_shipment_id" name="dispute_shipment_id">
                    <div class="row mb-2">
                        <div class="col-12 form-group">
                            <select name="city_select" id="city_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                <option></option>
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <select name="dispute_type_select" id="dispute_type_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                <option></option>
                                @foreach($dispute_types as $dispute)
                                    <option value="{{$dispute->id}}">{{$dispute->type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <input name="tracking_number" id="tracking_number" class="tracking_number" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <textarea name="description" id="description" class="form-control" cols="30" rows="3" placeholder="Enter Description" data-rule-required="true" data-msg-required="This field is required"></textarea>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="DisputeCreate" type="submit" class="btn btn-primary btn-block">Launch Dispute</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Dispute Modal -->
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

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            function print(selected_rows) {
                $.ajax({
                    url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                    method: 'POST',
                    data: {
                        'ids[]': selected_rows,
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
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: '<i class="la la-print"></i> Print',
                    className: 'btn btn-primary print',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        table.button(0).disable();
                        print(selected_rows);
                        table.rows().deselect();
                        selected_rows = [];
                      }
                    }, {
                      extend: 'selectAll',
                      text: 'Select All',
                      className: 'select_all',
                      action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                          var row = table.row(index);

                          if ($(row.node().firstChild).hasClass('select-checkbox')) {
                            row.select();

                            id = parseInt(row.id());

                            var index = $.inArray(id, selected_rows);

                            if (index === -1) {
                              selected_rows.push(id);
                            }

                            table.button('.print').enable();
                          }
                        });
                      }
                    }, {
                      extend: 'selectNone',
                      text: 'Select None',
                      className: 'select_none',
                      action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                          var row = table.row(index);

                          if ($(row.node().firstChild).hasClass('select-checkbox')) {
                            row.deselect();

                            id = parseInt(row.id());

                            var index = $.inArray(id, selected_rows);

                            if (index !== -1) {
                                selected_rows.splice(index, 1);
                            }

                            if (selected_rows.length == 0) {
                                table.button('.print').disable();
                            }
                          }
                        });
                      }
                    }
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('cod.orders.list') }}',
                rowId: 'shipment_id',
                order: [[14, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'service_type', name: 'bt.id', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'payment_status', name: 'payment_status', class: 'align-middle payment_status'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'phone', name: 'phone', class: 'align-middle phone'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
                    {data: 'instructions', name: 'shipments.special_instructions', class: 'align-middle instructions'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if (data.shipper_status_id === 1) {
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var payment_select = '<select name="payment_select" id="payment_select" class="select2 form-control"></select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    var product_select = '<select name="product_select" id="product_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.payment_status')){
                            $(payment_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    console.log($(this).val())
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.product_type')){
                            $(product_select).appendTo($(search))
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
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.text = obj.booking_type;

                        return obj;
                    });

                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data3 = $.map({!! $products !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data3 = $.map({!! $products !!}, function (obj) {
                        obj.text = obj.product_name;

                        return obj;
                    });

                    $("#product_select").prepend('<option value="" selected></option>').select2({
                        data:data3,
                        placeholder: "Select Product",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
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

            $('body').on('click','.cancel_order',function () {
               var id = parseInt($(this).parents('tr').attr('id'));
               if(id){
                   $.ajax({
                       url: '{!! route('cod.orders.cancel') !!}',
                       method: 'POST',
                       data: {
                           'shipment_id': id,
                           '_token': '{{ csrf_token() }}'
                       }
                   }).done(function (data) {
                        if(data.status === 1){
                            table.draw('false');
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                   });
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
                    table.button(0).enable();
                }
                else {
                    table.button(0).disable();
                }
            });

            //Dispute
            $('#city_select').select2({
                placeholder:'Select a city',
                dropdownParent:$('#dispute_form')
            });
            $('#dispute_type_select').select2({
                placeholder:'Select a Dispute type',
                dropdownParent:$('#dispute_form')
            });

            $('body').on('click','.dispute_modal',function () {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                $('#DisputeModal').modal('show');
                $('#dispute_shipment_id').val(shipment_id);
            });
            $('#DisputeModal').on('shown.bs.modal',function(){
                var id = $('#dispute_shipment_id').val();
                if(id){
                    $.ajax({
                        url: '{!! route('cod.dispute.data') !!}',
                        method: 'POST',
                        data:{
                            '_token': '{{ csrf_token() }}',
                            'shipment_id':id
                        }
                    }).done(function (data) {
                        if(data.success == 1){
                            $('#tracking_number').val(data.tracking);
                            select = $('#tracking_number').selectize({
                                placeholder: 'Tracking Number(s)*',
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
                                }
                            });
                        } else{
                            toastr.error(message, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        }
                    });
                }
            });

            var max_char = 190;
            $('#description').on('keypress copy paste',function (e) {
                // var comment = $(this).val();
                // console.log(comment)
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('body').on('change','#DisputeModal input,#DisputeModal textarea',function() {
                $(this).val($(this).val().trim());
            });
            $( "#dispute_form" ).validate({
                ignore: [],
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    var city_select = $('#city_select').val();
                    var dispute_type_select = $('#dispute_type_select').val();
                    var tracking_number = $('#tracking_number').val();
                    var description = $('#description').val();
                    $.ajax({
                        url: '{!! route('cod.dispute.create') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'city_select': city_select,
                            'dispute_type_select':dispute_type_select,
                            'tracking_number':tracking_number,
                            'description':description
                        }
                    }).done(function (data) {
                        $('#DisputeModal').modal('hide');

                        if (data.invalid !== undefined) {
                            var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                            toastr.error(message, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }

                        if (data.disallowed !== undefined) {
                            var message = 'Following Tracking Number(s) doesn\'t belong to you: ' + data.disallowed.join(', ');

                            toastr.error(message, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(data.success != undefined){
                            table.draw('false');
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                        }
                    });

                }


            });
            $('#DisputeModal').on('hidden.bs.modal',function (e) {
                $('#dispute_form')[0].reset();
                $('#DisputeCreate').removeAttr('disabled');
                select[0].selectize.destroy();
                $('#dispute_form').validate().resetForm();
                $('#city_select').val('').trigger('change');
                $('#dispute_type_select').val('').trigger('change');
            });

            $('body').on('click','.view_charges',function () {
                var shipment_id = $(this).parents('tr').attr('id');
                $('#ShipmentChargesModal').modal('show');
                $('#shipment_charges_modal_id').val(shipment_id);
                $.ajax({
                    url:'{!! route("cod.orders.charges") !!}',
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

            window.onresize = function() {
                $(".echart-container").each(function(){
                    var id = $(this).attr('_echarts_instance_');
                    window.echarts.getInstanceById(id).resize();
                });
            };

        });

    </script>

@endsection