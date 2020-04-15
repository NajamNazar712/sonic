@extends('admin.layout.master')

@section('title', 'Shippers Default Weight')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Shippers Default Weight
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
                                    <th class="border-primary border-darken-1">Weight</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
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
    {{--View Modal--}}
    <div class="modal fade text-left" id="AddUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddUserModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Weight</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_user_form" class="form-horizontal text-center" novalidate="novalidate">
                        {{ csrf_field() }}

                        <div class="row justify-content-center">
                            <div class="col-5">
                                <fieldset class="form-group">
                                    <select name="shipper" id="select_shipper" class="form-control select2">
                                        @foreach($shippers as $shipper)
                                            <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-3 form-group">
                                <input type="text" class="form-control weight" name="weight" id="weight" placeholder="Weight*" data-rule-required="true" data-msg-required="Weight is required">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary width-100" id="add_shipper_button">Add</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditUserModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Edit Weight</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_user_form" class="form-horizontal text-center" novalidate="novalidate">
                        {{ csrf_field() }}

                        <div class="row justify-content-center">
                            <input type="hidden" name="default_weight" id="edit_default_weight">
                            <div class="col form-group">
                                <input type="text" class="form-control weight" name="edit_weight" id="edit_weight" placeholder="Weight*" data-rule-required="true" data-msg-required="Weight is required">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary width-100" id="edit_shipper_button">Edit</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#select_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Shipper',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#AddUserModal')
            });

            $('#add_user_form input.weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'min': 0.1,
                'digits': 2
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.default_weight.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Shipper');
                            head.push('Weight');
                            head.push('Updated At');
                            head.push('Updated By');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.shipper);
                                row.push(values.weight);
                                row.push(values.updated_at);
                                row.push(values.updated_by);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add Shipper',
                        className: 'btn btn-primary add_shipper',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            // $('#AddUserModal .modal-body').html(html);
                            $('#AddUserModal').modal('show');
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Default Weight Shipper',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.settings.default_weight.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipper', name: 'u.name', class: 'align-middle holiday'},
                    {data: 'weight', name: 'default_weights.default_weight', class: 'align-middle reason'},
                    {data: 'updated_at', name: 'default_weights.updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle created_by'},
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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
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
                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click','tr .action button.edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#edit_default_weight').val(id);
                $('#EditUserModal').modal('show');
            });

            $('#add_shipper_button').on('click', function () {
                var shipper = $('#select_shipper').val();
                var weight = $('#weight').val();
                var flag = true;
                if(!shipper){
                    flag = false;
                    var error = "Please select Shipper!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(!weight){
                    flag = false;
                    var error = "Please enter Weight!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(flag){
                    $('#add_shipper_button').attr('disabled',true);
                    $.ajax({
                        url: '{!! route('admin.settings.default_weight.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipper': shipper,
                            'weight' : weight
                        }
                    })
                        .done(function(data) {
                            if (data.status == 1) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                $('#AddUserModal').modal('hide');
                                table.draw();
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#add_shipper_button').attr('disabled',false);
                        });
                }
            });
            $('#edit_shipper_button').on('click', function () {
                var weight_id = $('#edit_default_weight').val();
                var weight = $('#edit_weight').val();
                var flag = true;
                if(!weight){
                    flag = false;
                    var error = "Please enter Weight!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(flag){
                    $('#add_shipper_button').attr('disabled',true);
                    $.ajax({
                        url: '{!! route('admin.settings.default_weight.edit') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'weight_id': weight_id,
                            'weight' : weight
                        }
                    })
                        .done(function(data) {
                            if (data.status == 1) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                $('#EditUserModal').modal('hide');
                                table.draw();
                            }
                            $('#add_shipper_button').attr('disabled',false);
                        });
                }
            });

            $('#AddUserModal').on('hide.bs.modal', function (e) {
                $('#select_shipper').val('').trigger('change');
                $('#weight').val('');
            });
            $('#EditUserModal').on('hide.bs.modal', function (e) {
                $('#edit_weight').val('');
            });

        });

    </script>
@endsection