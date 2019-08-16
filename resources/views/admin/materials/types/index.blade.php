@extends('admin.layout.master')

@section('title', 'Packaging Material Types')

@section('content')
    <h1 class="mb-1">
        Packaging Material Types
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Type</th>
                        <th class="border-primary border-darken-1">Description</th>
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

    <div class="modal fade text-left" id="AddMaterialModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddMaterialModal"
         aria-hidden="true">
        <div class="modal-dialog modal-m" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_material_form" action="{{route('admin.packaging.types.add')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-8 form-group">
                                    <input type="text" name="type" id="type" class="form-control type" placeholder="Type *" data-rule-required="true" data-msg-required="Type name is required">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 form-group">
                                    <textarea name="description" id="description" class="form-control" placeholder="Description *" data-rule-required="true" data-msg-required="Description is required"></textarea>
                                </div>
                            </div>
                            <div id="size_charges_wrapper">
                                <div class="row">
                                    <div class="col-5 form-group">
                                        <input name="size[0]" id="size[0]" class="form-control" placeholder="Size *" data-rule-required="true" data-msg-required="Size name is required">
                                    </div>
                                    <div class="col-5 form-group">
                                        <input name="standard_charges[0]" id="standard_charges[0]" class="form-control decimal" placeholder="Standard Charges *" data-rule-required="true" data-msg-required="Standard charges is required">
                                    </div>
                                    {{--<div class="col-1">--}}
                                        {{--<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 row_close"><i class="ft-x"></i></span>--}}
                                    {{--</div>--}}
                                </div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-success mb-1" title="Add more sizes" id="add_row_btn"><i class="la la-plus"></i></button>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button id="AddNewStock" type="submit" class="btn btn-primary btn-block">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditMaterialModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditMaterialModal"
         aria-hidden="true">
        <div class="modal-dialog modal-m" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="edit_material_form" action="{{route('admin.packaging.types.edit')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-8 form-group" id="type_div">

                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 form-group" id="description_div">

                                </div>
                            </div>
                            <div id="edit_size_charges_wrapper">

                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-success mb-1" title="Add more sizes" id="add_edit_row_btn"><i class="la la-plus"></i></button>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button id="AddNewStock" type="submit" class="btn btn-primary btn-block">Edit</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        var index_count = 0;
        $(document).ready(function () {
            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 3,
                'min': 0.00,
                'max': 10000,
            });
            function masks() {
                $('.decimal').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 3,
                    'min': 0.00,
                    'max': 10000
                });
            }
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.types.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Type');
                            head.push('Description');
                            head.push('Created At');
                            head.push('Created By');
                            head.push('Updated At');
                            head.push('Updated By');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.type);
                                row.push(values.description);
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
                scrollX: true, scrollY: '500px',
                buttons: [
                    @if (session('role_id') == 1 || in_array(215, session('permissions')))
                        {
                            text: '<i class="la la-plus"></i> Add Material',
                            className: 'btn btn-primary add_material',
                            enabled: true,
                            action: function (e, dt, node, config) {
                                $('#AddMaterialModal').modal('show');

                            }
                        },
                    @endif{
                        extend: 'excel',
                        title: 'Packaging Material Types',
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
                ajax: '{{ route('admin.packaging.types.list') }}',
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'type', name: 'packaging_material_types.type', class: 'align-middle type'},
                    {data: 'description', name: 'packaging_material_types.description', class: 'align-middle description'},
                    {data: 'created_at', name: 'packaging_material_types.created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'ac.name', class: 'align-middle created_by'},
                    {data: 'updated_at', name: 'packaging_material_types.updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'au.name', class: 'align-middle updated_by'},
                    {data: 'status', name: 'packaging_material_types.status', class: 'align-middle status'},
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


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
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
            $('body').on('click', '#add_material_form #row_close',function () {
                $(this).parent().parent().remove();
            });

            var row_count = 1;
            $('#add_material_form #add_row_btn').on('click',function () {
                let htmdiv = '<div class="row size_row"><div class="col-5 form-group">' +
                    '<input name="size[' + row_count + ']" id="size[' + row_count + ']" class="form-control" placeholder="Size *" data-rule-required="true" data-msg-required="Size name is required">' +
                    '</div>' +
                    '<div class="col-5 form-group">' +
                    '<input name="standard_charges[' + row_count + ']" id="standard_charges[' + row_count + ']" class="form-control decimal" placeholder="Standard Charges *" data-rule-required="true" data-msg-required="Standard charges is required">' +
                    '</div>' +
                    '<div class="col-1">' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 row_close" id="row_close"><i class="ft-x"></i></span>' +
                    '</div></div>';
                $('#size_charges_wrapper').append(htmdiv);
                masks();
                row_count++;
                });

            $( "#add_material_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'New material type is being added!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                }
            });


            $('body').on('click', '#edit_material_form #row_close',function () {
                $(this).parent().parent().remove();
            });

            $('body').on('click','button.edit',function () {
                var id = $(this).parents('tr').attr('id');
                console.log(id);
                $.ajax({
                    url: '{!! route('admin.packaging.types.details') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        var html_type = '<input type="hidden" name="id" value="' + data.type.id + '"><input type="text" name="edit_type" id="edit_type" class="form-control type" value="' + data.type.type + '" placeholder="Type *" data-rule-required="true" data-msg-required="Type name is required">';
                        var html_description = '<textarea name="edit_description" id="edit_description" class="form-control" placeholder="Description *" data-rule-required="true" data-msg-required="Description is required">' + data.type.description + '</textarea>';
                        var html_sizes = '';
                        data.sizes.forEach(function(size, index) {
                            html_sizes += '<div class="row size_row"><input type="hidden" name="size_id[' + index +']" value="' + size.id + '"><div class="col-5 form-group">' +
                                '<input name="edit_size[' + index + ']" id="edit_size[' + index + ']" class="form-control" value="' + size.size + '" placeholder="Size *" data-rule-required="true" data-msg-required="Size name is required">' +
                                '</div>' +
                                '<div class="col-5 form-group">' +
                                '<input name="edit_standard_charges[' + index + ']" id="edit_standard_charges[' + index + ']" class="form-control decimal" value="' + size.standard_charges + '" placeholder="Standard Charges *" data-rule-required="true" data-msg-required="Standard charges is required">' +
                                '</div></div>';
                            index_count++;
                        });
                        $('#EditMaterialModal #type_div').html(html_type);
                        $('#EditMaterialModal #description_div').html(html_description);
                        $('#EditMaterialModal #edit_size_charges_wrapper').html(html_sizes);
                        masks();

                        $('#EditMaterialModal').modal('show');
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });

            $("#EditMaterialModal").on("hidden.bs.modal", function(){
                $("#EditMaterialModal  #type_div").html("");
                $("#EditMaterialModal  #description_div").html("");
                $("#EditMaterialModal  #edit_size_charges_wrapper").html("");
            });

            $('#edit_material_form #add_edit_row_btn').on('click',function () {
                let htmdiv = '<div class="row size_row"><div class="col-5 form-group">' +
                    '<input name="edit_size[' + index_count + ']" id="edit_size[' + index_count + ']" class="form-control" placeholder="Size *" data-rule-required="true" data-msg-required="Size name is required">' +
                    '</div>' +
                    '<div class="col-5 form-group">' +
                    '<input name="edit_standard_charges[' + index_count + ']" id="edit_standard_charges[' + index_count + ']" class="form-control decimal" placeholder="Standard Charges *" data-rule-required="true" data-msg-required="Standard charges is required">' +
                    '</div>' +
                    '<div class="col-1">' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 row_close" id="row_close"><i class="ft-x"></i></span>' +
                    '</div></div>';
                $('#EditMaterialModal #edit_size_charges_wrapper').append(htmdiv);
                index_count++;
                masks();

                row_count++;
            });
            $( "#edit_material_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Material type is being edited!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('body').on('click','button.disable',function () {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.packaging.types.enable_disable') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 0,
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
            });
            $('body').on('click','button.enable',function () {
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.packaging.types.enable_disable') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'status': 1,
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
            });

        });

    </script>
@endsection