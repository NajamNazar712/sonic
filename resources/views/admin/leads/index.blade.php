@extends('admin.layout.master')

@section('title', 'Leads Management')

@section('content')
    <h1 class="mb-1">
        Leads Management
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
                        <th class="border-primary border-darken-1">Lead ID</th>
                        <th class="border-primary border-darken-1">Contact Person</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Phone No</th>
                        <th class="border-primary border-darken-1">Email Address</th>
                        <th class="border-primary border-darken-1">Requested Date/Time</th>
                        <th class="border-primary border-darken-1">Sale Person Tagged</th>
                        <th class="border-primary border-darken-1">Lead Status</th>
{{--                        <th class="border-primary border-darken-1">Aging</th>--}}
{{--                        <th class="border-primary border-darken-1">Action</th>--}}
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="SalesTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <select name="Sale_person" id="saletag" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.requests.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Lead ID');
                            head.push('Contact Person');
                            head.push('City');
                            head.push('Phone No');
                            head.push('Email Address');
                            head.push('Requested Date/Time');
                            head.push('Sale Person Tagged');
                            head.push('Lead Status');
                            head.push('Aging');

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
                                row.push(values.requested_by);
                                row.push(values.aging);
                                row.push(values.confirmed_aging);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                        @if (session('role_id') == 1 || in_array(361, session('permissions')))
                    {
                        text: 'Bulk Tagging',
                        className: 'btn btn-primary bulk_tagging',
                        enabled:false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){

                                $('#SalesTagModal1').modal('show');
                                // console.log(selected_rows);
                                $('#salesTagSubmit1').on('click',function () {
                                    var assign = parseInt($('#saletag1').val());
                                    swal({
                                        text: 'Are you sure, you want to Tag?',
                                        icon: 'info',
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
                                            if (assign) {
                                                $.ajax({
                                                    url: '{!! route('admin.accounts.tag.submit.bulk') !!}',
                                                    method: 'POST',
                                                    data: {
                                                        'admin_id': assign,
                                                        'shipper_ids[]': selected_rows,
                                                        '_token': '{{ csrf_token() }}'
                                                    }
                                                })
                                                    .done(function (data) {
                                                        if (data.status == 1) {
                                                            $('#SalesTagModal1').modal('hide');
                                                            toastr.success(data.success, 'Success!', {
                                                                positionClass: 'toast-bottom-center',
                                                                containerId: 'toast-bottom-center'
                                                            });
                                                        } else {
                                                            toastr.error(data.error, 'Error!', {
                                                                positionClass: 'toast-top-center',
                                                                containerId: 'toast-top-center'
                                                            });
                                                        }
                                                        selected_rows = [];

                                                        table.rows().deselect();
                                                        $('#saletag1').val('').trigger('change');
                                                        $('#SalesTagModal1').modal('hide');
                                                        table.draw(true);
                                                        table.button('.bulk_tagging').disable();

                                                    });
                                            } else {
                                                var error = "Account Not Selected!";
                                                toastr.error(error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                        }
                                    });
                                });

                            }else{
                                var error = "Account Not selected!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                        @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    hub_id = $(row.node()).data('id');

                                    var allow = false;

                                    if(hub_ids.length == 0) {
                                        hub_ids.push(hub_id);

                                        allow = true;
                                    }
                                    else if(hub_ids[0] == hub_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.bulk_tagging').enable();
                                    }
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

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_tagging').disable();
                                        hub_ids.splice(index, 1);
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Lead Management',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
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
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.leads.list') }}',
                rowId: 'request_id',
                order: [[3, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id', name: 'packaging_material_requests.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'contact_person', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'phone_number', class: 'align-middle total_quantity_button',orderable: false, searchable: false},
                    {data: 'email_address', name: 'packaging_material_requests.amount', class: 'align-middle amount'},
                    {data: 'requested_date', name: 'packaging_material_requests.address', class: 'align-middle address'},
                    {data: 'message', name: 'ppm.id', class: 'align-middle mode'},
                    {data: 'status_id', name: 'status', class: 'align-middle status'},
                    {data: 'updated_by', name: 'sj.remarks', class: 'align-middle remarks_view'},
                    {data: 'sale_person', name:'rb.name', class: 'align-middle requested_by'},
                    {data: 'sale_person', class: 'align-middle aging', orderable: false, searchable: false},
                    // {data: 'confirmed_aging', class: 'align-middle confirmed_aging', orderable: false, searchable: false},
                    // {data: 'action', name: 'action', class: 'align-middle action',orderable: false, searchable: false}
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
                    {{--var data1 = $.map({!! $payment_mode !!}, function (obj) {--}}
                    {{--    obj.id = obj.id;--}}

                    {{--    return obj;--}}
                    {{--});--}}
                    {{--var data1 = $.map({!! $payment_mode !!}, function (obj) {--}}
                    {{--    obj.text = obj.mode;--}}

                    {{--    return obj;--}}
                    {{--});--}}

                    {{--$("#payment_mode_select").prepend('<option value="" selected></option>').select2({--}}
                    {{--    data:data1,--}}
                    {{--    placeholder: "Select Mode",--}}
                    {{--    width:'100%',--}}
                    {{--    containerCssClass: 'select-xs',--}}
                    {{--    dropdownCssClass: 'form-control-sm p-0'--}}
                    {{--});--}}
                    this.api().table().columns.adjust();
                }
            });
            $('body').on('click','#datatable .quantity',function(){
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

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                console.log(id);
                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.bulk_tagging').enable();

                }
                else {
                    table.button('.bulk_tagging').disable();
                }
            });

            $("#saletag").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sales Person",
                width:'100%',
                dropdownParent:$('#SalesTagModal')
            });
            $("#saletag1").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sales Person",
                width:'100%',
                dropdownParent:$('#SalesTagModal1')
            });
            $("#set_segment").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Segment",
                width:'100%',
                dropdownParent:$('#SetSegment')
            });
            $('#SalesTagModal').on('shown.bs.modal',function (e) {
                var $invoker = $(e.relatedTarget);
                var shipper_id = $invoker.data('target-id');
                $('#shipper_id').val(shipper_id);
            });
            $('#salesTagSubmit').on('click',function () {
                var shipper = $('#shipper_id').val();
                var tag = parseInt($('#saletag').val());
                if(tag){
                    $.ajax({
                        url: '{!! route('admin.accounts.tag.submit') !!}',
                        method: 'POST',
                        data: {
                            'admin_id': tag,
                            'shipper_id':shipper,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            if(data.status){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#saletag').val('').trigger('change');
                            $('#SalesTagModal').modal('hide');
                            table.draw(true);
                        });
                }else{
                    var error = "Sales Person Not Selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

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
        });

    </script>
@endsection