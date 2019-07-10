@extends('admin.layout.master')

@section('title', 'Warehouse List')

@section('content')
    <h1 class="mb-1">
        Warehouse List
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Warehouse Hub</th>
                        <th class="border-primary border-darken-1">Associated Hubs</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">Updated At</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddWarehouseModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddMaterialModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Warehouse</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="warehouse_add_form" action="{{ route('admin.packaging.warehouse.add') }}" method="POST" novalidate="novalidate">
                    @csrf
                <div class="modal-body p-3">
                    <div class="pl-1 form-group">
                        <select name="hub_id" id="ware_house_hub" data-rule-required="true" data-msg-required="Hub is required">
                            @foreach($hubs as $hub)
                                <option value="{{$hub->id}}">{{$hub->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fulfillment_hubs form-group">
                        <h3 class="pl-1">Fulfilment Cities</h3>
                        @foreach($fulfilment_hubs as $hub)
                        <fieldset class="d-inline-block m-1">
                            <input type="checkbox" id="city_{{$hub->id}}" class="city" name="city_ids[]" value="{{$hub->id}}">
                            <label for="city_{{$hub->id}}">{{$hub->name}}</label>
                        </fieldset>
                            @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="AddWarehouseBtn" type="submit" class="btn btn-info">Add Warehouse</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="AddMasterWarehouseModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddMasterWarehouseModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Assign Master Warehouse</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="warehouse_master_add_form" action="{{ route('admin.packaging.warehouse.master_add') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <div class="modal-body text-center">
                        <div class="pl-1 form-group">
                            <select name="hub" id="master_hub" data-rule-required="true" data-msg-required="Hub is required">
                                @foreach($all_warehouse_cities as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button id="AddMasterWarehouseBtn" type="submit" class="btn btn-info">Assign</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    {{--Edit Modal--}}
    <div class="modal fade text-left" id="EditWarehouseModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditMaterialModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Warehouse</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="warehouse_edit_form" action="{{ route('admin.packaging.warehouse.edit') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <input type="hidden" id="edit_warehouse_id" name="warehouse_id">
                    <div class="modal-body">
                        <div class="pl-1 form-group">
                            <select name="hub_id" id="edit_ware_house_hub" data-rule-required="true" data-msg-required="Hub is required">
                                @foreach($all_active_hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fulfillment_hubs form-group">
                            <h3 class="pl-1">Fulfilment Cities</h3>
                            <div id="fulfilment_city_div">

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="AddWarehouseBtn" type="submit" class="btn btn-info">Edit Warehouse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--View Modal--}}
    <div class="modal fade text-left" id="ViewWarehouseHubsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewWarehouseHubsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">View Warehouse Hubs</h4>
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

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
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
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        var index_count = 0;
        $(document).ready(function () {
            $("#ware_house_hub").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Hub",
                width:'300px',
                dropdownParent:$('#AddWarehouseModal')
            });

            $("#master_hub").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Hub",
                width:'300px',
                dropdownParent:$('#AddMasterWarehouseModal')
            });
            var master_hub = parseInt('{{$master_warehouse_hub}}');
            $("#master_hub").val(master_hub).trigger('change');
            $('#warehouse_add_form .city').each(function() {
                var checkbox = $(this);
                var label = checkbox.next();
                var text = label.text();

                label.remove();

                checkbox.iCheck({
                    checkboxClass: 'icheckbox_line pt-1 pb-1',
                    checkedClass: 'checked bg-success',
                    uncheckedClass: 'bg-danger',
                    insert: '<div class="icheck_line-icon"></div>' + text
                });
            });
            $('#warehouse_add_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                rules : {
                    "city_ids[]": { required: true, minlength: 1 }
                },
                messages: {
                    'city_ids[]': {
                        required: "Select at-least One fulfillment City please",
                    },
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Warehouse is being created!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#warehouse_master_add_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Master Warehouse is being assigned!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });




            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.warehouse.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Warehouse Hub');
                            head.push('Associated Hubs');
                            head.push('Created At');
                            head.push('Created By');
                            head.push('Updated At');
                            head.push('Updated By');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.hub);
                                row.push(values.associated_hubs);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                row.push(values.updated_at);
                                row.push(values.updated_by);
                                row.push(values.status);

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
                scrollX: true, scrollY: '350px',
                buttons: [
                        @if (session('role_id') == 1 || in_array(220, session('permissions')))
                    {
                        text: '<i class="la la-plus"></i> Assign Master Warehouse',
                        className: 'btn btn-primary assign_warehouse',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddMasterWarehouseModal').modal('show');

                        }
                    },
                    @endif
                        @if (session('role_id') == 1 || in_array(219, session('permissions')))
                    {
                        text: '<i class="la la-plus"></i> Add Warehouse',
                        className: 'btn btn-primary add_warehouse',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddWarehouseModal').modal('show');

                        }
                    },
                        @endif{
                        extend: 'excel',
                        title: 'Warehouses',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary',
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
                ajax: '{{ route('admin.packaging.warehouse.list') }}',
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'associated_hubs_button', name: 'associated_hubs', class: 'align-middle text-center associated_hubs',orderable: false, searchable: false},
                    {data: 'created_at', name: 'warehouses.created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'ac.name', class: 'align-middle created_by'},
                    {data: 'updated_at', name: 'warehouses.updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'au.name', class: 'align-middle updated_by'},
                    {data: 'status', name: 'warehouses.status', class: 'align-middle status'},
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Enabled</option>' +
                        '<option value="0">Disabled</option>' +
                        '</select>';
                    // var payment_mode_select = '<select name="payment_mode_select" id="payment_mode_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.associated_hubs') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click','button.disable',function () {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.packaging.warehouse.enable_disable') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 0,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        setTimeout(function() {
                            location.reload()
                        }, 1000);
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });
            $('body').on('click','button.enable',function () {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.packaging.warehouse.enable_disable') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 1,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        setTimeout(function() {
                            location.reload()
                        }, 1000);
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });

            $('body').on('click','button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    $.ajax({
                        url: '{!! route('admin.packaging.warehouse.edit_data') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status === 0){
                            // var fulfilment_hubs =
                            $('#EditWarehouseModal').modal('show');
                            $('#edit_warehouse_id').val(id);

                            $("#edit_ware_house_hub").prepend('<option value="" selected></option>').select2({
                                placeholder: "Select Hub",
                                width:'300px',
                                allowClear: true,
                                dropdownParent:$('#EditWarehouseModal')
                            });
                            $('#edit_ware_house_hub').val(data.warehouse.hub_id).trigger('change');
                            var fulfilment_cities = @json($all_active_hubs);
                            var boxes = '';
                            $.each(fulfilment_cities, function (index, value) {
                            boxes += '<fieldset class="d-inline-block m-1">';
                            boxes += '<input type="checkbox" id="city_'+ value.id +'" class="city" name="city_ids[]" value="'+value.id+'">';
                            boxes += '<label for="city_'+value.id+'">'+ value.name +'</label></fieldset>';

                            });
                            $('#fulfilment_city_div').html('');
                            $('#fulfilment_city_div').html(boxes);
                            $('#warehouse_edit_form .city').each(function() {
                                var id = parseInt($(this).val());
                                var checkbox = $(this);

                                if(data.associated_hubs.indexOf(id) > -1){
                                    checkbox.attr('checked', true);
                                }
                                var label = checkbox.next();
                                var text = label.text();

                                label.remove();

                                checkbox.iCheck({
                                    checkboxClass: 'icheckbox_line pt-1 pb-1',
                                    checkedClass: 'checked bg-success',
                                    uncheckedClass: 'bg-danger',
                                    insert: '<div class="icheck_line-icon"></div>' + text
                                });
                            });
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                }
            });

            $('body').on('click','button.associated_hubs',function () {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.packaging.warehouse.warehouse_hubs') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        var html = '';
                        html += '<table class="table table-sm datatable text-center">';
                        html += '<thead><tr><th>S No.</th><th><strong>Cities</strong></th></tr></thead>';
                        html += '<tbody>';
                        $.each(data.associated_hubs, function(index, value) {
                            var ind = index+1;
                            html += '<tr class=""><td>' + ind + '</td>';
                            html += '<td>' + value.name + '</td></tr>';
                        });
                        html += '</tbody></table>';

                        $('#ViewWarehouseHubsModal .modal-body').html(html);
                        $('#ViewWarehouseHubsModal').modal('show');
                    }
                });
            });

            $('#warehouse_edit_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                rules : {
                    "city_ids[]": { required: true, minlength: 1 }
                },
                messages: {
                    'city_ids[]': {
                        required: "Select at-least One fulfillment City please",
                    },
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Warehouse is being created!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });



        });

    </script>
@endsection