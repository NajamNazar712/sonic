@extends('admin.layout.master')

@section('title', 'Packaging Material Requests')

@section('content')
    <h1 class="mb-1">
        Packaging Material Requests
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Requested Date/Time</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Total Quantity</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Payment Mode</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Request Date Aging</th>
                        <th class="border-primary border-darken-1">Confirmed Date Aging</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    {{--View Modal--}}
    <div class="modal fade text-left" id="ViewTypeSizeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewTypeSizeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">View Type and Size</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddRemarks" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRemarks"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add/Update Remarks</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="#" id="update_remarks_form" class="form">
                        <input type="hidden" name="remarks_shipment_id" id="remarks_shipment_id">
                        <textarea type="text" name="packaging_remarks" class="form-group form-control" id="packaging_remarks" placeholder="Remarks" rows="7"></textarea>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="update_remarks_button">Update</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style type="text/css">
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            function print(id, booking_type_id) {
                if (booking_type_id != 4) {
                    var url = '{!! route('cod.shipment.book.print_air_waybill') !!}';
                }
                else {
                    var url = '{!! route('admin.shipment.book.print_air_waybill') !!}';
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        'ids[]': id,
                        'admin': true,
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

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.requests.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Shipper');
                            head.push('Requested Date/Time');
                            head.push('City');
                            head.push('Total Quantity');
                            head.push('Amount');
                            head.push('Address');
                            head.push('Payment Mode');
                            head.push('Status');
                            head.push('Remarks');
                            head.push('Requested Date Aging');
                            head.push('Confirmed Date Aging');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.created_at);
                                row.push(values.city);
                                row.push(values.total_quantity);
                                row.push(values.amount);
                                row.push(values.address);
                                row.push(values.mode);
                                row.push(values.status);
                                row.push(values.remarks);
                                row.push(values.aging);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                        @if (session('role_id') == 1 || in_array(221, session('permissions')))
                    {
                        text: '<i class="la la-align-justify"></i> View Inventory',
                        className: 'btn btn-primary view_inventory',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            var url = '{{route('admin.packaging.inventory.index')}}';
                            var win = window.open(url, '_blank');
                            win.focus();
                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        className: 'btn btn-primary',
                        title: 'Packaging Material Requests',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.packaging.requests.list') }}',
                rowId: 'request_id',
                order: [[3, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'packaging_material_requests.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'created_at', name: 'packaging_material_requests.created_at', class: 'align-middle created_at'},
                    {data: 'city', name: 'ct.name', class: 'align-middle city'},
                    {data: 'total_quantity_button', class: 'align-middle total_quantity_button',orderable: false, searchable: false},
                    {data: 'amount', name: 'packaging_material_requests.amount', class: 'align-middle amount'},
                    {data: 'address', name: 'packaging_material_requests.address', class: 'align-middle address'},
                    {data: 'mode', name: 'ppm.id', class: 'align-middle mode'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'remarks', name: 'sj.remarks', class: 'align-middle remarks_view'},
                    {data: 'aging', class: 'align-middle aging', orderable: false, searchable: false},
                    {data: 'confirmed_aging', class: 'align-middle confirmed_aging', orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'align-middle action',orderable: false, searchable: false}

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


                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.total_quantity_button') || $(header).is('.aging') || $(header).is('.confirmed_aging')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
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
            $('body').on('click','.quantity',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));

                $.ajax({
                    url: '{!! route('admin.packaging.requests.quantity_details') !!}',
                    method: 'POST',
                    data: {
                        'id': request_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        var html = '';
                        html += '<table class="table table-sm datatable text-center">';
                        html += '<thead><tr><th>S No.</th><th><strong>Type</strong></th><th><strong>Size</strong></th><th><strong>Quantity</strong></th></tr></thead>';
                        html += '<tbody>';
                        $.each(data.types, function(index, value) {
                            var ind = index+1;
                            html += '<tr class=""><td>' + ind + '</td>';
                            html += '<td>' + value.type + '</td>';
                            html += '<td>' + value.size + '</td>';
                            html += '<td>' + value.quantity + '</td></tr>';
                        });
                        html += '</tbody></table>';

                        $('#ViewTypeSizeModal .modal-body').html(html);
                        $('#ViewTypeSizeModal').modal('show');
                    }
                    // console.log(data.success);
                });
            });
            //grn
            $('body').on('click','.grn',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;

                    $.ajax({
                        url: '{!! route('admin.packaging.requests.good_receiving_note') !!}',
                        method: 'POST',
                        data: {
                        'id': request_id,
                        'shipment_id': shipment_id_data,
                        '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
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
                        // console.log(data.success);
                    });
            });
            //confirm
            $('body').on('click','.confirm',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                var booking_type_id = table.row($(this).parents('tr')).data().booking_type_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Confirm Packaging Material!',
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
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.confirm') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {

                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                if(shipment_id_data != null && booking_type_id != null){
                                    print(shipment_id_data, booking_type_id);
                                }
                                table.draw();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        });
                    }
                });

            });
            //cancel
            $('body').on('click','.cancel',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Cancel Packaging Material!',
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
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.cancel') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {

                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        });
                    }
                });

            });
            //dispatch
            $('body').on('click','.dispatch',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Dispatch Packaging Material!',
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
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.dispatch') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {

                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        });
                    }
                });

            });
            //complete
            $('body').on('click','.completed',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Complete Packaging Material!',
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
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.completed') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                });

            });
            //replenish
            $('body').on('click','.replenished',function(){
                var request_id = parseInt($(this).parents('tr').attr('id'));
                var shipment_id_data = table.row($(this).parents('tr')).data().shipment_id;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Replenish Packaging Material!',
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
                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.packaging.requests.replenish') !!}',
                            method: 'POST',
                            data: {
                                'id': request_id,
                                'shipment_id': shipment_id_data,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                });

            });
            //remarks
            $('body').on('click','.remarks',function(){
                var request_id = table.row($(this).parents('tr')).data().shipment_id;
                var remarks = table.row($(this).parents('tr')).data().remarks;
                $('#remarks_shipment_id').val(request_id);
                $('#packaging_remarks').val(remarks);

                $('#AddRemarks').modal('show');

            });
            $('#update_remarks_form').on('submit', function(e){
                e.preventDefault();
            });

            $('#update_remarks_button').on('click', function(){
                var remarks_shipment_id = $('#remarks_shipment_id').val();
                var packaging_remarks = $('#packaging_remarks').val();
                if(packaging_remarks == null || packaging_remarks == ''){
                    var error = 'Please enter remarks';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                else{
                    $('#update_remarks_button').attr('disabled', true);
                    $.ajax({
                        url: '{!! route('admin.packaging.requests.remarks') !!}',
                        method: 'POST',
                        data: {
                            'id': remarks_shipment_id,
                            'remarks': packaging_remarks,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status === 1){
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            table.draw();
                            $('#AddRemarks').modal('hide');
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        $('#update_remarks_button').attr('disabled', false);
                    });
                }
            });

            $('#AssignAgentModal').on('hide.bs.modal', function (e) {
                $('#remarks_shipment_id').val('');
                $('#packaging_remarks').val('');
            });


        });

    </script>
@endsection