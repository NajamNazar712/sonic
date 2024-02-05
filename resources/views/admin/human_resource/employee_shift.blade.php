@extends('admin.layout.master')

@section('title', 'Employee Shifts')

@section('content')
    <h1>Employee Shifts</h1>

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
                                        <th class="border-primary border-darken-1">Name</th>
                                        <th class="border-primary border-darken-1">Start-time</th>
                                        <th class="border-primary border-darken-1">End-time</th>
                                        <th class="border-primary border-darken-1">Grace Minutes</th>
                                        <th class="border-primary border-darken-1">Shift Type</th>
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
    <div class="modal fade text-left" id="addShiftModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="addShiftModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Add Shift</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{ route('admin.human_resource.employee_shifts.add_shift') }}"
                        class="form-horizontal mb-1 justify-content-center" method="POST" id="addLocationForm"
                        novalidate="novalidate">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label><b>Shift Name</b></label>
                            <input type="text" name="name" id="name" class="form-control"
                                data-rule-required="true" data-msg-required="Name is required">
                        </div>
                        <div class="form-group">
                            <label><b>Shift Start-time</b></label>
                            <input type="time" name="start_time" id="start_time" class="form-control"
                                data-rule-required="true" data-msg-required="Start-time is required">
                        </div>
                        <div class="form-group">
                            <label for="end_time"><b>Shift End-time</b></label>
                            <input type="time" name="end_time" id="end_time" class="form-control"
                                data-rule-required="true" data-msg-required="End-time is required">
                        </div>

                        <div class="form-group">
                            <fieldset class="form-group">
                                <label for="shift_select"><b>Select Shift</b></label>
                                <select name="shift_select" id="shifts_select" class="form-control select2">
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="form-group">
                            <label><b>Shift Type</b></label>
                            <input type="text" name="extension_minutes" id="extension_minutes"
                                class="form-control extension_minutes" data-rule-required="true"
                                data-msg-required="Grace Minutes is required" data-rule-min="0"
                                data-msg-min="Grace minutes can not be less than 0" value="0">
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="editShiftModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="editShiftModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Edit Shift</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{ route('admin.human_resource.employee_shifts.edit_shift') }}"
                        class="form-horizontal mb-1 justify-content-center" method="POST" id="editLocationForm"
                        novalidate="novalidate">
                        {{ csrf_field() }}
                        <input type="hidden" name="shift_id" id="shift_id" value="">
                        <div class="form-group">
                            <label><b>Shift Name</b></label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                data-rule-required="true" data-msg-required="Name is required">
                        </div>
                        <div class="form-group">
                            <label><b>Shift Start-time</b></label>
                            <input type="time" name="start_time" id="edit_start_time" class="form-control"
                                data-rule-required="true" data-msg-required="Start-time is required">
                        </div>
                        <div class="form-group">
                            <label for="end_time"><b>Shift End-time</b></label>
                            <input type="time" name="end_time" id="edit_end_time" class="form-control"
                                data-rule-required="true" data-msg-required="End-time is required">
                        </div>

                        <div class="form-group">
                            <fieldset class="form-group">
                                <label for="shift_select"><b>Select Shift</b></label>
                                <select name="shift_select" id="edit_shifts_select" class="form-control select2">
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="form-group">
                            <label><b>Grace Minutes</b></label>
                            <input type="text" name="extension_minutes" id="edit_extension_minutes"
                                class="form-control extension_minutes" data-rule-required="true"
                                data-msg-required="Grace Minutes is required" data-rule-min="0"
                                data-msg-min="Grace minutes can not be less than 0" value="0">
                        </div>

                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary edit"
                                value="edit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">

@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/textarea/autosize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('.extension_minutes').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#shifts_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: "Select Shift Type",
                allowClear: true,
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.employee_shifts.list') }}',
                        data: params,
                        success: function(result) {
                            head = [];
                            head.push('S.No');
                            head.push('Name');
                            head.push('Start Time');
                            head.push('End Time');
                            head.push('Grace Minutes');
                            head.push('Shift Type');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.start_time_formatted);
                                row.push(values.end_time_formatted);
                                row.push(values.extension_minutes);
                                row.push(values.shift_type_id);

                                row.push(values.status);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {
                        body: body,
                        header: head
                    };
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    @if (session('role_id') == 1 || session('role_id') == 6 || in_array(593, session('permissions')))
                        {
                            text: 'Add Shift',
                            className: 'btn btn-primary add_shift',
                            action: function(e, dt, node, config) {
                                $('#addShiftModal').modal('show');
                            }
                        },
                    @endif {
                        extend: 'excel',
                        title: 'Employee Shifts',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                scrollX: true,
                scrollY: '500px',
                lengthMenu: [
                    [50, 100, 500, 1000, -1],
                    [50, 100, 500, 1000, 'All']
                ],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.human_resource.employee_shifts.list') }}',
                order: [
                    [1, 'desc']
                ],
                rowId: 'id',
                columns: [{
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function(data, type, row) {
                            return '';
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        class: 'align-middle name'
                    },
                    {
                        data: 'start_time_formatted',
                        name: 'start_time',
                        class: 'align-middle start_time_formatted'
                    },
                    {
                        data: 'end_time_formatted',
                        name: 'end_time',
                        class: 'align-middle end_time_formatted'
                    },
                    {
                        data: 'extension_minutes',
                        name: 'extension_minutes',
                        class: 'align-middle extension_minutes'
                    },
                    {
                        data: 'shift_type_id',
                        name: 'shift_type_id',
                        class: 'align-middle status'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        class: 'align-middle status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        class: 'align-middle text-center action',
                        orderable: false,
                        searchable: false
                    }
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    if (data.shift_type_id == 1) {
                        $('td:eq(5)', row).text('Employee Shift')
                    }else{
                        $('td:eq(5)', row).text('Contractual Shift')

                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                        .appendTo(this.api().table().header());

                    var td =
                        '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input =
                        '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon =
                        '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select =
                        '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Active</option>' +
                        '<option value="0">In-Active</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(
                                header).is('.start_time_formatted') || $(header).is(
                                '.end_time_formatted')) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.status')) {
                            $(status_select).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else {
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
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            $('#addShiftModal').on('hide.bs.modal', function() {
                $('#name').val('');
                $('#start_time').val('');
                $('#end_time').val('');
                $('#extension_minutes').val('');
            });

            $('body').on('click', '.edit', function(e) {
                var data = table.row($(this).parents('tr')).data()
                console.log(data)
                var id = $(this).data('target-id');
                var name = table.row($(this).parents('tr')).data().name;
                var start_time = table.row($(this).parents('tr')).data().start_time;
                var end_time = table.row($(this).parents('tr')).data().end_time;
                var extension_minutes = table.row($(this).parents('tr')).data().extension_minutes;
                var shifts_select = table.row($(this).parents('tr')).data().shift_type_id;
                $('#shift_id').val(id);
                $('#edit_name').val(name);
                $('#edit_start_time').val(start_time);
                $('#edit_end_time').val(end_time);
                $('#edit_extension_minutes').val(extension_minutes);
                $('#edit_shifts_select').val(shifts_select);

                $('#editShiftModal').modal('show');
            });

            $('body').on('click', '.enable', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to enable Shift!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Shift is being Enabled',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_shifts.status') !!}',
                                method: 'POST',
                                data: {
                                    'id': id,
                                    'status': 1,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
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

            $('body').on('click', '.disable', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to disable Shift!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Shift is being Disabled',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_shifts.status') !!}',
                                method: 'POST',
                                data: {
                                    'id': id,
                                    'status': 0,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
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

            $("#addLocationForm").validate({
                errorClass: "danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Location is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $("#editLocationForm").validate({
                errorClass: "danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Location is being Updated!',
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
