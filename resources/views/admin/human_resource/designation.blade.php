@extends('admin.layout.master')

@section('title', 'Designation')

@section('content')
    <h1>Designation</h1>

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
                                    <th class="border-primary border-darken-1">Designation ID</th>
                                    <th class="border-primary border-darken-1">Designation Name</th>
                                    <th class="border-primary border-darken-1">Department Name</th>
                                    <th class="border-primary border-darken-1">Role</th>
                                    <th class="border-primary border-darken-1">Status</th>
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
    <div class="modal fade text-left" id="addDesignationModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="addDesignationModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Add Designation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.designation.add')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="addDesignationForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Designation Name*" data-rule-required="true" data-msg-required="Name is required">
                        </div>
                        <div class="form-group">
                            <select name="department_id" id="department" class="select2 form-control " data-rule-required="true" data-msg-required="Department is required" style="width: 100%">
                                @foreach($departments as $department)
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="role_id" id="role" class="select2 form-control " data-rule-required="true" data-msg-required="Role is required" style="width: 100%">
                            </select>
                        </div>
                        @if(session('role_id') == 1)
                        <div class="form-group">
                            <select name="hub_id[]" id="hubs" multiple class="select2 form-control " style="width: 100%">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" id="selectAll" class="btn btn-success">Select All</button>
                            <button type="button" id="unselectAll" class="btn btn-danger">Un-Select All</button>
                        </div>
                        @endif
                        <div class="form-group">
                            <textarea name="description" class="form-control" id="description" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="editDesignationModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="editDesignationModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Edit Designation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.designation.edit')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="editDesignationForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="designation_id" id="designation_id" value="">
                        <input type="hidden" id="role_dummy" value="">
                        <div class="form-group">
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="Designation Name*" data-rule-required="true" data-msg-required="Name is required">
                        </div>
                        <div class="form-group">
                            <select name="department_id" id="department_edit" disabled class="select2 form-control " data-rule-required="true" data-msg-required="Department is required" style="width: 100%">
                                @foreach($departments as $department)
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="role_id" id="role_edit" class="select2 form-control " data-rule-required="true" data-msg-required="Role is required" style="width: 100%">
                            </select>
                        </div>
                        @if(session('role_id') == 1)
                        <div class="form-group">
                            <select name="hub_id[]" id="hubs_edit" multiple class="select2 form-control " style="width: 100%">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" id="selectAll" class="btn btn-success">Select All</button>
                            <button type="button" id="unselectAll" class="btn btn-danger">Un-Select All</button>
                        </div>
                        @endif
                        <div class="form-group">
                            <textarea name="description" class="form-control" id="edit_description" placeholder="Description"></textarea>
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
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.designation.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Designation ID');
                            head.push('Designation Name');
                            head.push('Department Name');
                            head.push('Role');
                            head.push('Status');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.code);
                                row.push(values.name);
                                row.push(values.department);
                                row.push(values.role);
                                row.push(values.status);
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
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(482, session('permissions')))
                    {
                        text: 'Add Designation',
                        className: 'btn btn-primary add_designation',
                        action: function (e, dt, node, config) {
                            $('#addDesignationModal').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'Designation',
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
                ajax: '{{ route('admin.human_resource.designation.list') }}',
                order: [[1, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    {data: 'code', name: 'employee_designations.code', class: 'align-middle code'},
                    {data: 'name', name: 'employee_designations.name', class: 'align-middle name'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {data: 'role', name: 'r.name', class: 'align-middle role'},
                    {data: 'status', name: 'employee_designations.status', class: 'align-middle status'},
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
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Active</option>' +
                        '<option value="0">In-Active</option>' +
                        '</select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else {
                            var current = $(input).appendTo($(search)).on('change', function () {
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
            $('#addDesignationModal').on('hide.bs.modal', function () {
                $('#name').val('');
                $('#department').val('').trigger('change');
                $('#description').val('');
            });

            $('body').on('click', '.edit', function (e) {
                var id = $(this).data('target-id');
                var name = table.row($(this).parents('tr')).data().name;
                var department_id = table.row($(this).parents('tr')).data().department_id;
                var role_id = table.row($(this).parents('tr')).data().role_id;
                var description = table.row($(this).parents('tr')).data().description;
                var hubs_array = table.row($(this).parents('tr')).data().hubs;
                let hubs = [];
                $.each(hubs_array,function (i,v){
                    hubs.push(v['hub_id']);
                });
                $('#designation_id').val(id);
                $('#edit_name').val(name);
                $('#department_edit').val(department_id).trigger('change');
                $('#edit_description').val(description);
                $("#hubs_edit").val(hubs).trigger('change');
                console.log(hubs);
                $('#role_dummy').val(role_id);
                $('#editDesignationModal').modal('show');
            });

            $('body').on('click', '.enable', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to enable Designation!',
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
                        swal({
                            title: 'Please Wait!',
                            text: 'Designation is being Enabled',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.designation.status') !!}',
                            method: 'POST',
                            data: {
                                'id': id,
                                'status': 1,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 1) {
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
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.disable', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to disable Designation!',
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
                        swal({
                            title: 'Please Wait!',
                            text: 'Designation is being Disabled',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.designation.status') !!}',
                            method: 'POST',
                            data: {
                                'id': id,
                                'status': 0,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 1) {
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
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $("#addDesignationForm #department").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Department*",
                width:'100%',
                dropdownParent: $("#addDesignationForm")
            });

            @if(session('role_id') == 1)
            $("#addDesignationForm #hubs").select2({
                placeholder: "Select Hubs",
                width:'100%',
                dropdownParent: $("#addDesignationForm")
            });

            $("#addDesignationForm #selectAll").on('click',function (){
                $("#addDesignationForm #hubs > option").prop("selected","selected");
                $("#addDesignationForm #hubs").trigger("change");
            });

            $("#addDesignationForm #unselectAll").on('click',function (){
                $("#addDesignationForm #hubs > option").prop("selected","");
                $("#addDesignationForm #hubs").trigger("change");
            });
            @endif
            $("#editDesignationForm #department_edit").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Department*",
                width:'100%',
                dropdownParent: $("#editDesignationForm")
            });

            $("#editDesignationForm #role_edit").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Role*",
                width:'100%',
                dropdownParent: $("#editDesignationForm")
            });

            $("#addDesignationForm #role").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Role*",
                width:'100%',
                dropdownParent: $("#addDesignationForm")
            });

            @if(session('role_id') == 1)
            $("#editDesignationForm #hubs_edit").select2({
                placeholder: "Select Hubs",
                width:'100%',
                dropdownParent: $("#editDesignationForm")
            });

            $("#editDesignationForm #selectAll").on('click',function (){
                $("#editDesignationForm #hubs_edit > option").prop("selected","selected");
                $("#editDesignationForm #hubs_edit").trigger("change");
            });

            $("#editDesignationForm #unselectAll").on('click',function (){
                $("#editDesignationForm #hubs_edit > option").prop("selected","");
                $("#editDesignationForm #hubs_edit").trigger("change");
            });
            @endif

            $("#addDesignationForm #department").on('change',function (){
                department_id = $(this).val();
                $.ajax({
                    url: '{!! route('admin.human_resource.designation.roles') !!}',
                    method: 'POST',
                    data: {
                        'department_id': department_id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        $('#addDesignationForm #role').html('<option value="" selected>Select Role</option>');
                        if (data.status == 1) {
                            $.each(data.roles,function (i,v){
                                $('#addDesignationForm #role').append('<option value="'+v.id+'">'+v.name+'</option>');
                            });
                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
            });

            $("#editDesignationForm #department_edit").on('change',function (){
                department_id = $(this).val();
                $.ajax({
                    url: '{!! route('admin.human_resource.designation.roles') !!}',
                    method: 'POST',
                    data: {
                        'department_id': department_id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        $('#editDesignationForm #role_edit').html('<option value="" selected>Select Role</option>');
                        if (data.status == 1) {
                            $.each(data.roles,function (i,v){
                                $('#editDesignationForm #role_edit').append('<option value="'+v.id+'">'+v.name+'</option>');
                            });
                            $('#editDesignationForm #role_edit').val($('#editDesignationForm #role_dummy').val()).trigger('change');
                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
            });

            $("#addDesignationForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Designation is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $("#editDesignationForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Designation is being Updated!',
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