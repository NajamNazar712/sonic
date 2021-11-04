@extends('admin.layout.master')

@section('title', 'Department')

@section('content')
    <h1>Department</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Department ID</th>
                                    <th class="border-primary border-darken-1">Department Name</th>
                                    <th class="border-primary border-darken-1">Department Head</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="addDepartmentModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="addDepartmentModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Add Department</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.department.add')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="addDepartmentForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Department Name*" data-rule-required="true" data-msg-required="Name is required">
                        </div>
                        <div class="form-group">
                            <textarea name="description" class="form-control" id="description" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group text-left">
                            <select name="head_id" id="head_list" class="form-control select2 text-left" style="width: 100%;" data-rule-required="true" data-msg-required="Department Head is required">
                                @foreach($admins as $admin)
                                    <option value="{{$admin->id}}"> {{"$admin->name | $admin->trax_id"}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="editDepartmentModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="editDepartmentModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Edit Department</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.department.edit')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="editDepartmentForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="department_id" id="department_id" value="">
                        <div class="form-group">
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="Department Name*" data-rule-required="true" data-msg-required="Name is required">
                        </div>
                        <div class="form-group">
                            <textarea name="description" class="form-control" id="edit_description" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group text-left">
                            <select name="head_id" id="edit_head_list" class="form-control select2" style="width: 100%;" data-rule-required="true" data-msg-required="Department Head is required">
                                @foreach($admins as $admin)
                                    <option value="{{$admin->id}}"> {{"$admin->name | $admin->trax_id"}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary" value="edit">Update</button>
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

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#head_list').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Department Head *',
                dropdownParent: $("#addDepartmentForm")
            });
            $('#edit_head_list').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Department Head *',
                dropdownParent: $("#editDepartmentForm")
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.department.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Department ID');
                            head.push('Department Name');
                            head.push('Department Head');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.code);
                                row.push(values.name);
                                row.push(values.head);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(485, session('permissions')))
                    {
                        text: 'Add Department',
                        className: 'btn btn-primary add_department',
                        action: function (e, dt, node, config) {
                            $('#addDepartmentModal').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'Department',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.human_resource.department.list') }}',
                order: [[0, 'asc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    {data: 'code', name: 'admin_departments.code', class: 'align-middle code'},
                    {data: 'name', name: 'admin_departments.name', class: 'align-middle name'},
                    {data: 'head', name: 'a.name', class: 'align-middle head'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
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

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else {
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
            $('#addDepartmentModal').on('hide.bs.modal', function () {
                $('#name').val('');
                $('#description').val('');
            });

            $('body').on('click', '.edit', function (e) {
                var id = $(this).data('target-id');
                var name = table.row($(this).parents('tr')).data().name;
                var description = table.row($(this).parents('tr')).data().description;
                var head_id = table.row($(this).parents('tr')).data().head_id;
                $('#department_id').val(id);
                $('#edit_name').val(name);
                $('#edit_description').val(description);
                $('#edit_head_list').val(head_id).trigger('change');
                $('#editDepartmentModal').modal('show');
            });

            $("#addDepartmentForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Department is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $("#editDepartmentForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Department is being Updated!',
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