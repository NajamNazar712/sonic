@extends('admin.layout.master')

@section('title', 'Zones')

@section('content')
    <h1 class="mb-1">
        Zones
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Name</th>
                        <th class="border-primary border-darken-1">Gst</th>
                        <th class="border-primary border-darken-1">Bussiness Category Name</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddZoneManagementModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddZoneManagementModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white"  id="zoneModalTitle">Add Zone</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="route_management_add_form" class="form-horizontal" action="{{ route('admin.retail.international.zone.store') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <input type="hidden" name="id" value="" id="store_id">
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-6 form-group">
                                <label for="route_code">Zone Name</label>
                                <input type="text" name="name" id="zone_name" class="form-control zone_name" placeholder="Zone Name*" data-rule-required="true" data-msg-required=" is required">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-6 form-group">
                                <label for="zone_gst">Zone GST (%)</label>
                                <input type="number" step="0.01" name="gst" id="zone_gst"  class="form-control zone_gst"  placeholder="Enter GST percentage" 
                                       data-rule-required="true" 
                                       data-msg-required="Zone GST is required">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <!-- Status Dropdown -->
                            <div class="col-6 form-group">
                                <label for="status">Status</label>
                                <select name="status" id="is_active" class="form-control status" data-rule-required="true" data-msg-required="Status is required">
                                    <option value="">Select Status</option>
                                    <option value="1">Enable</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <!-- Status Dropdown -->
                            <div class="col-6 form-group">
                                <label for="business_category_id">Bussiness Category</label>
                                <select name="business_category_id" id="business_category_id" class="form-control business_category" data-rule-required="true" data-msg-required="Bussiness Category is required">
                                    @foreach ($businessCategory as $cat)
                                        <option value="{{ $cat->id}}">{{ $cat->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="zoneSaveBtn" type="submit" class="btn btn-info">Add</button>
                    </div>
                </form>
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
             
            $(".business_category").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Status",
                width:'100%',
                dropdownParent:$('#AddZoneManagementModal')
            });
            $(".status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Business Category Type",
                width:'100%',
                dropdownParent:$('#AddZoneManagementModal')
            });
            
            $('#route_management_add_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Runner is being created!',
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
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.route_management.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Name');
                            head.push('Gst');
                            head.push('Bussiness Category Name');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.gst);
                                row.push(values.business_category_name);
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
                scrollX: true, scrollY: '500px',
                buttons: [
                    @if (session('role_id') == 1 && session('department_id')  == 1)
                        {
                            text: '<i class="la la-plus"></i> Add Zone',
                            className: 'btn btn-primary add_route_management',
                            enabled: true,
                            action: function (e, dt, node, config) {
                                $('#is_active, #business_category_id, #store_id, #zone_name, #zone_gst').val('').trigger('change');
                                $('').val('');
                                $('#zone_gst').val('');
                                $('#zone_name').val('');
                                $('#AddZoneManagementModal').modal('show');
                            }
                        },
                    @endif
                    
                    {
                        extend: 'excel',
                        title: 'Zone Management',
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
                autoWidth: false,
                ajax: '{{ route('admin.retail.international.zone.list') }}',
                rowId: 'id',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle text-center serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'zones.name', class: 'align-middle text-center name'},
                    {data: 'gst', name: 'zones.gst', class: 'align-middle text-center gst'},
                    {data: 'business_category_name', name: 'bc.name', class: 'align-middle text-center business_category_name'},
                       {data: 'status', name: 'zones.status', class: 'align-middle text-center status'},
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
                    var status_select = '<select name="status_select" id="status_select" class="form-control">' +
                        '<option value="1">Enabled</option>' +
                        '<option value="0">Disabled</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number')|| $(header).is('.action')|| $(header).is('.starting_point')|| $(header).is('.end_point')|| $(header).is('.junctions')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
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
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var id = table.row( $(this).parents('tr') ).data().id;

                if ($(this).hasClass('status')) {
                    $.ajax({
                        url: '{!! route('admin.settings.route_management.enable_disable') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function(data) {
                        if (data.status) {
                            table.draw(true);
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                        }
                    });
                }
            });


            $('body').on('click','button.edit_zone',function () {

                var id = $(this).parents('tr').attr('id');
           
               $.get("/admin/retail/international/zone/" + id, function(response) {
                if (response.status === 1) {
                    let zone = response.data;

                    // Fill form fields
                    $('#store_id').val(zone.id);
                    $('#zone_name').val(zone.name);
                    $('#zone_gst').val(zone.gst);
                    $('#is_active').val(String(zone.status)).trigger('change');
                    $('#business_category_id').val(zone.business_category_id).trigger('change');

                    // $('#business_category_id').val(zone.business_category_id);

                    // Change modal title and button text
                    $('#zoneModalTitle').text('Edit Zone');
                    $('#zoneSaveBtn').text('Update');



                    // Show modal
                    $('#AddZoneManagementModal').modal('show');
                } else {
                    alert('Zone not found!');
                }
               })

            });
             /*$('body').on('click','button.disable',function () {*/
            /*$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.settings.runner.enable_disable') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 0,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        table.draw();
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });
            $('body').on('click','button.enable',function () {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.settings.runner.enable_disable') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 1,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        table.draw();
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });*/
        });

    </script>
@endsection