@extends('admin.layout.master')

@section('title', 'Return Parcels after Attempt')

@section('content')
    <h1 class="mb-1">
        Return Parcels after Attempt
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Attempt Days</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Updated At</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddShipperModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddMaterialModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Attempt Days</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="shipper_add_form" class="form-horizontal" action="{{ route('admin.settings.restrict_parcels_attempt.add') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-10 form-group">
                                <select class="form-control" name="shipper_id" id="shipper_id" data-rule-required="true" data-msg-required="Shipper is required">
                                    @foreach($shippers as $shipper)
                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-10 form-group">
                                <input type="text" name="attempt_days" id="add_attempt_days" class="form-control attempt_days" placeholder="Attempt Days*" data-rule-required="true" data-msg-required="Attempt Days is required" value="" data-rule-min="1" data-msg-min="Attempt Days can not be less than 1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="AddShipperBtn" type="submit" class="btn btn-info">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Edit Modal--}}
    <div class="modal fade text-left" id="EditShipperModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditMaterialModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Attempt Days</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="shipper_edit_form" action="{{ route('admin.settings.restrict_parcels_attempt.edit') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <input type="hidden" id="restrict_id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" name="attempt_days" class="form-control attempt_days" id="edit_attempt_days" placeholder="Attempt Days*" data-rule-required="true" data-msg-required="Attempt Days is required" value="" data-rule-min="1" data-msg-min="Attempt Days can not be less than 1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="EditShipperBtn" type="submit" class="btn btn-info">Edit</button>
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
            $('.attempt_days').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $("#shipper_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Shipper",
                width:'300px',
                dropdownParent:$('#AddShipperModal')
            });
            $('#shipper_add_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Setting is being created!',
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
                        url: '{{ route('admin.settings.restrict_parcels_attempt.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Shipper');
                            head.push('Attempt Days');
                            head.push('Status');
                            head.push('Created At');
                            head.push('Updated At');
                            head.push('Updated By');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.shipper);
                                row.push(values.attempt_days);
                                row.push(values.status);
                                row.push(values.created_at);
                                row.push(values.updated_at);
                                row.push(values.updated_by);

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
                    {
                        text: '<i class="la la-plus"></i> Add Shipper',
                        className: 'btn btn-primary add_warehouse',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddShipperModal').modal('show');
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Return Parcels After Attempt',
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
                ajax: '{{ route('admin.settings.restrict_parcels_attempt.list') }}',
                rowId: 'id',
                order: [[5, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle text-center serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'attempt_days', name: 'restrict_parcels_attempts.attempt_days', class: 'align-middle text-center attempt_days'},
                    {data: 'status', name: 'restrict_parcels_attempts.status', class: 'align-middle text-center status'},
                    {data: 'created_at', name: 'restrict_parcels_attempts.created_at', class: 'align-middle text-center created_at'},
                    {data: 'updated_at', name: 'restrict_parcels_attempts.updated_at', class: 'align-middle text-center updated_at'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle text-center updated_by'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}

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
                    url: '{!! route('admin.settings.restrict_parcels_attempt.enable_disable') !!}',
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
                    url: '{!! route('admin.settings.restrict_parcels_attempt.enable_disable') !!}',
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
            });

            $('body').on('click','button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                var attempt_days = parseInt(table.row($(this).parents('tr')).data().attempt_days);
                console.log(attempt_days);
                $('#restrict_id').val(id);
                $('#edit_attempt_days').val(attempt_days);
                $('#EditShipperModal').modal('show');
            });

            $('#shipper_edit_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Setting is being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('#AddShipperModal').on('hide.bs.modal', function (e) {
                $('#shipper_id').val('').change();
                $('#add_attempt_days').val('');
            });

            $('#AddShipperModal').on('hide.bs.modal', function (e) {
                $('#edit_attempt_days').val('');
            });


        });

    </script>
@endsection