@extends('admin.layout.master')

@section('title', 'Admin Products')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Admin Products
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Employee Id</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th class="border-primary border-darken-1">Action</th>

                                    {{--th class="border-primary border-darken-1">Area</th>--}}
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
    <div class="modal fade text-left" id="AdminProductModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AdminProductModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Admin Product</h4>
                </div>
                <form method="post" id="add_admin_product" action="{{route('admin.settings.admin_product.store')}}">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <select name="admin_id" id="admin_id" class="form-control select2" data-rule-required="true" data-msg-required="Admin is required">
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" > {{$admin->trax_id}}-{{ $admin->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="product_id" id="product_id" class="form-control select2" data-rule-required="true" data-msg-required="Product is required">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" > {{ $product->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="admin_product_submit">Add</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


{{-- Edit Admin Product Modal --}}
    <div class="modal fade text-left" id="EditAdminProductModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AdminProductModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Update Admin Product</h4>
                </div>
                <form method="post" id="edit_admin_product" action="{{route('admin.settings.admin_product.update')}}">
                    @csrf

                    <div class="modal-body">
                         <input type="hidden" name="admin_product_id" id="admin_product_id">
                        <div class="form-group">
                            <select name="edit_admin_id" id="edit_admin_id" class="form-control select2" data-rule-required="true" data-msg-required="Admin is required">
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" > {{$admin->trax_id}}-{{ $admin->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="edit_product_id" id="edit_product_id" class="form-control select2" data-rule-required="true" data-msg-required="Product is required">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" > {{ $product->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        {{--                        <div class="form-group">--}}
                        {{--                            <select name="city_area_id" id="edit_city_area_id" class="form-control select2">--}}
                        {{--                            </select>--}}
                        {{--                        </div>--}}
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="edit_admin_product_submit">Update</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>
                </form>

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


            $('#admin_id').prepend('<option value="" selected="selected">Select User</option>').select2({
                placeholder: 'Select User*',
                width: '100%',
                dropdownParent: $('#add_admin_product')
            });
            $('#product_id').prepend('<option value="" selected="selected">Select Product</option>').select2({
                placeholder: 'Select Product*',
                width: '100%',
                dropdownParent: $('#add_admin_product')


            });

            $('#edit_admin_id').prepend('<option value="" selected="selected">Select User</option>').select2({
                placeholder: 'Select User*',
                width: '100%',
                dropdownParent:$('#edit_admin_product')
            });
            $('#edit_product_id').prepend('<option value="" selected="selected">Select Product</option>').select2({
                placeholder: 'Select Product*',
                width: '100%',
                dropdownParent:$('#edit_admin_product')

            });
            {{--jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {--}}
            {{--    if ( this.context.length ) {--}}
            {{--        body = [];--}}
            {{--        var params = table.ajax.params();--}}
            {{--        params.start = 0;--}}
            {{--        params.length = -1;--}}
            {{--        params.excel = true;--}}
            {{--        var jsonResult = $.ajax({--}}
            {{--            url: '{{ route('admin.settings.holidays.list') }}',--}}
            {{--            method: 'POST',--}}
            {{--            headers: {--}}
            {{--                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
            {{--            },--}}
            {{--            data:params,--}}
            {{--            success: function (result) {--}}
            {{--                head = [];--}}

            {{--                head.push('S.No');--}}
            {{--                head.push('Date');--}}
            {{--                head.push('Reason');--}}
            {{--                head.push('Created At');--}}
            {{--                head.push('Created By');--}}
            {{--                $.each(result.data, function(index, values) {--}}
            {{--                    row = [];--}}

            {{--                    row.push(index + 1);--}}
            {{--                    row.push(values.holiday);--}}
            {{--                    row.push(values.reason);--}}
            {{--                    row.push(values.created_at);--}}
            {{--                    row.push(values.created_by);--}}


            {{--                    body.push(row);--}}
            {{--                });--}}
            {{--            },--}}
            {{--            async: false--}}
            {{--        });--}}

            {{--        return {body: body, header: head};--}}
            {{--    }--}}
            {{--} );--}}
            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add',
                        className: 'btn btn-primary add_admin_product',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AdminProductModal').modal('show');
                        }
                    },
                    // {
                    //     extend: 'excelHtml5',
                    //     title: 'Holidays',
                    //     text: '<i class="la la-file-excel-o"></i> Excel',
                    // },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                language: {
                    processing: data_table_loader
                },
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('admin.settings.admin_product.list') }}',
                },
                rowId: 'id',
                // order: [[3, 'desc']],
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'trax_id', name: 'ad.trax_id', class: 'align-middle trax_id'},
                    {data: 'name', name: 'ad.name', class: 'align-middle name'},
                    {data: 'product_name', name: 's.name', class: 'align-middle product_name'},
                    {
                        data: 'action',
                        name: 'action',
                        class: 'text-center align-middle action p-1',
                        orderable: false,
                        searchable: false
                    }


                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
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

            //Submit form
            $("#add_admin_product").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    form.submit();
                }

            });

            //Update form
            $("#edit_admin_product").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    form.submit();
                }

            });

        });

        $("body").on('click','tr td.action .btn-group .dropdown-menu .dropdown-item.edit',function(){
            var admin_product_id = parseInt($(this).closest('tr').attr('id'));
            $.ajax({
                url: '{!! route("admin.settings.admin_product.edit") !!}',
                method:'POST',
                data:{
                    'admin_product_id' :admin_product_id,
                    '_token': '{{ csrf_token() }}'
                }
            }).done(function (data){

                if(data.status == 1){
                    $("#edit_admin_id").val(data.pms_admin_product.user_id).change();
                    $("#edit_product_id").val(data.pms_admin_product.product_id).change();
                    $("#admin_product_id").val(data.pms_admin_product.id);


                    $("#EditAdminProductModal").modal('show');


                }else{
                    toastr.error(data.error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }

            });
            // $("#admin_product_id").val($admin_product_id);
            // // $("#edit_admin_id").val()
            // // console.log($admin_product_id);
        });

    </script>
@endsection