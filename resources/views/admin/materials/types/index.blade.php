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
                        <th class="border-primary border-darken-1">Name</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Packaging Type</th>
                        <th class="border-primary border-darken-1">Category</th>
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
                    <form id="add_material_form"  class="form" action="{{route('admin.packaging.types.add')}}" method="post" enctype="multipart/form-data">
                        
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-8 form-group">
                                    <input type="text" name="type" id="type" class="form-control type" placeholder="Type Name *" data-rule-required="true" data-msg-required="Type name is required">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 form-group">
                                    <textarea name="description" id="description" class="form-control" placeholder="Description *" data-rule-required="true" data-msg-required="Description is required"></textarea>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 card">
                                    <div class="card-header">
                                        <h4 class="card-title">Packaging Material Type</h4>

                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <fieldset>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-primary add_internal" value="internal" name="packaging_type" id="colorRadio1">
                                                    <label class="custom-control-label" for="colorRadio1">Internal</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-success add_external" value="external" name="packaging_type" id="colorRadio2">
                                                    <label class="custom-control-label" for="colorRadio2">External</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-danger add_both" value="both" name="packaging_type" id="colorRadio3" checked>
                                                    <label class="custom-control-label" for="colorRadio3">Both</label>
                                                </div> 
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-danger add_both" value="only_shipper" name="packaging_type" id="colorRadio7">
                                                    <label class="custom-control-label" for="colorRadio7">Selected Shipper</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-warning add_marco" value="marco" name="packaging_type" id="colorRadio4">
                                                    <label class="custom-control-label" for="colorRadio4">Marco</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="card-body" id="shippers_select">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-12 card">
                                    <div class="card-header">
                                        <h4 class="card-title">Category</h4>

                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <fieldset>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-primary" value="1" name="category" id="colorRadio10" checked>
                                                    <label class="custom-control-label" for="colorRadio10">Packaging Material</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-success" value="2" name="category" id="colorRadio11">
                                                    <label class="custom-control-label" for="colorRadio11">Stationary</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-12 form-group">
                                    <label for="packaging_picture">Default Picture</label>
                                    <input class="form-control form-control-sm" type="file" name="packaging_picture" id="packaging_picture" data-rule-required="true" data-msg-required="Picture is required" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                </div>
                                <div class="col-12 form-group">
                                    <label for="packaging_picture1">Picture 1</label>
                                    <input class="form-control form-control-sm" type="file" name="packaging_picture1" id="packaging_picture1" data-rule-required="true" data-msg-required="Picture 1 is required" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                </div>
                                
                                <div class="col-12 form-group">
                                    <label for="packaging_picture2">Picture 2</label>
                                    <input class="form-control form-control-sm" type="file" name="packaging_picture2" id="packaging_picture2" data-rule-required="true" data-msg-required="Picture 2 is required" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                </div>
                                
                                <div class="col-12 form-group">
                                    <label for="packaging_picture3">Picture 3</label>
                                    <input class="form-control form-control-sm" type="file" name="packaging_picture3" id="packaging_picture3" data-rule-required="true" data-msg-required="Picture 3 is required" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                </div>
                                <div class="col-12 form-group">
                                    <label for="packaging_picture4">Picture 4</label>
                                    <input type="file" name="packaging_picture4" class="form-control form-control-sm" id="packaging_picture4" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Picture 4 is required">
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
                                    <button type="submit" class="btn btn-primary btn-block">Add</button>
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
                    <form id="edit_material_form" action="{{route('admin.packaging.types.edit')}}" method="post" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-8 form-group" id="type_div">

                                </div>
                                <div class="col-8 form-group" id="type_existing">

                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 form-group" id="description_div">

                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 card">
                                    <div class="card-header">
                                        <h4 class="card-title">Packaging Material Type</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <fieldset>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-primary edit_internal" value="internal" name="packaging_type_edit" id="colorRadio14">
                                                    <label class="custom-control-label" for="colorRadio14">Internal</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-success edit_external" value="external" name="packaging_type_edit" id="colorRadio5">
                                                    <label class="custom-control-label" for="colorRadio5">External</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-danger edit_both" value="both" name="packaging_type_edit" id="colorRadio6">
                                                    <label class="custom-control-label" for="colorRadio6">Both</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-danger edit_only_shipper" value="only_shipper" name="packaging_type_edit" id="colorRadio8">
                                                    <label class="custom-control-label" for="colorRadio8">Selected Shipper</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-warning edit_marco" value="marco" name="packaging_type_edit" id="colorRadio9">
                                                    <label class="custom-control-label" for="colorRadio9">Marco</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="card-body" id="shippers_select_edit">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 card">
                                    <div class="card-header">
                                        <h4 class="card-title">Category</h4>

                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <fieldset>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-primary packaging_category" value="1" name="category_edit" id="colorRadio12">
                                                    <label class="custom-control-label" for="colorRadio12">Packaging Material</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-radio mr-1">
                                                    <input type="radio" class="custom-control-input bg-success stationary_category" value="2" name="category_edit" id="colorRadio13">
                                                    <label class="custom-control-label" for="colorRadio13">Stationary</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 form-group" id="picture_div">

                                </div>
                                <div class="col-12 form-group" id="picture_div1">

                                </div>
                                <div class="col-12 form-group" id="picture_div2">

                                </div>
                                <div class="col-12 form-group" id="picture_div3">

                                </div>
                                <div class="col-12 form-group" id="picture_div4">

                                </div>
                                
                            </div>
                            <div id="edit_size_charges_wrapper">

                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-success mb-1" title="Add more sizes" id="add_edit_row_btn"><i class="la la-plus"></i></button>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button id="AddNewStock" type="submit" class="btn btn-primary btn-block">Update</button>
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
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        var index_count = 0;
        $(document).ready(function () {


            $('input[name="packaging_type"]').change(function(e) { // Select the radio input group

                // This returns the value of the checked radio button
                // which triggered the event.
                if($(this).val() =="only_shipper"){
                    $.ajax({
                        url: '{{route('admin.packaging.types.all_shippers')}}',
                        success: function (result) {
                            $("#shippers_select").html(result);
                            $('#shippers_ids').select2({
                                width:'100%',
                                placeholder:"Select Shippers",
                                allowClear:true,
                                dropdownParent:$('#add_material_form')
                             });

                        }
                    });



                }else{
                    $("#shippers_select").html('');
                } 
            });
            


                        $('input[name="packaging_type_edit"]').change(function(e) { // Select the radio input group

                // This returns the value of the checked radio button
                // which triggered the event.
                if($(this).val() =="only_shipper"){
                    $.ajax({
                        url: '{{route('admin.packaging.types.all_shippers_edit')}}',
                        success: function (result) {

                            $("#shippers_select_edit").html(result);
                            $('#shippers_ids_edit').select2({
                                width:'100%',
                                placeholder:"Select Shippers",
                                allowClear:true,
                                dropdownParent:$('#edit_material_form')
                             });

                             var temp = document.getElementById('existing_type').value.split(',');
                             for (a in temp ) {
                                    temp[a] = parseInt(temp[a], 10); // Explicitly include base as per Álvaro's comment
                                }
                                if(temp.length>0){
                                $('#shippers_ids_edit').val(temp).trigger('change');

                                }


                             @if(count($arr_shipper_packing_types) > 0)
                                var ids = @json($arr_shipper_packing_types);
                            @endif
                        }
                    });
                }else{
                    $("#shippers_select_edit").html('');
                } 
            });
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
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.packaging.types.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Name');
                            head.push('Description');
                            head.push('Packaging Type');
                            head.push('Category');
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
                                row.push(values.packaging_type);
                                row.push(values.category);
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
                order: [[5, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'type', name: 'packaging_material_types.type', class: 'align-middle type'},
                    {data: 'description', name: 'packaging_material_types.description', class: 'align-middle description'},
                    {data: 'packaging_type', name: 'packaging_material_types.packaging_type', class: 'align-middle packaging_type'},
                    {data: 'category', name: 'packaging_material_types.category', class: 'align-middle category'},
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
                    var category_select = '<select name="status_select" id="category_select" class="select2 form-control">' +
                        '<option value="1">Packaging Material</option>' +
                        '<option value="2">Stationary</option>' +
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
                        else if($(header).is('.category')){
                            $(category_select).appendTo($(search))
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
                    $("#category_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Category",
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
                $.ajax({
                    url: '{!! route('admin.packaging.types.details') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                var html_type_existing = '<input type="hidden" name="existing_type" id="existing_type" value="' + data.arr_shipper_packing_types + '">';

                        var html_type = '<input type="hidden" name="id" value="' + data.type.id + '"><input type="text" name="edit_type" id="edit_type" class="form-control type" value="' + data.type.type + '" placeholder="Type *" data-rule-required="true" data-msg-required="Type name is required">';
                        var html_description = '<textarea name="edit_description" id="edit_description" class="form-control" placeholder="Description *" data-rule-required="true" data-msg-required="Description is required">' + data.type.description + '</textarea>';
                        var html_picture = '<label for="edit_packaging_picture">Default Picture</label><input class="form-control form-control-sm" type="file" name="edit_packaging_picture" id="edit_packaging_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">';
                        var html_picture1 = '<label for="edit_packaging_picture1">Picture 1</label><input class="form-control form-control-sm" type="file" name="edit_packaging_picture1" id="edit_packaging_picture1" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">';
                        var html_picture2 = '<label for="edit_packaging_picture2">Picture 2</label><input class="form-control form-control-sm" type="file" name="edit_packaging_picture2" id="edit_packaging_picture2" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">';
                        var html_picture3 = '<label for="edit_packaging_picture3">Picture 3</label><input class="form-control form-control-sm" type="file" name="edit_packaging_picture3" id="edit_packaging_picture3" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">';
                        var html_picture4 = '<label for="edit_packaging_picture4">Picture 4</label><input class="form-control form-control-sm" type="file" name="edit_packaging_picture4" id="edit_packaging_picture4" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">';
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
                        $('#edit_material_form input[name="packaging_type_edit"]').attr('checked', false);
                        $('#edit_material_form input[name="category_edit"]').attr('checked', false);

                        if(data.type.packaging_type === 1){
                            $('input.edit_internal').attr('checked', true);
                            $("#shippers_select_edit").html('');

                        }else if(data.type.packaging_type === 2){
                            $('input.edit_external').attr('checked', true);
                            $("#shippers_select_edit").html('');

                        }else if(data.type.packaging_type === 5){
                            $('input.edit_marco').attr('checked', true);
                            $("#shippers_select_edit").html('');

                        }else if(data.type.packaging_type === 4) {
                            $('input.edit_only_shipper').attr('checked', true);

                            $.ajax({
                        url: '{{route('admin.packaging.types.all_shippers_edit')}}',
                        success: function (result) {

                            $("#shippers_select_edit").html(result);
                            $('#shippers_ids_edit').select2({
                                width:'100%',
                                placeholder:"Select Shippers",
                                allowClear:true,
                                dropdownParent:$('#edit_material_form')
                             });

                             var temp = document.getElementById('existing_type').value.split(',');
                             for (a in temp ) {
                                    temp[a] = parseInt(temp[a], 10); // Explicitly include base as per Álvaro's comment
                                }
                                if(temp.length>0){
                                $('#shippers_ids_edit').val(temp).trigger('change');

                                }


                             @if(count($arr_shipper_packing_types) > 0)
                                var ids = @json($arr_shipper_packing_types);
                            @endif
                        }
                    });
                        }
                        else{
                            $('input.edit_both').attr('checked', true);
                            $("#shippers_select_edit").html('');

                        }
                        if(data.type.category === 1){
                            $('input.packaging_category').attr('checked', true);
                        }else if(data.type.category === 2){
                            $('input.stationary_category').attr('checked', true);
                        }
                        
                        $('#EditMaterialModal #type_div').html(html_type);
                        $('#EditMaterialModal #type_existing').html(html_type_existing);

                        $('#EditMaterialModal #description_div').html(html_description);
                        $('#EditMaterialModal #picture_div').html(html_picture);
                        $('#EditMaterialModal #picture_div1').html(html_picture1);
                        $('#EditMaterialModal #picture_div2').html(html_picture2);
                        $('#EditMaterialModal #picture_div3').html(html_picture3);
                        $('#EditMaterialModal #picture_div4').html(html_picture4);
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