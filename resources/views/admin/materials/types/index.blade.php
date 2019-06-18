@extends('admin.layout.master')

@section('title', 'Packaging Material List')

@section('content')
    <h1 class="mb-1">
        Packaging Material List
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                {{--<div class="container justify-content-center pb-2 text-center">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-3"><h4>Small Flyers: <u id="sm_flyers_title">{{number_format($packaging->small_flyers)}}</u></h4></div>--}}
                        {{--<div class="col-3"><h4>Medium Flyers: <u id="md_flyers_title">{{number_format($packaging->medium_flyers)}}</u></h4></div>--}}
                        {{--<div class="col-3"><h4>Large Flyers: <u id="lg_flyers_title">{{number_format($packaging->large_flyers)}}</u></h4></div>--}}
                        {{--<div class="col-3"><h4>Boxes: <u id="box_title">{{number_format($packaging->boxes)}}</u></h4></div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Type</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">Updated At</th>
                        <th class="border-primary border-darken-1">Updated By</th>
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
                    <form id="add_stock_form" action="{{route('admin.packaging.add.submit')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-8 form-group">
                                    <input type="text" name="type" id="type" class="form-control type" placeholder="Type *" data-rule-required="true" data-msg-required="This field is required">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 form-group">
                                    <textarea name="description" id="description" class="form-control" placeholder="Description *"></textarea>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-5 form-group">
                                    <textarea name="description" id="description" class="form-control" placeholder="Description *"></textarea>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewStock" type="submit" class="btn btn-primary btn-block">Add</button>
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
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

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
                            head.push('Type');
                            head.push('Status');
                            head.push('Updated At');
                            head.push('Updated By');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.type);
                                row.push(values.status);
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
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add Material',
                        className: 'btn btn-primary add_material',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddMaterialModal').modal('show');

                        }
                    },{
                        extend: 'excel',
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
                ajax: '{{ route('admin.packaging.types.list') }}',
                rowId: 'id',
                order: [[5, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'type', name: 'packaging_material_types.type', class: 'align-middle type'},
                    {data: 'description', name: 'packaging_material_types.description', class: 'align-middle description'},
                    {data: 'status', name: 'packaging_material_types.status', class: 'align-middle status'},
                    {data: 'created_at', name: 'packaging_material_types.created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'ac.name', class: 'align-middle created_by'},
                    {data: 'updated_at', name: 'ct.name', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'au.name', class: 'align-middle updated_by'},
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
                        '<option value="0">Enabled</option>' +
                        '<option value="1">Disabled</option>' +
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
            //dispatch
            {{--$('body').on('click','.dispatch',function(){--}}
                {{--var request_id = parseInt($(this).parents('tr').attr('id'));--}}
                {{--swal({--}}
                    {{--title: 'Are You Sure?',--}}
                    {{--text: 'Select Yes to Dispatch Packaging Material!',--}}
                    {{--icon: 'warning',--}}
                    {{--buttons: {--}}
                        {{--cancel: {--}}
                            {{--text: 'No',--}}
                            {{--value: null,--}}
                            {{--visible: true,--}}
                            {{--closeModal: true,--}}
                        {{--},--}}
                        {{--confirm: {--}}
                            {{--text: 'Yes',--}}
                            {{--value: true,--}}
                            {{--visible: true,--}}
                            {{--closeModal: true--}}
                        {{--}--}}
                    {{--},--}}
                    {{--closeOnClickOutside: false,--}}
                    {{--closeOnEsc: false,--}}
                    {{--dangerMode: true--}}
                {{--}).then(function (confirm) {--}}
                    {{--if (confirm) {--}}
                        {{--$.ajax({--}}
                            {{--url: '{!! route('admin.packaging.requests.dispatch') !!}',--}}
                            {{--method: 'POST',--}}
                            {{--data: {--}}
                                {{--'id': request_id,--}}
                                {{--'_token': '{{ csrf_token() }}'--}}
                            {{--}--}}
                        {{--}).done(function (data) {--}}

                            {{--if(data.status === 1){--}}
                                {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
                                {{--setTimeout(function(){--}}
                                    {{--window.location.reload();--}}
                                {{--},2000);--}}
                            {{--}else{--}}
                                {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

                            {{--}--}}
                        {{--});--}}
                    {{--}--}}
                {{--});--}}

            {{--});--}}

        });

    </script>
@endsection